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

# Activation du module Rewrite
RUN a2enmod rewrite

# Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

# LE SECRET : On ajoute --ignore-platform-reqs pour forcer l'installation
RUN composer install --no-dev --optimize-autoloader --no-scripts --ignore-platform-reqs

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/000-default.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# ... (tes installations précédentes) ...

# On s'assure que les dossiers existent
RUN mkdir -p storage/framework/sessions \
    mkdir -p storage/framework/views \
    mkdir -p storage/framework/cache \
    mkdir -p bootstrap/cache

# FIX DES PERMISSIONS (Crucial pour l'erreur 500)
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# On remet migrate (sans fresh ni seed si c'est déjà fait, sinon garde-les une dernière fois)
RUN echo 'php artisan config:clear && php artisan migrate --force && apache2-foreground' > /usr/local/bin/start.sh

RUN chmod +x /usr/local/bin/start.sh

CMD ["/usr/local/bin/start.sh"]