<?php
session_start();

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST");

// TODO: eliminar session y utilizar persistencia

// Crear dos usuarios de prueba
/* if (!isset($_SESSION['usuarios'])) { */
/*     $_SESSION['next_user_id'] = 1; */
/*     $_SESSION['usuarios'] = [ */
/*         [ */
/*             "ID_US" => $_SESSION['next_user_id']++, */
/*             "Nombre" => "Administrador", */
/*             "Email" => "admin@test.com", */
/*             "Contrasena" => "123456", */
/*             "Rol" => "admin" */
/*         ], */
/*         [ */
/*             "ID_US" => $_SESSION['next_user_id']++, */
/*             "Nombre" => "Vecino Ejemplo", */
/*             "Email" => "vecino@test.com", */
/*             "Contrasena" => "123456", */
/*             "Rol" => "vecino" */
/*         ] */
/*     ]; */
/* } */

// recibe la peticion en formato json
$rawInput = file_get_contents("php://input");

// decodificar json y convertirlo en variables php dentro
// de un array asociativo data
$data = json_decode($rawInput, true);

// guardar la variable action para procesarla
// luego en el case
$action = $data['action'] ?? $_GET['action'];

// enviar un mensaje de error cuando accion no es especificada
if (!$action) {
    echo json_encode([
        "status" => "error", 
        "message" => "Acción no especificada. Acciones disponibles: login, register, check_session"
    ]);
    exit;
}

function login($data) {
    $email = trim($data['email'] ?? '');
    $password = $data['password'] ?? '';

    if (empty($email) || empty($password)) {
        echo json_encode(["status" => "error", "message" => "Completa email y contraseña."]);
        exit;
    }
    
    $usuarioEncontrado = null;
    foreach ($_SESSION['usuarios'] as $user) {
        if (strtolower($user['Email']) === strtolower($email) && $user['Contrasena'] === $password) {
            $usuarioEncontrado = $user;
            break;
        }
    }

    if ($usuarioEncontrado) {
        $_SESSION['user_logged_in'] = [
            "ID_US" => $usuarioEncontrado['ID_US'],
            "Nombre" => $usuarioEncontrado['Nombre'],
            "Email" => $usuarioEncontrado['Email'],
            "Rol" => $usuarioEncontrado['Rol'] ?? 'vecino'
        ];

        echo json_encode([
            "status" => "success",
            "message" => "¡Inicio de sesión exitoso!",
            "user" => $_SESSION['user_logged_in']
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Credenciales incorrectas o usuario no registrado."
        ]);
    }
}

function register($data) {
        $nombre = trim($data['username'] ?? $data['nombre'] ?? '');
        $email = trim($data['email'] ?? '');
        $password = $data['password'] ?? '';

        if (empty($nombre) || empty($email) || empty($password)) {
            echo json_encode(["status" => "error", "message" => "Todos los campos son obligatorios."]);
            exit;
        }

        foreach ($_SESSION['usuarios'] as $user) {
            if (strtolower($user['Email']) === strtolower($email)) {
                echo json_encode(["status" => "error", "message" => "El correo ya está registrado."]);
                exit;
            }
        }

        $nuevoUsuario = [
            "ID_US" => $_SESSION['next_user_id']++,
            "Nombre" => $nombre,
            "Email" => $email,
            "Contrasena" => $password,
            "Rol" => "vecino",
            "Fecha_Registro" => date('Y-m-d')
        ];

        $_SESSION['usuarios'][] = $nuevoUsuario;

        echo json_encode([
            "status" => "success",
            "message" => "Registro exitoso. Ya puedes iniciar sesión."
        ]);
}

function check_session() {
    if (isset($_SESSION['user_logged_in'])) {
        echo json_encode([
            "status" => "success",
            "logged_in" => true,
            "user" => $_SESSION['user_logged_in']
        ]);
    } else {
        echo json_encode([
            "status" => "success",
            "logged_in" => false
        ]);
    }
}

switch ($action) {
    case 'login':
        login($data);
        break;
    case 'register':
        register($data);
        break;
    case 'check_session':
        check_session();
        break;
    default:
        echo json_encode(["status" => "error", "message" => "Acción no válida."]);
        break;
}
?>
