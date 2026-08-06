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

# Instalar dependencias de PHP con Composer
RUN composer install --no-dev --optimize-autoloader

# Dar permisos a las carpetas de almacenamiento y caché
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Cambiar la raíz del documento de Apache a la carpeta public de Laravel
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf
RUN sed -i 's!/var/www/!/var/www/html/public!g' /etc/apache2/apache2.conf

# Habilitar mod_rewrite para las rutas amigables
RUN a2enmod rewrite