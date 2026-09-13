#!/usr/bin/bash
#Cronie y nano vienen instalados de forma predeterminada.
sudo dnf install -y epel-release
sudo dnf config-manager --set-enabled crb
sudo dnf update -y
sudo dnf install -y htop vim policycoreutils-python-utils
