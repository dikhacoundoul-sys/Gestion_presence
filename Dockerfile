FROM php:8.2-apache

# Installation des dépendances système et des extensions PHP nécessaires (mysqli, zip, intl)
RUN apt-get update && apt-get install -y \
    libzip-dev \
    libicu-dev \
    zip \
    && docker-php-ext-install mysqli zip intl

# Activer le module Apache rewrite pour les routes CodeIgniter
RUN a2enmod rewrite

# Configuration du DocumentRoot vers le dossier /public de CodeIgniter
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/conf-available/*.conf

# Copier les fichiers du projet
COPY . /var/www/html/

# Permissions
RUN chown -R www-data:www-data /var/www/html/writable /var/www/html/public

EXPOSE 80