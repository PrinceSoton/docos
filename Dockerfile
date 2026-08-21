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
RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader --no-scripts --no-progress
COPY . .
RUN composer dump-autoload --optimize

# --- Production image (PHP-FPM + Nginx) ---
FROM php:8.2-fpm-alpine

RUN echo "nameserver 8.8.8.8" > /etc/resolv.conf \
    && echo "nameserver 1.1.1.1" >> /etc/resolv.conf \
    && apk add --no-cache nginx bash libpng-dev libjpeg-turbo-dev freetype-dev libzip-dev zlib icu-dev oniguruma-dev curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd zip intl \
    && rm -rf /var/cache/apk/*

WORKDIR /var/www/html

# Copy application code and dependencies
COPY --from=composer_builder /app /var/www/html
# Copy built frontend assets
COPY --from=node_builder /app/public/build /var/www/html/public/build

# Nginx config and entrypoint
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
