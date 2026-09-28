# 1. Gunakan image PHP 8.2 dengan web server Apache bawaan
FROM php:8.2-apache-bullseye

# 2. Instal library sistem yang dibutuhkan Linux untuk PDF, Excel, dan Zip
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    wkhtmltopdf \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 3. Konfigurasi dan Instal ekstensi PHP (GD untuk Excel/PDF, PDO untuk Database, dll)
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# 4. Instal Composer (Package Manager PHP)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 5. Set lokasi kerja di dalam container
WORKDIR /var/www/html

# 6. Copy seluruh kode aplikasi dari GitHub ke dalam container
COPY . /var/www/html

# 7. Instal dependensi PHP (Vendor) dari composer.json
RUN composer install --optimize-autoloader --no-dev

# 8. Berikan hak akses (permission) khusus untuk folder Laravel
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# 9. Ubah DocumentRoot Apache agar mengarah ke folder /public milik Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 10. Aktifkan mod_rewrite Apache (wajib untuk routing Laravel)
RUN a2enmod rewrite

# 11. Buka port 80 untuk web server
EXPOSE 80
