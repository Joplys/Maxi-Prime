<?php

require_once __DIR__ . './db.php';

class User {
    private $conn;

    /*
     * $data = [
     * "type" => "administrativo"
     * ];
     */

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function createUser($data) { 
        try {
            $this->conn->beginTransaction();

            $stmt = $this->conn->prepare("
            INSERT INTO Usuarios(Nombre, Correo_Electronico, Contrasena)
            VALUES (?,?,?)
            ");

            $stmt->bindParam(1, $data['nombre']);
            $stmt->bindParam(2, $data['correo']);
            $stmt->bindParam(3, $data['contrasena']);

            $stmt->execute();

            $idUsuario = $this->conn->lastInsertId();

            switch($data['type']) {
            case "U_Administrativo":
                $stmt = $this->conn->prepare("
                INSERT INTO U_Administrativo(ID_Usuario)
                VALUES (?)
                ");

                $stmt->bindParam(1, $idUsuario);

                $stmt->execute();
            break;
            case "U_Vecino":
                $stmt = $this->conn->prepare("
                INSERT INTO U_Vecino(ID_Usuario) 
                VALUES (?)
                ");

                $stmt->bindParam(1, $idUsuario);

                $stmt->execute();
            break;
            case "U_Gestor_De_Planta":
                $stmt = $this->conn->prepare("
                INSERT INTO U_Gestor_De_Planta(ID_Usuario, ID_Planta_Asig)
                VALUES (?, ?)
                ");

                $stmt->bindParam(1, $idUsuario);
                $stmt->bindParam(2, $data['id_planta']);

                $stmt->execute();
            break;
            case "U_Camionero":
                $stmt = $this->conn->prepare("
                INSERT INTO U_Camionero(ID_Usuario)
                VALUES (?)
                ");

                $stmt->bindParam(1, $idUsuario);

                $stmt->execute();
            break;
            default:
                $this->conn->rollBack();
                throw new Exception("Tipo de usuario no especificado");
                return;
            break;
            }

            $this->conn->commit();

        } catch (Exception $e) {
            $this->conn->rollBack(); 
            throw $e;
        }
    }

    public function readUser($id, $type) {
        $this->conn->prepare("
        SELECT * FROM Usuarios
        WHERE ID_Usuario = ?
        ");
    }

    public function updateUser($data) {
        try {
            $this->conn->beginTransaction();

            $stmt = $this->conn->prepare("
            SELECT * FROM Usuarios
            WHERE ID_Usuario = ?
            ");

            $stmt->bindParam(1, );

            $stmt->execute();

            $this->conn->commit();

        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    public function deleteUser($id, $type) {
        try {
            $this->conn->beginTransaction();

            $stmt = $this->conn->prepare("
            DELETE FROM Usuarios WHERE ID_Usuario = ?
            ");

            $stmt->bindParam(1, $id);

            $this->conn->commit();
            
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }
}
?>
