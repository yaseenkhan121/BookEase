#!/bin/bash

echo "==> Preparing application..."

# 1. Clean up ANY existing caches first (before running any php commands)
echo "==> Cleaning stale bootstrap caches..."
rm -f /var/www/html/bootstrap/cache/*.php
rm -rf /var/www/html/storage/framework/cache/data/*
rm -f /var/www/html/storage/framework/views/*.php
rm -f /var/www/html/storage/framework/sessions/*

# 2. ALWAYS create .env file from environment variables (overwrite any existing)
echo "==> Creating .env file from environment variables..."
# Filter out current DB_ variables to avoid duplicates when we parse DATABASE_URL
env | grep -E '^(APP_|GOOGLE_|PUSHER_|MAIL_|BROADCAST_|QUEUE_|CACHE_|SESSION_|LOG_|FILESYSTEM_|VITE_)' | while IFS='=' read -r key value; do
    echo "$key=$value"
done > /var/www/html/.env

# 3. Parse DATABASE_URL if present using PHP (most reliable way)
if [ -n "$DATABASE_URL" ]; then
    echo "==> Parsing DATABASE_URL with PHP..."
    
    DB_HOST=$(php -r "echo parse_url(getenv('DATABASE_URL'), PHP_URL_HOST);")
    DB_PORT=$(php -r "\$p = parse_url(getenv('DATABASE_URL'), PHP_URL_PORT); echo \$p ? \$p : '5432';")
    DB_DATABASE=$(php -r "echo ltrim(parse_url(getenv('DATABASE_URL'), PHP_URL_PATH), '/');")
    DB_USERNAME=$(php -r "echo parse_url(getenv('DATABASE_URL'), PHP_URL_USER);")
    DB_PASSWORD=$(php -r "echo parse_url(getenv('DATABASE_URL'), PHP_URL_PASS);")

    # Write DB variables to .env
    echo "DB_CONNECTION=pgsql" >> /var/www/html/.env
    echo "DB_HOST=$DB_HOST" >> /var/www/html/.env
    echo "DB_PORT=$DB_PORT" >> /var/www/html/.env
    echo "DB_DATABASE=$DB_DATABASE" >> /var/www/html/.env
    echo "DB_USERNAME=$DB_USERNAME" >> /var/www/html/.env
    echo "DB_PASSWORD=$DB_PASSWORD" >> /var/www/html/.env
    echo "DATABASE_URL=$DATABASE_URL" >> /var/www/html/.env

    echo "==> Parsed Connection: Host=$DB_HOST Port=$DB_PORT Database=$DB_DATABASE User=$DB_USERNAME"
fi

# Print .env for debugging (masking password)
echo "==> .env file contents (sanitized):"
cat /var/www/html/.env | sed 's/PASSWORD=.*$/PASSWORD=********/'

# 4. Clear any old cached config via artisan (now safe to run)
echo "==> Running Artisan maintenance tasks..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# 5. Set storage link
echo "==> Creating storage link..."
php artisan storage:link --force 2>/dev/null || true

# 6. Run migrations
echo "==> Running migrations..."
php artisan migrate --force 2>&1 || echo "Migration warning, continuing..."

# 7. Fix permissions after migration
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 8. Start PHP-FPM in background
echo "==> Starting PHP-FPM..."
php-fpm -D

# 9. Start Nginx in foreground
echo "==> Starting Nginx..."
nginx -g "daemon off;"
