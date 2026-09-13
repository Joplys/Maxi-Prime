#!/usr/bin/bash
contraroot='iV7!qL9#rT2@xM8$z'

sudo dnf install -y https://repo.mysql.com/yum/mysql-8.4-community/el/10/x86_64/mysql84-community-release-el10-1.noarch.rpm

# sin dependencias debiles ya que hay conflicto con paquetes de mariadb
sudo dnf install mysql-community-server     --setopt=install_weak_deps=False --nogpgcheck
sudo systemctl enable --now mysqld

sleep 5

contradefault=$(sudo grep 'temporary password' /var/log/mysqld.log)

mysql --connect-expired-password \
    -u root \
    -p"$contradefault" <<EOF
ALTER USER 'root'@'localhost' IDENTIFIED BY '${contraroot}';
CREATE USER IF NOT EXISTS 'root'@'%' IDENTIFIED BY '${contraroot}';
GRANT ALL PRIVILEGES ON *.* TO 'root'@'%' WITH GRANT OPTION;
FLUSH PRIVILEGES;
EOF

echo "contra root: $contraroot"
