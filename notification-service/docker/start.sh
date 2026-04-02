#!/bin/sh
cd /var/www/html
php artisan config:clear
php artisan route:clear
php-fpm -D
nginx -g "daemon off;"
