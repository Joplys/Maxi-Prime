<?php
require_once __DIR__ . "/../model/element.php";

class elementController {
    private Element $element;

    public function __construct() {
        $this->element = new Element(); 
    }

    public function createContenedor(array $data) {
        $requerido = [
            'Latitud',
            'Longitud',
            'Estado',
            'ID_Districto',
            'N_Serie',
            'Tipo_Cont',
            'Tipo_Res'
        ];

        $data['Tipo'] = "Elem_Contenedores";
    
        foreach ($requerido as $dato) {
            if (empty($data[$dato])) {
                throw new Exception("Informacion faltante: $dato");
            }
        }
    
        $this->element->createElem($data);
    }

    public function createPlanta(array $data) {
        $requerido = [
            'Latitud',
            'Longitud',
            'Estado',
            'ID_Districto',
            'Tipo_Planta'
        ];

        $data['Tipo'] = "Elem_Planta";
    
        foreach ($requerido as $dato) {
            if (empty($data[$dato])) {
                throw new Exception("Informacion faltante: $dato");
            }
        }
    
        $this->element->createElem($data);
    }

    public function createTaller(array $data) {
        $requerido = [
            'Latitud',
            'Longitud',
            'Estado',
            'ID_Districto',
        ];

        $data['Tipo'] = "Elem_Taller";
    
        foreach ($requerido as $dato) {
            if (empty($data[$dato])) {
                throw new Exception("Informacion faltante: $dato");
            }
        }
    
        $this->element->createElem($data);
    }

    public function createFlota(array $data) {
        $requerido = [
            'Latitud',
            'Longitud',
            'Estado',
            'ID_Districto',
            'Matricula',
            'Res_Recolector',
            'Tipo_Vehiculo',
            'ID_Taller_Almacen'
        ];

        $data['Tipo'] = "Elem_Flota";
    
        foreach ($requerido as $dato) {
            if (empty($data[$dato])) {
                throw new Exception("Informacion faltante: $dato");
            }
        }
    
        $this->element->createElem($data);
    }
}
?>
