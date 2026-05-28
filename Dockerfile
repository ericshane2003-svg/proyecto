FROM php:8.2-apache

# Instalación de dependencias
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libfreetype6-dev zip unzip git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd mysqli pdo pdo_mysql

# Habilitar Apache Rewrite para que las rutas funcionen
RUN a2enmod rewrite

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# COPIAR TODO EL CONTENIDO A LA RAÍZ DEL SERVIDOR
# Si tu proyecto en GitHub tiene las carpetas afuera, esto las moverá a /var/www/html/
COPY . /var/www/html/

# Instalar Dompdf
RUN composer install --no-dev --optimize-autoloader

# Permisos
RUN chown -R www-data:www-data /var/www/html/ \
    && chmod -R 755 /var/www/html/

# Exponer el puerto
EXPOSE 80
