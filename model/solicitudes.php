<?php

$crearAdmin = "
START TRANSACTION;

INSERT INTO Usuarios(Nombre, Correo_Electronico, Contrasena)
VALUES ('Nombre', 'mi@correo.com', 'HASH');

set @id_usuario = LAST_INSERT_ID();

INSERT INTO U_Administrativo(ID_Usuario)
VALUES (@id_usuario);

COMMIT; 
";

?>

