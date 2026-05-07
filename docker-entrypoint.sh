#!/bin/sh
set -e

echo "Starting Laravel deployment sequence..."

# 0. Ensure env file exists and app key is generated
if [ ! -f .env ]; then
  echo "Creating .env from .env.example..."
  cp .env.example .env
fi

if [ -z "$APP_KEY" ]; then
  echo "Generating application key..."
  php artisan key:generate --force
fi

# 1. Create the symbolic link for storage/app/public to public/storage
echo "Linking storage..."
php artisan storage:link || true

# 2. Run database migrations
# Using --force to bypass the confirmation prompt in production
echo "Running migrations..."
php artisan migrate --force || true

# 3. Run database seeders (safe for production as they use firstOrCreate)
echo "Running seeders..."
php artisan db:seed --force || true

# 4. Cache configurations, routes, and views for optimal performance
echo "Caching configurations..."
php artisan config:cache

echo "Caching routes..."
php artisan route:cache

echo "Caching views..."
php artisan view:cache

echo "Deployment sequence complete. Starting Apache..."

# Execute the container's main process (Apache)
exec "$@"
