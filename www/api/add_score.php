<?php
require_once '../config/database.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judge_id = (int)($_POST['judge_id'] ?? 0);
    $user_id = (int)($_POST['user_id'] ?? 0);
    $points = (int)($_POST['points'] ?? -1);
    if ($judge_id && $user_id && $points >= 0 && $points <= 100) {
        $db = new Database();
        $db->addScore($judge_id, $user_id, $points);
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
    }
}