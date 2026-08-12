FROM php:8.2-apache

# Installer les dépendances système nécessaires à Laravel et Vite
RUN apt-get update && apt-get install -y --no-install-recommends \
    build-essential \
    git \
    curl \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libsqlite3-0 \
    sqlite3 \
    libsqlite3-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libicu-dev \
    libxslt1-dev \
    ca-certificates \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_sqlite mbstring exif pcntl bcmath gd intl zip \
    && a2enmod rewrite headers \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

# Copier les dépendances PHP et Node pour un build plus rapide
COPY composer.json composer.lock* ./
COPY package*.json ./

RUN composer install --no-interaction --no-progress --prefer-dist --no-scripts --optimize-autoloader \
    && npm install --no-fund --no-audit

# Copier le code source
COPY . .

# Configuration Laravel
RUN cp -n .env.example .env \
    && php artisan key:generate --force \
    && php artisan storage:link || true \
    && npm run build \
    && mkdir -p storage/framework/{cache,sessions,views,testing} bootstrap/cache database \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database

# Apache pointe vers le dossier public de Laravel
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e "s!/var/www/html!${APACHE_DOCUMENT_ROOT}!g" /etc/apache2/sites-available/000-default.conf \
    && sed -ri -e "s!/var/www/html!${APACHE_DOCUMENT_ROOT}!g" /etc/apache2/apache2.conf

EXPOSE 80

CMD ["apache2-foreground"]
