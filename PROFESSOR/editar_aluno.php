<?php

declare(strict_types=1);

require_once __DIR__ . '/php/verificar.php';
require_once __DIR__ . '/php/conexao.php';

$professorId = filter_var(
    $_SESSION['id'] ?? null,
    FILTER_VALIDATE_INT
);

$alunoId = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT
);

if (!$professorId) {
    header('Location: index.php');
    exit;
}

if (!$alunoId) {
    http_response_code(400);
    exit('Aluno inválido.');
}

try {
    $sql = "
        SELECT
            id,
            nome,
            usuario,
            data_nascimento,
            turma_id
        FROM alunos
        WHERE
            id = :aluno_id
            AND professor_id = :professor_id
        LIMIT 1
    ";

    $stmt =
        $conexao->prepare($sql);

    $stmt->execute([
        ':aluno_id' => $alunoId,
        ':professor_id' => $professorId
    ]);

    $aluno =
        $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$aluno) {
        http_response_code(404);

        exit('Aluno não encontrado.');
    }

    $sqlTurmas = "
        SELECT
            id,
            nome
        FROM turmas
        WHERE professor_id = :professor_id
        ORDER BY nome ASC
    ";

    $stmtTurmas =
        $conexao->prepare($sqlTurmas);

    $stmtTurmas->execute([
        ':professor_id' => $professorId
    ]);

    $turmas =
        $stmtTurmas->fetchAll(
            PDO::FETCH_ASSOC
        );
} catch (Throwable $e) {
    error_log(
        'Erro ao carregar edição do aluno: ' .
            $e->getMessage()
    );

    http_response_code(500);

    exit('Não foi possível carregar os dados do aluno.');
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
        Editar Aluno | Lumi
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
            Editar Aluno
        </h1>
        <p>
            Atualize os dados do aluno.
        </p>
        <form
            action="php/editar_aluno.php"
            method="POST"
            id="formEditarAluno">
            <input
                type="hidden"
                name="id"
                value="<?= (int) $aluno['id'] ?>">
            <input
                type="text"
                name="nome"
                placeholder="Nome do aluno"
                value="<?= htmlspecialchars(
                            (string) $aluno['nome'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                minlength="3"
                maxlength="150"
                required>
            <input
                type="text"
                name="usuario"
                placeholder="Usuário de login"
                value="<?= htmlspecialchars(
                            (string) $aluno['usuario'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                maxlength="100"
                required>
            <input
                type="date"
                name="data_nascimento"
                value="<?= htmlspecialchars(
                            (string) (
                                $aluno['data_nascimento'] ?? ''
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>">
            <select
                name="turma_id"
                required
                style="
                width:100%;
                padding:15px;
                margin-bottom:18px;
                border:2px solid #ddd;
                border-radius:12px;
                outline:none;
                background:white;
                font-family:'Poppins',sans-serif;
                font-size:15px;
            ">
                <option value="">
                    Selecione a turma
                </option>
                <?php foreach ($turmas as $turma): ?>
                    <option
                        value="<?= (int) $turma['id'] ?>"
                        <?= (
                            (int) $turma['id'] ===
                            (int) ($aluno['turma_id'] ?? 0)
                        )
                            ? 'selected'
                            : ''
                        ?>>
                        <?= htmlspecialchars(
                            (string) $turma['nome'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button
                type="submit"
                class="entrar"
                id="botaoSalvar">
                Salvar Alterações
            </button>
        </form>
        <div class="links">
            <a href="aluno.php?id=<?= (int) $aluno['id'] ?>">
                Voltar
            </a>
        </div>
    </main>

    <script>
        document
            .getElementById('formEditarAluno')
            .addEventListener(
                'submit',
                function() {
                    const botao =
                        document.getElementById(
                            'botaoSalvar'
                        );

                    botao.disabled = true;
                    botao.textContent =
                        'Salvando...';
                }
            );
    </script>
</body>

</html>