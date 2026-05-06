<?php
// classes/Database.php
// OOP Database Connection using PDO
// ─────────────────────────────────────────────────────────────────
//  ★  UPDATE THE FOUR LINES BELOW with your hosting panel values  ★
// ─────────────────────────────────────────────────────────────────

class Database {
    private static $instance = null;
    private $connection;

    private $host     = 'sql208.infinityfree.com';
    private $db_name  = 'if0_41796011_smart_order_db';
    private $username = 'if0_41796011';
    private $password = 'K84Zk4H9iy7j38A';

    private function __construct() {
        try {
            $this->connection = new PDO(
                "mysql:host={$this->host};dbname={$this->db_name};charset=utf8",
                $this->username,
                $this->password,
                array(
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                )
            );
        } catch (PDOException $e) {
            // Show a readable error instead of a blank page
            http_response_code(500);
            echo '<div style="font-family:monospace;background:#fee;border:1px solid #c00;padding:20px;margin:20px;border-radius:8px;">';
            echo '<strong>Database connection failed.</strong><br><br>';
            echo 'Error: ' . htmlspecialchars($e->getMessage()) . '<br><br>';
            echo 'Please check the host, db_name, username and password in <code>classes/Database.php</code>.';
            echo '</div>';
            exit;
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->connection;
    }
}