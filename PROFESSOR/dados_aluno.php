<?php

declare(strict_types=1);

require_once __DIR__ . '/php/verificar.php';
require_once __DIR__ . '/php/conexao.php';

$professorId = $_SESSION['id'] ?? null;
$alunoId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$professorId) {
    header('Location: index.php');
    exit;
}

if (!$alunoId) {
    http_response_code(400);
    exit('Aluno inválido.');
}

try {
    $sql = "SELECT a.id,a.nome,a.usuario,a.senha_visivel,t.nome AS turma_nome FROM alunos a LEFT JOIN turmas t ON t.id = a.turma_id WHERE a.id = :aluno_id AND a.professor_id = :professor_id LIMIT 1";

    $stmt = $conexao->prepare($sql);

    $stmt->execute([
        ':aluno_id' => $alunoId,
        ':professor_id' => $professorId
    ]);

    $aluno = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$aluno) {
        http_response_code(404);
        exit('Aluno não encontrado.');
    }
} catch (PDOException $e) {
    error_log(
        'Erro ao buscar acesso do aluno: ' . $e->getMessage()
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
    <title>Acesso do Aluno | Lumi</title>
    <link
        rel="stylesheet"
        href="css/style.css">
</head>

<body>
    <div class="login-card">
        <img
            src="img/logo.png"
            class="logo"
            alt="Logo Lumi">

        <h1>
            Acesso do Aluno
        </h1>

        <p>
            Entregue essas informações ao aluno para que ele
            possa acessar o aplicativo.
        </p>

        <h3>
            Nome
        </h3>

        <p>
            <?= htmlspecialchars(
                $aluno['nome'],
                ENT_QUOTES,
                'UTF-8'
            ); ?>
        </p>

        <h3>
            Usuário
        </h3>

        <p>
            <?= htmlspecialchars(
                $aluno['usuario'],
                ENT_QUOTES,
                'UTF-8'
            ); ?>
        </p>

        <?php if (!empty($aluno['turma_nome'])): ?>
            <h3>
                Turma
            </h3>

            <p>
                <?= htmlspecialchars(
                    $aluno['turma_nome'],
                    ENT_QUOTES,
                    'UTF-8'
                ); ?>
            </p>
        <?php endif; ?>

        <h3>
            Senha
        </h3>

        <p>
            <strong id="senhaAluno">
                <?= htmlspecialchars(
                    $aluno['senha_visivel'] ?? '',
                    ENT_QUOTES,
                    'UTF-8'
                ); ?>
            </strong>
        </p>
        <button
            type="button"
            class="entrar"
            onclick="copiarAcesso()">
            📋 Copiar acesso
        </button>

        <div class="links">
            <a href="javascript:history.back()">
                Voltar
            </a>
        </div>
    </div>

    <script>
        function copiarAcesso() {
            const nome = <?= json_encode($aluno['nome']); ?>;
            const usuario = <?= json_encode($aluno['usuario']); ?>;
            const senha = <?= json_encode($aluno['senha_visivel'] ?? ''); ?>;
            const texto =
                'Lumi - Acesso do Aluno\n\n' +
                'Nome: ' + nome + '\n' +
                'Usuário: ' + usuario + '\n' +
                'Senha: ' + senha;

            navigator.clipboard.writeText(texto)
                .then(function() {
                    alert('Dados de acesso copiados!');
                })
                .catch(function() {
                    alert(
                        'Não foi possível copiar automaticamente. ' +
                        'Copie os dados manualmente.'
                    );
                });
        }
    </script>
</body>

</html>