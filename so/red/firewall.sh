
# Configuración servidor firewall

#WAN interfaz ens3
sudo nmcli con add \
type ethernet \
ifname ens3 \
con-name WAN \
ipv4.method manual \
ipv4.addresses 192.168.1.2/24 \
ipv4.gateway 192.168.1.1 \
ipv4.dns 8.8.8.8
sudo nmcli con up WAN
#LAN interfaz ens4
sudo nmcli con add \
type ethernet \
ifname ens4 \
con-name LAN \
ipv4.method manual \
ipv4.addresses 192.168.10.1/24
sudo nmcli con up LAN
#Habilitar enrutamiento
echo "net.ipv4.ip_forward=1" | sudo tee /etc/sysctl.d/firewall.conf
sudo sysctl --system
#Firewall
sudo firewall-cmd --permanent --zone=external --change-interface=ens3
sudo firewall-cmd --permanent --zone=internal --change-interface=ens4
sudo firewall-cmd --permanent --zone=external --add-masquerade
sudo firewall-cmd --permanent --zone=internal
#Enrutar http y https hacia el servidor
sudo firewall-cmd --permanent \
--add-forward-port=port=80:proto=tcp:toaddr=192.168.10.10:toport=80
sudo firewall-cmd --permanent \
--add-forward-port=port=443:proto=tcp:toaddr=192.168.10.10:toport=443
#Habilitar ssh puerto 32487
sudo firewall-cmd --permanent --add-port=32487/tcp
sudo firewall-cmd --reload
#Configurar SElinux para permitir el puerto 32487
sudo semanage port -a -t ssh_port_t -p tcp 32487
#Configurar ssh para que utilice dicho puerto
echo "Port 32487" | sudo tee /etc/ssh/sshd_config.d/port.conf
sudo systemctl restart sshd

