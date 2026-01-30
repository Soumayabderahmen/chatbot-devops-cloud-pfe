### ------------ Étape 1 : Composer ------------ ###
FROM composer:2.7 AS composer_builder
WORKDIR /app

COPY composer.json composer.lock ./
RUN mkdir -p app bootstrap config database public resources routes storage tests && \
    touch artisan && \
    composer install --no-dev --optimize-autoloader --no-interaction
COPY . .
RUN composer dump-autoload -o

### ------------ Étape 2 : Vite / Vue (build assets) ------------ ###
FROM node:20-bullseye AS node_builder
WORKDIR /app

# Dépendances front
COPY package.json package-lock.json ./
RUN npm ci || npm install --legacy-peer-deps

# Fichiers de config
COPY vite.config.js .
COPY tailwind.config.js .
COPY postcss.config.js .

# Sources
COPY resources/ ./resources/
COPY public/ ./public/
COPY --from=composer_builder /app/vendor ./vendor

# Build de production + copie du manifest à la racine de build
RUN npm run build && \
    test -f public/build/.vite/manifest.json && \
    cp public/build/.vite/manifest.json public/build/manifest.json

# Diagnostics (optionnel)
RUN ls -la public/build/ && \
    head -n 20 public/build/.vite/manifest.json || true

### ------------ Étape 3 : PHP-FPM avec Laravel ------------ ###
FROM php:8.2-fpm
WORKDIR /var/www

RUN apt-get update && apt-get install -y \
    unzip libzip-dev git curl libpng-dev libonig-dev libxml2-dev libicu-dev zlib1g-dev \
 && docker-php-ext-install pdo_mysql zip mbstring bcmath intl xml gd \
 && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer
COPY --from=composer_builder /app ./
COPY --from=node_builder   /app/public/build ./public/build

# ✅ Symlink public/storage -> storage/app/public dans l'image
RUN mkdir -p /var/www/storage/app/public && \
    ln -sfn /var/www/storage/app/public /var/www/public/storage && \
    chown -h www-data:www-data /var/www/public/storage

# Vérification manifest
RUN test -f public/build/manifest.json && echo "✅ manifest.json OK" || (echo "❌ manifest.json manquant" && exit 1)

# Permissions Laravel
RUN mkdir -p storage bootstrap/cache && \
    chown -R www-data:www-data storage bootstrap/cache /var/www

EXPOSE 9000
CMD ["php-fpm", "--nodaemonize"]
