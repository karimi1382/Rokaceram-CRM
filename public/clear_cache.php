<?php
require __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel app
$app = require_once __DIR__.'/../bootstrap/app.php';

// Clear the cache
$artisan = $app->make(Illuminate\Contracts\Console\Kernel::class);
$artisan->call('config:clear');
$artisan->call('cache:clear');
$artisan->call('route:clear');
$artisan->call('view:clear');

echo "Cache cleared successfully!";
