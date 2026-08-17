<?php

require_once __DIR__ . '/db.php';

$db = new Database();
$conn = $db->getConnection();

class User {

    /* CREATE TABLE Usuarios ( */
    /* ID_Usuario INT AUTO_INCREMENT PRIMARY KEY, */
    /* Nombre VARCHAR(50) NOT NULL, */
    /* Correo_Electronico VARCHAR(100) NOT NULL, */
    /* Contrasena VARCHAR(255) NOT NULL, */
    /* Fecha_Registro DATE */
    /* ); */

    // Utilize estos nombres en este orden por dos motivos:
    //
    // 1- claridad, ya que si utilizace solo "$miclase->create()", podria dar
    // lugar a confusion con otras clases que operen con crud
    //
    // 2- create, read, update y delete forman las siglas crud,
    // lo cual mantiene consistencia, osea imaginate que ponga
    // modify y en otra clase ponga q se yo alter

    public function createUser($name, $email, $password) {

    }

    public function readUser($id) {
        
    }

    public function updateUser() {

    }

    public function deleteUser() {

    }

}
?>
