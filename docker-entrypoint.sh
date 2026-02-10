#!/bin/bash
set -e

echo "🚀 Starting Laravel Application..."

# Wait for database to be ready
echo "⏳ Waiting for database connection..."
php artisan db:show || true

# Run migrations
echo "📦 Running database migrations..."
php artisan migrate --force || echo "⚠️ Migration failed, continuing..."

# Create storage link if not exists
echo "🔗 Creating storage symlink..."
php artisan storage:link || echo "⚠️ Storage link already exists"

# Clear and cache config
echo "⚡ Optimizing application..."
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Application ready!"

# Start Apache
exec apache2-foreground
