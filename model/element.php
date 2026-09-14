<?php

require_once __DIR__ . '/db.php';

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
            Ubicacion(Latitud, Longitud)
            VALUES(?, ?)
            ");

            $stmt->execute();

            $idUbicacion = $this->conn->lastInsertId();

            $stmt = $this->conn->prepare("
            INSERT INTO
            Elementos(Estado, ID_Ubicacion, ID_Districto, Tipo)
            VALUES (?,?,?,?)
            ");

            $stmt->bindParam(1, $data['Estado']);
            $stmt->bindParam(2, $idUbicacion);
            $stmt->bindParam(3, $data['ID_Districto']);
            $stmt->bindParam(4, $data['Tipo']);

            $stmt->execute();

            $idElem = $this->conn->lastInsertId();

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
        $tablas = [
            'Elem_Contenedores',
            'Elem_Planta',
            'Elem_Taller',
            'Elem_Flota'
        ];
    
        if (!in_array($tipo, $tablas, true)) {
            throw new Exception("Tabla restringida");
        }
    
        $stmt = $this->conn->prepare("
            SELECT
                Elementos.*,
                Ubicacion.Latitud,
                Ubicacion.Longitud,
                $tipo.*
            FROM Elementos
            JOIN Ubicacion
                ON Elementos.ID_Ubicacion = Ubicacion.ID_Ubicacion
            JOIN $tipo
                ON Elementos.ID_Elemento = $tipo.ID_Elemento
            WHERE Elementos.ID_Elemento = ?
        ");
    
        $stmt->bindParam(1, $id);
        $stmt->execute();
    
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
        return $result;
    }
    public function updateElem(array $data) {
        try {
            $this->conn->beginTransaction();

            $stmt = $this->conn->prepare("
            SELECT ID_Ubicacion
            FROM Elementos
            WHERE ID_Elemento = ?
            ");

            $stmt->bindParam(1, $data['ID_Elemento']);
            $stmt->execute();

            $idUbicacion = $stmt->fetchColumn();

            if ($idUbicacion === false) {
                throw new Exception("ID_Ubicacion no encontrado");
            }

            $stmt = $this->conn->prepare("
            UPDATE Ubicacion
            SET
            Latitud = ?,
            Longitud = ?
            WHERE ID_Ubicacion = ?
            ");

            $stmt->bindParam(1, $data['Latitud']);
            $stmt->bindParam(2, $data['Longitud']);
            $stmt->bindParam(3, $idUbicacion);

            $stmt->execute();

            $stmt = $this->conn->prepare("
            UPDATE Elementos
            SET
            Estado = ?,
            ID_Distrito = ?
            WHERE ID_Elemento = ?
            ");
    
            $stmt->bindParam(1, $data['Estado']);
            $stmt->bindParam(2, $data['ID_Distrito']);
            $stmt->bindParam(3, $data['ID_Elemento']);
    
            $stmt->execute();
    
            switch ($data['Tipo']) {
                case "Elem_Contenedores":
                    $stmt = $this->conn->prepare("
                    UPDATE Elem_Contenedores
                    SET
                    N_Serie = ?,
                    Tipo_Cont = ?,
                    Tipo_Res = ?
                    WHERE ID_Elemento = ?
                    ");
    
                    $stmt->bindParam(1, $data['N_Serie']);
                    $stmt->bindParam(2, $data['Tipo_Cont']);
                    $stmt->bindParam(3, $data['Tipo_Res']);
                    $stmt->bindParam(4, $data['ID_Elemento']);
    
                    $stmt->execute();
                break;
    
                case "Elem_Planta":
                    $stmt = $this->conn->prepare("
                    UPDATE Elem_Planta
                    SET
                    Tipo_Planta = ?
                    WHERE ID_Elemento = ?
                    ");
    
                    $stmt->bindParam(1, $data['Tipo_Planta']);
                    $stmt->bindParam(2, $data['ID_Elemento']);
    
                    $stmt->execute();
                break;
    
                case "Elem_Taller":
                break;
    
                case "Elem_Flota":
                    $stmt = $this->conn->prepare("
                    UPDATE Elem_Flota
                    SET
                    Matricula = ?,
                    Res_Recolector = ?,
                    Tipo_Vehiculo = ?,
                    ID_Taller_Almacen = ?
                    WHERE ID_Elemento = ?
                ");
    
                $stmt->bindParam(1, $data['Matricula']);
                $stmt->bindParam(2, $data['Res_Recolector']);
                $stmt->bindParam(3, $data['Tipo_Vehiculo']);
                $stmt->bindParam(4, $data['ID_Taller_Almacen']);
                $stmt->bindParam(5, $data['ID_Elemento']);

                $stmt->execute();
                break;
                default:
                    throw new Exception("Tipo de elemento no especificado");
                break;
            }
    
            $this->conn->commit();
    
        } catch (Exception $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            throw $e;
        }
    }

    public function deleteElem(int $id) {
        try {
            $this->conn->beginTransaction();

            // on delete cascade ya hace que el resto de las filas se eliminen
            //cambiar
            $stmt = $this->conn->prepare("
            DELETE FROM Elementos WHERE ID_Elemento = ?
            ");

            $stmt->bindParam(1, $id);

            $stmt->execute();

            $this->conn->commit();
            
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    private function createUbicacion() {

    }
    private function deleteUbicacion() {
    }
}
?>

