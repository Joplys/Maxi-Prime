<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST");

require 'db.php';

$data = json_decode(file_get_contents("php://input"));

$action = $data->action ?? $_GET['action'] ?? null;

if (!$action) {
    echo json_encode(["status" => "error", "message" => "Debes especificar una acción ('register' o 'login')."]);
    exit;
}

switch ($action) {
    case 'register':
        handleRegister($pdo, $data);
        break;

    case 'login':
        handleLogin($pdo, $data);
        break;

    default:
        echo json_encode(["status" => "error", "message" => "Acción no válida."]);
        break;
}

function handleRegister($pdo, $data) {
    if (!empty($data->username) && !empty($data->email) && !empty($data->password)) {
        $nombre = $data->username;
        $email = $data->email;
        $fecha_registro = date('Y-m-d');
        $contrasena_hash = password_hash($data->password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO U_Vecino (Nombre, Email, Fecha_Registro, Contrasena) 
                VALUES (:nombre, :email, :fecha, :contrasena)";
        $stmt = $pdo->prepare($sql);

        try {
            $stmt->execute([
                ':nombre' => $nombre,
                ':email' => $email,
                ':fecha' => $fecha_registro,
                ':contrasena' => $contrasena_hash
            ]);
            echo json_encode(["status" => "success", "message" => "¡Cuenta creada con éxito!"]);
        } catch (PDOException $e) {
            echo json_encode(["status" => "error", "message" => "Error al registrar. Es probable que el correo ya esté en uso."]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Completa todos los campos (username, email, password)."]);
    }
}

function handleLogin($pdo, $data) {
    if (!empty($data->email) && !empty($data->password)) {
        $email = $data->email;
        $password = $data->password;

        $sql = "SELECT ID_US, Nombre, Contrasena FROM U_Vecino WHERE Email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['Contrasena'])) {
            echo json_encode([
                "status" => "success",
                "message" => "¡Bienvenido de nuevo, " . $user['Nombre'] . "!",
                "user_id" => $user['ID_US'],
                "username" => $user['Nombre']
            ]);
        } else {
            echo json_encode(["status" => "error", "message" => "Correo o contraseña incorrectos."]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Completa todos los campos (email, password)."]);
    }
}
?>
