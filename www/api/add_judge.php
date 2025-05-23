<?php
require_once '../config/database.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $display_name = $_POST['display_name'] ?? '';
    if ($username && $display_name) {
        $db = new Database();
        $db->addJudge($username, $display_name);
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
    }
}