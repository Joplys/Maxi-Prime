CREATE DATABASE proyecto;

USE proyecto;

CREATE TABLE Distritos(  
    ID_Distrito INT AUTO_INCREMENT PRIMARY KEY,
    Serie VARCHAR (5)
);

CREATE TABLE Usuarios (
    ID_Usuario INT AUTO_INCREMENT PRIMARY KEY,
    Nombre VARCHAR(50) NOT NULL,
    Correo_Electronico VARCHAR(100) NOT NULL,
    Contrasena VARCHAR(255) NOT NULL,
    Fecha_Registro DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE Ubicacion(
    ID_Ubicacion INT AUTO_INCREMENT PRIMARY KEY,
    Latitud FLOAT NOT NULL,
    Longitud FLOAT NOT NULL
);

CREATE TABLE Elementos(
    ID_Elemento INT AUTO_INCREMENT PRIMARY KEY,
    Estado ENUM ('Disponible','Req. Asistencia','Req. Asistencia Inmediata') DEFAULT 'Disponible',
    ID_Ubicacion INT,
    Distrito INT,
    FOREIGN KEY (ID_Ubicacion) REFERENCES Ubicacion(ID_Ubicacion),
    FOREIGN KEY (Distrito) REFERENCES Distritos(ID_Distrito)
);

CREATE TABLE Elem_Contenedores(
    ID_Elemento INT PRIMARY KEY,
    N_serie VARCHAR (6),
    Tipo_Cont ENUM('Domiciliario','Intradomiciliario','Intradomiciliario_doble'),
    Tipo_Res ENUM('Reciclables','Mezclados','Organicos'),
    FOREIGN KEY (ID_Elemento) REFERENCES Elementos(ID_Elemento)
);

CREATE TABLE Elem_Planta(
    ID_Elemento INT PRIMARY KEY,
    Tipo_Planta ENUM('Reciclaje','Acopio','Vertedero','Transferencia'),
    FOREIGN KEY (ID_Elemento) REFERENCES Elementos(ID_Elemento)
);

CREATE TABLE Maquinaria(
    ID_Maquinaria INT AUTO_INCREMENT PRIMARY KEY,
    Matricula VARCHAR (7),
    Estado ENUM('Operable','Req_mantenimiento','Inoperable','En_mantenimiento') DEFAULT 'Operable',
    Modelo VARCHAR (250),
    ID_Planta INT,
    Tipo ENUM('Chipeadoras','Trituracion','Compostaje','Harneado','Separacion'),
    FOREIGN KEY (ID_Planta) REFERENCES Elem_Planta(ID_Elemento)
);

CREATE TABLE Elem_Taller(
    ID_Elemento INT PRIMARY KEY,
    FOREIGN KEY (ID_Elemento) REFERENCES Elementos(ID_Elemento)
);

CREATE TABLE Elem_Flota(
    ID_Elemento INT PRIMARY KEY,
    Matricula VARCHAR (7),
    Res_Recolector ENUM('Reciclables','Mezclados','Organicos'),
    Tipo_Vehiculo ENUM('Camion_Lateral','Camion Trasero','Motocarro'),
    ID_Taller_Almacen INT,
    FOREIGN KEY (ID_Elemento) REFERENCES Elementos(ID_Elemento),
    FOREIGN KEY (ID_Taller_Almacen) REFERENCES Elem_Taller(ID_Elemento)
);

CREATE TABLE Rutas(
    ID_Ruta INT AUTO_INCREMENT PRIMARY KEY,
    Tipo ENUM ('Recoleccion','Traslado')
);

CREATE TABLE Puntos_Ruta(
    ID_Punto_Ruta INT AUTO_INCREMENT PRIMARY KEY,
    ID_Ruta INT NOT NULL,
    ID_Ubicacion INT NOT NULL,
    FOREIGN KEY (ID_Ruta) REFERENCES Rutas(ID_Ruta),
    FOREIGN KEY (ID_Ubicacion) REFERENCES Ubicacion(ID_Ubicacion)
);

CREATE TABLE U_Vecino(
    ID_Usuario INT PRIMARY KEY,
    FOREIGN KEY (ID_Usuario) REFERENCES Usuarios(ID_Usuario)
);

CREATE TABLE U_Camionero(
    ID_Usuario INT PRIMARY KEY,
    ID_Ruta_Asig INT,
    ID_Flota_Asig INT,
    FOREIGN KEY (ID_Usuario) REFERENCES Usuarios(ID_Usuario),
    FOREIGN KEY (ID_Ruta_Asig) REFERENCES Rutas(ID_Ruta),
    FOREIGN KEY (ID_Flota_Asig) REFERENCES Elem_Flota(ID_Elemento)
);

CREATE TABLE U_Gestor_De_Planta(
    ID_Usuario INT PRIMARY KEY,
    ID_Planta_Asig INT,
    FOREIGN KEY (ID_Usuario) REFERENCES Usuarios(ID_Usuario),
    FOREIGN KEY (ID_Planta_Asig) REFERENCES Elem_Planta(ID_Elemento)
);

CREATE TABLE U_Administrativo(
    ID_Usuario INT PRIMARY KEY,
    FOREIGN KEY (ID_Usuario) REFERENCES Usuarios(ID_Usuario)
);

CREATE TABLE Incidencias(
    ID_Incidencia INT AUTO_INCREMENT PRIMARY KEY,
    ID_Elemento INT,
    ID_Usuario INT,
    Fecha_Hora DATETIME DEFAULT CURRENT_TIMESTAMP,
    Descripcion VARCHAR (3500),
    Archivo VARCHAR (1500),
    Estado ENUM('En_cola','En_revision','Aprobada','Denegada') DEFAULT 'En_cola',
    FOREIGN KEY (ID_Elemento) REFERENCES Elementos(ID_Elemento),
    FOREIGN KEY (ID_Usuario) REFERENCES Usuarios(ID_Usuario)
);
