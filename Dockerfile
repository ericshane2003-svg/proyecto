# Usamos la imagen oficial de PHP con Apache
FROM php:8.2-apache

# Instalamos dependencias, wget, unzip y las librerías gráficas para Dompdf
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    wget \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd mysqli pdo pdo_mysql

# Habilitamos el módulo rewrite de Apache
RUN a2enmod rewrite

# Copiamos TU código (Vista, Modelo, Controlador) al servidor
COPY . /var/www/html/

# ¡EL TRUCO MAESTRO CORREGIDO! Usamos el link oficial de la última versión estable (3.1.5)
RUN wget https://github.com/dompdf/dompdf/releases/download/v3.1.5/dompdf-3.1.5.zip \
    && unzip dompdf-3.1.5.zip -d /var/www/html/ \
    && rm dompdf-3.1.5.zip

# Le damos permisos a Apache para que no haya errores al generar los PDF
RUN chown -R www-data:www-data /var/www/html/
RUN chmod -R 755 /var/www/html/

# Exponemos el puerto 80
EXPOSE 80
