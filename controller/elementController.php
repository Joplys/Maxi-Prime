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
            'ID_Distrito',
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

    public function readContenedor(int $id) {
        $data = $this->element->readElem($id, 'Elem_Contenedores'); 
        if (!empty($data)) {
            return $data;
        } else {
            throw new Exception("Contenedor no encontrado.");
        }
    }

    public function updateContenedor(array $data) {
        $requerido = [
            'ID_Elemento',
            'Latitud',
            'Longitud',
            'Estado',
            'ID_Distrito',
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
    
        $this->element->updateElem($data);
    }

    public function createPlanta(array $data) {
        $requerido = [
            'Latitud',
            'Longitud',
            'Estado',
            'ID_Distrito',
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

    public function readPlanta(int $id) {
        $data = $this->element->readElem($id, 'Elem_Planta'); 
        if (!empty($data)) {
            return $data;
        } else {
            throw new Exception("Planta no encontrada.");
        }
    }

    public function updatePlanta(array $data) {
        $requerido = [
            'ID_Elemento',
            'Latitud',
            'Longitud',
            'Estado',
            'ID_Distrito',
            'Tipo_Planta'
        ];

        $data['Tipo'] = "Elem_Planta";
    
        foreach ($requerido as $dato) {
            if (empty($data[$dato])) {
                throw new Exception("Informacion faltante: $dato");
            }
        }
    
        $this->element->updateElem($data);
    }

    public function createTaller(array $data) {
        $requerido = [
            'Latitud',
            'Longitud',
            'Estado',
            'ID_Distrito',
        ];

        $data['Tipo'] = "Elem_Taller";
    
        foreach ($requerido as $dato) {
            if (empty($data[$dato])) {
                throw new Exception("Informacion faltante: $dato");
            }
        }
    
        $this->element->createElem($data);
    }

    public function readTaller(int $id) {
        $data = $this->element->readElem($id, 'Elem_Taller'); 
        if (!empty($data)) {
            return $data;
        } else {
            throw new Exception("Taller no encontrado.");
        }
    }

    public function updateTaller(array $data) {
        $requerido = [
            'ID_Elemento',
            'Latitud',
            'Longitud',
            'Estado',
            'ID_Distrito',
        ];

        $data['Tipo'] = "Elem_Taller";
    
        foreach ($requerido as $dato) {
            if (empty($data[$dato])) {
                throw new Exception("Informacion faltante: $dato");
            }
        }
    
        $this->element->updateElem($data);
    }

    public function createFlota(array $data) {
        $requerido = [
            'Latitud',
            'Longitud',
            'Estado',
            'ID_Distrito',
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

    public function readFlota(int $id) {
        $data = $this->element->readElem($id, 'Elem_Flota'); 
        if (!empty($data)) {
            return $data;
        } else {
            throw new Exception("Flota no encontrada.");
        }
    }

    public function updateFlota(array $data) {
        $requerido = [
            'ID_Elemento',
            'Latitud',
            'Longitud',
            'Estado',
            'ID_Distrito',
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
    
        $this->element->updateElem($data);
    }

    public function deleteElem(int $id) {
        if(empty($id)) {
           throw new Exception("No se proporciono un id."); 
        } else {
           $this->element->deleteElem($id); 
        }
    }
}
