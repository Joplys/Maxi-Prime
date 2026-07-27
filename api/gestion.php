<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST");

require 'db.php';

$data = json_decode(file_get_contents("php://input"));
$action = $data->action ?? $_GET['action'] ?? null;

if (!$action) {
    echo json_encode([
        "status" => "error", 
        "message" => "Acción no especificada. Acciones disponibles: list_users, add_user, delete_user, list_elements, add_element, delete_element"
    ]);
    exit;
}

switch ($action) {

    case 'list_users':
        listUsers($pdo);
        break;
    case 'add_user':
        addUser($pdo, $data);
        break;
    case 'delete_user':
        deleteUser($pdo, $data);
        break;

    case 'list_elements':
        listElements($pdo);
        break;
    case 'add_element':
        addElement($pdo, $data);
        break;
    case 'delete_element':
        deleteElement($pdo, $data);
        break;

    default:
        echo json_encode(["status" => "error", "message" => "Acción no válida."]);
        break;
}



function listUsers($pdo) {
    try {
        $sql = "
            SELECT ID_US, Nombre, Email, Fecha_Registro, 'vecino' AS Rol, NULL AS ID_Planta, NULL AS ID_Ruta_Asign, NULL AS Flota_Asign FROM U_Vecino
            UNION ALL
            SELECT ID_US, Nombre, Email, Fecha_Registro, 'camionero' AS Rol, NULL AS ID_Planta, ID_Ruta_Asign, Flota_Asign FROM U_Camionero
            UNION ALL
            SELECT ID_US, Nombre, Email, Fecha_Registro, 'gestor' AS Rol, ID_Planta, NULL AS ID_Ruta_Asign, NULL AS Flota_Asign FROM U_Gestor_de_Planta
            UNION ALL
            SELECT ID_US, Nombre, Email, Fecha_Registro, 'admin' AS Rol, NULL AS ID_Planta, NULL AS ID_Ruta_Asign, NULL AS Flota_Asign FROM U_Administrativo
            ORDER BY ID_US DESC
        ";
        
        $stmt = $pdo->query($sql);
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(["status" => "success", "data" => $usuarios]);
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => "Error al listar usuarios: " . $e->getMessage()]);
    }
}

function addUser($pdo, $data) {
    if (empty($data->nombre) || empty($data->email) || empty($data->password) || empty($data->rol)) {
        echo json_encode(["status" => "error", "message" => "Faltan datos obligatorios (nombre, email, password, rol)."]);
        return;
    }

    $rol = strtolower($data->rol);
    $fecha = date('Y-m-d');
    $passHash = password_hash($data->password, PASSWORD_DEFAULT);

    try {
        if ($rol === 'vecino') {
            $sql = "INSERT INTO U_Vecino (Nombre, Email, Fecha_Registro, Contrasena) VALUES (:nombre, :email, :fecha, :pass)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':nombre' => $data->nombre, ':email' => $data->email, ':fecha' => $fecha, ':pass' => $passHash]);

        } elseif ($rol === 'admin' || $rol === 'administrativo') {
            $sql = "INSERT INTO U_Administrativo (Nombre, Email, Fecha_Registro, Contrasena) VALUES (:nombre, :email, :fecha, :pass)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':nombre' => $data->nombre, ':email' => $data->email, ':fecha' => $fecha, ':pass' => $passHash]);

        } elseif ($rol === 'gestor') {
            $sql = "INSERT INTO U_Gestor_de_Planta (Nombre, Email, Fecha_Registro, Contrasena, ID_Planta) VALUES (:nombre, :email, :fecha, :pass, :planta)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nombre' => $data->nombre, 
                ':email' => $data->email, 
                ':fecha' => $fecha, 
                ':pass' => $passHash,
                ':planta' => $data->id_planta ?? null
            ]);

        } elseif ($rol === 'camionero') {
            $sql = "INSERT INTO U_Camionero (Nombre, Email, Fecha_Registro, Contrasena, ID_Ruta_Asign, Flota_Asign) VALUES (:nombre, :email, :fecha, :pass, :ruta, :flota)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nombre' => $data->nombre, 
                ':email' => $data->email, 
                ':fecha' => $fecha, 
                ':pass' => $passHash,
                ':ruta' => $data->id_ruta ?? null,
                ':flota' => $data->flota_asign ?? null
            ]);

        } else {
            echo json_encode(["status" => "error", "message" => "Rol no válido ('vecino', 'camionero', 'gestor', 'admin')."]);
            return;
        }

        echo json_encode(["status" => "success", "message" => "Usuario registrado exitosamente con rol: " . $rol]);
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => "Error al agregar usuario: " . $e->getMessage()]);
    }
}

function deleteUser($pdo, $data) {
    if (empty($data->id_us) || empty($data->rol)) {
        echo json_encode(["status" => "error", "message" => "Debes indicar 'id_us' y 'rol'."]);
        return;
    }

    $rol = strtolower($data->rol);
    $tabla = match($rol) {
        'vecino' => 'U_Vecino',
        'camionero' => 'U_Camionero',
        'gestor' => 'U_Gestor_de_Planta',
        'admin', 'administrativo' => 'U_Administrativo',
        default => null
    };

    if (!$tabla) {
        echo json_encode(["status" => "error", "message" => "Rol no válido."]);
        return;
    }

    try {
        $stmt = $pdo->prepare("DELETE FROM $tabla WHERE ID_US = :id");
        $stmt->execute([':id' => $data->id_us]);

        if ($stmt->rowCount() > 0) {
            echo json_encode(["status" => "success", "message" => "Usuario eliminado correctamente de $tabla."]);
        } else {
            echo json_encode(["status" => "error", "message" => "No se encontró un usuario con ese ID en la tabla $tabla."]);
        }
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => "Error al eliminar usuario: " . $e->getMessage()]);
    }
}


function listElements($pdo) {
    try {
        $sql = "SELECT e.ID_Elemento, e.Tipo_Elemento, 
                       c.N_Serie, c.Tipo_Cont, c.Tipo_Res, c.Estado AS Estado_Contenedor,
                       p.Tipo_Planta, p.Estado AS Estado_Planta,
                       f.Matricula, f.Tipo_Vehiculo, f.Estado AS Estado_Flota,
                       t.Estado AS Estado_Taller
                FROM Elementos e
                LEFT JOIN Elem_Contenedores c ON e.ID_Elemento = c.ID_Contenedor
                LEFT JOIN Elem_Planta p ON e.ID_Elemento = p.ID_Planta
                LEFT JOIN Elem_Flota f ON e.ID_Elemento = f.ID_Flota
                LEFT JOIN Elem_Taller t ON e.ID_Elemento = t.ID_Taller
                ORDER BY e.ID_Elemento DESC";

        $stmt = $pdo->query($sql);
        echo json_encode(["status" => "success", "data" => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => "Error al listar elementos: " . $e->getMessage()]);
    }
}

