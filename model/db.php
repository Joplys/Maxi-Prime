<?php
class Database {

    private PDO $conn;

    public function __construct() {
        $this->conn = new PDO(
            "mysql:host=db;port=3306;dbname=proyecto;charset=utf8mb4",
            "proyecto",
            "proyecto_password"
        );

        $this->conn->setAttribute(
            PDO::ATTR_ERRMODE,
            PDO::ERRMODE_EXCEPTION
        );

        $this->conn->setAttribute(
            PDO::ATTR_DEFAULT_FETCH_MODE,
            PDO::FETCH_ASSOC
        );
    }

    public function getConnection(): PDO {
        return $this->conn;
    }
}
