#!/bin/sh
cd /var/www/html
php artisan config:clear
php artisan route:clear
php artisan migrate --force
php-fpm -D
nginx -g "daemon off;"
