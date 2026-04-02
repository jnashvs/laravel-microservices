#!/bin/sh

cd /var/www/html

php artisan config:cache
php artisan route:cache

php-fpm -D

nginx -g "daemon off;"