#!/usr/bin/env bash
# -------------------------------------------------------------------
# docker_run.sh — Entrypoint script for LBAW Laravel + Nginx container
#
# This script prepares Laravel’s caches for production
# and starts both PHP-FPM (background) and Nginx (foreground).
# -------------------------------------------------------------------
set -euo pipefail

cd /var/www

# Ensure Laravel runtime directories exist and have correct permissions
mkdir -p storage/framework/{cache,sessions,views} bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Clear and cache Laravel configuration for faster boot (run as www-data)
su -s /bin/bash www-data -c "php artisan config:clear"
su -s /bin/bash www-data -c "php artisan route:clear"
su -s /bin/bash www-data -c "php artisan view:clear"

# Rebuild optimized caches (run as www-data)
su -s /bin/bash www-data -c "php artisan config:cache"
su -s /bin/bash www-data -c "php artisan route:cache"
su -s /bin/bash www-data -c "php artisan view:cache"

# Start PHP-FPM in background
php-fpm -D

# Start nginx in foreground (keeps container alive)
exec nginx -g "daemon off;"