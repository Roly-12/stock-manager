FROM php:8.3-apache

# Installation des dépendances système (avec celles pour Excel/Images)
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libicu-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    zip \
    unzip \
    git \
    curl

# Configuration de l'extension GD (nécessaire pour les formats Excel)
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip intl

# Augmenter la mémoire pour les gros fichiers Excel
RUN echo "memory_limit=512M" > /usr/local/etc/php/conf.d/memory-limit.ini

# Installation de Node.js v22
RUN curl -sL https://deb.nodesource.com/setup_22.x | bash - && \
    apt-get install -y nodejs

# Activation du module Rewrite pour Laravel
RUN a2enmod rewrite

# Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

# Installation des dépendances PHP
RUN composer install --no-dev --optimize-autoloader --no-scripts --ignore-platform-reqs

RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/framework/laravel-excel
RUN chown -R www-data:www-data /var/www/html/storage

# --- LE FIX POUR LE NOT FOUND (404) ---
RUN echo "<VirtualHost *:80>\n\
    DocumentRoot /var/www/html/public\n\
    <Directory /var/www/html/public>\n\
        Options Indexes FollowSymLinks\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
</VirtualHost>" > /etc/apache2/sites-available/000-default.conf
# --------------------------------------

# Création des dossiers de cache et sessions
RUN mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache bootstrap/cache

# Installation JS et Build Vite
RUN npm install
RUN npm run build

# FIX DES PERMISSIONS FINAL
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Script de démarrage
RUN echo '#!/bin/sh\nphp artisan config:clear\nphp artisan migrate --force\nexec apache2-foreground' > /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

CMD ["/usr/local/bin/start.sh"]