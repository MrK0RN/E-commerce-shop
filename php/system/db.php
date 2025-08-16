<?php
// db_connection.php

class DBConnection {
    private $host;
    private $username;
    private $password;
    private $database;
    private $conn;

    public function __construct() {
        $this->host = 'localhost'; // Ваш хост MySQL
        $this->username = 'your_username'; // Ваш пользователь MySQL
        $this->password = 'your_password'; // Ваш пароль MySQL
        $this->database = 'rollershutters_db'; // Название вашей БД
        
        $this->connect();
        $this->createTables();
    }

    private function connect() {
        $this->conn = new mysqli($this->host, $this->username, $this->password, $this->database);
        
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
        
        $this->conn->set_charset("utf8mb4");
    }

    private function createTables() {
        $sql = [
            "CREATE TABLE IF NOT EXISTS contacts (
                id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                data_name VARCHAR(12) NOT NULL,
                data_value VARCHAR(100) NOT NULL,
                show VARCHAR(20) NOT NULL
            ) ENGINE=InnoDB",
            
            "CREATE TABLE IF NOT EXISTS quotes (
                id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                contact_id INT(6) UNSIGNED,
                width DECIMAL(10,2),
                height DECIMAL(10,2),
                material ENUM('aluminum', 'steel') NOT NULL,
                color VARCHAR(50),
                automation TINYINT(1) DEFAULT 0,
                notes TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (contact_id) REFERENCES contacts(id) ON DELETE CASCADE
            ) ENGINE=InnoDB",
            
            "CREATE TABLE IF NOT EXISTS subscribers (
                id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                email VARCHAR(100) NOT NULL UNIQUE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB"
        ];

        foreach ($sql as $query) {
            if (!$this->conn->query($query)) {
                error_log("Error creating table: " . $this->conn->error);
            }
        }
    }

    public function getConnection() {
        return $this->conn;
    }

    public function closeConnection() {
        if ($this->conn) {
            $this->conn->close();
        }
    }

    // Метод для безопасного выполнения запросов
    public function query($sql, $params = [], $types = '') {
        $stmt = $this->conn->prepare($sql);
        
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        
        return $result;
    }

    // Метод для вставки данных
    public function insert($table, $data) {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $types = str_repeat('s', count($data));
        $values = array_values($data);
        
        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($types, ...$values);
        $result = $stmt->execute();
        $insertId = $stmt->insert_id;
        $stmt->close();
        
        return $result ? $insertId : false;
    }
}

// Создаем экземпляр подключения
$db = new DBConnection();

// Функция для удобного доступа к подключению
function getDBConnection() {
    global $db;
    return $db->getConnection();
}
?>