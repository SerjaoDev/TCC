<?php
declare(strict_types=1);

require_once __DIR__ . '/verificar.php';
require_once __DIR__ . '/conexao.php';

$professorId =
    (int) $_SESSION['professor_id'];

try {
    $sql = "
        SELECT
            a.id,
            a.nome,
            a.usuario,
            a.foto,
            a.data_nascimento,
            a.turma_id,
            t.nome AS turma_nome,
            a.data_cadastro
        FROM alunos a

        LEFT JOIN turmas t
            ON t.id = a.turma_id
            AND t.professor_id = a.professor_id

        WHERE a.professor_id = :professor_id

        ORDER BY a.nome ASC
    ";

    $stmt = $conexao->prepare($sql);

    $stmt->execute([
        ':professor_id' => $professorId
    ]);

    $alunos = $stmt->fetchAll(
        PDO::FETCH_ASSOC
    );
} catch (Throwable $e) {
    error_log(
        'ERRO LISTAR ALUNOS: ' .
        $e->getMessage()
    );

    $alunos = [];
}