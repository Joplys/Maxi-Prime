#!/usr/bin/bash
sudo dnf config-manager --add-repo https://download.docker.com/linux/rhel/docker-ce.repo
sudo dnf -y install docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin
sudo systemctl --now enable docker
sudo usermod -a -G docker admin
echo "Es necesario reiniciar la sesión para que el grupo se integre al entorno."
echo "Ejecute exit y luego vuelva a iniciar sesión con su usuario"
