CREATE DATABASE proyecto;
USE proyecto;

CREATE TABLE Distritos (
    Serie VARCHAR(5) PRIMARY KEY,
    N_Plantas INT,
    N_Contenedores INT,
    N_Talleres INT
);

CREATE TABLE Ubicacion (
    ID_Ubicacion INT AUTO_INCREMENT PRIMARY KEY,
    Distrito VARCHAR(5),
    Longitud FLOAT,
    Latitud FLOAT,
    FOREIGN KEY (Distrito) REFERENCES Distritos(Serie)
);

CREATE TABLE Elementos (
    ID_Elemento INT AUTO_INCREMENT PRIMARY KEY,
    Tipo_Elemento ENUM(
        'contenedor',
        'planta',
        'flota',
        'taller'
    )
);

CREATE TABLE Rutas (
    ID_Ruta INT AUTO_INCREMENT PRIMARY KEY,
    Tipo ENUM('recoleccion','traslado'),
    Puntos_Ruta INT
);

CREATE TABLE Puntos_Ruta (
    ID_PR INT AUTO_INCREMENT PRIMARY KEY,
    ID_Ruta INT,
    ID_Elemento INT,
    ID_Ubicacion INT,
    FOREIGN KEY (ID_Ruta) REFERENCES Rutas(ID_Ruta),
    FOREIGN KEY (ID_Elemento) REFERENCES Elementos(ID_Elemento),
    FOREIGN KEY (ID_Ubicacion) REFERENCES Ubicacion(ID_Ubicacion)
);

CREATE TABLE Elem_Contenedores (
    ID_Contenedor INT PRIMARY KEY,
    N_Serie VARCHAR(6),
    Tipo_Cont ENUM(
        'domiciliario',
        'intradomiciliario',
        'intradomiciliario_doble'
    ),
    Tipo_Res ENUM(
        'reciclables',
        'mezclados',
        'organicos'
    ),
    Ubicacion INT,
    Estado ENUM(
        'operable',
        'req_asistencia',
        'req_asistencia_inmediata'
    ) DEFAULT 'operable',
    FOREIGN KEY (ID_Contenedor) REFERENCES Elementos(ID_Elemento),
    FOREIGN KEY (Ubicacion) REFERENCES Ubicacion(ID_Ubicacion)
);

CREATE TABLE Elem_Planta (
    ID_Planta INT PRIMARY KEY,
    Ubicacion INT,
    Estado ENUM(
        'operable',
        'req_asistencia',
        'req_asistencia_inmediata'
    ) DEFAULT 'operable',
    Tipo_Planta ENUM(
        'reciclaje',
        'vertedero',
        'acopio',
        'transferencia'
    ),
    FOREIGN KEY (ID_Planta) REFERENCES Elementos(ID_Elemento),
    FOREIGN KEY (Ubicacion) REFERENCES Ubicacion(ID_Ubicacion)
);

CREATE TABLE Maquinaria (
    Matricula VARCHAR(7) PRIMARY KEY,
    Estado ENUM(
        'operable',
        'req_mantenimiento',
        'en_mantenimiento',
        'inoperable'
    ) DEFAULT 'operable',
    Modelo VARCHAR(250),
    ID_Planta INT,
    Tipo ENUM(
        'chipeadoras',
        'trituracion',
        'compostaje',
        'horneado',
        'separacion'
    ),
    FOREIGN KEY (ID_Planta) REFERENCES Elem_Planta(ID_Planta)
);

CREATE TABLE Elem_Taller (
    ID_Taller INT PRIMARY KEY,
    Ubicacion INT,
    Estado ENUM(
        'operable',
        'req_asistencia',
        'req_asistencia_inmediata'
    ) DEFAULT 'operable',
    FOREIGN KEY (ID_Taller) REFERENCES Elementos(ID_Elemento),
    FOREIGN KEY (Ubicacion) REFERENCES Ubicacion(ID_Ubicacion)
);

CREATE TABLE Elem_Flota (
    ID_Flota INT PRIMARY KEY,
    Matricula VARCHAR(7),
    Res_Recolector ENUM(
        'reciclables',
        'mezclados',
        'organicos'
    ),
    Tipo_Vehiculo ENUM(
        'camion_lateral',
        'camion_trasero',
        'motocarro'
    ),
    Ubicacion INT,
    ID_Taller INT,
    Estado ENUM(
        'operable',
        'req_asistencia',
        'req_asistencia_inmediata'
    ) DEFAULT 'operable',
    FOREIGN KEY (ID_Flota) REFERENCES Elementos(ID_Elemento),
    FOREIGN KEY (Ubicacion) REFERENCES Ubicacion(ID_Ubicacion),
    FOREIGN KEY (ID_Taller) REFERENCES Elem_Taller(ID_Taller)
);

CREATE TABLE U_Camionero (
    ID_US INT AUTO_INCREMENT PRIMARY KEY,
    Nombre VARCHAR(40),
    Email VARCHAR(150),
    Fecha_Registro DATE,
    Contrasena VARCHAR(255),
    ID_Ruta_Asign INT,
    Flota_Asign INT,
    FOREIGN KEY (ID_Ruta_Asign) REFERENCES Rutas(ID_Ruta),
    FOREIGN KEY (Flota_Asign) REFERENCES Elem_Flota(ID_Flota)
);

CREATE TABLE U_Gestor_de_Planta (
    ID_US INT AUTO_INCREMENT PRIMARY KEY,
    Nombre VARCHAR(40),
    Email VARCHAR(150),
    Fecha_Registro DATE,
    Contrasena VARCHAR(255),
    ID_Planta INT,
    FOREIGN KEY (ID_Planta) REFERENCES Elem_Planta(ID_Planta)
);

CREATE TABLE U_Vecino (
    ID_US INT AUTO_INCREMENT PRIMARY KEY,
    Nombre VARCHAR(40),
    Email VARCHAR(150),
    Fecha_Registro DATE,
    Contrasena VARCHAR(255)
);

CREATE TABLE U_Administrativo (
    ID_US INT AUTO_INCREMENT PRIMARY KEY,
    Nombre VARCHAR(40),
    Email VARCHAR(150),
    Fecha_Registro DATE,
    Contrasena VARCHAR(255)
);

CREATE TABLE Incidencias (
    ID_INC INT AUTO_INCREMENT PRIMARY KEY,
    ID_US_Reportador INT,
    ID_Elemento_Reportado INT,
    Ubicacion_Elem INT,
    Fecha_Hora DATETIME,
    Descripcion VARCHAR(3500),
    Archivo VARCHAR(1500),
    Estado ENUM(
        'en_cola',
        'en_revision',
        'aprobada',
        'denegada'
    ) DEFAULT 'en_cola',
    FOREIGN KEY (ID_US_Reportador) REFERENCES U_Vecino(ID_US),
    FOREIGN KEY (ID_Elemento_Reportado) REFERENCES Elementos(ID_Elemento),
    FOREIGN KEY (Ubicacion_Elem) REFERENCES Ubicacion(ID_Ubicacion)
);



