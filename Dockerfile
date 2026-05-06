FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    nginx \
    git curl zip unzip \
    libpng-dev libonig-dev libxml2-dev libzip-dev \
    libfreetype6-dev libjpeg62-turbo-dev libwebp-dev \
    libcurl4-openssl-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install pdo pdo_mysql mbstring xml bcmath gd zip opcache curl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Node.js 20
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy application files
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts
RUN npm install && npm run build

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# Remove default Nginx config and create Laravel-specific one
RUN rm -f /etc/nginx/sites-enabled/default \
    && rm -f /etc/nginx/sites-available/default

# Create Nginx configuration for Laravel
RUN printf 'server {\n\
    listen 8080 default_server;\n\
    listen [::]:8080 default_server;\n\
    \n\
    root /var/www/html/public;\n\
    index index.php index.html;\n\
    \n\
    server_name _;\n\
    \n\
    location / {\n\
        try_files $uri $uri/ /index.php?$query_string;\n\
    }\n\
    \n\
    location ~ \\.php$ {\n\
        fastcgi_pass 127.0.0.1:9000;\n\
        fastcgi_index index.php;\n\
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;\n\
        include fastcgi_params;\n\
    }\n\
    \n\
    location ~ /\\.(?!well-known).* {\n\
        deny all;\n\
    }\n\
}\n\
' > /etc/nginx/sites-available/laravel \
    && ln -s /etc/nginx/sites-available/laravel /etc/nginx/sites-enabled/laravel

# Create startup script
RUN printf '#!/bin/bash\n\
set -e\n\
mkdir -p /var/www/html/storage/logs /var/www/html/storage/framework/sessions /var/www/html/storage/framework/views /var/www/html/storage/framework/cache\n\
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache\n\
\n\
echo "Starting Laravel Inventory System..."\n\
\n\
# Generate APP_KEY if not set\n\
if [ -z "$APP_KEY" ]; then\n\
    echo "Generating APP_KEY..."\n\
    php artisan key:generate --force\n\
fi\n\
\n\
# Create storage link\n\
php artisan storage:link --force 2>/dev/null || true\n\
\n\
# Wait for database to be ready\n\
if [ -n "$DB_HOST" ] && [ -n "$DB_DATABASE" ]; then\n\
    echo "Waiting for database connection..."\n\
    for i in {1..30}; do\n\
        if php -r "new PDO(\"mysql:host=$DB_HOST;port=${DB_PORT:-3306};dbname=$DB_DATABASE\", \"$DB_USERNAME\", \"$DB_PASSWORD\");" 2>/dev/null; then\n\
            echo "Database connected!"\n\
            break\n\
        fi\n\
        echo "Attempt $i/30..."\n\
        sleep 2\n\
    done\n\
    \n\
    # Run migrations\n\
    echo "Running migrations..."\n\
    php artisan migrate --force || echo "Migrations failed or already up to date"\n\
    php artisan db:seed --force || echo "Seeding failed or already seeded"\n\
fi\n\
\n\
# Cache optimization\n\
php artisan config:cache || true\n\
php artisan route:cache || true\n\
php artisan view:cache || true\n\
\n\
# Update Nginx port if PORT env variable is set\n\
if [ -n "$PORT" ]; then\n\
    echo "Configuring Nginx to listen on port $PORT"\n\
    sed -i "s/listen 8080/listen $PORT/g" /etc/nginx/sites-available/laravel\n\
    sed -i "s/listen \\[::\\]:8080/listen [::]:$PORT/g" /etc/nginx/sites-available/laravel\n\
fi\n\
\n\
# Start PHP-FPM in background\n\
echo "Starting PHP-FPM..."\n\
php-fpm -D\n\
\n\
# Start Nginx in foreground\n\
echo "Starting Nginx..."\n\
exec nginx -g "daemon off;"\n\
' > /usr/local/bin/start.sh && chmod +x /usr/local/bin/start.sh

EXPOSE 8080

CMD ["/usr/local/bin/start.sh"]
