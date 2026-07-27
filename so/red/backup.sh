
# Servidor de Respaldos

sudo nmcli con add \
type ethernet \
ifname ens3 \
con-name LAN \
ipv4.method manual \
ipv4.addresses 192.168.10.30/24 \
ipv4.gateway 192.168.10.1
sudo nmcli con up LAN
#Habilitar puerto 1221 para ssh
sudo firewall-cmd \
--permanent \
--add-port=1221/tcp
sudo firewall-cmd --reload
#Configurar SElinux para permitir el puerto 1221
sudo semanage port -a -t ssh_port_t -p tcp 1221
#Configurar ssh para que utilice dicho puerto
echo "Port 1221" | sudo tee /etc/ssh/sshd_config.d/port.conf
sudo systemctl restart sshd


