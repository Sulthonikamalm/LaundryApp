<?php
// Temporary debug file - DELETE after fixing!
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Debug Information</h1>";

// Test 1: PHP Version
echo "<h2>1. PHP Version</h2>";
echo "PHP Version: " . phpversion() . "<br>";

// Test 2: Extensions
echo "<h2>2. Required Extensions</h2>";
$required = ['pdo', 'pdo_mysql', 'mbstring', 'openssl', 'json', 'zip'];
foreach ($required as $ext) {
    $status = extension_loaded($ext) ? '✅' : '❌';
    echo "$status $ext<br>";
}

// Test 3: File Permissions
echo "<h2>3. File Permissions</h2>";
$paths = [
    '../storage/logs' => is_writable(__DIR__ . '/../storage/logs'),
    '../storage/framework' => is_writable(__DIR__ . '/../storage/framework'),
    '../bootstrap/cache' => is_writable(__DIR__ . '/../bootstrap/cache'),
];
foreach ($paths as $path => $writable) {
    $status = $writable ? '✅' : '❌';
    echo "$status $path<br>";
}

// Test 4: Environment Variables
echo "<h2>4. Environment Variables</h2>";
$env_vars = ['APP_ENV', 'APP_KEY', 'APP_DEBUG', 'DB_HOST', 'DB_DATABASE'];
foreach ($env_vars as $var) {
    $value = getenv($var) ?: 'NOT SET';
    if ($var === 'APP_KEY' && $value !== 'NOT SET') {
        $value = substr($value, 0, 20) . '...'; // Hide full key
    }
    echo "$var: $value<br>";
}

// Test 5: Database Connection
echo "<h2>5. Database Connection</h2>";
try {
    $host = getenv('DB_HOST');
    $port = getenv('DB_PORT') ?: 3306;
    $db = getenv('DB_DATABASE');
    $user = getenv('DB_USERNAME');
    $pass = getenv('DB_PASSWORD');
    
    if (!$host || !$db || !$user) {
        echo "❌ Database credentials not set<br>";
    } else {
        $dsn = "mysql:host=$host;port=$port;dbname=$db";
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
        ]);
        echo "✅ Database connection successful<br>";
        echo "Database: $db @ $host:$port<br>";
    }
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "<br>";
}

// Test 6: Laravel Bootstrap
echo "<h2>6. Laravel Bootstrap Test</h2>";
try {
    require __DIR__ . '/../vendor/autoload.php';
    echo "✅ Autoloader loaded<br>";
    
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    echo "✅ Application bootstrapped<br>";
    
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    echo "✅ Kernel created<br>";
    
} catch (Exception $e) {
    echo "❌ Laravel bootstrap failed: " . $e->getMessage() . "<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<hr>";
echo "<p><strong>Next Steps:</strong></p>";
echo "<ol>";
echo "<li>Check which tests failed (❌)</li>";
echo "<li>Fix the issues in Koyeb environment variables</li>";
echo "<li>DELETE this debug.php file after fixing!</li>";
echo "</ol>";
