# Image PHP officielle avec Apache
FROM php:8.2-apache

# Installation des extensions PHP nécessaires pour Laravel + PostgreSQL
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    zip \
    unzip \
    nodejs \
    npm \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Activer mod_rewrite pour Laravel
RUN a2enmod rewrite

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Définir le dossier de travail
WORKDIR /var/www/html

# Copier tous les fichiers du projet
COPY . .

# Installer les dépendances PHP
RUN composer install --no-dev --optimize-autoloader

# Installer les dépendances JS et compiler les assets
RUN npm install && npm run build

# Copier le fichier .env.example en .env
RUN cp .env.example .env

# Générer la clé Laravel
RUN php artisan key:generate --force

# Permissions sur les dossiers storage et cache
RUN chown -R www-data:www-data /var/www/html/storage \
    && chown -R www-data:www-data /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Configuration Apache : pointer vers /public
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' \
    /etc/apache2/sites-available/000-default.conf

# Autoriser .htaccess dans le dossier public
RUN echo '<Directory /var/www/html/public>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' >> /etc/apache2/sites-available/000-default.conf

# Script de démarrage : migrate + lancer Apache
RUN echo '#!/bin/bash\n\
php artisan config:clear\n\
php artisan migrate --force\n\
apache2-foreground' > /start.sh \
&& chmod +x /start.sh

# Port exposé
EXPOSE 80

# Lancer le script de démarrage
CMD ["/start.sh"]