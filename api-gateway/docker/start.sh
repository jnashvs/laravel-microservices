#!/bin/sh
cd /var/www/html
php artisan config:clear
php artisan route:clear

# Start subscriber with auto-restart loop
(
    while true; do
        echo "[subscriber] Starting Redis subscriber..."
        php artisan redis:subscribe-tickets
        echo "[subscriber] Subscriber exited. Restarting in 5 seconds..."
        sleep 5
    done
) &

php-fpm -D
nginx -g "daemon off;"