<?php

require_once __DIR__ . './db.php';

class User {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function createUser($data) { 
        try {
            // Utilizar vecino como default si no se pasa un tipo
            $tipo = $data['tipo'] ?? 'U_Vecino';

            $this->conn->beginTransaction();

            $stmt = $this->conn->prepare("
            INSERT INTO Usuarios(Nombre, Correo_Electronico, Contrasena, Tipo)
            VALUES (?,?,?,?)
            ");

            $stmt->bindParam(1, $data['nombre']);
            $stmt->bindParam(2, $data['correo']);
            $stmt->bindParam(3, $data['contrasena']);
            $stmt->bindParam(4, $tipo);

            $stmt->execute();

            $idUsuario = $this->conn->lastInsertId();

            switch($tipo) {
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
                throw new Exception("Tipo de usuario no especificado");
            break;
            }

            $this->conn->commit();

        } catch (Exception $e) {
            $this->conn->rollBack(); 
            throw $e;
        }
    }

    public function readUser($id, $tipo) {

        $tiposdeusuario = [
            "U_Administrativo",
            "U_Vecino",
            "U_Gestor_De_Planta",
            "U_Camionero"
        ];

        if(!in_array($tipo, $tiposdeusuario, true)) {
            return false;
        }

        $stmt = $this->conn->prepare("
        SELECT *
        FROM Usuarios
        JOIN $tipo 
            ON Usuarios.ID_Usuario = $tipo.ID_Usuario
        WHERE Usuarios.ID_Usuario = ?
        ");

        $stmt->bindParam(1, $id);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);

    }

    public function updateUser($data) {
        try {
            $this->conn->beginTransaction();

            $stmt = $this->conn->prepare("
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

            // on delete cascade ya hace que el resto de las filas se eliminen
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
