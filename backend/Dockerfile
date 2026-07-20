FROM php:8.2-fpm

# Install system dependencies and PHP extensions for Laravel & PostgreSQL/MySQL
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nginx \
    libpq-dev

RUN docker-php-ext-install pdo pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd

# Get Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

# Install dependencies (production optimized)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Set up permissions for Laravel storage and cache
RUN chmod -R 775 storage bootstrap/cache

# Copy Nginx configuration template
COPY ./docker/nginx.conf /etc/nginx/sites-available/default

# Expose Render's dynamic port via an entrypoint script
EXPOSE 10000

CMD ["sh", "-c", "php artisan config:cache && php artisan route:cache && php artisan migrate --force && sed -i 's/80/10000/g' /etc/nginx/sites-available/default && service nginx start && php-fpm"]