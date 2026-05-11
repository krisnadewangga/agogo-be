<?php
/**
 * Clear Artisan caches via browser.
 * Usage: https://yourdomain/clear-artisan.php?token=agogo2024&cmd=all
 * Security: change the token before using in production or remove the file afterwards.
 */

header('Content-Type: text/plain; charset=UTF-8');

$token = $_GET['token'] ?? '';
if ($token !== 'agogo2024') {
    http_response_code(401);
    echo "Unauthorized. Provide valid token=...\n";
    exit;
}

$cmd = $_GET['cmd'] ?? 'all';
$basePath = dirname(__DIR__);

$commands = [
    'config-clear' => 'php artisan config:clear',
    'cache-clear' => 'php artisan cache:clear',
    'route-clear'  => 'php artisan route:clear',
    'view-clear'   => 'php artisan view:clear',
    'optimize-clear' => 'php artisan optimize:clear',
    'all' => 'php artisan config:clear && php artisan cache:clear && php artisan route:clear && php artisan view:clear && php artisan optimize:clear',
];

if (!isset($commands[$cmd])) {
    echo "Unknown cmd. Available: " . implode(', ', array_keys($commands)) . "\n";
    exit;
}

chdir($basePath);

echo "Base path: $basePath\n";
echo "Running: {$commands[$cmd]}\n\n";

$output = shell_exec($commands[$cmd] . ' 2>&1');
echo $output;

echo "\nDone. Remove or secure this file after use.\n";

?>
