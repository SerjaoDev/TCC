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
            a.id,
            a.nome,
            a.usuario,
            a.senha_visivel,
            t.nome AS turma_nome
        FROM alunos a
        LEFT JOIN turmas t
            ON t.id = a.turma_id
        WHERE
            a.id = :aluno_id
            AND a.professor_id = :professor_id
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
} catch (Throwable $e) {
    error_log(
        'Erro ao buscar acesso do aluno: ' .
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
        Acesso do Aluno | Lumi
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
            Acesso do Aluno
        </h1>
        <p>
            Entregue essas informações ao aluno
            para que ele possa acessar o aplicativo.
        </p>
        <h3>
            Nome
        </h3>
        <p>
            <?= htmlspecialchars(
                (string) $aluno['nome'],
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </p>
        <h3>
            Usuário
        </h3>
        <p>
            <?= htmlspecialchars(
                (string) $aluno['usuario'],
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </p>
        <?php if (!empty($aluno['turma_nome'])): ?>
            <h3>
                Turma
            </h3>
            <p>
                <?= htmlspecialchars(
                    (string) $aluno['turma_nome'],
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </p>
        <?php endif; ?>
        <h3>
            Senha
        </h3>
        <p>
            <strong id="senhaAluno">
                <?= htmlspecialchars(
                    (string) (
                        $aluno['senha_visivel'] ?? ''
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </strong>
        </p>
        <button
            type="button"
            class="entrar"
            id="copiarAcesso">
            📋 Copiar acesso
        </button>
        <div class="links">
            <a href="alunos.php">
                Voltar para alunos
            </a>
        </div>
    </main>
    <script>
        const nome =
            <?= json_encode(
                (string) $aluno['nome'],
                JSON_UNESCAPED_UNICODE |
                    JSON_HEX_TAG |
                    JSON_HEX_AMP |
                    JSON_HEX_APOS |
                    JSON_HEX_QUOT
            ) ?>;

        const usuario =
            <?= json_encode(
                (string) $aluno['usuario'],
                JSON_UNESCAPED_UNICODE |
                    JSON_HEX_TAG |
                    JSON_HEX_AMP |
                    JSON_HEX_APOS |
                    JSON_HEX_QUOT
            ) ?>;

        const senha =
            <?= json_encode(
                (string) (
                    $aluno['senha_visivel'] ?? ''
                ),
                JSON_UNESCAPED_UNICODE |
                    JSON_HEX_TAG |
                    JSON_HEX_AMP |
                    JSON_HEX_APOS |
                    JSON_HEX_QUOT
            ) ?>;

        document
            .getElementById('copiarAcesso')
            .addEventListener(
                'click',
                async function() {
                    const texto =
                        'Lumi - Acesso do Aluno\n\n' +
                        'Nome: ' + nome + '\n' +
                        'Usuário: ' + usuario + '\n' +
                        'Senha: ' + senha;

                    try {
                        await navigator
                            .clipboard
                            .writeText(texto);

                        alert(
                            'Dados de acesso copiados!'
                        );
                    } catch (erro) {
                        alert(
                            'Não foi possível copiar automaticamente. ' +
                            'Copie os dados manualmente.'
                        );
                    }
                }
            );
    </script>
</body>

</html>