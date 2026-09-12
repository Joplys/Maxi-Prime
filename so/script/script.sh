#!/usr/bin/env bash

isroot(){
    [ "$1" = "root" ]
}

usuarios() {
while true; do
echo "Gestion de usuarios:
1- Crear usuario
2- Eliminar usuario
3- Bloquear usuario
4- Desbloquar usuario
5- Ver usuarios activos
6- Ver usuarios registrados
0- Volver"

read op_u
case $op_u in
    1)
        read user -p "Usuario:"
        if isroot "$user"; then
            echo "No esta permitido operar sobre root."
            continue
        fi
        sudo useradd "$user"
    ;;
    2)
        read user -p "Usuario:"
        if isroot "$user"; then
            echo "No esta permitido operar sobre root."
            continue
        fi
        echo "Esta seguro de que desea eliminar a $user?"
        read confirm -p "y/N"
        case "$confirm" in 
            [yY]) sudo userdel "$user";;
            [nN]) continue;;
            *) continue;;
        esac
    ;;
    3)
        read user -p "Usuario:"
        if isroot "$user"; then
            echo "No esta permitido operar sobre root."
            continue
        fi
        sudo usermod -L "$user"
    ;;
    4)
        read user -p "Usuario:"
        if isroot "$user"; then
            echo "No esta permitido operar sobre root."
            continue
        fi
        sudo usermod -U "$user"
    ;;
    5)who;;
    6)getent passwd | cut -d : -f 1,5 -O "   " 2>/dev/null;;
    0)break;;
    *)echo "Opcion invalida";;
esac
done
}

menu() {
while true; do

echo "Bienvenido al script del servidor
1- Gestion de servicios
2- Gestion de grupos
3- Gestion de respaldos
4- Gestion de redes
5- Gestion de base de datos
6- Gestion de firewall
7- Gestion de logs del sistema"

read op_m

case "$op_m" in
    1) usuarios;;
    2) grupos;;
    3) respaldos;;
    4) redes;;
    5) basededatos;;
    6) firewall;;
    7) logs;;
    0) break;;
    *) echo "Opcion invalida.";;
esac
done;
}

menu
