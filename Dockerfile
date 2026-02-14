FROM php:8.3-apache

# Installation des dépendances système
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libicu-dev \
    zip \
    unzip \
    git \
    curl

# Installation des extensions PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip intl

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

# Configuration correcte du DocumentRoot Apache
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/000-default.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

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