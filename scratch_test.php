<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "DB Connection: " . DB::connection()->getName() . "\n";
echo "DB Database: " . DB::connection()->getDatabaseName() . "\n";
echo "DB Host: " . DB::connection()->getConfig('host') . "\n";
echo "DB Driver: " . DB::connection()->getConfig('driver') . "\n";
echo "ENV DB_CONNECTION: " . env('DB_CONNECTION') . "\n";
echo "ENV DB_DATABASE: " . env('DB_DATABASE') . "\n";
