FROM php:8.1-apache

# Installation des extensions PHP requises (Intl, PDO, MySQL, etc.)
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-install intl pdo pdo_mysql zip

# Activer mod_rewrite pour Apache (nécessaire pour le routage CodeIgniter/Laravel)
RUN a2enmod rewrite

# Copier les fichiers du projet
COPY . /var/www/html/

# Configurer le dossier racine d'Apache sur public/
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Donner les permissions sur les dossiers d'écriture
RUN chown -R www-data:www-data /var/www/html/writable /var/www/html/storage 2>/dev/null || true

EXPOSE 80