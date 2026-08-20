<?php

require_once __DIR__ . '/db.php';

class User {
    private $db;
    private $conn = $db->getConnection();

    public function createUser($type, $data) { 

        try {
            $this->conn->beginTransaction();

            $stmt = $this->conn->prepare("
            INSERT INTO Usuarios(Nombre, Correo_Electronico, Contrasena)
            VALUES (?,?,?);
            ");

            $stmt->bindParam(1, $data['nombre']);
            $stmt->bindParam(2, $data['correo']);
            $stmt->bindParam(3, $data['contrasena']);

            $stmt->execute();

            $idUsuario = $this->conn->lastInsertId();

            switch($type) {
            case "admin":
                $stmt = $this->conn->prepare("
                INSERT INTO U_Administrativo(ID_Usuario)
                VALUES (?)
                ");

                $stmt->bindParam(1, $idUsuario);

                $stmt->execute();
            break;
            case "vecino":
            break;
            case "gestor":
            break;
            case "camionero":
            break;
            }

            $this->conn->commit();

        } catch (Exception $e) {
            $this->conn->rollBack(); 
            throw $e;
        }
    }

    public function readUser($id) {
        
    }

    public function updateUser($type, $data) {

    }

    public function deleteUser($id) {

    }

}
?>
