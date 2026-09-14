<?php

require_once __DIR__ . '/../controller/elementController.php';
require_once __DIR__ . '/../controller/maquinariaController.php';
require_once __DIR__ . '/../controller/userController.php';

header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

$metodo = $_SERVER['REQUEST_METHOD'];

$rawInput = file_get_contents("php://input");
$data = json_decode($rawInput, true);

try {
    $element = new elementController();
    $maquinaria = new maquinariaController();
    $user = new userController();

    $resource = $_GET['resource'] ?? null;
    $id = isset($_GET['id']) ? (int)$_GET['id'] : null;

    if ($resource === 'contenedor') {
        switch ($metodo) {
            case 'POST':
                $element->createContenedor($data);
                echo json_encode([
                    "status" => "success",
                    "message" => "Contenedor creado."
                ]);
                break;

            case 'GET':
                echo json_encode([
                    "status" => "success",
                    "data" => $element->readContenedor($id)
                ]);
                break;

            case 'PUT':
                $element->updateContenedor($data);
                echo json_encode([
                    "status" => "success",
                    "message" => "Contenedor actualizado."
                ]);
                break;

            case 'DELETE':
                $element->deleteElem($id);
                echo json_encode([
                    "status" => "success",
                    "message" => "Contenedor eliminado."
                ]);
                break;
        }
    }

    if ($resource === 'planta') {
        switch ($metodo) {
            case 'POST':
                $element->createPlanta($data);
                echo json_encode([
                    "status" => "success",
                    "message" => "Planta creada."
                ]);
                break;

            case 'GET':
                echo json_encode([
                    "status" => "success",
                    "data" => $element->readPlanta($id)
                ]);
                break;

            case 'PUT':
                $element->updatePlanta($data);
                echo json_encode([
                    "status" => "success",
                    "message" => "Planta actualizada."
                ]);
                break;

            case 'DELETE':
                $element->deleteElem($id);
                echo json_encode([
                    "status" => "success",
                    "message" => "Planta eliminada."
                ]);
                break;
        }
    }

    if ($resource === 'taller') {
        switch ($metodo) {
            case 'POST':
                $element->createTaller($data);
                echo json_encode([
                    "status" => "success",
                    "message" => "Taller creado."
                ]);
                break;

            case 'GET':
                echo json_encode([
                    "status" => "success",
                    "data" => $element->readTaller($id)
                ]);
                break;

            case 'PUT':
                $element->updateTaller($data);
                echo json_encode([
                    "status" => "success",
                    "message" => "Taller actualizado."
                ]);
                break;

            case 'DELETE':
                $element->deleteElem($id);
                echo json_encode([
                    "status" => "success",
                    "message" => "Taller eliminado."
                ]);
                break;
        }
    }

    if ($resource === 'flota') {
        switch ($metodo) {
            case 'POST':
                $element->createFlota($data);
                echo json_encode([
                    "status" => "success",
                    "message" => "Flota creada."
                ]);
                break;

            case 'GET':
                echo json_encode([
                    "status" => "success",
                    "data" => $element->readFlota($id)
                ]);
                break;

            case 'PUT':
                $element->updateFlota($data);
                echo json_encode([
                    "status" => "success",
                    "message" => "Flota actualizada."
                ]);
                break;

            case 'DELETE':
                $element->deleteElem($id);
                echo json_encode([
                    "status" => "success",
                    "message" => "Flota eliminada."
                ]);
                break;
        }
    }

    if ($resource === 'maquinaria') {
        switch ($metodo) {
            case 'POST':
                $maquinaria->createMaquinaria($data);
                echo json_encode([
                    "status" => "success",
                    "message" => "Maquinaria creada."
                ]);
                break;

            case 'GET':
                echo json_encode([
                    "status" => "success",
                    "data" => $maquinaria->readMaquinaria($id)
                ]);
                break;

            case 'PUT':
                $maquinaria->updateMaquinaria($data);
                echo json_encode([
                    "status" => "success",
                    "message" => "Maquinaria actualizada."
                ]);
                break;

            case 'DELETE':
                $maquinaria->deleteMaquinaria($id);
                echo json_encode([
                    "status" => "success",
                    "message" => "Maquinaria eliminada."
                ]);
                break;
        }
    }

} catch (Exception $e) {
    http_response_code(400);

    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
