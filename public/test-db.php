<?php
// Database Connection Test - DELETE after fixing!
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Database Connection Test</h1>";

// Get environment variables
$host = getenv('DB_HOST') ?: 'NOT SET';
$port = getenv('DB_PORT') ?: '3306';
$database = getenv('DB_DATABASE') ?: 'NOT SET';
$username = getenv('DB_USERNAME') ?: 'NOT SET';
$password = getenv('DB_PASSWORD') ? '***SET***' : 'NOT SET';

echo "<h2>Configuration</h2>";
echo "Host: $host<br>";
echo "Port: $port<br>";
echo "Database: $database<br>";
echo "Username: $username<br>";
echo "Password: $password<br>";

if ($host === 'NOT SET' || $database === 'NOT SET' || $username === 'NOT SET') {
    echo "<h2 style='color: red;'>❌ Database credentials not configured!</h2>";
    echo "<p>Please set DB_HOST, DB_DATABASE, DB_USERNAME, and DB_PASSWORD in Koyeb environment variables.</p>";
    exit;
}

echo "<h2>Connection Test</h2>";

// Test 1: Basic TCP connection
echo "<h3>1. TCP Connection Test</h3>";
$connection = @fsockopen($host, $port, $errno, $errstr, 5);
if ($connection) {
    echo "✅ TCP connection to $host:$port successful<br>";
    fclose($connection);
} else {
    echo "❌ TCP connection failed: $errstr ($errno)<br>";
    echo "<p>This means the server cannot reach the database host. Check firewall/network settings.</p>";
}

// Test 2: PDO Connection without SSL
echo "<h3>2. PDO Connection Test (No SSL)</h3>";
try {
    $dsn = "mysql:host=$host;port=$port;dbname=" . getenv('DB_DATABASE');
    $pdo = new PDO($dsn, getenv('DB_USERNAME'), getenv('DB_PASSWORD'), [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 10,
    ]);
    echo "✅ PDO connection successful (no SSL)<br>";
    $pdo = null;
} catch (PDOException $e) {
    echo "❌ PDO connection failed: " . $e->getMessage() . "<br>";
}

// Test 3: PDO Connection with SSL disabled verification
echo "<h3>3. PDO Connection Test (SSL without verification)</h3>";
try {
    $dsn = "mysql:host=$host;port=$port;dbname=" . getenv('DB_DATABASE');
    $pdo = new PDO($dsn, getenv('DB_USERNAME'), getenv('DB_PASSWORD'), [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 10,
        PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
    ]);
    echo "✅ PDO connection successful (SSL without verification)<br>";
    
    // Test query
    $stmt = $pdo->query("SELECT DATABASE() as db, VERSION() as version");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Connected to: {$result['db']}<br>";
    echo "MySQL Version: {$result['version']}<br>";
    
    // Test tables
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "<br><strong>Tables found: " . count($tables) . "</strong><br>";
    if (count($tables) === 0) {
        echo "<p style='color: orange;'>⚠️ No tables found! You need to run migrations.</p>";
    } else {
        echo "<ul>";
        foreach ($tables as $table) {
            echo "<li>$table</li>";
        }
        echo "</ul>";
    }
    
    $pdo = null;
} catch (PDOException $e) {
    echo "❌ PDO connection failed: " . $e->getMessage() . "<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<hr>";
echo "<h2>Next Steps</h2>";
echo "<ol>";
echo "<li>If all tests pass but no tables found: Run migrations</li>";
echo "<li>If connection fails: Check DB credentials in Koyeb</li>";
echo "<li>If TCP fails: Check TiDB Cloud firewall settings</li>";
echo "<li><strong>DELETE this file after fixing!</strong></li>";
echo "</ol>";
