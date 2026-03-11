#!/bin/bash
cd /var/www

# Créer .env depuis les variables d'environnement
cat > .env << ENVEOF
APP_NAME=${APP_NAME}
APP_ENV=${APP_ENV}
APP_KEY=${APP_KEY}
APP_DEBUG=${APP_DEBUG}
APP_URL=${APP_URL}
ASSET_URL=${APP_URL}
LOG_CHANNEL=stderr
LOG_LEVEL=error
DB_CONNECTION=${DB_CONNECTION}
DB_HOST=${DB_HOST}
DB_PORT=${DB_PORT}
DB_DATABASE=${DB_DATABASE}
DB_USERNAME=${DB_USERNAME}
DB_PASSWORD=${DB_PASSWORD}
SESSION_DRIVER=cookie
SESSION_LIFETIME=120
CACHE_STORE=array
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=local
ENVEOF

# Force HTTPS
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations
php artisan migrate --force

# Start supervisor
/usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf