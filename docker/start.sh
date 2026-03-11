#!/bin/bash
cd /var/www

# Generate app key if not set
php artisan key:generate --force

# Cache config
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations
php artisan migrate --force

# Start supervisor
/usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf