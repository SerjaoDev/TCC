<?php
$host    = getenv('DB_HOST') ?: 'localhost';
$usuario = getenv('DB_USER') ?: 'postgres';
$senha   = getenv('DB_PASSWORD') ?: '';
$banco   = getenv('DB_NAME') ?: 'lumi_professor';
$porta   = getenv('DB_PORT') ?: '5432';

try {
    $conexao = new PDO("pgsql:host=$host;port=$porta;dbname=$banco;sslmode=require", $usuario, $senha);
    $conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão com o banco PostgreSQL: " . $e->getMessage());
}
?>