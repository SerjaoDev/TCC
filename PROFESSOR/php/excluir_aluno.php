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

$alunoId = $_POST['id'] ?? '';

if (
    !filter_var(
        $alunoId,
        FILTER_VALIDATE_INT
    )
) {
    header(
        'Location: ../turmas.php?erro=aluno'
    );
    exit;
}

try {
    $stmt = $conexao->prepare(
        "
        DELETE FROM alunos
        WHERE id = :aluno_id
        AND professor_id = :professor_id
        "
    );

    $stmt->execute([
        ':aluno_id' => (int) $alunoId,
        ':professor_id' => $professorId
    ]);

    if ($stmt->rowCount() === 0) {
        header(
            'Location: ../turmas.php?erro=aluno_nao_encontrado'
        );

        exit;
    }

    header(
        'Location: ../turmas.php?sucesso=aluno_excluido'
    );

    exit;
} catch (PDOException $e) {
    error_log(
        'ERRO EXCLUIR ALUNO: ' .
        $e->getMessage()
    );

    http_response_code(500);

    exit(
        'Erro ao excluir aluno.'
    );
}