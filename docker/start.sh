#!/bin/bash
cd /var/www

# Supprimer le cache ancien
php artisan config:clear
php artisan cache:clear

# Créer .env depuis les variables d'environnement
cat > .env << ENVEOF
APP_NAME=${APP_NAME:-TaskFlow}
APP_ENV=${APP_ENV:-production}
APP_KEY=${APP_KEY}
APP_DEBUG=${APP_DEBUG:-false}
APP_URL=${APP_URL}
ASSET_URL=${APP_URL}
LOG_CHANNEL=stderr
LOG_LEVEL=error
DB_CONNECTION=${DB_CONNECTION:-pgsql}
DB_HOST=${DB_HOST}
DB_PORT=${DB_PORT:-5432}
DB_DATABASE=${DB_DATABASE}
DB_USERNAME=${DB_USERNAME}
DB_PASSWORD=${DB_PASSWORD}
SESSION_DRIVER=cookie
SESSION_LIFETIME=120
CACHE_STORE=array
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=local
ENVEOF

echo "=== DB_CONNECTION: ${DB_CONNECTION} ==="
echo "=== DB_HOST: ${DB_HOST} ==="

# Nouveau cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# ✅ migrate (sans fresh = ne supprime pas les données)
php artisan migrate --force

# ✅ Créer l'admin s'il n'existe pas
php artisan db:seed --class=AdminSeeder --force

# Start supervisor
/usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf