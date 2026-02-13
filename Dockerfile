FROM richarvey/php-apache-heroku:latest

# Copie le code du projet dans le serveur
COPY . /var/www/app

# Configuration de la racine de Laravel
ENV WEBROOT /var/www/app/public
ENV PHP_ERRORS_STDERR 1
ENV COMPOSER_ALLOW_SUPERUSER 1

# Installation des dépendances
RUN composer install --no-dev --optimize-autoloader

# On expose le port 80 pour Render
EXPOSE 80