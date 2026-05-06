# Deployment Guide - Laravel Inventory System

## Railway Deployment

### Prerequisites
1. Railway account with MySQL database plugin
2. GitHub repository connected to Railway

### Environment Variables (Railway)
Set these in Railway dashboard:

```bash
# App Configuration
APP_NAME="Laravel Inventory"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-app.up.railway.app

# Database (Railway MySQL Plugin auto-provides these)
DB_CONNECTION=mysql
DB_HOST=${{MYSQLHOST}}
DB_PORT=${{MYSQLPORT}}
DB_DATABASE=${{MYSQLDATABASE}}
DB_USERNAME=${{MYSQLUSER}}
DB_PASSWORD=${{MYSQLPASSWORD}}

# Logging
LOG_CHANNEL=errorlog
LOG_LEVEL=error

# Session & Cache
SESSION_DRIVER=file
CACHE_STORE=file
FILESYSTEM_DISK=public
```

### Deploy Steps
1. Connect GitHub repo to Railway
2. Add MySQL database plugin
3. Set environment variables above
4. Railway will auto-detect Dockerfile and deploy

---

## Render Deployment

### Using render.yaml (Recommended)
The `render.yaml` file is already configured. Simply:

1. Connect your GitHub repo to Render
2. Render will auto-detect `render.yaml`
3. Click "Apply" to create both web service and MySQL database
4. Render auto-generates APP_KEY and MYSQL_PASSWORD

### Manual Render Setup
1. Create new **Web Service** (Docker environment)
2. Create new **Private MySQL Service**
3. Set environment variables from Render MySQL internal connection URL

---

## Common Issues Fixed

### ✅ Apache MPM Conflict
- **Fixed**: Removed all mpm_event modules before enabling mpm_prefork
- **Solution**: Lines 10-13 in Dockerfile explicitly remove conflicting MPMs

### ✅ Database Connection Error
- **Fixed**: Added database readiness check before migrations
- **Solution**: Startup script waits for DB connection (lines 28-39 in Dockerfile)
- **Also Fixed**: Changed default DB connection from sqlite to mysql in `config/database.php`

### ✅ Node.js Version Too Old
- **Fixed**: Using Node.js 20 (Vite 7 compatible)
- **Solution**: Line 17 in Dockerfile installs Node 20.x

### ✅ Missing libcurl4-openssl-dev
- **Fixed**: Added to system dependencies
- **Solution**: Line 7 in Dockerfile

---

## Post-Deployment Checklist

1. ✅ Verify APP_KEY is generated
2. ✅ Check database migrations ran successfully
3. ✅ Test file uploads (storage/app/public linked)
4. ✅ Verify environment is set to "production"
5. ✅ Check logs are going to errorlog/stderr (not files)

---

## Local Development

```bash
# Install dependencies
composer install
npm install

# Copy environment file
cp .env.example .env

# Generate key
php artisan key:generate

# Run migrations
php artisan migrate

# Build assets
npm run build

# Serve (development)
php artisan serve
npm run dev
```

---

## Support

- Railway Docs: https://docs.railway.app
- Render Docs: https://render.com/docs
- Laravel Deployment: https://laravel.com/docs/deployment
