#!/bin/bash

echo "==> SCRIPT VERSION: 8.0 (Trim everything with PHP)"
echo "==> Preparing application..."

# 0. Strip ALL carriage returns and whitespace from core variables
echo "==> Sanitizing environment variables..."
export DATABASE_URL=$(echo "$DATABASE_URL" | tr -d '\r' | xargs)
export APP_URL=$(echo "$APP_URL" | tr -d '\r' | xargs)

# 1. Unset ANY existing DB variables to prevent contamination
echo "==> Clearing existing DB environment variables..."
unset DB_CONNECTION DB_HOST DB_PORT DB_DATABASE DB_USERNAME DB_PASSWORD

# 2. Clean up caches
echo "==> Cleaning stale bootstrap caches..."
rm -f /var/www/html/bootstrap/cache/*.php
rm -rf /var/www/html/storage/framework/cache/data/*
rm -f /var/www/html/storage/framework/views/*.php
rm -f /var/www/html/storage/framework/sessions/*

# 3. CREATE FRESH .env file
echo "==> Creating fresh .env file..."
# Grab env vars, exclude DB ones, and STRIP \r from everything
env | grep -v '^DB_' | grep -E '^(APP_|GOOGLE_|PUSHER_|MAIL_|BROADCAST_|QUEUE_|CACHE_|SESSION_|LOG_|FILESYSTEM_|VITE_)' | tr -d '\r' > /var/www/html/.env

# 4. Use PHP to parse and TRIM DATABASE_URL components (Most reliable)
if [ -n "$DATABASE_URL" ]; then
    echo "==> Parsing DATABASE_URL with PHP..."
    
    DB_HOST=$(php -r "echo trim(parse_url(getenv('DATABASE_URL'), PHP_URL_HOST));")
    DB_PORT=$(php -r "\$p = parse_url(getenv('DATABASE_URL'), PHP_URL_PORT); echo \$p ? trim(\$p) : '5432';")
    # Path parsing: ltrim the leading slash then trim any trailing junk
    DB_DATABASE=$(php -r "\$path = parse_url(getenv('DATABASE_URL'), PHP_URL_PATH); echo trim(ltrim(\$path, '/'));")
    DB_USERNAME=$(php -r "echo trim(parse_url(getenv('DATABASE_URL'), PHP_URL_USER));")
    DB_PASSWORD=$(php -r "echo trim(parse_url(getenv('DATABASE_URL'), PHP_URL_PASS));")

    # Extra safety: strip any lingering non-printable chars from DB_DATABASE
    DB_DATABASE=$(echo "$DB_DATABASE" | tr -cd '[:print:]')

    # Force write to .env
    {
        echo "DB_CONNECTION=pgsql"
        echo "DB_HOST=$DB_HOST"
        echo "DB_PORT=$DB_PORT"
        echo "DB_DATABASE=$DB_DATABASE"
        echo "DB_USERNAME=$DB_USERNAME"
        echo "DB_PASSWORD=$DB_PASSWORD"
    } >> /var/www/html/.env

    # Export for current process
    export DB_CONNECTION=pgsql
    export DB_HOST=$DB_HOST
    export DB_PORT=$DB_PORT
    export DB_DATABASE=$DB_DATABASE
    export DB_USERNAME=$DB_USERNAME
    export DB_PASSWORD=$DB_PASSWORD

    echo "==> Connection Configured: Host=$DB_HOST, Port=$DB_PORT, Database=$DB_DATABASE (Length: ${#DB_DATABASE})"
fi

# 5. Artisan tasks
echo "==> Running Artisan maintenance tasks..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# 6. Set storage link
echo "==> Creating storage link..."
php artisan storage:link --force 2>/dev/null || true

# 7. Run migrations
echo "==> Running migrations..."
php artisan migrate --force 2>&1 || echo "Migration warning: check database logs."

# 8. Fix permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 9. Start Services
echo "==> Starting PHP-FPM..."
php-fpm -D

echo "==> Starting Nginx..."
nginx -g "daemon off;"
