@echo off
REM ========================================
REM LaundryApp - Performance Optimization Script
REM ========================================
REM DeepPerformance: Clear all caches and rebuild
REM ========================================

echo.
echo ========================================
echo  LaundryApp Performance Optimization
echo ========================================
echo.

echo [1/8] Clearing configuration cache...
php artisan config:clear
if %errorlevel% neq 0 goto error

echo [2/8] Clearing application cache...
php artisan cache:clear
if %errorlevel% neq 0 goto error

echo [3/8] Clearing view cache...
php artisan view:clear
if %errorlevel% neq 0 goto error

echo [4/8] Clearing route cache...
php artisan route:clear
if %errorlevel% neq 0 goto error

echo [5/8] CRITICAL: Caching Filament icons (Windows fix)...
php artisan icons:cache
if %errorlevel% neq 0 goto error

echo [6/8] Rebuilding configuration cache...
php artisan config:cache
if %errorlevel% neq 0 goto error

echo [7/8] Optimizing autoloader...
composer dump-autoload -o
if %errorlevel% neq 0 goto error

echo [8/8] Clearing OPcache (if enabled)...
php -r "if (function_exists('opcache_reset')) { opcache_reset(); echo 'OPcache cleared'; } else { echo 'OPcache not enabled'; }"
echo.

echo.
echo ========================================
echo  SUCCESS! Optimization Complete
echo ========================================
echo.
echo CRITICAL FIXES APPLIED:
echo - Icons cache (Windows performance fix)
echo - Optimized autoloader
echo - All caches cleared
echo.
echo NEXT STEPS:
echo 1. Restart Apache di XAMPP Control Panel
echo 2. Refresh browser (Ctrl+F5)
echo 3. Check performance improvement
echo.
pause
exit /b 0

:error
echo.
echo ========================================
echo  ERROR! Optimization Failed
echo ========================================
echo.
echo Please check the error message above.
echo.
pause
exit /b 1
