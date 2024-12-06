FROM composer:2 AS composer-stage

WORKDIR /app

COPY composer.json composer.lock ./

RUN composer install --no-dev --no-interaction --optimize-autoloader

FROM php:8.1-cli

RUN apt-get update && apt-get install -y libsqlite3-dev && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo_sqlite

WORKDIR /var/www/html

COPY . /var/www/html

COPY --from=composer-stage /app/vendor /var/www/html/vendor

EXPOSE 8080

CMD ["php", "-S", "0.0.0.0:8080", "-t", "public/"]