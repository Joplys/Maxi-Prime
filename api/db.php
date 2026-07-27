<?php
$host = 'localhost';
$dbname = 'proyecto';
$user = 'mimi';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode(["status" => "error", "message" => "Error de conexión: " . $e->getMessage()]));
}
?>
