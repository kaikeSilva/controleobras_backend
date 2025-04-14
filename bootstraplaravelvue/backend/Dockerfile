FROM php:8.2-fpm

# Set working directory
WORKDIR /var/www

# Install dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    libpq-dev

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create system user to run Composer and Artisan Commands
RUN useradd -G www-data,root -u 1000 -d /home/www www
RUN mkdir -p /home/www/.composer && \
    chown -R www:www-data /home/www

# Copy application code
COPY . /var/www

# Set proper permissions for Laravel
RUN mkdir -p /var/www/storage /var/www/bootstrap/cache
RUN chown -R www:www-data /var/www
RUN chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Install dependencies with Composer as root user
RUN cd /var/www && composer install --no-interaction --optimize-autoloader

# Set proper ownership
RUN chown -R www:www-data /var/www

# Switch to non-root user
USER www

# Expose port 8000 for Laravel's built-in server
EXPOSE 8000

# Start Laravel server
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
