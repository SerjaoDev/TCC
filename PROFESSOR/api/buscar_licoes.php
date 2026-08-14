<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=UTF-8');

require_once __DIR__ . '/../php/conexao.php';

try {
    $sql = "
        SELECT
            id,
            titulo,
            descricao,
            nivel,
            categoria
        FROM licoes
        ORDER BY nivel ASC, id ASC
    ";

    $stmt = $conexao->prepare($sql);
    $stmt->execute();

    $licoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'sucesso' => true,
        'licoes' => $licoes
    ], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {

    error_log('Erro buscar_licoes.php: ' . $e->getMessage());

    http_response_code(500);

    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Erro ao buscar lições.'
    ], JSON_UNESCAPED_UNICODE);
}

exit;