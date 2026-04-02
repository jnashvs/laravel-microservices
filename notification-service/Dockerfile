FROM php:8.4-fpm-alpine

RUN apk add --no-cache bash git curl unzip libzip-dev oniguruma-dev \
    && docker-php-ext-install pdo_mysql mbstring zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

RUN rm -rf vendor composer.lock \
    && composer clear-cache \
    && composer install --no-interaction --optimize-autoloader

EXPOSE 80
CMD ["php-fpm"]