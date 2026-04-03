#!/bin/sh
cd /var/www/html
php artisan config:clear
php artisan route:clear

# Iniciar subscriber em background
php artisan redis:subscribe-tickets &

php-fpm -D
nginx -g "daemon off;"