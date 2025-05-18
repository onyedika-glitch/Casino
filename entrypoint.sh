#!/bin/bash

# Run Composer if vendor/ is missing
if [ ! -d "vendor" ]; then
  composer install --no-dev --optimize-autoloader
fi

# Laravel config and cache
php artisan config:cache
php artisan route:cache
php artisan migrate --force

# Start services
service php8.1-fpm start
service nginx start

# Keep container running
tail -f /dev/null
