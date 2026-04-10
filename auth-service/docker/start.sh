#!/bin/bash
set -e

cd /var/www/html

# Wait for DB
echo "Waiting for database..."
until php artisan db:monitor --databases=mysql 2>/dev/null; do
    sleep 2
done
echo "Database is ready."

# Run migrations
php artisan migrate --force

# Generate Passport keys if they don't exist
if [ ! -f storage/oauth-private.key ]; then
    php artisan passport:keys --force
    echo "Passport keys generated."
fi

# Cache config
php artisan config:cache
php artisan route:cache

# Start services
php-fpm -D
nginx -g "daemon off;"