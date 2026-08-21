#!/bin/bash
set -e

# Copie de .env.prod dans .env
[ -f .env ] || cp .env.prod .env

# Générer APP_KEY si elle n'existe pas ou est vide
if ! grep -q "^APP_KEY=" .env || [ -z "$(grep "^APP_KEY=" .env | cut -d= -f2)" ]; then
    echo "[entrypoint] Génération de APP_KEY..."
    php artisan key:generate --force
fi

# Vide cache & Migration
php artisan config:clear
php artisan migrate --force

# Seed de l'utilisateur admin (si nécessaire)
php artisan db:seed --class=Database\\Seeders\\UserSeeder --force

chown -R www-data:www-data storage bootstrap/cache

echo "[entrypoint] Démarrage de PHP-FPM..."
php-fpm -D

echo "[entrypoint] Démarrage de Nginx..."
exec nginx -g "daemon off;"