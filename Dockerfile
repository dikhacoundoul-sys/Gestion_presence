FROM php:8.2-apache

# Installation des dépendances système requises par CodeIgniter (zip, intl, mysqli, git)
RUN apt-get update && apt-get install -y \
    libzip-dev \
    libicu-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-install mysqli zip intl

# Copie de l'exécutable Composer depuis l'image officielle
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Activation du module rewrite pour les routes CodeIgniter 4
RUN a2enmod rewrite

# Pointer Apache directement sur le sous-dossier /public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/conf-available/*.conf

# Définir le dossier de travail
WORKDIR /var/www/html

# Copier le code source de l'application
COPY . /var/www/html/

# Installer les dépendances Composer et générer le dossier vendor/
RUN composer install --no-dev --optimize-autoloader

# Ajuster les permissions pour CodeIgniter (écriture dans writable/)
RUN chown -R www-data:www-data /var/www/html/writable /var/www/html/public

EXPOSE 80