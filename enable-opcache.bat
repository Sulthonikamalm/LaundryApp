@echo off
REM ========================================
REM LaundryApp - Enable OPcache Script
REM ========================================
REM DeepPerformance: Enable OPcache for 30-50% faster execution
REM ========================================

echo.
echo ========================================
echo  Enable OPcache for PHP
echo ========================================
echo.

echo [1/3] Checking current OPcache status...
php -r "echo 'OPcache Status: ' . (function_exists('opcache_get_status') ? 'ENABLED' : 'DISABLED') . PHP_EOL;"
echo.

php -r "if (function_exists('opcache_get_status')) { echo 'OPcache is already enabled! No action needed.'; exit(0); }"
if %errorlevel% equ 0 (
    echo.
    echo ========================================
    echo  OPcache Already Enabled!
    echo ========================================
    echo.
    pause
    exit /b 0
)

echo [2/3] OPcache is DISABLED. Manual action required.
echo.
echo ========================================
echo  MANUAL STEPS TO ENABLE OPCACHE
echo ========================================
echo.
echo 1. Open file: C:\xampp\php\php.ini
echo.
echo 2. Find section [opcache] (around line 1800-1900)
echo.
echo 3. Uncomment and set these values:
echo.
echo    zend_extension=opcache
echo    opcache.enable=1
echo    opcache.enable_cli=1
echo    opcache.memory_consumption=128
echo    opcache.interned_strings_buffer=8
echo    opcache.max_accelerated_files=10000
echo    opcache.revalidate_freq=2
echo    opcache.fast_shutdown=1
echo.
echo 4. Save file
echo.
echo 5. Restart Apache in XAMPP Control Panel
echo.
echo 6. Run this script again to verify
echo.
echo ========================================
echo.

echo [3/3] Opening php.ini file for you...
timeout /t 2 >nul
start notepad "C:\xampp\php\php.ini"

echo.
echo File opened in Notepad.
echo Follow the steps above, then restart Apache.
echo.
pause
