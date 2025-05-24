<?php
// Database configuration
define('DB_HOST', 'db');
define('DB_USER', 'okelo');
define('DB_PASS', 'kothbiro');
define('DB_NAME', 'judging_system');

class Database {
    private $connection;
    
    public function __construct() {
        $this->connect();
    }
    
    private function connect() {
        try {
            $this->connection = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    // Helper method to execute queries
    public function query($sql, $params = []) {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    
    // Get all judges
    public function getJudges() {
        return $this->query("SELECT * FROM judges ORDER BY display_name")->fetchAll();
    }
    
    // Get all users/participants
    public function getUsers() {
        return $this->query("SELECT * FROM users ORDER BY display_name")->fetchAll();
    }
    
    // Get scoreboard data
    public function getScoreboard() {
        return $this->query("SELECT * FROM scoreboard")->fetchAll();
    }
    
    // Add new judge
    public function addJudge($username, $display_name) {
        return $this->query(
            "INSERT INTO judges (username, display_name) VALUES (?, ?)",
            [$username, $display_name]
        );
    }
    
    // Add new user/participant
    public function addUser($username, $display_name) {
        return $this->query(
            "INSERT INTO users (username, display_name) VALUES (?, ?)",
            [$username, $display_name]
        );
    }
    
    // Add new score 
    public function addScore($judge_id, $user_id, $points) {
        return $this->query(
            "INSERT INTO scores (judge_id, user_id, points) VALUES (?, ?, ?)",
            [$judge_id, $user_id, $points]
        );
    }
    
    // Get scores for a specific judge
    public function getJudgeScores($judge_id) {
        return $this->query(
            "SELECT s.*, u.display_name as user_name 
             FROM scores s 
             JOIN users u ON s.user_id = u.id 
             WHERE s.judge_id = ? 
             ORDER BY s.created_at DESC, u.display_name",
            [$judge_id]
        )->fetchAll();
    }
    
}
?>