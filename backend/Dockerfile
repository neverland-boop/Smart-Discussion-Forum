FROM php:8.2-fpm

# Install system dependencies and PHP extensions for Laravel & PostgreSQL
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

# Expose Render's dynamic port
EXPOSE 10000

# Startup script: Copy .env.example to .env if .env doesn't exist, clear cache, run migrations, and start server
CMD ["sh", "-c", "if [ ! -f .env ]; then cp .env.example .env; fi && php artisan key:generate --force && php artisan config:clear && php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=10000"]