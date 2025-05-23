<?php
header("Content-Type: application/json");
require_once '../config/database.php';

$db = new Database();

try {
    $scores = $db->query("SELECT * FROM scoreboard")->fetchAll();
    echo json_encode([
        "status" => "success",
        "data" => $scores
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Failed to retrieve scoreboard data.",
        "error" => $e->getMessage()
    ]);
}
