FROM php:8.2-apache

# ── Extensions PHP ──
RUN apt-get update && apt-get install -y \
    git curl zip unzip \
    libpng-dev libonig-dev libxml2-dev libpq-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql pgsql mbstring exif pcntl bcmath gd \
    && a2enmod rewrite \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# ── Composer ──
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ── Copier le projet (inclut déjà public/build et public/css) ──
WORKDIR /var/www/html
COPY . .

# ── Dépendances PHP uniquement (pas npm, les assets sont déjà compilés) ──
RUN composer install --no-dev --optimize-autoloader --no-interaction

# ── Créer .env ──
RUN cp .env.example .env && php artisan key:generate --force

# ── Permissions ──
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 777 /var/www/html/storage \
    && chmod -R 777 /var/www/html/bootstrap/cache

# ── Apache → /public ──
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' \
    /etc/apache2/sites-available/000-default.conf

RUN echo '<Directory /var/www/html/public>\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' >> /etc/apache2/sites-available/000-default.conf

# ── Script démarrage ──
RUN printf '#!/bin/bash\nphp artisan config:clear\nphp artisan migrate --force\napache2-foreground\n' > /start.sh \
    && chmod +x /start.sh

EXPOSE 80
CMD ["/start.sh"]