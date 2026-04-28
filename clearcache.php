<?php

header('Content-Type: text/plain; charset=UTF-8');

$targets = [
    __DIR__ . '/bootstrap/cache/config.php',
    __DIR__ . '/bootstrap/cache/packages.php',
    __DIR__ . '/bootstrap/cache/services.php',
];

foreach ($targets as $path) {
    if (file_exists($path)) {
        @unlink($path);
    }
}

echo "cache cleared\n";