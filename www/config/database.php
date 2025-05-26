<?php
define('DB_HOST', $_ENV['MYSQLHOST'] ?? 'localhost');
define('DB_USER', $_ENV['MYSQLUSER'] ?? 'okelo');
define('DB_PASS', $_ENV['MYSQLPASSWORD'] ?? 'kothbiro');
define('DB_NAME', $_ENV['MYSQLDATABASE'] ?? 'judging_system');
define('DB_PORT', $_ENV['MYSQLPORT'] ?? 3306);

class Database {
    private $connection;
    
    public function __construct() {
        $this->connect();
    }
    
    private function connect() {
        try {
            // Include port in DSN for Railway MySQL
            $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            
            $this->connection = new PDO(
                $dsn,
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::ATTR_TIMEOUT => 10,
                    PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false
                ]
            );
            

            $this->connection->exec("SET time_zone = '+03:00'");
            
        } catch (PDOException $e) {
            // Log error details for debugging
            error_log("Database connection failed: " . $e->getMessage());
            die("Connection failed. Please check database configuration.");
        }
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    public function query($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log("Query failed: " . $e->getMessage() . " SQL: " . $sql);
            throw $e;
        }
    }
    
    // Initialize database tables if they don't exist
    public function initializeTables() {
        try {
            // Create judges table
            $this->connection->exec("
                CREATE TABLE IF NOT EXISTS judges (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    username VARCHAR(50) UNIQUE NOT NULL,
                    display_name VARCHAR(100) NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )
            ");
            
            // Create users table
            $this->connection->exec("
                CREATE TABLE IF NOT EXISTS users (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    username VARCHAR(50) UNIQUE NOT NULL,
                    display_name VARCHAR(100) NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )
            ");
            
            // Create scores table
            $this->connection->exec("
                CREATE TABLE IF NOT EXISTS scores (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    judge_id INT NOT NULL,
                    user_id INT NOT NULL,
                    points INT NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (judge_id) REFERENCES judges(id) ON DELETE CASCADE,
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                    UNIQUE KEY unique_judge_user (judge_id, user_id)
                )
            ");
            
            // Create scoreboard view
            $this->connection->exec("
                CREATE OR REPLACE VIEW scoreboard AS
                SELECT 
                    u.id as user_id,
                    u.display_name as user_name,
                    COALESCE(SUM(s.points), 0) as total_points,
                    COUNT(s.id) as total_scores
                FROM users u
                LEFT JOIN scores s ON u.id = s.user_id
                GROUP BY u.id, u.display_name
                ORDER BY total_points DESC
            ");
            
        } catch (PDOException $e) {
            error_log("Table initialization failed: " . $e->getMessage());
            throw $e;
        }
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
            "INSERT INTO scores (judge_id, user_id, points) VALUES (?, ?, ?) 
             ON DUPLICATE KEY UPDATE points = VALUES(points)",
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
    
    // Check database connection health
    public function healthCheck() {
        try {
            $this->query("SELECT 1")->fetch();
            return true;
        } catch (PDOException $e) {
            error_log("Health check failed: " . $e->getMessage());
            return false;
        }
    }
}

// Auto-initialize tables when database class is loaded
try {
    $db = new Database();
    $db->initializeTables();
} catch (Exception $e) {
    error_log("Database initialization error: " . $e->getMessage());
}
?>