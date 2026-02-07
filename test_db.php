<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$start = microtime(true);
DB::select('select 1');
$end = microtime(true);

echo "Latency: " . (($end - $start) * 1000) . " ms\n";
