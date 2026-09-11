#!/bin/sh
set -e

mkdir -p /app/storage/framework/cache /app/storage/framework/sessions /app/storage/framework/views
mkdir -p /app/storage/logs /app/bootstrap/cache /app/db-vol

PORT="${PORT:-8080}"
envsubst '${PORT}' < /etc/nginx/templates/server.conf.template > /etc/nginx/http.d/server.conf

if [ -z "$APP_KEY" ]; then
    [ -f /app/.env ] || cp /app/.env.example /app/.env
    php artisan key:generate --no-interaction
fi

touch "${DB_DATABASE:-/app/db-vol/database.sqlite}"
chown -R www-data:www-data "$(dirname "${DB_DATABASE:-/app/db-vol/database.sqlite}")"
php artisan migrate --force --no-interaction

php-fpm -D
exec nginx -g 'daemon off;'
