<?php

$host = "localhost";
$usuario = "root";
$senha = "";
$banco = "lumi_professor";

$conexao = new mysqli(
    $host,
    $usuario,
    $senha,
    $banco
);

if ($conexao->connect_error) {
    die("Erro na conexão com o banco: " . $conexao->connect_error);
}

?>