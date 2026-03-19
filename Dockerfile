# PHP + Nginx Dockerfile for Laravel
FROM php:8.2-fpm

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libpq-dev \
    unzip \
    git \
    curl \
    nginx \
    dos2unix \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd zip pdo pdo_pgsql pdo_mysql

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy project files
COPY . .

# Fix Windows line endings for all shell scripts
RUN find . -name "*.sh" -exec dos2unix {} \;

# Create .env file if not present
RUN if [ ! -f .env ]; then echo "APP_KEY=" > .env; fi

# Install PHP dependencies (let post-install scripts run for proper auto-discovery)
RUN composer install --no-dev --optimize-autoloader

# Install Node.js and build assets
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && npm install \
    && npm run build

# Configure Nginx
COPY conf/nginx.conf /etc/nginx/sites-available/default
RUN ln -sf /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default
# Remove default nginx config that conflicts
RUN rm -f /etc/nginx/sites-enabled/default.bak 2>/dev/null || true

# Set permissions
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port
EXPOSE 80

# Start script
COPY scripts/start-app.sh /usr/local/bin/start-app.sh
RUN dos2unix /usr/local/bin/start-app.sh
RUN chmod +x /usr/local/bin/start-app.sh

CMD ["/usr/local/bin/start-app.sh"]
