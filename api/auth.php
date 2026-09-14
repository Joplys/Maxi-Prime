<?php
session_start();

require_once __DIR__ . '/../controller/userController.php';

header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST");

$rawInput = file_get_contents("php://input");
$data = json_decode($rawInput, true);

$action = $data['action'] ?? $_GET['action'] ?? null;

if (!$action) {
    echo json_encode([
        "status" => "error",
        "message" => "Accion no especificada."
    ]);
    exit;
}

$userController = new userController();

try {
    switch ($action) {
    case 'login':
        $email = trim($data['email'] ?? '');
        $contrasena = $data['contrasena'] ?? '';

        if (empty($email) || empty($contrasena)) {
            throw new Exception("Correo y contraseña son obligatorios.");
        }

        $usuario = $userController->login($email, $contrasena);

        if ($usuario === false) {
            throw new Exception("Contraseña incorrecta.");
        }

        $_SESSION['user'] = $usuario;

        echo json_encode([
            "status" => "success",
            "message" => "Inicio de sesion exitoso.",
            "user" => $usuario
        ]);
    break;

    case 'check_session':
        if (isset($_SESSION['user'])) {
            echo json_encode([
                "status" => "success",
                "logged_in" => true,
                "user" => $_SESSION['user']
            ]);
        } else {
            echo json_encode([
                "status" => "success",
                "logged_in" => false
            ]);
        }
    break;

    case 'logout':
        session_unset();
        session_destroy();

        echo json_encode([
            "status" => "success",
            "message" => "Sesion cerrada."
        ]);
    break;

    default:
        throw new Exception("Accion no valida.");
    }

} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
