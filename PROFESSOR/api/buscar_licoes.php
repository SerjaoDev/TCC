<?php
header("Content-Type: application/json; charset=utf-8");

require_once __DIR__ . "/../php/conexao.php";

try {
    $sql = "SELECT id, titulo, descricao, nivel, categoria FROM licoes ORDER BY nivel ASC";

    $stmt = $conexao->prepare($sql);
    $stmt->execute();

    $licoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "sucesso" => true,
        "licoes" => $licoes
    ]);
} catch (PDOException $erro) {
    http_response_code(500);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Erro ao buscar lições"
    ]);
}
?>