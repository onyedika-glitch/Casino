#!/bin/bash

# Start PHP-FPM and NGINX via supervisor
composer install --no-dev --optimize-autoloader

php artisan config:cache
php artisan route:cache

# Start PHP-FPM and NGINX
service php8.1-fpm start
nginx -g "daemon off;"
