<?php

header('Content-Type: text/plain; charset=UTF-8');

echo 'PHP_VERSION=' . PHP_VERSION . PHP_EOL;
echo 'PHP_SAPI=' . PHP_SAPI . PHP_EOL;
echo 'DOCUMENT_ROOT=' . ($_SERVER['DOCUMENT_ROOT'] ?? '') . PHP_EOL;
echo 'REQUEST_URI=' . ($_SERVER['REQUEST_URI'] ?? '') . PHP_EOL;
echo 'SCRIPT_FILENAME=' . ($_SERVER['SCRIPT_FILENAME'] ?? '') . PHP_EOL;
echo 'cwd=' . getcwd() . PHP_EOL;