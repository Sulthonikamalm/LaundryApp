#!/bin/bash

# Post-Deployment Script for Koyeb
# Run this after first deployment or after major updates

echo "🚀 Starting Post-Deployment Tasks..."

# 1. Run Migrations
echo "📦 Running database migrations..."
php artisan migrate --force

# 2. Create Storage Link
echo "🔗 Creating storage symlink..."
php artisan storage:link

# 3. Clear All Caches
echo "🧹 Clearing caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# 4. Cache Config & Routes (Production Optimization)
echo "⚡ Caching config and routes..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 5. Test URL Generation
echo "🔍 Testing URL generation..."
php artisan test:url-generation

echo "✅ Post-Deployment Tasks Completed!"
echo ""
echo "Next Steps:"
echo "1. Test WhatsApp integration by creating a transaction"
echo "2. Verify tracking links work correctly"
echo "3. Check Koyeb logs for any errors"
