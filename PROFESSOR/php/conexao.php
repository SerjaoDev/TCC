<?php
declare(strict_types=1);

$host = getenv('PGHOST') ?: getenv('DB_HOST');
$database = getenv('PGDATABASE') ?: getenv('DB_NAME');
$user = getenv('PGUSER') ?: getenv('DB_USER');
$password = getenv('PGPASSWORD') ?: getenv('DB_PASSWORD');
$port = getenv('PGPORT') ?: getenv('DB_PORT') ?: '5432';

if (!$host || !$database || !$user || !$password) {
    http_response_code(500);

    die('Erro: variáveis de conexão com o banco não configuradas.');
}

$endpointId = getenv('NEON_ENDPOINT_ID');

if (!$endpointId) {
    $endpointId = explode('.', $host)[0];
}

try {
    $dsn = sprintf(
        'pgsql:host=%s;port=%s;dbname=%s;sslmode=require;options=endpoint%%3D%s',
        $host,
        $port,
        $database,
        $endpointId
    );

    $conexao = new PDO(
        $dsn,
        $user,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    error_log(
        'Erro PostgreSQL/Neon: ' . $e->getMessage()
    );

    http_response_code(500);

    die(
        'Erro ao conectar com o banco de dados: ' .
        $e->getMessage()
    );
}