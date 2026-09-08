<?php

require_once __DIR__ . './db.php';

class Element {
    private PDO $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function createElem(array $data) { 
        try {
            $this->conn->beginTransaction();

            $stmt = $this->conn->prepare("
            INSERT INTO
            Elementos(Estado, ID_Ubicacion, ID_Districto, Tipo)
            VALUES (?,?,?,?)
            ");

            $stmt->bindParam(1, $data['Estado']);
            $stmt->bindParam(2, $data['ID_Ubicacion']);
            $stmt->bindParam(3, $data['ID_Districto']);
            $stmt->bindParam(4, $data['Tipo']);

            $stmt->execute();

            $idElem = $this->conn->lastInsertId();

            //cambiar
            switch($data['Tipo']) {
            case "Elem_Contenedores":
                $stmt = $this->conn->prepare("
                INSERT INTO Elem_Contenedores(ID_Elemento, N_Serie, Tipo_Cont, Tipo_Res)
                VALUES (?, ?, ?, ?)
                ");

                $stmt->bindParam(1, $idElem);
                $stmt->bindParam(2, $data['N_Serie']);
                $stmt->bindParam(3, $data['Tipo_Cont']);
                $stmt->bindParam(4, $data['Tipo_Res']);

                $stmt->execute();
            break;
            case "Elem_Planta":
                $stmt = $this->conn->prepare("
                INSERT INTO Elem_Planta(ID_Elemento, Tipo_Planta) 
                VALUES (?,?)
                ");

                $stmt->bindParam(1, $idElem);
                $stmt->bindParam(2, $data['Tipo_Planta']);

                $stmt->execute();
            break;
            case "Elem_Taller":
                $stmt = $this->conn->prepare("
                INSERT INTO Elem_Taller(ID_Elemento)
                VALUES (?)
                ");

                $stmt->bindParam(1, $idElem);
                $stmt->execute();
            break;
            case "Elem_Flota":
                $stmt = $this->conn->prepare("
                INSERT INTO Elem_Flota(ID_Elemento, Matricula, Res_Recolector, Tipo_Vehiculo, ID_Taller_Almacen)
                VALUES (?, ?, ?, ?, ?)
                ");

                $stmt->bindParam(1, $idElem);
                $stmt->bindParam(2, $data['Matricula']);
                $stmt->bindParam(3, $data['Res_Recolector']);
                $stmt->bindParam(4, $data['Tipo_Vehiculo']);
                $stmt->bindParam(5, $data['ID_Taller_Almacen']);

                $stmt->execute();
            break;
            default:
                throw new Exception("Tipo de elemento no especificado");
            break;
            }

            $this->conn->commit();

        } catch (Exception $e) {
            $this->conn->rollBack(); 
            throw $e;
        }
    }

    public function readElem(int $id, string $tipo): array {
        $stmt = $this->conn->prepare("
        SELECT *
        FROM Elementos 
        JOIN $tipo 
        ON Elementos.ID_Elemento = $tipo.ID_Elemento
        WHERE Elementos.ID_Elemento = ?
        ");

        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateElem(array $data) {
        try {
            $this->conn->beginTransaction();

            $stmt = $this->conn->prepare("
            UPDATE Elementos
            SET
            Estado = ?,
            ID_Ubicacion = ?,
            ID_Distrito = ?
            Tipo = ?
            WHERE ID_Elemento = ?
            ");

            $stmt->bindParam(1, $data['Estado']);
            $stmt->bindParam(2, $data['ID_Ubicacion']);
            $stmt->bindParam(3, $data['ID_Distrito']);
            $stmt->bindParam(4, $data['Tipo']);

            $stmt->execute();

            //cambiar
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
            }

            $this->conn->commit();

        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    public function deleteElem(int $id) {
        try {
            $this->conn->beginTransaction();

            // on delete cascade ya hace que el resto de las filas se eliminen
            //cambiar
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
}
?>

