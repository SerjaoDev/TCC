<?php
declare(strict_types=1);

require_once __DIR__ . '/verificar.php';
require_once __DIR__ . '/conexao.php';

$professorId =
    (int) $_SESSION['professor_id'];

try {
    $stmt = $conexao->prepare(
        "
        SELECT
            t.id,
            t.nome,
            t.descricao,
            COUNT(a.id) AS quantidade_alunos
        FROM turmas t

        LEFT JOIN alunos a
            ON a.turma_id = t.id
            AND a.professor_id = t.professor_id

        WHERE t.professor_id = :professor_id

        GROUP BY
            t.id,
            t.nome,
            t.descricao

        ORDER BY
            t.nome ASC
        "
    );

    $stmt->execute([
        ':professor_id' => $professorId
    ]);

    $turmas = $stmt->fetchAll(
        PDO::FETCH_ASSOC
    );
} catch (Throwable $e) {
    error_log(
        'ERRO LISTAR TURMAS: ' .
        $e->getMessage()
    );

    $turmas = [];
}