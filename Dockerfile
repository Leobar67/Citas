FROM php:8.2-apache

# Instalar dependencias de PostgreSQL
RUN apt-get update && apt-get install -y \
    libpq-dev \
    git \
    unzip \
    && docker-php-ext-install pdo_pgsql pdo_mysql mysqli

# Activar mod_rewrite
RUN a2enmod rewrite

# Copiar todo el proyecto
COPY . /var/www/html/

# Permisos
RUN chown -R www-data:www-data /var/www/html
