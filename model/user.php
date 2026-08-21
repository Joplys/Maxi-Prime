<?php

require_once __DIR__ . './db.php';

class User {
    private $conn;

    //TODO: Guardar a los tipos de usuario en un array asociativo aqui.
    //asi no hay que andar marabaleando tipos de usuarios.

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function isUserInsideTable($id, $table) {
        try {
            $this->conn->beginTransaction();

            $stmt = $this->conn->prepare("SELECT * FROM (?) WHERE id = (?);");
            $stmt->bindParam(1, $table);
            $stmt->bindParam(2, $id);
            $stmt->execute();

            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!empty($data)) {
                // user found, awesome! :D
                return true;
            } else {
                // user not found, big sad :(
                return false;
            }

        } catch (Exception $e) {
            $this->conn->rollBack(); 
            throw $e;
        }
    }

    public function createUser($data, $type) { 
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
                VALUES (?);
                ");

                $stmt->bindParam(1, $idUsuario);

                $stmt->execute();
            break;
            case "vecino":
                $stmt = $this->conn->prepare("
                INSERT INTO U_Vecino(ID_Usuario) 
                VALUES (?);
                ");

                $stmt->bindParam(1, $idUsuario);

                $stmt->execute();
            break;
            case "gestor":
                $stmt = $this->conn->prepare("
                INSERT INTO U_Gestor_De_Planta(ID_Usuario, ID_Planta_Asig)
                VALUES (?, ?);
                ");

                $stmt->bindParam(1, $idUsuario);
                $stmt->bindParam(2, $data['id_planta']);

                $stmt->execute();
            break;
            case "camionero":
                $stmt = $this->conn->prepare("
                INSERT INTO U_Camionero(ID_Usuario)
                VALUES (?);
                ");

                $stmt->bindParam(1, $idUsuario);

                $stmt->execute();
            break;
            default:
            //TODO: Add default
            break;
            }

            $this->conn->commit();

        } catch (Exception $e) {
            $this->conn->rollBack(); 
            throw $e;
        }
    }

    public function readUser($id) {
        try {
        } catch (Exception $e) {
            $this->conn->rollBack(); 
            throw $e;
        }
    }

    public function updateUser($data, $type) {
        try {
            $this->conn->beginTransaction();

            // Conseguir los datos del usuario
            $stmt = $this->conn->prepare("
            SELECT * FROM Usuarios
            WHERE ID_Usuario = ?;
            ");

            $stmt->execute();

            $this->conn->commit();

        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    public function deleteUser($id) {
        try {
        } catch (Exception $e) {
            $this->conn->rollBack();
        }
    }

}
?>
