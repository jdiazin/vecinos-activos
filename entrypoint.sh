#!/bin/bash
php artisan migrate --force
php artisan user:create-admin
apache2-foreground