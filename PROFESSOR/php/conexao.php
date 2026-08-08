<?php
$host = getenv("PGHOST");
$database = getenv("PGDATABASE");
$user = getenv("PGUSER");
$password = getenv("PGPASSWORD");
$ssl = getenv("PGSSLMODE") ?: "require";

try {
    $conexao = new PDO(
        "pgsql:host=$host;dbname=$database;sslmode=$ssl",
        $user,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $erro) {
    http_response_code(500);

    die("Erro ao conectar com o banco de dados: "
        . $erro->getMessage());
}
?>