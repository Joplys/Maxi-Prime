<?php
require_once __DIR__ . '/../model/user.php';

class userController {
    private User $user;

    public function __construct() {
        $this->user = new User();
    }

    public function createAdministrativo(array $data) {
        $requerido = [
            'Nombre',
            'Correo_Electronico',
            'Contrasena'
        ];

        $data['Tipo'] = "U_Administrativo";
    
        foreach ($requerido as $dato) {
            if (empty($data[$dato])) {
                throw new Exception("Informacion faltante: $dato");
            }
        }
    
        $this->user->createUser($data);
    }

    public function readAdministrativo(int $id) {
        $data = $this->user->readUser($id, 'U_Administrativo'); 
        if (!empty($data)) {
            return $data;
        } else {
            throw new Exception("Usuario administrativo no encontrado.");
        }
    }

    public function updateAdministrativo(array $data) {
        $requerido = [
            'ID_Usuario',
            'Nombre',
            'Correo_Electronico',
            'Contrasena'
        ];

        $data['Tipo'] = "U_Administrativo";
    
        foreach ($requerido as $dato) {
            if (empty($data[$dato])) {
                throw new Exception("Informacion faltante: $dato");
            }
        }
    
        $this->user->updateUser($data);
    }

    public function createVecino(array $data) {
        $requerido = [
            'Nombre',
            'Correo_Electronico',
            'Contrasena'
        ];

        $data['Tipo'] = "U_Vecino";
    
        foreach ($requerido as $dato) {
            if (empty($data[$dato])) {
                throw new Exception("Informacion faltante: $dato");
            }
        }
    
        $this->user->createUser($data);
    }

    public function readVecino(int $id) {
        $data = $this->user->readUser($id, 'U_Vecino'); 
        if (!empty($data)) {
            return $data;
        } else {
            throw new Exception("Vecino no encontrado.");
        }
    }

    public function updateVecino(array $data) {
        $requerido = [
            'ID_Usuario',
            'Nombre',
            'Correo_Electronico',
            'Contrasena'
        ];

        $data['Tipo'] = "U_Vecino";
    
        foreach ($requerido as $dato) {
            if (empty($data[$dato])) {
                throw new Exception("Informacion faltante: $dato");
            }
        }
    
        $this->user->updateUser($data);
    }

    public function createGestorDePlanta(array $data) {
        $requerido = [
            'Nombre',
            'Correo_Electronico',
            'Contrasena',
            'ID_Planta_Asig'
        ];

        $data['Tipo'] = "U_Gestor_De_Planta";
    
        foreach ($requerido as $dato) {
            if (empty($data[$dato])) {
                throw new Exception("Informacion faltante: $dato");
            }
        }
    
        $this->user->createUser($data);
    }

    public function readGestorDePlanta(int $id) {
        $data = $this->user->readUser($id, 'U_Gestor_De_Planta'); 
        if (!empty($data)) {
            return $data;
        } else {
            throw new Exception("Gestor de planta no encontrado.");
        }
    }

    public function updateGestorDePlanta(array $data) {
        $requerido = [
            'ID_Usuario',
            'Nombre',
            'Correo_Electronico',
            'Contrasena',
            'ID_Planta_Asig'
        ];

        $data['Tipo'] = "U_Gestor_De_Planta";
    
        foreach ($requerido as $dato) {
            if (empty($data[$dato])) {
                throw new Exception("Informacion faltante: $dato");
            }
        }
    
        $this->user->updateUser($data);
    }

    public function createCamionero(array $data) {
        $requerido = [
            'Nombre',
            'Correo_Electronico',
            'Contrasena'
        ];

        $data['Tipo'] = "U_Camionero";
    
        foreach ($requerido as $dato) {
            if (empty($data[$dato])) {
                throw new Exception("Informacion faltante: $dato");
            }
        }
    
        $this->user->createUser($data);
    }

    public function readCamionero(int $id) {
        $data = $this->user->readUser($id, 'U_Camionero'); 
        if (!empty($data)) {
            return $data;
        } else {
            throw new Exception("Camionero no encontrado.");
        }
    }

    public function updateCamionero(array $data) {
        $requerido = [
            'ID_Usuario',
            'Nombre',
            'Correo_Electronico',
            'Contrasena',
            'ID_Ruta_Asig',
            'ID_Flota_Asig'
        ];

        $data['Tipo'] = "U_Camionero";
    
        foreach ($requerido as $dato) {
            if (empty($data[$dato])) {
                throw new Exception("Informacion faltante: $dato");
            }
        }
    
        $this->user->updateUser($data);
    }

    public function deleteUser(int $id) {
        if(empty($id)) {
           throw new Exception("No se proporciono un id."); 
        } else {
           $this->user->deleteUser($id); 
        }
    }
}
?>

