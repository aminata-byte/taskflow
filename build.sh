#!/usr/bin/env bash
# Script de déploiement pour Render.com

# Arrêter le script si une commande échoue
set -e

# Installer les dépendances PHP (sans les packages de dev)
echo "📦 Installation des dépendances..."
composer install --no-dev --optimize-autoloader

# Copier le fichier .env.example en .env
echo "⚙️ Configuration de l'environnement..."
cp .env.example .env

# Générer la clé de l'application
php artisan key:generate --force

# Mettre en cache la config, les routes et les vues
echo "🚀 Optimisation..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Lancer les migrations
echo "🗄️ Migration de la base de données..."
php artisan migrate --force

echo "✅ Déploiement terminé !"