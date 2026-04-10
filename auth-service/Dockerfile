FROM php:8.4-fpm-alpine

RUN apk add --no-cache bash git curl unzip libzip-dev oniguruma-dev nginx \
    && docker-php-ext-install pdo_mysql mbstring zip bcmath

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

RUN rm -rf vendor composer.lock \
    && composer clear-cache \
    && composer install --no-interaction --optimize-autoloader

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database

COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 80
CMD ["/start.sh"]