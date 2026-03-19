#!/bin/bash

echo "==> Preparing application..."

# ALWAYS create .env file from environment variables (overwrite any existing)
echo "==> Creating .env file from environment variables..."
env | grep -E '^(APP_|DB_|DATABASE_|GOOGLE_|PUSHER_|MAIL_|BROADCAST_|QUEUE_|CACHE_|SESSION_|LOG_|FILESYSTEM_|VITE_)' | while IFS='=' read -r key value; do
    echo "$key=$value"
done > /var/www/html/.env

# Parse DATABASE_URL if present and set individual DB_ variables
if [ -n "$DATABASE_URL" ]; then
    echo "==> Parsing DATABASE_URL..."
    # Normalize: replace postgresql:// with postgres:// for consistent parsing
    NORMALIZED_URL=$(echo "$DATABASE_URL" | sed 's|^postgresql://|postgres://|')

    DB_HOST=$(echo "$NORMALIZED_URL" | sed -e 's|^postgres://[^@]*@||' -e 's|:[0-9]*/.*||' -e 's|/.*||')
    DB_PORT=$(echo "$NORMALIZED_URL" | sed -e 's|^.*@[^:]*:||' -e 's|/.*||')
    DB_DATABASE=$(echo "$NORMALIZED_URL" | sed -e 's|^.*/||')
    DB_USERNAME=$(echo "$NORMALIZED_URL" | sed -e 's|^postgres://||' -e 's|:.*||')
    DB_PASSWORD=$(echo "$NORMALIZED_URL" | sed -e 's|^postgres://[^:]*:||' -e 's|@.*||')

    # Write DB variables to .env
    echo "DB_CONNECTION=pgsql" >> /var/www/html/.env
    echo "DB_HOST=$DB_HOST" >> /var/www/html/.env
    echo "DB_PORT=$DB_PORT" >> /var/www/html/.env
    echo "DB_DATABASE=$DB_DATABASE" >> /var/www/html/.env
    echo "DB_USERNAME=$DB_USERNAME" >> /var/www/html/.env
    echo "DB_PASSWORD=$DB_PASSWORD" >> /var/www/html/.env

    echo "==> DB_HOST=$DB_HOST DB_PORT=$DB_PORT DB_DATABASE=$DB_DATABASE DB_USERNAME=$DB_USERNAME"
fi

# Print .env for debugging
echo "==> .env file contents:"
cat /var/www/html/.env

# Clear any old cached config
echo "==> Clearing cached config..."
php artisan config:clear 2>/dev/null || true
php artisan cache:clear 2>/dev/null || true
php artisan route:clear 2>/dev/null || true
php artisan view:clear 2>/dev/null || true

# Set storage link
echo "==> Creating storage link..."
php artisan storage:link --force 2>/dev/null || true

# Run migrations
echo "==> Running migrations..."
php artisan migrate --force 2>&1 || echo "Migration warning, continuing..."

# Fix permissions after migration
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Start PHP-FPM in background
echo "==> Starting PHP-FPM..."
php-fpm -D

# Start Nginx in foreground
echo "==> Starting Nginx..."
nginx -g "daemon off;"
