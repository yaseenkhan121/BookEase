#!/bin/bash

echo "==> SCRIPT VERSION: 9.0 (Regex Character Stripping)"
echo "==> Preparing application..."

# 0. Strip ALL hidden characters from the source DATABASE_URL
echo "==> Sanitizing source environment variables..."
DATABASE_URL=$(echo "$DATABASE_URL" | tr -d '\r\n\t' | xargs)

# 1. Clear ANY existing DB variables
unset DB_CONNECTION DB_HOST DB_PORT DB_DATABASE DB_USERNAME DB_PASSWORD

# 2. Clean up caches
echo "==> Cleaning stale bootstrap caches..."
rm -rf /var/www/html/bootstrap/cache/*.php
rm -rf /var/www/html/storage/framework/cache/data/*
rm -f /var/www/html/storage/framework/views/*.php
rm -f /var/www/html/storage/framework/sessions/*

# 3. CREATE FRESH .env file
echo "==> Creating fresh .env file..."
env | grep -v '^DB_' | grep -E '^(APP_|GOOGLE_|PUSHER_|MAIL_|BROADCAST_|QUEUE_|CACHE_|SESSION_|LOG_|FILESYSTEM_|VITE_)' | tr -d '\r' > /var/www/html/.env

# 4. Use PHP with REGEX to parse DATABASE_URL components (The nuclear option)
if [ -n "$DATABASE_URL" ]; then
    echo "==> Parsing DATABASE_URL with PHP and Regex..."
    
    export DB_HOST=$(php -r "\$h = parse_url(getenv('DATABASE_URL'), PHP_URL_HOST); echo preg_replace('/[^a-zA-Z0-9_.-]/', '', \$h);")
    export DB_PORT=$(php -r "\$p = parse_url(getenv('DATABASE_URL'), PHP_URL_PORT); \$p = \$p ? \$p : '5432'; echo preg_replace('/[^0-9]/', '', \$p);")
    export DB_DATABASE=$(php -r "\$path = parse_url(getenv('DATABASE_URL'), PHP_URL_PATH); \$db = ltrim(\$path, '/'); echo preg_replace('/[^a-zA-Z0-9_-]/', '', \$db);")
    export DB_USERNAME=$(php -r "\$u = parse_url(getenv('DATABASE_URL'), PHP_URL_USER); echo preg_replace('/[^a-zA-Z0-9_-]/', '', \$u);")
    export DB_PASSWORD=$(php -r "echo trim(parse_url(getenv('DATABASE_URL'), PHP_URL_PASS));")

    # Force write to .env (No extra spaces!)
    printf "DB_CONNECTION=pgsql\n" >> /var/www/html/.env
    printf "DB_HOST=%s\n" "$DB_HOST" >> /var/www/html/.env
    printf "DB_PORT=%s\n" "$DB_PORT" >> /var/www/html/.env
    printf "DB_DATABASE=%s\n" "$DB_DATABASE" >> /var/www/html/.env
    printf "DB_USERNAME=%s\n" "$DB_USERNAME" >> /var/www/html/.env
    printf "DB_PASSWORD=%s\n" "$DB_PASSWORD" >> /var/www/html/.env
    printf "DATABASE_URL=%s\n" "$DATABASE_URL" >> /var/www/html/.env

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

# 7. Run migrations and seeders
echo "==> Running migrations (FRESH REBUILD)..."
php artisan migrate:fresh --force 2>&1 || echo "Migration warning: check database logs."

echo "==> Running seeders..."
php artisan db:seed --force 2>&1 || echo "Seeding warning: check database logs."

# 8. Fix permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 9. Start Services
PORT="${PORT:-80}"
echo "==> Configuring Nginx to listen on port $PORT..."
sed -i "s/listen 80;/listen ${PORT};/g" /etc/nginx/sites-available/default

echo "==> Starting PHP-FPM in background..."
php-fpm &

echo "==> Starting Nginx in foreground..."
nginx -g "daemon off;"
