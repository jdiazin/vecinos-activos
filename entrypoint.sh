#!/bin/bash

# Salir inmediatamente si falla algún comando
set -e

echo "Ejecutando migraciones de la base de datos..."
php artisan migrate --force

echo "Creando o verificando el usuario administrador..."
php artisan user:create-admin

echo "Iniciando servidor Apache..."
exec apache2-foreground