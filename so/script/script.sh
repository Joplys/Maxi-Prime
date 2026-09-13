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
grupos() {
while true; do
echo "Gestion de usuarios:
1- Crear grupo 
2- Eliminar grupo 
3- Agregar usuario a grupo
4- Quitar usuario de grupo
5- Ver grupos registrados
0- Volver"

read op_g
case $op_g in
    1)
        read grp -p "Grupo:"
        sudo groupadd "$grp"
    ;;
    2)
        read grp -p "Grupo:"
        echo "Esta seguro de que desea eliminar el grupo $grp?"
        read confirm -p "y/N"
        case "$confirm" in 
            [yY]) sudo groupdel "$grp";;
            *) continue;;
        esac
    ;;
    3)
        read usu -p "Usuario:"
        read grp -p "Grupo/s (separados con comma):"
        sudo usermod -aG "$grp" "$usu"
    ;;
    4)
        read usu -p "Usuario:"
        read grp -p "Grupo/s (separados con comma):"
        # este check se puede evadir al remover varios grupos pero no tengo ni ganas de implementar una busqueda de substring
        if [ "$grp" = "wheel" ]; then
            echo "No esta permitido operar sobre wheel."
            continue
        fi
        echo "Esta seguro de que desea quitar al usuario $usu del grupo $grp?"
        read confirm -p "y/N"
        case "$confirm" in 
            [yY])sudo usermod -rG "$grp" "$usu";;
            *) continue;;
        esac
    ;;
    5)getent group | cut -d : -f 1;;
    0)break;;
    *)echo "Opcion invalida";;
esac
done
}

respaldos() {
}

redes(){
}

basededatos(){
}

firewall() {
while true; do
echo "Gestion de firewall:
1- Ver reglas
2- Abrir puerto
3- Cerrar puerto
0- Volver"

read op_f
case $op_f in
    1)
        sudo firewall-cmd --list-all
    ;;
    2)
        read zona -p "Zona:"
        read port -p "Puerto:"
        read proto -p "Protocolo (tcp/udp):"
        sudo firewall-cmd --permanent --zone="$zona" --add-port="$port/$proto"
        sudo firewall-cmd --reload
    ;;
    3)
        read port -p "Puerto:"
        read proto -p "Protocolo (tcp/udp):"
        sudo firewall-cmd --permanent --remove-port="$port/$proto"
        sudo firewall-cmd --reload
    ;;
    0) break ;;
    *) echo "Opcion invalida" ;;
esac
done
}

logs() {
while true; do
echo "Gestion de logs del sistema:
1- Ver logs recientes
2- Ver errores
3- Ver advertencias
4- Ver logs del arranque actual
5- Seguir logs en tiempo real
6- Ver logs de un servicio
7- Ver logs desde una fecha
0- Volver"

read op_l
case $op_l in
    1)journalctl -n 50;;
    2) journalctl -p err ;;
    3) journalctl -p warning ;;
    4) journalctl -b ;;
    5) journalctl -f ;;
    6)
        read serv -p "Servicio:"
        journalctl -u "$serv" 
    ;;
    7)
        read fecha -p "Desde (YYYY-MM-DD HH:MM):"
        journalctl --since "$fecha"
    ;;
    0) break ;;
    *) echo "Opcion invalida" ;;
esac
done
}

menu() {
while true; do

echo "Bienvenido al script del servidor
1- Gestion de usuarios
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
