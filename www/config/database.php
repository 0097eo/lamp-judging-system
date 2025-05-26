<?php

$mysql_url = $_ENV['MYSQL_URL'] ?? null;

if ($mysql_url) {
    $parsed = parse_url($mysql_url);
    define('DB_HOST', $parsed['host']);
    define('DB_USER', $parsed['user']);
    define('DB_PASS', $parsed['pass']);
    define('DB_NAME', ltrim($parsed['path'], '/'));
    define('DB_PORT', $parsed['port'] ?? 3306);
} else {
    define('DB_HOST', $_ENV['MYSQLHOST'] ?? 'localhost');
    define('DB_USER', $_ENV['MYSQLUSER'] ?? 'okelo');
    define('DB_PASS', $_ENV['MYSQLPASSWORD'] ?? 'kothbiro');
    define('DB_NAME', $_ENV['MYSQLDATABASE'] ?? 'judging_system');
    define('DB_PORT', $_ENV['MYSQLPORT'] ?? 3306);
}

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
                    PDO::ATTR_TIMEOUT => 30,
                    PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false
                ]
            );
            
            $this->connection->exec("SET time_zone = '+03:00'");
            
        } catch (PDOException $e) {
            // Debug information
            $debug_info = [
                'Host' => DB_HOST,
                'User' => DB_USER,
                'Database' => DB_NAME,
                'Port' => DB_PORT,
                'DSN' => $dsn ?? 'Not set',
                'Error' => $e->getMessage(),
                'Available Environment Variables' => array_keys($_ENV)
            ];
            
            echo "<h3>Database Connection Debug Info:</h3>";
            echo "<pre>" . print_r($debug_info, true) . "</pre>";
            
            error_log("Database connection failed: " . $e->getMessage());
            die("Connection failed. Check debug info above.");
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
    
    // Initialize database tables from init.sql file
    public function initializeTables() {
        try {
            // Check if tables already exist to avoid re-running initialization
            $tables_exist = $this->checkTablesExist();
            if ($tables_exist) {
                return; 
            }
            
            $init_sql_paths = [
                __DIR__ . '/../../mysql/init.sql',
                __DIR__ . '/../mysql/init.sql',
                $_SERVER['DOCUMENT_ROOT'] . '/../mysql/init.sql',
                '/var/www/mysql/init.sql'
            ];
            
            $init_sql_content = null;
            $used_path = null;
            
            foreach ($init_sql_paths as $path) {
                if (file_exists($path) && is_readable($path)) {
                    $init_sql_content = file_get_contents($path);
                    $used_path = $path;
                    break;
                }
            }
            
            if (!$init_sql_content) {
                error_log("init.sql file not found in any of the expected locations");
                throw new Exception("Database initialization file (init.sql) not found");
            }
            
            error_log("Using init.sql from: " . $used_path);
            
            $statements = $this->splitSqlStatements($init_sql_content);
            
            foreach ($statements as $statement) {
                $statement = trim($statement);
                if (!empty($statement)) {
                    $this->connection->exec($statement);
                }
            }
            
            error_log("Database tables initialized successfully from init.sql");
            
        } catch (Exception $e) {
            error_log("Table initialization failed: " . $e->getMessage());
            throw $e;
        }
    }
    
    private function checkTablesExist() {
        try {
            $result = $this->query("SHOW TABLES LIKE 'judges'")->fetch();
            return !empty($result);
        } catch (PDOException $e) {
            return false;
        }
    }
    
    private function splitSqlStatements($sql) {
        $sql = preg_replace('/--.*$/m', '', $sql);
        $sql = preg_replace('/\/\*.*?\*\//s', '', $sql);
        $statements = [];
        $current_statement = '';
        $in_string = false;
        $string_char = null;
        
        for ($i = 0; $i < strlen($sql); $i++) {
            $char = $sql[$i];
            
            if (!$in_string && ($char === '"' || $char === "'")) {
                $in_string = true;
                $string_char = $char;
            } elseif ($in_string && $char === $string_char) {
                if ($i > 0 && $sql[$i-1] !== '\\') {
                    $in_string = false;
                    $string_char = null;
                }
            } elseif (!$in_string && $char === ';') {
                $statements[] = $current_statement;
                $current_statement = '';
                continue;
            }
            
            $current_statement .= $char;
        }
        
        if (trim($current_statement)) {
            $statements[] = $current_statement;
        }
        
        return $statements;
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