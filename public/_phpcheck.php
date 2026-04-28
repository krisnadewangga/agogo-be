<?php

header('Content-Type: text/plain; charset=UTF-8');

echo 'PHP_VERSION=' . PHP_VERSION . PHP_EOL;
echo 'PHP_SAPI=' . PHP_SAPI . PHP_EOL;
echo 'DOCUMENT_ROOT=' . ($_SERVER['DOCUMENT_ROOT'] ?? '') . PHP_EOL;
echo 'SERVER_NAME=' . ($_SERVER['SERVER_NAME'] ?? '') . PHP_EOL;
echo 'mbstring=' . (extension_loaded('mbstring') ? 'yes' : 'no') . PHP_EOL;
echo 'openssl=' . (extension_loaded('openssl') ? 'yes' : 'no') . PHP_EOL;
echo 'pdo_mysql=' . (extension_loaded('pdo_mysql') ? 'yes' : 'no') . PHP_EOL;