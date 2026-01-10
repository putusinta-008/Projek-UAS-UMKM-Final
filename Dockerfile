# Gunakan image resmi PHP dengan ekstensi yang dibutuhkan Laravel
FROM php:8.2-fpm

# Set working directory
WORKDIR /var/www

# Install dependencies sistem
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libonig-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    curl \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Salin file composer dan install dependencies Laravel
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader

# Salin seluruh project ke container
COPY . .

# Generate Laravel key (opsional, bisa juga via env vars di Render)
RUN php artisan key:generate

# Expose port yang digunakan Laravel
EXPOSE 8000

# Jalankan Laravel ketika container start
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
