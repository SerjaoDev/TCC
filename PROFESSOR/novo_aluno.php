<?php

declare(strict_types=1);

require_once __DIR__ . '/php/verificar.php';
require_once __DIR__ . '/php/conexao.php';

$professorId = filter_var(
    $_SESSION['id'] ?? null,
    FILTER_VALIDATE_INT
);

$turmaId = filter_input(
    INPUT_GET,
    'turma',
    FILTER_VALIDATE_INT
);

if (!$professorId) {
    header('Location: index.php');
    exit;
}

if (!$turmaId) {
    header('Location: turmas.php');
    exit;
}

try {
    $sql = "
        SELECT
            id,
            nome
        FROM turmas
        WHERE
            id = :turma_id
            AND professor_id = :professor_id
        LIMIT 1
    ";

    $stmt =
        $conexao->prepare($sql);

    $stmt->execute([
        ':turma_id' => $turmaId,
        ':professor_id' => $professorId
    ]);

    $turma =
        $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$turma) {
        header('Location: turmas.php');
        exit;
    }
} catch (Throwable $e) {
    error_log(
        'Erro ao carregar turma: ' .
            $e->getMessage()
    );

    http_response_code(500);

    exit('Não foi possível carregar a turma.');
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">
    <title>
        Novo Aluno | Lumi
    </title>
    <link
        rel="stylesheet"
        href="css/style.css">
</head>

<body>
    <div class="background"></div>
    <main class="login-card">
        <img
            src="img/logo.png"
            class="logo"
            alt="Logo Lumi">
        <h1>
            Novo Aluno
        </h1>
        <p>
            Cadastre um aluno para acessar o aplicativo.
        </p>
        <p>
            <strong>
                Turma:
            </strong>
            <?= htmlspecialchars(
                (string) $turma['nome'],
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </p>
        <form
            action="php/cadastrar_aluno.php"
            method="POST"
            autocomplete="off">
            <input
                type="hidden"
                name="turma_id"
                value="<?= (int) $turmaId ?>">
            <input
                type="text"
                name="nome"
                placeholder="Nome do aluno"
                maxlength="150"
                minlength="3"
                autocomplete="name"
                required>
            <input
                type="text"
                name="usuario"
                placeholder="Usuário de login"
                maxlength="100"
                autocomplete="username"
                required>
            <input
                type="password"
                name="senha"
                placeholder="Senha do aluno"
                minlength="6"
                autocomplete="new-password"
                required>
            <input
                type="date"
                name="data_nascimento">
            <button
                type="submit"
                class="entrar">
                Criar aluno
            </button>
        </form>
        <div class="links">
            <a href="relatorios.php?turma=<?= (int) $turmaId ?>">
                Voltar para a turma
            </a>
        </div>
    </main>
</body>

</html>