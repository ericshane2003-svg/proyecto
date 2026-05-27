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

# Instalamos Composer (el gestor profesional de PHP)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Habilitamos rewrite
RUN a2enmod rewrite

# Copiamos todo el código
COPY . /var/www/html/

# Instalamos dompdf usando composer (esto es 100% confiable)
RUN composer install --no-dev --optimize-autoloader

# Permisos
RUN chown -R www-data:www-data /var/www/html/ \
    && chmod -R 755 /var/www/html/

EXPOSE 80
