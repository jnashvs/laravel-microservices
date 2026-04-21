#!/bin/sh
cd /var/www/html
php artisan config:clear
php artisan route:clear

# Rodar queue em background
php artisan queue:work &

php-fpm -D
nginx -g "daemon off;"