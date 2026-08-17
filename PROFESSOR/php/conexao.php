<?php
declare(strict_types=1);

$host = trim((string) getenv('PGHOST'));
$database = trim((string) getenv('PGDATABASE'));
$user = trim((string) getenv('PGUSER'));
$password = (string) getenv('PGPASSWORD');
$port = trim((string) (getenv('PGPORT') ?: '5432'));

if (
    $host === '' ||
    $database === '' ||
    $user === '' ||
    $password === ''
) {
    error_log('Configuração do PostgreSQL incompleta.');

    http_response_code(500);

    exit('Erro interno: configuração do banco de dados não encontrada.');
}

$dsn = sprintf(
    'pgsql:host=%s;port=%s;dbname=%s;sslmode=require',
    $host,
    $port,
    $database
);

try {
    $conexao = new PDO(
        $dsn,
        $user,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,

            PDO::ATTR_DEFAULT_FETCH_MODE =>
                PDO::FETCH_ASSOC,

            PDO::ATTR_EMULATE_PREPARES =>
                false,

            PDO::ATTR_STRINGIFY_FETCHES =>
                false
        ]
    );
} catch (PDOException $e) {
    error_log(
        'Erro ao conectar ao PostgreSQL/Neon: ' .
        $e->getMessage()
    );

    http_response_code(500);

    exit(
        'Erro ao conectar com o banco de dados.'
    );
}