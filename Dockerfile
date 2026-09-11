# syntax=docker/dockerfile:1

# Stage 1: build frontend assets with Vite
FROM node:20-alpine AS frontend

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY . .
RUN npm run build

# Stage 2: production image
FROM php:8.4-fpm-alpine

RUN apk add --no-cache \
        nginx \
        gettext \
        libzip-dev \
        libpng-dev \
        oniguruma-dev \
        icu-dev \
        sqlite-dev \
    && docker-php-ext-install \
        zip bcmath opcache

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . .
COPY --from=frontend /app/public/build /app/public/build

RUN composer install --no-dev --optimize-autoloader --no-interaction \
    && mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views \
              storage/logs bootstrap/cache db-vol \
    && chown -R www-data:www-data storage bootstrap/cache db-vol \
    && chmod -R 775 storage bootstrap/cache db-vol

COPY docker/server.conf.template /etc/nginx/templates/server.conf.template
COPY docker/start.sh /app/start.sh
RUN chmod +x /app/start.sh

ENV DB_DATABASE=/app/db-vol/database.sqlite

EXPOSE 8080

CMD ["/app/start.sh"]
