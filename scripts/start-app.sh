#!/bin/bash

echo "==> Preparing application..."

# Create .env file from environment variables if it doesn't exist
if [ ! -f /var/www/html/.env ]; then
    echo "==> Creating .env file from environment variables..."
    env | grep -E '^(APP_|DB_|DATABASE_|GOOGLE_|PUSHER_|MAIL_|BROADCAST_|QUEUE_|CACHE_|SESSION_|LOG_|FILESYSTEM_|VITE_)' | while IFS='=' read -r key value; do
        echo "$key=$value"
    done > /var/www/html/.env
fi

# Parse DATABASE_URL if present and set individual DB_ variables
if [ -n "$DATABASE_URL" ]; then
    echo "==> Parsing DATABASE_URL..."
    # Normalize: replace postgresql:// with postgres:// for consistent parsing
    NORMALIZED_URL=$(echo "$DATABASE_URL" | sed 's|^postgresql://|postgres://|')

    export DB_CONNECTION=pgsql
    export DB_HOST=$(echo "$NORMALIZED_URL" | sed -e 's|^postgres://[^@]*@||' -e 's|:[0-9]*/.*||' -e 's|/.*||')
    export DB_PORT=$(echo "$NORMALIZED_URL" | sed -e 's|^.*@[^:]*:||' -e 's|/.*||')
    export DB_DATABASE=$(echo "$NORMALIZED_URL" | sed -e 's|^.*/||')
    export DB_USERNAME=$(echo "$NORMALIZED_URL" | sed -e 's|^postgres://||' -e 's|:.*||')
    export DB_PASSWORD=$(echo "$NORMALIZED_URL" | sed -e 's|^postgres://[^:]*:||' -e 's|@.*||')

    # Also write these to .env
    echo "DB_CONNECTION=pgsql" >> /var/www/html/.env
    echo "DB_HOST=$DB_HOST" >> /var/www/html/.env
    echo "DB_PORT=$DB_PORT" >> /var/www/html/.env
    echo "DB_DATABASE=$DB_DATABASE" >> /var/www/html/.env
    echo "DB_USERNAME=$DB_USERNAME" >> /var/www/html/.env
    echo "DB_PASSWORD=$DB_PASSWORD" >> /var/www/html/.env
    echo "DATABASE_URL=$DATABASE_URL" >> /var/www/html/.env

    echo "==> DB_HOST=$DB_HOST DB_PORT=$DB_PORT DB_DATABASE=$DB_DATABASE DB_USERNAME=$DB_USERNAME"
fi

# Set storage link
echo "==> Creating storage link..."
php artisan storage:link --force 2>/dev/null || true

# Cache config
echo "==> Caching configuration..."
php artisan config:cache 2>/dev/null || true
php artisan route:cache 2>/dev/null || true
php artisan view:cache 2>/dev/null || true

# Run migrations
echo "==> Running migrations..."
php artisan migrate --force 2>&1 || echo "Migration failed, continuing..."

# Fix permissions after migration
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Start PHP-FPM in background
echo "==> Starting PHP-FPM..."
php-fpm -D

# Start Nginx in foreground
echo "==> Starting Nginx..."
nginx -g "daemon off;"
