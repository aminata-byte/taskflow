#!/bin/bash
cd /var/www

# Créer .env depuis les variables d'environnement
touch .env
echo "APP_NAME=${APP_NAME}" >> .env
echo "APP_ENV=${APP_ENV}" >> .env
echo "APP_KEY=${APP_KEY}" >> .env
echo "APP_DEBUG=${APP_DEBUG}" >> .env
echo "APP_URL=${APP_URL}" >> .env
echo "LOG_CHANNEL=stderr" >> .env
echo "LOG_LEVEL=error" >> .env
echo "DB_CONNECTION=${DB_CONNECTION}" >> .env
echo "DB_HOST=${DB_HOST}" >> .env
echo "DB_PORT=${DB_PORT}" >> .env
echo "DB_DATABASE=${DB_DATABASE}" >> .env
echo "DB_USERNAME=${DB_USERNAME}" >> .env
echo "DB_PASSWORD=${DB_PASSWORD}" >> .env
echo "SESSION_DRIVER=${SESSION_DRIVER:-cookie}" >> .env
echo "SESSION_LIFETIME=120" >> .env
echo "CACHE_STORE=${CACHE_STORE:-array}" >> .env
echo "QUEUE_CONNECTION=sync" >> .env
echo "FILESYSTEM_DISK=local" >> .env

# Cache config
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations
php artisan migrate --force

# Start supervisor
/usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf