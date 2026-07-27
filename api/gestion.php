<?php
session_start();

header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST");

if (!isset($_SESSION['elementos'])) {
    $_SESSION['elementos'] = [];
    $_SESSION['next_element_id'] = 1;
}

if (!isset($_SESSION['usuarios'])) {
    $_SESSION['usuarios'] = [];
    $_SESSION['next_user_id'] = 1;
}

$rawInput = file_get_contents("php://input");
$data = json_decode($rawInput);


$action = $data->action ?? $_GET['action'] ?? null;

if (!$action) {
    echo json_encode([
        "status" => "error", 
        "message" => "Acción no especificada. Acciones soportadas: list_elements, add_element, delete_element, list_users, add_user, delete_user, reset_session"
    ]);
    exit;
}

switch ($action) {
    case 'list_elements':
        listarElementos();
        break;
    case 'add_element':
        agregarElemento($data);
        break;
    case 'delete_element':
        eliminarElemento($data);
        break;

    case 'list_users':
        listarUsuarios();
        break;
    case 'add_user':
        agregarUsuario($data);
        break;
    case 'delete_user':
        eliminarUsuario($data);
        break;
    
    case 'reset_session':
        $_SESSION['elementos'] = [];
        $_SESSION['usuarios'] = [];
        $_SESSION['next_element_id'] = 1;
        $_SESSION['next_user_id'] = 1;
        echo json_encode(["status" => "success", "message" => "Datos de sesión reiniciados."]);
        break;

    default:
        echo json_encode(["status" => "error", "message" => "Acción no válida: '$action'"]);
        break;
}

function listarElementos() {
    echo json_encode([
        "status" => "success",
        "data" => array_values($_SESSION['elementos'])
    ]);
}

function agregarElemento($data) {
    if (empty($data->tipo_elemento)) {
        echo json_encode(["status" => "error", "message" => "El campo 'tipo_elemento' es obligatorio."]);
        return;
    }

    $tipo = strtolower($data->tipo_elemento);
    $id = $_SESSION['next_element_id']++;

    $nuevoElemento = [
        "ID_Elemento"       => $id,
        "Tipo_Elemento"     => $tipo,
        "Ubicacion"         => $data->ubicacion ?? null,
        "Estado_Contenedor" => null,
        "Estado_Planta"     => null,
        "Estado_Flota"      => null,
        "Estado_Taller"     => null,
        "N_Serie"           => null,
        "Tipo_Cont"         => null,
        "Tipo_Res"          => null,
        "Tipo_Planta"       => null,
        "Matricula"         => null,
        "Tipo_Vehiculo"     => null
    ];

    switch ($tipo) {
        case 'contenedor':
            $nuevoElemento['N_Serie']           = $data->n_serie ?? 'CNT' . str_pad($id, 3, '0', STR_PAD_LEFT);
            $nuevoElemento['Tipo_Cont']         = $data->tipo_cont ?? 'domiciliario';
            $nuevoElemento['Tipo_Res']          = $data->tipo_res ?? 'reciclables';
            $nuevoElemento['Estado_Contenedor'] = $data->estado ?? 'operable';
            break;

        case 'planta':
            $nuevoElemento['Tipo_Planta']       = $data->tipo_planta ?? 'reciclaje';
            $nuevoElemento['Estado_Planta']     = $data->estado ?? 'operable';
            break;

        case 'flota':
            $nuevoElemento['Matricula']         = $data->matricula ?? 'ABC' . rand(1000, 9999);
            $nuevoElemento['Tipo_Vehiculo']     = $data->tipo_vehiculo ?? 'camion_trasero';
            $nuevoElemento['Estado_Flota']      = $data->estado ?? 'operable';
            break;

        case 'taller':
            $nuevoElemento['Estado_Taller']     = $data->estado ?? 'operable';
            break;

        default:
            echo json_encode(["status" => "error", "message" => "Tipo de elemento inválido."]);
            return;
    }

    $_SESSION['elementos'][] = $nuevoElemento;

    echo json_encode([
        "status" => "success",
        "message" => "Elemento creado exitosamente en la sesión.",
        "id_elemento" => $id,
        "elemento" => $nuevoElemento
    ]);
}

function eliminarElemento($data) {
    if (empty($data->id_elemento)) {
        echo json_encode(["status" => "error", "message" => "Debes especificar 'id_elemento'."]);
        return;
    }

    $idABorrar = (int)$data->id_elemento;
    $encontrado = false;

    foreach ($_SESSION['elementos'] as $index => $elem) {
        if ((int)$elem['ID_Elemento'] === $idABorrar) {
            unset($_SESSION['elementos'][$index]);
            $_SESSION['elementos'] = array_values($_SESSION['elementos']);
            $encontrado = true;
            break;
        }
    }

    if ($encontrado) {
        echo json_encode(["status" => "success", "message" => "Elemento ID $idABorrar eliminado de la sesión."]);
    } else {
        echo json_encode(["status" => "error", "message" => "No se encontró el elemento ID $idABorrar."]);
    }
}

function listarUsuarios() {
    echo json_encode([
        "status" => "success",
        "data" => array_values($_SESSION['usuarios'])
    ]);
}

function agregarUsuario($data) {
    if (empty($data->nombre) || empty($data->email) || empty($data->password) || empty($data->rol)) {
        echo json_encode(["status" => "error", "message" => "Faltan campos obligatorios (nombre, email, password, rol)."]);
        return;
    }

    $rol = strtolower($data->rol);
    $id = $_SESSION['next_user_id']++;

    $nuevoUsuario = [
        "ID_US"          => $id,
        "Nombre"         => $data->nombre,
        "Email"          => $data->email,
        "Fecha_Registro" => date('Y-m-d'),
        "Rol"            => $rol,
        "ID_Planta"      => ($rol === 'gestor') ? ($data->id_planta ?? null) : null,
        "ID_Ruta_Asign"  => ($rol === 'camionero') ? ($data->id_ruta ?? null) : null,
        "Flota_Asign"    => ($rol === 'camionero') ? ($data->flota_asign ?? null) : null
    ];

    $_SESSION['usuarios'][] = $nuevoUsuario;

    echo json_encode([
        "status" => "success",
        "message" => "Usuario agregado exitosamente en la sesión.",
        "usuario" => $nuevoUsuario
    ]);
}

function eliminarUsuario($data) {
    if (empty($data->id_us)) {
        echo json_encode(["status" => "error", "message" => "Debes indicar 'id_us'."]);
        return;
    }

    $idABorrar = (int)$data->id_us;
    $encontrado = false;

    foreach ($_SESSION['usuarios'] as $index => $us) {
        if ((int)$us['ID_US'] === $idABorrar) {
            unset($_SESSION['usuarios'][$index]);
            $_SESSION['usuarios'] = array_values($_SESSION['usuarios']);
            $encontrado = true;
            break;
        }
    }

    if ($encontrado) {
        echo json_encode(["status" => "success", "message" => "Usuario ID $idABorrar eliminado de la sesión."]);
    } else {
        echo json_encode(["status" => "error", "message" => "No se encontró el usuario ID $idABorrar."]);
    }
}
?>
