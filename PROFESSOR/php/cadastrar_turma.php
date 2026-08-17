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
    header('Location: ../turmas.php');
    exit;
}

$professorId =
    (int) $_SESSION['professor_id'];

$nome = trim(
    (string) ($_POST['nome'] ?? '')
);

$descricao = trim(
    (string) ($_POST['descricao'] ?? '')
);

if ($nome === '') {
    header(
        'Location: ../adicionar_turma.php?erro=nome'
    );

    exit;
}

if (mb_strlen($nome) > 150) {
    header(
        'Location: ../adicionar_turma.php?erro=nome_longo'
    );

    exit;
}

try {
    $stmt = $conexao->prepare(
        "
        INSERT INTO turmas
        (
            professor_id,
            nome,
            descricao
        )
        VALUES
        (
            :professor_id,
            :nome,
            :descricao
        )
        "
    );

    $stmt->execute([
        ':professor_id' => $professorId,
        ':nome' => $nome,
        ':descricao' =>
            $descricao !== ''
                ? $descricao
                : null
    ]);

    header(
        'Location: ../turmas.php?sucesso=turma_criada'
    );

    exit;
} catch (PDOException $e) {
    error_log(
        'ERRO CADASTRAR TURMA: ' .
        $e->getMessage()
    );

    http_response_code(500);

    exit(
        'Erro ao criar a turma.'
    );
}