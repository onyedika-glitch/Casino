#!/bin/bash
set -e

# Run Composer if vendor/ is missing
if [ ! -d "vendor" ]; then
  composer install --no-dev --optimize-autoloader
fi

# Laravel setup
cp .env.example .env || true
php artisan key:generate || true
php artisan config:cache
php artisan route:cache
php artisan migrate --force || true

# Start supervisord (runs both php-fpm and nginx)
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
