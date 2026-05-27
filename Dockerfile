FROM php:8.2-apache

# Instalamos dependencias básicas
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd mysqli pdo pdo_mysql

# Instalamos Composer profesionalmente
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Habilitamos rewrite
RUN a2enmod rewrite

# Copiamos TODO a la raíz del servidor
COPY . /var/www/html/

# Instalamos las librerías automáticamente (esto sustituye al wget roto)
RUN composer install --no-dev --optimize-autoloader

# Permisos para Apache
RUN chown -R www-data:www-data /var/www/html/ \
    && chmod -R 755 /var/www/html/

EXPOSE 80