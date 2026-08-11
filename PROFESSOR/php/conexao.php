<?php
declare(strict_types=1);

$host = getenv('PGHOST');
$database = getenv('PGDATABASE');
$user = getenv('PGUSER');
$password = getenv('PGPASSWORD');
$port = getenv('PGPORT') ?: '5432';

if (!$host || !$database || !$user || !$password) {
    error_log('Variáveis do PostgreSQL não configuradas.');

    http_response_code(500);

    die('Erro interno: configuração do banco não encontrada.');
}

$endpointId = getenv('NEON_ENDPOINT_ID');

if (!$endpointId) {
    $endpointId = explode('.', $host)[0];
}

$senhaNeon = 'endpoint=' . $endpointId . ';' . $password;

$dsn =
    'pgsql:' .
    'host=' . $host . ';' .
    'port=' . $port . ';' .
    'dbname=' . $database . ';' .
    'sslmode=require';

try {
    $conexao = new PDO(
        $dsn,
        $user,
        $senhaNeon,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,

            PDO::ATTR_DEFAULT_FETCH_MODE =>
                PDO::FETCH_ASSOC,

            PDO::ATTR_EMULATE_PREPARES =>
                false
        ]
    );
} catch (PDOException $e) {
    error_log(
        'Erro PostgreSQL Neon: ' .
        $e->getMessage()
    );

    http_response_code(500);

    die(
        'Erro ao conectar com o banco de dados.'
    );
}