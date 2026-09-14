<?php

require_once __DIR__ . '/db.php';

class Maquinaria {
    private PDO $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function createMaquinaria(array $data) {
        $stmt = $this->conn->prepare("
        INSERT INTO Maquinaria(Matricula, Estado, Modelo, ID_Planta, Tipo)
        VALUES (?, ?, ?, ?, ?)
        ");

        $stmt->bindParam(1, $data['Matricula']);
        $stmt->bindParam(2, $data['Estado']);
        $stmt->bindParam(3, $data['Modelo']);
        $stmt->bindParam(4, $data['ID_Planta']);
        $stmt->bindParam(5, $data['Tipo']);

        $stmt->execute();

        return $this->conn->lastInsertId();
    }

    public function readMaquinaria(int $id): array {
        $stmt = $this->conn->prepare("
        SELECT * FROM Maquinaria
        WHERE ID_Maquinaria = ?
        ");

        $stmt->bindParam(1, $id);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result;
    }

    public function updateMaquinaria(array $data) {
        $stmt = $this->conn->prepare("
        UPDATE Maquinaria
        SET
        Matricula = ?,
        Estado = ?,
        Modelo = ?,
        ID_Planta = ?,
        Tipo = ?
        WHERE ID_Maquinaria = ?
        ");

        $stmt->bindParam(1, $data['Matricula']);
        $stmt->bindParam(2, $data['Estado']);
        $stmt->bindParam(3, $data['Modelo']);
        $stmt->bindParam(4, $data['ID_Planta']);
        $stmt->bindParam(5, $data['Tipo']);
        $stmt->bindParam(6, $data['ID_Maquinaria']);

        $stmt->execute();
    }

    public function deleteMaquinaria(int $id) {
        $stmt = $this->conn->prepare("
            DELETE FROM Maquinaria
            WHERE ID_Maquinaria = ?
        ");

        $stmt->bindParam(1, $id);
        $stmt->execute();
    }
}
