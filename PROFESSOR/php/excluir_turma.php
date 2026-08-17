<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/conexao.php';

if (
    !isset($_SESSION['professor_id']) ||
    !is_numeric($_SESSION['professor_id'])
) {
    header('Location: ../index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header(
        'Location: ../turmas.php?erro=metodo'
    );
    exit;
}

$professorId =
    (int) $_SESSION['professor_id'];

$turmaId = $_POST['id'] ?? '';

if (
    !filter_var(
        $turmaId,
        FILTER_VALIDATE_INT
    )
) {
    header(
        'Location: ../turmas.php?erro=turma'
    );
    exit;
}

try {
    $conexao->beginTransaction();

    $stmt = $conexao->prepare(
        "
        SELECT id
        FROM turmas
        WHERE id = :turma_id
        AND professor_id = :professor_id
        LIMIT 1
        "
    );

    $stmt->execute([
        ':turma_id' => (int) $turmaId,
        ':professor_id' => $professorId
    ]);

    if (!$stmt->fetch()) {
        $conexao->rollBack();

        header(
            'Location: ../turmas.php?erro=turma_nao_encontrada'
        );

        exit;
    }

    $stmt = $conexao->prepare(
        "
        UPDATE alunos
        SET turma_id = NULL
        WHERE turma_id = :turma_id
        AND professor_id = :professor_id
        "
    );

    $stmt->execute([
        ':turma_id' => (int) $turmaId,
        ':professor_id' => $professorId
    ]);

    $stmt = $conexao->prepare(
        "
        DELETE FROM turmas
        WHERE id = :turma_id
        AND professor_id = :professor_id
        "
    );

    $stmt->execute([
        ':turma_id' => (int) $turmaId,
        ':professor_id' => $professorId
    ]);

    if ($stmt->rowCount() === 0) {
        throw new RuntimeException(
            'Turma não pôde ser excluída.'
        );
    }

    $conexao->commit();

    header(
        'Location: ../turmas.php?sucesso=turma_excluida'
    );

    exit;
} catch (Throwable $e) {
    if ($conexao->inTransaction()) {
        $conexao->rollBack();
    }

    error_log(
        'ERRO EXCLUIR TURMA: ' .
        $e->getMessage()
    );

    http_response_code(500);

    exit(
        'Erro ao excluir turma.'
    );
}