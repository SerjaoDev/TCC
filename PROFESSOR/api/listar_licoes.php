<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=UTF-8');

require_once __DIR__ . '/../php/conexao.php';

try {

    $sql = "
        SELECT *
        FROM licoes
        ORDER BY nivel ASC, id ASC
    ";

    $stmt = $conexao->prepare($sql);
    $stmt->execute();

    $licoes = $stmt->fetchAll(
        PDO::FETCH_ASSOC
    );

    echo json_encode(
        [
            'sucesso' => true,
            'licoes' => $licoes
        ],
        JSON_UNESCAPED_UNICODE
    );

    exit;

} catch (Throwable $e) {

    error_log(
        'Erro listar_licoes.php: ' .
        $e->getMessage()
    );

    http_response_code(500);

    echo json_encode(
        [
            'sucesso' => false,
            'mensagem' => 'Erro ao listar lições.'
        ],
        JSON_UNESCAPED_UNICODE
    );

    exit;
}