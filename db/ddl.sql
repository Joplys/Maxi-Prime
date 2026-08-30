CREATE DATABASE proyecto;

USE proyecto;

CREATE TABLE Distritos(  
    ID_Distrito INT AUTO_INCREMENT PRIMARY KEY,
    Serie VARCHAR (5) UNIQUE NOT NULL
);

CREATE TABLE Usuarios (
    ID_Usuario INT AUTO_INCREMENT PRIMARY KEY,
    Nombre VARCHAR(50) NOT NULL,
    Correo_Electronico VARCHAR(100) NOT NULL UNIQUE,
    Contrasena VARCHAR(255) NOT NULL,
    Fecha_Registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    Tipo ENUM ('U_Camionero','U_Gestor_De_Planta','U_Vecino','U_Administrativo') DEFAULT 'U_Vecino'
);

CREATE TABLE Ubicacion(
    ID_Ubicacion INT AUTO_INCREMENT PRIMARY KEY,
    Latitud DOUBLE NOT NULL,
    Longitud DOUBLE NOT NULL
);

CREATE TABLE Elementos(
    ID_Elemento INT AUTO_INCREMENT PRIMARY KEY,
    Estado ENUM ('Disponible','Req. Asistencia','Req. Asistencia Inmediata') DEFAULT 'Disponible' NOT NULL,
    ID_Ubicacion INT NOT NULL,
    ID_Distrito INT NOT NULL,
    FOREIGN KEY (ID_Ubicacion) REFERENCES Ubicacion(ID_Ubicacion),
    FOREIGN KEY (ID_Distrito) REFERENCES Distritos(ID_Distrito),
    Tipo ENUM ('Elem_Contenedores','Elem_Planta','Elem_Taller','Elem_Flota') NOT NULL
);

CREATE TABLE Elem_Contenedores(
    ID_Elemento INT PRIMARY KEY,
    N_Serie VARCHAR (6) UNIQUE NOT NULL,
    Tipo_Cont ENUM('Domiciliario','Intradomiciliario','Intradomiciliario_doble') NOT NULL,
    Tipo_Res ENUM('Reciclables','Mezclados','Organicos') NOT NULL,
    FOREIGN KEY (ID_Elemento) REFERENCES Elementos(ID_Elemento) ON DELETE CASCADE
);

CREATE TABLE Elem_Planta(
    ID_Elemento INT PRIMARY KEY,
    Tipo_Planta ENUM('Reciclaje','Acopio','Vertedero','Transferencia') NOT NULL,
    FOREIGN KEY (ID_Elemento) REFERENCES Elementos(ID_Elemento) ON DELETE CASCADE
);

CREATE TABLE Maquinaria(
    ID_Maquinaria INT AUTO_INCREMENT PRIMARY KEY,
    Matricula VARCHAR (7) UNIQUE,
    Estado ENUM('Operable','Req_mantenimiento','Inoperable','En_mantenimiento') DEFAULT 'Operable' NOT NULL,
    Modelo VARCHAR (250),
    ID_Planta INT NOT NULL,
    Tipo ENUM('Chipeadoras','Trituracion','Compostaje','Harneado','Separacion'),
    FOREIGN KEY (ID_Planta) REFERENCES Elem_Planta(ID_Elemento)
);

CREATE TABLE Elem_Taller(
    ID_Elemento INT PRIMARY KEY,
    FOREIGN KEY (ID_Elemento) REFERENCES Elementos(ID_Elemento) ON DELETE CASCADE
);

CREATE TABLE Elem_Flota(
    ID_Elemento INT PRIMARY KEY,
    Matricula VARCHAR (7) UNIQUE,
    Res_Recolector ENUM('Reciclables','Mezclados','Organicos') NOT NULL,
    Tipo_Vehiculo ENUM('Camion_Lateral','Camion_Trasero','Motocarro') NOT NULL,
    ID_Taller_Almacen INT,
    FOREIGN KEY (ID_Elemento) REFERENCES Elementos(ID_Elemento),
    FOREIGN KEY (ID_Taller_Almacen) REFERENCES Elem_Taller(ID_Elemento)
);

CREATE TABLE Rutas(
    ID_Ruta INT AUTO_INCREMENT PRIMARY KEY,
    Tipo ENUM ('Recoleccion','Traslado') NOT NULL
);

CREATE TABLE Puntos_Ruta(
    ID_Punto_Ruta INT AUTO_INCREMENT PRIMARY KEY,
    ID_Ruta INT NOT NULL,
    ID_Ubicacion INT NOT NULL,
    FOREIGN KEY (ID_Ruta) REFERENCES Rutas(ID_Ruta) ON DELETE CASCADE,
    FOREIGN KEY (ID_Ubicacion) REFERENCES Ubicacion(ID_Ubicacion)
);

CREATE TABLE U_Vecino(
    ID_Usuario INT PRIMARY KEY,
    FOREIGN KEY (ID_Usuario) REFERENCES Usuarios(ID_Usuario) ON DELETE CASCADE
);

CREATE TABLE U_Camionero(
    ID_Usuario INT PRIMARY KEY,
    ID_Ruta_Asig INT,
    ID_Flota_Asig INT,
    FOREIGN KEY (ID_Usuario) REFERENCES Usuarios(ID_Usuario) ON DELETE CASCADE,
    FOREIGN KEY (ID_Ruta_Asig) REFERENCES Rutas(ID_Ruta),
    FOREIGN KEY (ID_Flota_Asig) REFERENCES Elem_Flota(ID_Elemento)
);

CREATE TABLE U_Gestor_De_Planta(
    ID_Usuario INT PRIMARY KEY,
    ID_Planta_Asig INT,
    FOREIGN KEY (ID_Usuario) REFERENCES Usuarios(ID_Usuario) ON DELETE CASCADE,
    FOREIGN KEY (ID_Planta_Asig) REFERENCES Elem_Planta(ID_Elemento)
);

CREATE TABLE U_Administrativo(
    ID_Usuario INT PRIMARY KEY,
    FOREIGN KEY (ID_Usuario) REFERENCES Usuarios(ID_Usuario) ON DELETE CASCADE
);

CREATE TABLE Incidencias(
    ID_Incidencia INT AUTO_INCREMENT PRIMARY KEY,
    ID_Elemento INT,
    ID_Usuario INT,
    Fecha_Hora DATETIME DEFAULT CURRENT_TIMESTAMP,
    Descripcion VARCHAR (3500),
    Archivo VARCHAR (1500),
    Estado ENUM('En_Cola','En_Revision','Aprobada','Denegada') DEFAULT 'En_Cola' NOT NULL,
    FOREIGN KEY (ID_Elemento) REFERENCES Elementos(ID_Elemento) ON DELETE CASCADE,
    FOREIGN KEY (ID_Usuario) REFERENCES Usuarios(ID_Usuario) ON DELETE SET NULL
);
