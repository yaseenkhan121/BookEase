#!/bin/bash

echo "==> SCRIPT VERSION: 6.0 (Nuclear Option: Unset & PHP Parsing)"
echo "==> Preparing application..."

# 0. Unset ANY existing DB variables to prevent contamination from Render's auto-injection
echo "==> Clearing existing DB environment variables..."
unset DB_CONNECTION DB_HOST DB_PORT DB_DATABASE DB_USERNAME DB_PASSWORD

# 1. Clean up ANY existing caches first
echo "==> Cleaning stale bootstrap caches..."
rm -f /var/www/html/bootstrap/cache/*.php
rm -rf /var/www/html/storage/framework/cache/data/*
rm -f /var/www/html/storage/framework/views/*.php
rm -f /var/www/html/storage/framework/sessions/*

# 2. CREATE FRESH .env file (Overwrite everything)
echo "==> Creating fresh .env file..."
# Grab basic Laravel/App env vars but EXCLUDE any DB_ ones from the initial dump
env | grep -v '^DB_' | grep -E '^(APP_|GOOGLE_|PUSHER_|MAIL_|BROADCAST_|QUEUE_|CACHE_|SESSION_|LOG_|FILESYSTEM_|VITE_)' > /var/www/html/.env

# 3. Use PHP to parse DATABASE_URL (Most reliable)
if [ -n "$DATABASE_URL" ]; then
    echo "==> Parsing DATABASE_URL with PHP..."
    
    # Export it so getenv() inside php -r works
    export DATABASE_URL="$DATABASE_URL"

    DB_HOST=$(php -r "echo parse_url(getenv('DATABASE_URL'), PHP_URL_HOST);")
    DB_PORT=$(php -r "\$p = parse_url(getenv('DATABASE_URL'), PHP_URL_PORT); echo \$p ? \$p : '5432';")
    DB_DATABASE=$(php -r "echo ltrim(parse_url(getenv('DATABASE_URL'), PHP_URL_PATH), '/');")
    DB_USERNAME=$(php -r "echo parse_url(getenv('DATABASE_URL'), PHP_URL_USER);")
    DB_PASSWORD=$(php -r "echo parse_url(getenv('DATABASE_URL'), PHP_URL_PASS);")

    # Force write to .env (Append to our clean file)
    {
        echo "DB_CONNECTION=pgsql"
        echo "DB_HOST=$DB_HOST"
        echo "DB_PORT=$DB_PORT"
        echo "DB_DATABASE=$DB_DATABASE"
        echo "DB_USERNAME=$DB_USERNAME"
        echo "DB_PASSWORD=$DB_PASSWORD"
        echo "DATABASE_URL=$DATABASE_URL"
    } >> /var/www/html/.env

    # Export them for the current process (artisan commands)
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

# 4. Clear/Rebuild Artisan cache
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
php artisan migrate --force 2>&1 || echo "Migration warning: check database logs."

# 7. Fix permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 8. Start Services
echo "==> Starting PHP-FPM..."
php-fpm -D

echo "==> Starting Nginx..."
nginx -g "daemon off;"
