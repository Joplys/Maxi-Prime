CREATE DATABASE Proyecto;

USE Proyecto;

CREATE TABLE Distritos(  
    ID_Distrito INT AUTO_INCREMENT PRIMARY KEY,
    Serie VARCHAR (5)
);

CREATE TABLE Usuarios (
    ID_Usuario INT AUTO_INCREMENT PRIMARY KEY,
    Nombre VARCHAR(50) NOT NULL,
    Correo_Electronico VARCHAR(100) NOT NULL,
    Contrasena VARCHAR(255) NOT NULL,
    Fecha_Registro DATE
);

CREATE TABLE Ubicacion(
    ID_Ubicacion INT AUTO_INCREMENT PRIMARY KEY,
    Latitud FLOAT NOT NULL,
    Longitud FLOAT NOT NULL,
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

CREATE TABLE Elementos(
    ID_Elemento INT AUTO_INCREMENT PRIMARY KEY,
    Estado ENUM ('Disponible','Req. Asistencia','Req. Asistencia Inmediata') DEFAULT 'Disponible',
    ID_Ubicacion INT,
    Distrito INT,
    FOREIGN KEY (ID_Ubicacion) REFERENCES Ubicacion(ID_Ubicacion),
    FOREIGN KEY (Distrito) REFERENCES Distritos(ID_Distrito)
);

CREATE TABLE Elem_Contenedores(
    ID_Elemento INT,
    N_serie VARCHAR (6),
    Tipo_Cont ENUM('Domiciliario','Intradomiciliario','Intradomiciliario_doble'),
    Tipo_Res ENUM('Reciclables','Mezclados','Organicos'),
    FOREIGN KEY (ID_Elemento) REFERENCES Elementos(ID_Elemento)
);

CREATE TABLE Elem_Planta(
    ID_Elemento INT,
    Tipo_Planta ENUM('Reciclaje','Acopio','Vertedero','Transferencia'),
    FOREIGN KEY (ID_Elemento) REFERENCES Elementos(ID_Elemento)
);

CREATE TABLE Maquinaria(
    ID_Maquinaria INT AUTO_INCREMENT PRIMARY KEY,
    Matricula VARCHAR (7),
    Estado ENUM('Operable','Req_mantenimiento','Inoperable','En_mantenimiento') DEFAULT 'Operable',
    Modelo VARCHAR (250),
    ID_Planta INT,
    Tipo ENUM('Chipeadoras','Trituracion','Compostaje','Harneado','Separacion')
    FOREIGN KEY (ID_Planta) REFERENCES Elem_Planta(ID_Elemento)
);

CREATE TABLE Elem_Taller(
    ID_Elemento INT,
    FOREIGN KEY (ID_Elemento) REFERENCES Elementos(ID_Elemento)
);

CREATE TABLE Elem_Flota(
    ID_Elemento INT,
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

CREATE TABLE Incidencias(
    ID_Incidencia INT AUTO_INCREMENT PRIMARY KEY,
    ID_Elemento INT,
    ID_Usuario INT,
    Fecha_Hora DATETIME,
    Descripcion VARCHAR (3500),
    Archivo VARCHAR (1500),
    Estado ENUM('En_cola','En_revision','Aprobada','Denegada') DEFAULT 'En_cola',
    FOREIGN KEY (ID_Elemento) REFERENCES Elementos(ID_Elemento),
    FOREIGN KEY (ID_Usuario) REFERENCES Usuarios(ID_Usuario)
);

/*Consultas SQL*/


/*Creacion del distrito*/

INSERT INTO `Distritos`(`Serie`) VALUES ('[X-111]');


/*Creacion general del elemento (Independientemente el elemento que se quiera crear, se debe hacer)*/ 

INSERT INTO `Ubicacion`(`Latitud`, `Longitud`) VALUES ('[111,1]','[-111,1]'); /*1 Se crea la ubicacion*/

INSERT INTO `Elementos`(`Estado`, `ID_Ubicacion`, `Distrito`) VALUES ('[1,2 o 3]' , SELECT MAX(ID_Ubicacion) FROM Ubicacion, '[FK_Distrito]'); /*2 Con la ubicacion creada, se toma el ultimo ID de la tabla Ubicacion para asignarselo automaticamente. El distrito viene del backend, insertado x el admin, quien vera claramente el ID de los distritos para asignar el correspondiente*/


    /*Creacion especifica para:*/

        /*Contenedores*/
        INSERT INTO `Elem_Contenedores`('ID_Elemento','N_serie','Tipo_Cont','Tipo_Res') VALUES (SELECT MAX (ID_Elemento) FROM Elementos,'[XXXXXX]','[1,2 o 3]','[1,2 o 3]');

        /*Plantas:*/
        INSERT INTO 'Elem_Planta'('ID_Elemento','Tipo_Planta') VALUES (SELECT MAX (ID_Elemento) FROM Elementos,'1,2,3 o 4');

        INSERT INTO 'Maquinaria'('Matricula','Estado','Modelo','ID_Planta','Tipo') VALUES ('[XXXXXXX]','[1,2,3 o 4]','[XXXXXXX]', SELECT MAX (ID_Elemento) FROM Elementos,'[1,2,3,4, o 5]');
            /*Repetir consulta cuantas veces sea necesario en funcion de la maquinaria que fue ingresada por el U.A al momento de crear la planta*/

        /*Talleres*/
        INSERT INTO 'Elem_Taller'('ID_Elemento') VALUES (SELECT MAX (ID_Elemento) FROM Elementos);

        /*Flota*/
        INSERT INTO 'Elem_Flota'('ID_Elemento','Matricula','Res_Recolector','Tipo_Vehiculo','ID_Taller_Almacen') VALUES (SELECT MAX (ID_Elemento) FROM Elementos,'[XXXXXXX]','[1,2 o 3]','[1,2 o 3]','[FK_Taller SI AMERITA]');

/*Creacion general de usuarios*/

INSERT INTO Usuarios ('Nombre','Correo_Electronico','Contraseña','Fecha_Registro') VALUES ('[AaBbCcDd]','[AAA@BBB.CCC]','[####]',CURRENT_DATE());

    /*Creacion especifica para:*/

            /*Vecinos*/
            INSERT INTO U_Vecino ('ID_Usuario') VALUES (SELECT MAX (ID_Usuario) FROM Usuarios);

            /*Administrativos*/
            INSERT INTO U_Administrativo ('ID_Usuario') VALUES ('[FK_PK_Usuario]');

            /*Gestor de planta*/
            INSERT INTO U_Gestor_De_Planta ('ID_Usuario','ID_Planta_Asig') VALUES ('[FK_PK_Usuario]', '[FK_Planta]');

            /*Camionero*/
            INSERT INTO U_Camionero ('ID_Usuario','ID_Flota_Asig') VALUES ('[FK_PK_Usuario]','[FK_Flota]');

/*Maquinaria*/

        INSERT INTO 'Maquinaria'('Matricula','Estado','Modelo','ID_Planta','Tipo') VALUES ('[XXXXXXX]','[1,2,3 o 4]','[XXXXXXX]', '[FK_Planta]','[1,2,3,4, o 5]');

/*Rutas*/

        INSERT INTO 'Rutas'('Tipo') VALUES ('[1 o 2]');

        INSERT INTO 'Puntos_Ruta'('ID_Ruta','ID_Ubicacion') VALUES (SELECT MAX (ID_Ruta) FROM Rutas, '[FK_Ubicacion]');
        /*Repetir consulta puntos ruta en funcion de la cantidad de puntos que marco el U.A*/

/*Incidencias*/

        INSERT INTO 'Incidencias' ('ID_Elemento','ID_Usuario','Fecha_Hora','Descripcion','Archivo','Estado') VALUES ('[FK_Elemento heredado de donde se esta mandando la inc.]','[FK_Usuario heredado de quien envio la inc]',NOW(),'[Lo que haya ocurrido redactado en el form.]','URL del archivo enviado en la bandeja multimedia','[1,2,3 o 4]');