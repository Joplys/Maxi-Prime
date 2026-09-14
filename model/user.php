<?php

require_once __DIR__ . '/db.php';

class User {
    private PDO $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    // $data es un array asociativo que guarda
    // tanto la informacion del usuario (Usuarios)
    // como la especifica de su rol (U_Rol)

    public function createUser(array $data) { 
        try {
            $this->conn->beginTransaction();

            $stmt = $this->conn->prepare("
            INSERT INTO
            Usuarios(Nombre, Correo_Electronico, Contrasena, Tipo)
            VALUES (?,?,?,?)
            ");

            $contraHash = password_hash($data['Contrasena'], PASSWORD_DEFAULT);

            $stmt->bindParam(1, $data['Nombre']);
            $stmt->bindParam(2, $data['Correo_Electronico']);
            $stmt->bindParam(3, $contraHash);
            $stmt->bindParam(4, $data['Tipo']);

            $stmt->execute();

            $idUsuario = $this->conn->lastInsertId();

            switch($data['Tipo']) {
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
                $stmt->bindParam(2, $data['ID_Planta_Asig']);
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

    public function readUser(int $id, string $tipo): array {
        $tablas = [
            'U_Administrativo',
            'U_Vecino',
            'U_Gestor_De_Planta',
            'U_Camionero'
        ];
        
        if (!in_array($tipo, $tablas, true)) {
            throw new Exception("Tipo de usuario no válido");
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

    public function updateUser(array $data) {
        try {
            $this->conn->beginTransaction();

            $stmt = $this->conn->prepare("
            UPDATE Usuarios
            SET
            Nombre = ?,
            Correo_Electronico = ?,
            Contrasena = ?
            WHERE ID_Usuario = ?
            ");

            $contraHash = password_hash($data['Contrasena'], PASSWORD_DEFAULT);

            $stmt->bindParam(1, $data['Nombre']);
            $stmt->bindParam(2, $data['Correo_Electronico']);
            $stmt->bindParam(3, $contraHash);
            $stmt->bindParam(4, $data['ID_Usuario']);

            $stmt->execute();

            switch($data['Tipo']) {
            case "U_Administrativo":
                // no tiene info especifica
            break;
            case "U_Vecino":
                // no tiene info especifica
            break;
            case "U_Gestor_De_Planta":
                $stmt = $this->conn->prepare("
                UPDATE U_Gestor_De_Planta
                SET 
                ID_Planta_Asig = ?
                WHERE ID_Usuario = ?
                ");
                $stmt->bindParam(1, $data['ID_Planta_Asig']);
                $stmt->bindParam(2, $data['ID_Usuario']);
                $stmt->execute();
            break;
            case "U_Camionero":
                $stmt = $this->conn->prepare("
                UPDATE U_Camionero
                SET 
                ID_Ruta_Asig = ?,
                ID_Flota_Asig = ?
                WHERE ID_Usuario = ?
                ");
                $stmt->bindParam(1, $data['ID_Ruta_Asig']);
                $stmt->bindParam(2, $data['ID_Flota_Asig']);
                $stmt->bindParam(3, $data['ID_Usuario']);
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

    public function deleteUser(int $id) {
        try {
            $this->conn->beginTransaction();

            // on delete cascade ya hace que el resto de las filas se eliminen
            $stmt = $this->conn->prepare("
            DELETE FROM Usuarios WHERE ID_Usuario = ?
            ");

            $stmt->bindParam(1, $id);

            $stmt->execute();

            $this->conn->commit();
            
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    public function loginUser(string $email, string $contrasena) {
        $stmt = $this->conn->prepare("
        SELECT Contrasena FROM Usuarios WHERE Correo_Electronico = ?
        ");
        $stmt->bindParam(1, $email);
        $stmt->execute();

        $passhash = $stmt->fetchColumn();

        if (empty($passhash)) {
            throw new Exception("Credenciales incorrectas.");
        }

        if (password_verify($contrasena, $passhash)) {
            return true;
        } else {
            return false;
        }
    }
}
?>
