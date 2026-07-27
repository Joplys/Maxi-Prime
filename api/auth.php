<?php
session_start();

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST");

if (!isset($_SESSION['usuarios'])) {
    $_SESSION['next_user_id'] = 1;
    $_SESSION['usuarios'] = [
        [
            "ID_US" => $_SESSION['next_user_id']++,
            "Nombre" => "Administrador",
            "Email" => "admin@test.com",
            "Contrasena" => "123456",
            "Rol" => "admin"
        ],
        [
            "ID_US" => $_SESSION['next_user_id']++,
            "Nombre" => "Vecino Ejemplo",
            "Email" => "vecino@test.com",
            "Contrasena" => "123456",
            "Rol" => "vecino"
        ]
    ];
}

$rawInput = file_get_contents("php://input");
$data = json_decode($rawInput, true);

$action = $data['action'] ?? $_GET['action'] ?? null;

if (!$action) {
    echo json_encode([
        "status" => "error", 
        "message" => "Acción no especificada. Acciones disponibles: login, register, check_session"
    ]);
    exit;
}

switch ($action) {

    case 'login':
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
        break;

    case 'register':
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
        break;

    case 'check_session':
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
        break;

    default:
        echo json_encode(["status" => "error", "message" => "Acción no válida."]);
        break;
}
?>
