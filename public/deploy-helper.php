<?php
/**
 * Deploy Helper - Jalankan artisan commands via browser
 * Akses: https://pos.agogo-bakery.com/deploy-helper.php
 */

header('Content-Type: text/plain');

// Simple password check untuk security
$token = $_GET['token'] ?? '';
if ($token !== 'agogo2024') {
    die("Unauthorized. Token required.\n");
}

$command = $_GET['cmd'] ?? 'help';
$basePath = dirname(__DIR__);

echo "=== Deploy Helper ===\n";
echo "Base Path: $basePath\n";
echo "PHP Version: " . PHP_VERSION . "\n";
echo "Command: $command\n\n";

// Allowed commands
$allowed = [
    'help' => 'Show available commands',
    'clear' => 'php artisan optimize:clear',
    'cache' => 'php artisan config:cache',
    'composer' => 'composer install --no-dev --optimize-autoloader --no-interaction',
    'migrate' => 'php artisan migrate --force',
    'storage' => 'php artisan storage:link',
    'version' => 'php artisan --version',
    'env' => 'env',
];

if ($command === 'help') {
    echo "Available commands:\n";
    foreach ($allowed as $cmd => $desc) {
        echo "  $cmd: $desc\n";
    }
    echo "\nUsage: ?token=agogo2024&cmd=COMMAND\n";
    exit;
}

if (!isset($allowed[$command])) {
    die("Unknown command: $command\n");
}

chdir($basePath);
$output = shell_exec($allowed[$command] . ' 2>&1');
echo $output;
echo "\n=== Done ===\n";
?>
