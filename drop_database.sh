#!/bin/bash
set -e

##############
# VARIABLES  #
##############
DB_NAME="complaints_chat"

#############
# FUNCTIONS #
#############

check_root() {
    if [ "$(id -u)" -ne 0 ]; then
        echo "¡Este script debe ejecutarse como root o usando sudo!"
        exit 1
    fi
}

confirm_delete() {
    read -p "¿Realmente desea eliminar la base de datos ${DB_NAME}? (s/n): " confirm
    case "$confirm" in
        [sS])
            return 0  # Sí
            ;;
        [nN])
            echo "Eliminación cancelada."
            exit 0
            ;;
        *)
            echo "Respuesta no válida. Por favor responda con 's' o 'n'."
            exit 1
            ;;
    esac
}

delete_database() {
    # Si tu usuario root de MySQL tiene contraseña, añade -p y sigue las instrucciones
    mysql -u root -e "DROP DATABASE IF EXISTS ${DB_NAME};"
    echo "La base de datos '${DB_NAME}' ha sido eliminada."
}

#############
# EXECUTION #
#############
check_root
confirm_delete
delete_database