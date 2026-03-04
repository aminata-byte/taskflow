FROM php:8.2-apache

# ── Installer les extensions PHP nécessaires pour Laravel ──
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd \
    && a2enmod rewrite

# ── Installer Composer ──
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ── Copier le code du projet ──
COPY . /var/www/html

WORKDIR /var/www/html

# ── Installer les dépendances Laravel ──
RUN composer install --no-dev --optimize-autoloader --no-interaction

# ── Permissions correctes ──
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 777 /var/www/html/storage \
    && chmod -R 777 /var/www/html/bootstrap/cache

# ── Pointer Apache vers le dossier public de Laravel ──
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' \
    /etc/apache2/sites-available/000-default.conf

# ── Générer la clé Laravel ──
RUN php artisan key:generate --force

EXPOSE 80