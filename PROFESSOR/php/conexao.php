<?php

declare(strict_types=1);

$host = getenv('DB_HOST') ?: 'localhost';

$porta = getenv('DB_PORT') ?: '5432';

$banco = getenv('DB_NAME') ?: 'lumi_professor';

$usuario = getenv('DB_USER') ?: 'postgres';

$senha = getenv('DB_PASSWORD') ?: '';

$dsn =
    'pgsql:' .
    'host=' . $host .
    ';port=' . $porta .
    ';dbname=' . $banco;

$opcoes = [

    PDO::ATTR_ERRMODE =>
        PDO::ERRMODE_EXCEPTION,

    PDO::ATTR_DEFAULT_FETCH_MODE =>
        PDO::FETCH_ASSOC,

    PDO::ATTR_EMULATE_PREPARES =>
        false

];

try {

    $conexao = new PDO(
        $dsn,
        $usuario,
        $senha,
        $opcoes
    );

} catch (PDOException $e) {
    error_log(
        'ERRO DE CONEXÃO COM POSTGRESQL: ' .
        $e->getMessage()
    );

    http_response_code(500);

    exit(
        'Não foi possível conectar ao banco de dados.'
    );
}