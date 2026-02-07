@echo off
REM ========================================
REM LaundryApp - Performance Check Script
REM ========================================
REM DeepPerformance: Verify optimization settings
REM ========================================

echo.
echo ========================================
echo  LaundryApp Performance Check
echo ========================================
echo.

echo [1/7] Checking PHP version...
php -v | findstr "PHP"
echo.

echo [2/7] Checking required extensions...
php -m | findstr /C:"pdo_mysql" /C:"gd" /C:"mbstring" /C:"openssl"
echo.

echo [3/7] Checking .env configuration...
echo SESSION_DRIVER:
findstr "SESSION_DRIVER" .env
echo CACHE_DRIVER:
findstr "CACHE_DRIVER" .env
echo.

echo [4/7] Checking database connection...
php artisan tinker --execute="echo 'DB Connection: ' . (DB::connection()->getPdo() ? 'OK' : 'FAILED') . PHP_EOL;"
echo.

echo [5/7] Checking cache status...
php artisan config:show cache.default
echo.

echo [6/7] CRITICAL: Checking OPcache status...
php -r "echo 'OPcache: ' . (function_exists('opcache_get_status') ? 'ENABLED' : 'DISABLED') . PHP_EOL;"
echo.

echo [7/7] CRITICAL: Checking Icons cache...
if exist "bootstrap\cache\blade-icons.php" (
    echo Icons Cache: ENABLED
) else (
    echo Icons Cache: DISABLED - Run: php artisan icons:cache
)
echo.

echo ========================================
echo  Performance Check Complete
echo ========================================
echo.
echo RECOMMENDATIONS:
echo - SESSION_DRIVER should be: cookie
echo - CACHE_DRIVER should be: array or redis
echo - DB Connection should be: OK
echo - OPcache should be: ENABLED (CRITICAL!)
echo - Icons Cache should be: ENABLED (CRITICAL!)
echo.
echo If OPcache is DISABLED, run: enable-opcache.bat
echo If Icons Cache is DISABLED, run: php artisan icons:cache
echo.
pause
