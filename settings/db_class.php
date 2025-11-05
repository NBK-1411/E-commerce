<?php
// Ensure database credentials are loaded before using constants
if (!defined('DB_HOST')) {
    require_once __DIR__ . '/db_cred.php';
}

class Database {
    private $host = DB_HOST;
    private $user = DB_USER;
    private $pass = DB_PASS;
    private $db_name = DB_NAME;
    private $conn;
    private $stmt;

    public function connect() {
        $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->db_name);
        
        if ($this->conn->connect_error) {
            die('Connection Error: ' . $this->conn->connect_error);
        }
        
        return $this->conn;
    }

    public function write($query, $types = '', $params = []) {
        $this->stmt = $this->conn->prepare($query);
        
        if (!$this->stmt) {
            return ['success' => false, 'message' => 'Prepare failed: ' . $this->conn->error];
        }
        
        if (!empty($params)) {
            $this->stmt->bind_param($types, ...$params);
        }
        
        if ($this->stmt->execute()) {
            return ['success' => true, 'message' => 'Operation successful', 'insert_id' => $this->stmt->insert_id];
        } else {
            return ['success' => false, 'message' => 'Execute failed: ' . $this->stmt->error];
        }
    }

    public function read($query, $types = '', $params = []) {
        try {
            if (!$this->conn) {
                $this->connect();
            }
            
            $this->stmt = $this->conn->prepare($query);
            
            if (!$this->stmt) {
                error_log("Database read prepare failed: " . $this->conn->error . " | Query: " . $query);
                return [];
            }
            
            if (!empty($params)) {
                $this->stmt->bind_param($types, ...$params);
            }
            
            if (!$this->stmt->execute()) {
                error_log("Database read execute failed: " . $this->stmt->error . " | Query: " . $query);
                return [];
            }
            
            $result = $this->stmt->get_result();
            
            if (!$result) {
                error_log("Database read get_result failed: " . $this->conn->error);
                return [];
            }
            
            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            
            return $data;
        } catch (Exception $e) {
            error_log("Database read exception: " . $e->getMessage());
            return [];
        }
    }

    public function getConnection() {
        return $this->conn;
    }

    public function close() {
        if ($this->stmt) {
            $this->stmt->close();
        }
        if ($this->conn) {
            $this->conn->close();
        }
    }
}
?>
