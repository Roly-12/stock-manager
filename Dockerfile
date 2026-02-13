FROM php:8.2-apache

# Installation des extensions PHP nécessaires pour Laravel et MySQL
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Activation du module Rewrite d'Apache pour Laravel
RUN a2enmod rewrite

# Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copie du projet
WORKDIR /var/www/html
COPY . .

# Installation des dépendances PHP
RUN composer install --no-dev --optimize-autoloader

# On donne les droits au serveur web sur le dossier storage
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# On change le dossier racine d'Apache vers /public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/000-default.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

EXPOSE 80