<?php

declare(strict_types=1);

require_once __DIR__ . '/php/verificar.php';
require_once __DIR__ . '/php/conexao.php';

$turma_id = filter_input(
    INPUT_GET,
    'turma',
    FILTER_VALIDATE_INT
);

if (!$turma_id) {
    header('Location: turmas.php');
    exit;
}

$professor_id = $_SESSION['id'] ?? null;

if (!$professor_id) {
    header('Location: index.php');
    exit;
}

$sql = "SELECT id, nome FROM turmas WHERE id = :turma_id AND professor_id = :professor_id LIMIT 1";

$stmt = $conexao->prepare($sql);

$stmt->execute([
    ':turma_id' => $turma_id,
    ':professor_id' => $professor_id
]);

$turma = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$turma) {
    header('Location: turmas.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">
    <title>Novo Aluno | Lumi</title>
    <link
        rel="stylesheet"
        href="css/style.css">
</head>

<body>
    <div class="background"></div>
    <div class="login-card">
        <h1>
            Novo Aluno
        </h1>

        <p>
            Cadastre um aluno para acessar o aplicativo.
        </p>

        <p>
            <strong>Turma:</strong>
            <?= htmlspecialchars(
                $turma['nome'],
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </p>

        <form
            action="php/cadastrar_aluno.php"
            method="POST">

            <input
                type="hidden"
                name="turma_id"
                value="<?= (int) $turma_id ?>">

            <input
                type="text"
                name="nome"
                placeholder="Nome do aluno"
                maxlength="150"
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
            <a href="relatorios.php?turma=<?= (int) $turma_id ?>">
                Voltar
            </a>
        </div>
    </div>
</body>

</html>