#!/bin/bash
set -e

##############
# VARIABLES  #
##############
DB_NAME="complaints_chat"
SQL_PATH="./sql/database.sql"

#############
# FUNCTIONS #
#############

check_root() {
    if [ "$(id -u)" -ne 0 ]; then
        echo "¡Este script debe ejecutarse como root o usando sudo!"
        exit 1
    fi
}

install_packages() {
    apt update
    apt install -y mysql-server mysql-client php php-mysql
}

setup_database() {
    if [ ! -f "$SQL_PATH" ]; then
        echo "No se encuentra el archivo: $SQL_PATH"
        exit 1
    fi
    mysql -u root < "$SQL_PATH"
    echo "Base de datos importada correctamente."
}

#############
# EXECUTION #
#############
check_root
install_packages
setup_database