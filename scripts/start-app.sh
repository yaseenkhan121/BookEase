#!/bin/bash

echo "==> SCRIPT VERSION: 7.0 (The Final Polish: STRIP \r)"
echo "==> Preparing application..."

# 0. Strip ALL carriage returns from the entire environment to be safe
# This handles the "Invalid URI" and "database does not exist" errors caused by Windows line endings
echo "==> Sanitizing environment variables..."
export DATABASE_URL=$(echo "$DATABASE_URL" | tr -d '\r')
export APP_URL=$(echo "$APP_URL" | tr -d '\r')

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

# 4. Use PHP to parse DATABASE_URL (Now sanitized)
if [ -n "$DATABASE_URL" ]; then
    echo "==> Parsing DATABASE_URL with PHP..."
    
    DB_HOST=$(php -r "echo parse_url(getenv('DATABASE_URL'), PHP_URL_HOST);")
    DB_PORT=$(php -r "\$p = parse_url(getenv('DATABASE_URL'), PHP_URL_PORT); echo \$p ? \$p : '5432';")
    DB_DATABASE=$(php -r "echo ltrim(parse_url(getenv('DATABASE_URL'), PHP_URL_PATH), '/');")
    DB_USERNAME=$(php -r "echo parse_url(getenv('DATABASE_URL'), PHP_URL_USER);")
    DB_PASSWORD=$(php -r "echo parse_url(getenv('DATABASE_URL'), PHP_URL_PASS);")

    # Force write to .env (Stripped again just in case)
    {
        echo "DB_CONNECTION=pgsql"
        echo "DB_HOST=$DB_HOST"
        echo "DB_PORT=$DB_PORT"
        echo "DB_DATABASE=$DB_DATABASE"
        echo "DB_USERNAME=$DB_USERNAME"
        echo "DB_PASSWORD=$DB_PASSWORD"
        echo "DATABASE_URL=$DATABASE_URL"
    } | tr -d '\r' >> /var/www/html/.env

    # Export for current process
    export DB_CONNECTION=pgsql
    export DB_HOST=$DB_HOST
    export DB_PORT=$DB_PORT
    export DB_DATABASE=$DB_DATABASE
    export DB_USERNAME=$DB_USERNAME
    export DB_PASSWORD=$DB_PASSWORD

    echo "==> Connection Configured: Host=$DB_HOST, Port=$DB_PORT, Database=$DB_DATABASE"
fi

# Print .env for debugging (masking sensitive info)
echo "==> Final .env configuration (sanitized):"
cat /var/www/html/.env | sed -E 's/(PASSWORD|SECRET|KEY)=.*$/\1=********/'

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