function addElement($pdo, $data) {
    if (empty($data->tipo_elemento)) {
        echo json_encode(["status" => "error", "message" => "El campo 'tipo_elemento' es obligatorio."]);
        return;
    }

    $tipo = strtolower($data->tipo_elemento);

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("INSERT INTO Elementos (Tipo_Elemento) VALUES (:tipo)");
        $stmt->execute([':tipo' => $tipo]);
        $idElemento = $pdo->lastInsertId();

        if ($tipo === 'contenedor') {
            $sql = "INSERT INTO Elem_Contenedores (ID_Contenedor, N_Serie, Tipo_Cont, Tipo_Res, Ubicacion, Estado) 
                    VALUES (:id, :n_serie, :tipo_cont, :tipo_res, :ubicacion, :estado)";
            $pdo->prepare($sql)->execute([
                ':id' => $idElemento,
                ':n_serie' => $data->n_serie ?? null,
                ':tipo_cont' => $data->tipo_cont ?? 'domiciliario',
                ':tipo_res' => $data->tipo_res ?? 'reciclables',
                ':ubicacion' => $data->ubicacion ?? null,
                ':estado' => $data->estado ?? 'operable'
            ]);
        } elseif ($tipo === 'planta') {
            $sql = "INSERT INTO Elem_Planta (ID_Planta, Ubicacion, Estado, Tipo_Planta) 
                    VALUES (:id, :ubicacion, :estado, :tipo_planta)";
            $pdo->prepare($sql)->execute([
                ':id' => $idElemento,
                ':ubicacion' => $data->ubicacion ?? null,
                ':estado' => $data->estado ?? 'operable',
                ':tipo_planta' => $data->tipo_planta ?? 'reciclaje'
            ]);
        } elseif ($tipo === 'flota') {
            $sql = "INSERT INTO Elem_Flota (ID_Flota, Matricula, Res_Recolector, Tipo_Vehiculo, Ubicacion, ID_Taller, Estado) 
                    VALUES (:id, :matricula, :res_recolector, :tipo_vehiculo, :ubicacion, :id_taller, :estado)";
            $pdo->prepare($sql)->execute([
                ':id' => $idElemento,
                ':matricula' => $data->matricula ?? null,
                ':res_recolector' => $data->res_recolector ?? 'reciclables',
                ':tipo_vehiculo' => $data->tipo_vehiculo ?? 'camion_trasero',
                ':ubicacion' => $data->ubicacion ?? null,
                ':id_taller' => $data->id_taller ?? null,
                ':estado' => $data->estado ?? 'operable'
            ]);
        } elseif ($tipo === 'taller') {
            $sql = "INSERT INTO Elem_Taller (ID_Taller, Ubicacion, Estado) VALUES (:id, :ubicacion, :estado)";
            $pdo->prepare($sql)->execute([
                ':id' => $idElemento,
                ':ubicacion' => $data->ubicacion ?? null,
                ':estado' => $data->estado ?? 'operable'
            ]);
        } else {
            $pdo->rollBack();
            echo json_encode(["status" => "error", "message" => "Tipo de elemento no válido."]);
            return;
        }

        $pdo->commit();
        echo json_encode(["status" => "success", "message" => "Elemento creado exitosamente.", "id_elemento" => $idElemento]);
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo json_encode(["status" => "error", "message" => "Error al agregar elemento: " . $e->getMessage()]);
    }
}

function deleteElement($pdo, $data) {
    if (empty($data->id_elemento)) {
        echo json_encode(["status" => "error", "message" => "El campo 'id_elemento' es obligatorio."]);
        return;
    }

    try {
        $pdo->beginTransaction();
        $id = $data->id_elemento;

        $pdo->prepare("DELETE FROM Elem_Contenedores WHERE ID_Contenedor = :id")->execute([':id' => $id]);
        $pdo->prepare("DELETE FROM Elem_Planta WHERE ID_Planta = :id")->execute([':id' => $id]);
        $pdo->prepare("DELETE FROM Elem_Flota WHERE ID_Flota = :id")->execute([':id' => $id]);
        $pdo->prepare("DELETE FROM Elem_Taller WHERE ID_Taller = :id")->execute([':id' => $id]);

        $stmt = $pdo->prepare("DELETE FROM Elementos WHERE ID_Elemento = :id");
        $stmt->execute([':id' => $id]);

        if ($stmt->rowCount() > 0) {
            $pdo->commit();
            echo json_encode(["status" => "success", "message" => "Elemento eliminado exitosamente."]);
        } else {
            $pdo->rollBack();
            echo json_encode(["status" => "error", "message" => "No se encontró el elemento."]);
        }
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo json_encode(["status" => "error", "message" => "Error al eliminar elemento: " . $e->getMessage()]);
    }
}
?>
