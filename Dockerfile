# Gunakan PHP 8.2 dengan Apache
FROM php:8.2-apache

# Install ekstensi yang dibutuhkan Laravel
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl

# Install Driver Database & Lainnya
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Aktifkan Modul Rewrite Apache (Wajib untuk Laravel)
RUN a2enmod rewrite

# Install Composer (Manajer Paket PHP)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set Folder Kerja
WORKDIR /var/www/html

# Copy Semua Kode ke Server
COPY . .

# Buat folder cache yang diperlukan Laravel sebelum composer install
RUN mkdir -p storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

# Install Library PHP
RUN composer install --no-dev --optimize-autoloader

# Atur Izin Folder Storage (PENTING AGAR TIDAK ERROR 500)
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Setting Apache agar membaca folder 'public'
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# Buka Port 80
EXPOSE 80
