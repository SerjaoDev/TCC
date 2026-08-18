<?php

header('Content-Type: text/plain; charset=utf-8');

echo "=== TESTE DO DRIVER PHP ===\n\n";

echo "PHP: " . PHP_VERSION . "\n";
echo "SAPI: " . PHP_SAPI . "\n\n";

echo "PDO disponível: ";
echo class_exists('PDO') ? "SIM\n" : "NÃO\n";

echo "Drivers PDO disponíveis:\n";

$drivers = PDO::getAvailableDrivers();

if (empty($drivers)) {
    echo "NENHUM\n";
} else {
    foreach ($drivers as $driver) {
        echo "- " . $driver . "\n";
    }
}

echo "\nPDO PostgreSQL: ";

if (in_array('pgsql', $drivers, true)) {
    echo "SIM\n";
} else {
    echo "NÃO\n";
}

echo "\nExtensão pgsql: ";
echo extension_loaded('pgsql') ? "SIM\n" : "NÃO\n";

echo "\nExtensão pdo_pgsql: ";
echo extension_loaded('pdo_pgsql') ? "SIM\n" : "NÃO\n";