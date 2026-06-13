<?php
/**
 * Faculty of Engineering at Shubra - Benha University
 * Database Connection Class using PDO
 * Fixed: Consistent timezone handling, proper error handling
 */

// Database Configuration
$db_config = [
    'host' => 'localhost',
    'database' => 'benha_engineering',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci'
];

class Database {
    private static $instance = null;
    private $pdo;
    private $stmt;

    private function __construct() {
        global $db_config;
        try {
            $dsn = "mysql:host={$db_config['host']};dbname={$db_config['database']};charset={$db_config['charset']}";

            // Get current timezone offset from PHP
            $tz = new DateTimeZone('Africa/Cairo');
            $dt = new DateTime('now', $tz);
            $offset = $dt->format('P'); // Format: +02:00 or +03:00

            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                // Sync MySQL timezone with PHP timezone dynamically
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$db_config['charset']} COLLATE {$db_config['collation']}; SET time_zone = '{$offset}'; SET SESSION sql_mode = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';"
            ];

            $this->pdo = new PDO($dsn, $db_config['username'], $db_config['password'], $options);
        } catch (PDOException $e) {
            error_log("Database Connection Error: " . $e->getMessage());
            die("Database connection failed. Please contact the administrator.");
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->pdo;
    }

    public function query($sql, $params = []) {
        $this->stmt = $this->pdo->prepare($sql);
        $this->stmt->execute($params);
        return $this;
    }

    public function fetchAll() {
        return $this->stmt->fetchAll();
    }

    public function fetch() {
        return $this->stmt->fetch();
    }

    public function rowCount() {
        return $this->stmt->rowCount();
    }

    public function lastInsertId() {
        return $this->pdo->lastInsertId();
    }

    public function beginTransaction() {
        return $this->pdo->beginTransaction();
    }

    public function commit() {
        return $this->pdo->commit();
    }

    public function rollback() {
        return $this->pdo->rollback();
    }
}

// Helper function to get DB instance
define('DB', Database::getInstance());