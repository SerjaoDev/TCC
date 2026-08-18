<?php

declare(strict_types=1);

function obterVariavel(string $nome): string
{
    $valor = getenv($nome);

    if ($valor === false) {
        return '';
    }

    return trim((string) $valor);
}

$databaseUrl = obterVariavel('DATABASE_URL');

if ($databaseUrl !== '') {
    if (
        stripos($databaseUrl, 'sslmode=') === false
    ) {
        $databaseUrl .=
            (str_contains($databaseUrl, '?') ? '&' : '?') .
            'sslmode=require';
    }

    try {

        $conexao = new PDO(
            $databaseUrl,
            null,
            null,
            [
                PDO::ATTR_ERRMODE =>
                    PDO::ERRMODE_EXCEPTION,

                PDO::ATTR_DEFAULT_FETCH_MODE =>
                    PDO::FETCH_ASSOC,

                PDO::ATTR_EMULATE_PREPARES =>
                    false,

                PDO::ATTR_STRINGIFY_FETCHES =>
                    false
            ]
        );

        return;

    } catch (PDOException $e) {

        error_log(
            'Erro usando DATABASE_URL: ' .
            $e->getMessage()
        );
    }
}

$host = obterVariavel('PGHOST');
$database = obterVariavel('PGDATABASE');
$user = obterVariavel('PGUSER');
$password = obterVariavel('PGPASSWORD');
$port = obterVariavel('PGPORT');

if ($port === '') {
    $port = '5432';
}

if (
    $host === '' ||
    $database === '' ||
    $user === '' ||
    $password === ''
) {

    error_log(
        'Configuração PostgreSQL incompleta. ' .
        'DATABASE_URL e/ou variáveis PG* não encontradas.'
    );

    http_response_code(500);

    exit(
        'Erro interno: configuração do banco de dados não encontrada.'
    );
}

$dsn =
    'pgsql:' .
    'host=' . $host .
    ';port=' . $port .
    ';dbname=' . $database .
    ';sslmode=require';

try {

    $conexao = new PDO(
        $dsn,
        $user,
        $password,
        [
            PDO::ATTR_ERRMODE =>
                PDO::ERRMODE_EXCEPTION,

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
        'Erro ao conectar ao PostgreSQL: ' .
        $e->getMessage()
    );

    http_response_code(500);

    exit(
        'Erro ao conectar com o banco de dados.'
    );
}