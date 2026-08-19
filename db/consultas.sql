/*************/
/* Consultas */
/*************/

/****************************************/
/* Como crear un usuario administrativo */
/****************************************/
-- 1. Crear usuario basico
INSERT INTO Usuarios(Nombre, Correo_Electronico, Contrasena)
VALUES ('Nombre', 'mi@correo.com', 'HASH');

-- 2. Tomar el id autoincrement creado en la consulta anterior.
set @id_uadmin = LAST_INSERT_ID();

-- 3. Agregar el id usuario de administrador a su respectiva tabla.
INSERT INTO U_Administrativo(ID_Usuario)
VALUES (@id_uadmin);

/**************************************/
/* Como crear un contenedor (largito) */
/**************************************/

START TRANSACTION;

-- 1. Crear distrito
INSERT INTO Distritos(Serie)
VALUES ('[X-111]');

set @id_distrito = LAST_INSERT_ID();

-- 2. Crear ubicacion
INSERT INTO Ubicacion(Latitud, Longitud)
VALUES (-50,70);

set @id_ubicacion = LAST_INSERT_ID();

-- 3. Crear elemento
INSERT INTO Elementos(ID_Ubicacion, Distrito)
VALUES (@id_ubicacion, @id_distrito);

set @id_elemento = LAST_INSERT_ID();

-- 4. Crear subelemento (contenedor)
INSERT INTO Elem_Contenedores(ID_Elemento, N_serie, Tipo_Cont, Tipo_Res)
VALUES (@id_elemento, 123456, Domiciliario, Mezclados)

COMMIT; 
