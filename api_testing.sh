#!/usr/bin/env bash

# unit testing re rusheado con curl

echo "crear contenedor"
curl -k -X POST "https://localhost:8000/api/gestion.php?resource=contenedor" \
  -H "Content-Type: application/json" \
  -d '{"Latitud":-34.6037,"Longitud":-58.3816,"Estado":"Disponible","ID_Distrito":1,"N_Serie":"0002","Tipo_Cont":"Intradomiciliario_doble","Tipo_Res":"Mezclados"}'

echo "conseguir contenedor por id"
curl -k -X GET "https://localhost:8000/api/gestion.php?resource=contenedor&id=1"

echo "borrar contenedor 1"
curl -k -X DELETE "https://localhost:8000/api/gestion.php?resource=contenedor&id=1"
