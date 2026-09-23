# --- Builder for frontend assets ---
FROM node:18-alpine AS node_builder
WORKDIR /app
COPY package*.json ./
COPY vite.config.js postcss.config.js tailwind.config.js ./
COPY resources ./resources
RUN npm ci --silent && npm run build

# --- Composer stage: install PHP deps ---
FROM composer:2 AS composer_builder
WORKDIR /app
COPY composer.json composer.lock ./

# Forcer l'utilisation du dépôt officiel packagist.org pour éviter les 429 des miroirs
RUN composer config repos.packagist composer https://packagist.org

RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader --no-scripts --no-progress
COPY . .
RUN composer dump-autoload --optimize

# --- Production image (PHP-FPM + Nginx) ---
FROM php:8.2-fpm-alpine

# Installation des dépendances système et extensions PHP
RUN apk add --no-cache nginx bash libpng-dev libjpeg-turbo-dev freetype-dev libzip-dev zlib icu-dev oniguruma-dev curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd zip intl \
    && rm -rf /var/cache/apk/*

RUN { \
    echo 'upload_max_filesize=60M'; \
    echo 'post_max_size=64M'; \
    echo 'memory_limit=256M'; \
    echo 'max_execution_time=120'; \
} > /usr/local/etc/php/conf.d/uploads.ini
    
WORKDIR /var/www/html

# Copie du code applicatif et des dépendances PHP
COPY --from=composer_builder /app /var/www/html
# Copie des assets frontend buildés
COPY --from=node_builder /app/public/build /var/www/html/public/build

# Configuration Nginx et point d'entrée
COPY nginx/default.conf /etc/nginx/http.d/default.conf
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh \
    && mkdir -p /run/nginx \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80

ENV APP_ENV=production \
    APP_DEBUG=false

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["nginx", "-g", "daemon off;"]