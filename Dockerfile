FROM php:8.2-apache

# Instalar dependencias del sistema y extensiones necesarias para Laravel
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Limpiar cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar extensiones de PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Copiar Composer desde su imagen oficial
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar directorio de trabajo
WORKDIR /var/www/html

# Copiar los archivos del proyecto
COPY . /var/www/html

# Dar permisos a las carpetas de almacenamiento y caché
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Cambiar la raíz del servidor Apache a la carpeta public de Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUsed SED -ri -s,-/var/www/html,-/var/www/html/public,,g /etc/apache2/sites-available/*.conf
RUN sed -ri -s,-/var/www/,-/var/www/html/public,,g /etc/apache2/apache2.conf

# Habilitar mod_rewrite de Apache para las rutas amigables de Laravel
RUN a2enmod rewrite