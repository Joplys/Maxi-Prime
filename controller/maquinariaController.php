<?php
require_once __DIR__ . '/../model/maquinaria.php';

class maquinariaController {
    private Maquinaria $maquinaria;

    public function __construct() {
        $this->maquinaria = new Maquinaria();
    }

    public function createMaquinaria(array $data) {
        $requerido = [
            'Matricula',
            'Estado',
            'Modelo',
            'ID_Planta',
            'Tipo'
        ];

        foreach ($requerido as $dato) {
            if (empty($data[$dato])) {
                throw new Exception("Informacion faltante: $dato");
            }
        }

        $this->maquinaria->createMaquinaria($data);
    }

    public function readMaquinaria(int $id) {
        $data = $this->maquinaria->readMaquinaria($id);

        if (!empty($data)) {
            return $data;
        } else {
            throw new Exception("Maquinaria no encontrada.");
        }
    }

    public function updateMaquinaria(array $data) {
        $requerido = [
            'ID_Maquinaria',
            'Matricula',
            'Estado',
            'Modelo',
            'ID_Planta',
            'Tipo'
        ];

        foreach ($requerido as $dato) {
            if (empty($data[$dato])) {
                throw new Exception("Informacion faltante: $dato");
            }
        }

        $this->maquinaria->updateMaquinaria($data);
    }

    public function deleteMaquinaria(int $id) {
        if(empty($id)) {
           throw new Exception("No se proporciono un id."); 
        } else {
           $this->maquinaria->deleteMaquinaria($id); 
        }
    }
}
