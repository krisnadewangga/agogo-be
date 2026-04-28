<?php

header('Content-Type: text/plain; charset=UTF-8');

$targets = [
    __DIR__ . '/bootstrap/cache/config.php',
    __DIR__ . '/bootstrap/cache/packages.php',
    __DIR__ . '/bootstrap/cache/services.php',
];

$deleted = [];
$failed = [];

foreach ($targets as $path) {
    if (file_exists($path)) {
        if (@unlink($path)) {
            $deleted[] = $path;
        } else {
            $failed[] = $path;
        }
    }
}

echo 'Deleted:' . PHP_EOL;
if ($deleted) {
    echo implode(PHP_EOL, $deleted) . PHP_EOL;
} else {
    echo '(none)' . PHP_EOL;
}

echo PHP_EOL . 'Failed:' . PHP_EOL;
if ($failed) {
    echo implode(PHP_EOL, $failed) . PHP_EOL;
} else {
    echo '(none)' . PHP_EOL;
}