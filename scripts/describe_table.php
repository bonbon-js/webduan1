<?php
if (php_sapi_name() !== 'cli') {
    exit;
}

if ($argc < 2) {
    fwrite(STDERR, "Usage: php scripts/describe_table.php <table>\n");
    exit(1);
}

$table = $argv[1];

require __DIR__ . '/../configs/env.php';

$dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8', DB_HOST, DB_PORT, DB_NAME);
$pdo = new PDO($dsn, DB_USERNAME, DB_PASSWORD, DB_OPTIONS);

$stmt = $pdo->query("DESCRIBE {$table}");

foreach ($stmt as $row) {
    echo implode(' | ', $row), PHP_EOL;
}

