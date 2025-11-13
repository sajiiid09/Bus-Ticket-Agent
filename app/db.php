<?php
/**
 * Database Connection Wrapper
 * Centralized DB connection using mysqli
 */

class Database {
    private static $instance = null;
    private $connection;

    private function __construct() {
        $this->connection = mysqli_connect('localhost', 'root', '', 'bus_service');
        
        if (!$this->connection) {
            die('Database connection failed: ' . mysqli_connect_error());
        }
        
        mysqli_set_charset($this->connection, 'utf8');
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->connection;
    }

    public function query($sql) {
        return mysqli_query($this->connection, $sql);
    }

    public function escape($string) {
        return mysqli_real_escape_string($this->connection, $string);
    }

    public function fetchArray($result) {
        return mysqli_fetch_array($result);
    }

    public function fetchAssoc($result) {
        return mysqli_fetch_assoc($result);
    }

    public function numRows($result) {
        return mysqli_num_rows($result);
    }

    public function affectedRows() {
        return mysqli_affected_rows($this->connection);
    }

    public function getLastId() {
        return mysqli_insert_id($this->connection);
    }

    public function closeConnection() {
        if ($this->connection) {
            mysqli_close($this->connection);
        }
    }
}

// Shorthand helper function
function getDB() {
    return Database::getInstance()->getConnection();
}
?>
