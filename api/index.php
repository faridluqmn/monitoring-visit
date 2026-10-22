<?php

// Pastikan direktori writable di /tmp untuk Laravel Serverless di Vercel
$storageDirs = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
];

foreach ($storageDirs as $dir) {
    if (! is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Teruskan request ke Laravel entrypoint
require __DIR__.'/../public/index.php';
