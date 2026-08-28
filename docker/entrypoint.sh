#!/bin/sh
set -e

# Default PORT to 80 if not set by Railway
export PORT=${PORT:-80}

echo "==> Configuring Nginx port to $PORT..."
envsubst '${PORT}' < /etc/nginx/nginx.conf.template > /etc/nginx/nginx.conf

# Remove vite hot file if it exists from local development
rm -f /var/www/html/public/hot

# Ensure storage directories exist and have proper permissions
mkdir -p /var/www/html/storage/framework/cache/data \
         /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/logs \
         /var/www/html/bootstrap/cache

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo "==> Creating storage link..."
php artisan storage:link --force || true

# Run database migrations if RUN_MIGRATIONS is set to true
if [ "$RUN_MIGRATIONS" = "true" ]; then
    echo "==> Running database migrations..."
    php artisan migrate --force
fi

# Cache configuration, routes, and views if in production
if [ "$APP_ENV" = "production" ]; then
    echo "==> Caching configuration, routes and views for production..."
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
fi

echo "==> Starting application via Supervisord..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
