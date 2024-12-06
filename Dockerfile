# Stage 1: Use official Composer image for dependency installation
FROM composer:2 AS composer-stage

WORKDIR /app

# Copy composer files for efficient caching
COPY composer.json composer.lock ./

# Install dependencies
RUN composer install --no-dev --no-interaction --optimize-autoloader

# Stage 2: Final runtime stage with PHP CLI
FROM php:8.1-cli

# Install SQLite dev library so that pdo_sqlite can be compiled
RUN apt-get update && apt-get install -y libsqlite3-dev && rm -rf /var/lib/apt/lists/*

# Install pdo_sqlite extension
RUN docker-php-ext-install pdo_sqlite

WORKDIR /var/www/html

# Copy application code
COPY . /var/www/html

# Copy vendor directory from composer stage
COPY --from=composer-stage /app/vendor /var/www/html/vendor

EXPOSE 8080

CMD ["php", "-S", "0.0.0.0:8080", "-t", "public/"]