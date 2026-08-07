<?php
header("Content-Type: application/json; charset=utf-8");

require_once __DIR__ . "/../php/conexao.php";

try {
    $sql = "SELECT * FROM licoes ORDER BY nivel ASC";

    $stmt = $conexao->prepare($sql);
    $stmt->execute();

    echo json_encode([
        "sucesso" => true,
        "licoes" => $stmt->fetchAll(PDO::FETCH_ASSOC)
    ]);
} catch (PDOException $erro) {
    http_response_code(500);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Erro ao listar lições"
    ]);
}