<?php
class Database {

    private PDO connection;

    //TODO: Esto esta pensado para lampp, luego se tiene que
    //cambiar para que utilize el servidor de db.

    // El objetivo es tener una sola conexion por script
    // por motivos de seguridad, por lo cual se revisa si
    // la conexion no existe y unicamente en ese caso se crea.
    
    // Este script se debe requerir una vez por script via require_once().

    public __construct() {
        if ($this->connection === null) {
            $this->connection = new PDO("mysql;dbname=proyecto;host=localhost;port=3306",'root','');
        }
    }

    public getConnection() {
        return $this->connection; 
    }
}
?>
