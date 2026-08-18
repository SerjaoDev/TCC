<?php

declare(strict_types=1);

header('Content-Type: text/plain; charset=UTF-8');

echo "=== TESTE DE CONEXÃO LUMI ===\n\n";

echo "PHP: " . PHP_VERSION . "\n";

echo "PDO disponível: "
    . (class_exists('PDO') ? 'SIM' : 'NÃO')
    . "\n";

echo "PDO PostgreSQL disponível: "
    . (in_array('pgsql', PDO::getAvailableDrivers(), true) ? 'SIM' : 'NÃO')
    . "\n\n";

$databaseUrl = getenv('DATABASE_URL');

echo "DATABASE_URL: "
    . ($databaseUrl !== false && trim($databaseUrl) !== ''
        ? 'ENCONTRADA'
        : 'NÃO ENCONTRADA')
    . "\n";

$pgHost = getenv('PGHOST');
$pgDatabase = getenv('PGDATABASE');
$pgUser = getenv('PGUSER');
$pgPassword = getenv('PGPASSWORD');
$pgPort = getenv('PGPORT');

echo "PGHOST: "
    . ($pgHost !== false && trim($pgHost) !== ''
        ? 'ENCONTRADA'
        : 'NÃO ENCONTRADA')
    . "\n";

echo "PGDATABASE: "
    . ($pgDatabase !== false && trim($pgDatabase) !== ''
        ? 'ENCONTRADA'
        : 'NÃO ENCONTRADA')
    . "\n";

echo "PGUSER: "
    . ($pgUser !== false && trim($pgUser) !== ''
        ? 'ENCONTRADA'
        : 'NÃO ENCONTRADA')
    . "\n";

echo "PGPASSWORD: "
    . ($pgPassword !== false && trim($pgPassword) !== ''
        ? 'ENCONTRADA'
        : 'NÃO ENCONTRADA')
    . "\n";

echo "PGPORT: "
    . ($pgPort !== false && trim($pgPort) !== ''
        ? $pgPort
        : 'não definida')
    . "\n\n";

if (!class_exists('PDO')) {
    exit("ERRO: PDO não está disponível.\n");
}

if (!in_array('pgsql', PDO::getAvailableDrivers(), true)) {
    exit("ERRO: PDO PostgreSQL (pgsql) não está disponível.\n");
}

if (
    $databaseUrl !== false &&
    trim($databaseUrl) !== ''
) {

    echo "Tentando conexão usando DATABASE_URL...\n";

    $dsn = trim($databaseUrl);

    if (stripos($dsn, 'sslmode=') === false) {
        $dsn .=
            (str_contains($dsn, '?') ? '&' : '?') .
            'sslmode=require';
    }

    try {

        $teste = new PDO(
            $dsn,
            null,
            null,
            [
                PDO::ATTR_ERRMODE =>
                    PDO::ERRMODE_EXCEPTION
            ]
        );

        echo "\n=================================\n";
        echo "CONEXÃO FUNCIONOU!\n";
        echo "=================================\n";

        $resultado = $teste->query(
            'SELECT version()'
        );

        echo "\nPostgreSQL:\n";
        echo $resultado->fetchColumn();
        echo "\n";

        exit;

    } catch (Throwable $e) {

        echo "\n=================================\n";
        echo "ERRO REAL DO DATABASE_URL:\n";
        echo "=================================\n";

        echo $e->getMessage();
        echo "\n";

        exit;
    }
}

echo "\nDATABASE_URL não encontrada.\n";
echo "Tentando usar variáveis PG*...\n\n";

if (
    !$pgHost ||
    !$pgDatabase ||
    !$pgUser ||
    !$pgPassword
) {

    exit(
        "ERRO: as variáveis PG* também estão incompletas.\n"
    );
}

$port = $pgPort ?: '5432';

$dsn =
    "pgsql:" .
    "host={$pgHost};" .
    "port={$port};" .
    "dbname={$pgDatabase};" .
    "sslmode=require";

try {

    $teste = new PDO(
        $dsn,
        $pgUser,
        $pgPassword,
        [
            PDO::ATTR_ERRMODE =>
                PDO::ERRMODE_EXCEPTION
        ]
    );

    echo "=================================\n";
    echo "CONEXÃO FUNCIONOU!\n";
    echo "=================================\n";

    $resultado = $teste->query(
        'SELECT version()'
    );

    echo "\nPostgreSQL:\n";
    echo $resultado->fetchColumn();
    echo "\n";

} catch (Throwable $e) {

    echo "=================================\n";
    echo "ERRO REAL DO POSTGRESQL:\n";
    echo "=================================\n";

    echo $e->getMessage();
    echo "\n";
}