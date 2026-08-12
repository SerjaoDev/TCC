<?php
declare(strict_types=1);

require_once __DIR__ . '/php/verificar.php';
require_once __DIR__ . '/php/conexao.php';

$id = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT
);

if (!$id) {
    http_response_code(400);
    exit('Aluno inválido.');
}

$professorId = (int) $_SESSION['id'];

$sql = "SELECT id,nome,usuario,foto,data_nascimento,turma_id FROM alunos WHERE id = :id AND professor_id = :professor_id LIMIT 1";

$stmt = $conexao->prepare($sql);

$stmt->execute([
    ':id' => $id,
    ':professor_id' => $professorId
]);

$aluno = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$aluno) {
    http_response_code(404);
    exit('Aluno não encontrado.');
}

$sqlProgresso = "SELECT nivel_atual,estrelas,moedas,licoes_concluidas,acertos,erros,tempo_estudo,ultimo_acesso FROM progresso WHERE aluno_id = :aluno_id LIMIT 1";

$stmtProgresso = $conexao->prepare($sqlProgresso);

$stmtProgresso->execute([
    ':aluno_id' => $id
]);

$progresso = $stmtProgresso->fetch(PDO::FETCH_ASSOC);

$estrelas = (int) ($progresso['estrelas'] ?? 0);

$moedas = (int) ($progresso['moedas'] ?? 0);

$licoesConcluidas =
    (int) ($progresso['licoes_concluidas'] ?? 0);

$acertos =
    (int) ($progresso['acertos'] ?? 0);

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >
    <title>
        <?php echo htmlspecialchars($aluno['nome']); ?> | Lumi
    </title>
    <link
        rel="stylesheet"
        href="css/aluno.css"
    >
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet"
    >
</head>
<body>
    <main class="conteudo">
        <h1>
            <?php echo htmlspecialchars($aluno['nome']); ?>
        </h1>

        <div class="cards">
            <div class="card">
                <h3>⭐ Estrelas</h3>
                <span>
                    <?php echo $estrelas; ?>
                </span>
            </div>

            <div class="card">
                <h3>🪙 Moedas</h3>
                <span>
                    <?php echo $moedas; ?>
                </span>
            </div>

            <div class="card">
                <h3>📚 Lições</h3>
                <span>
                    <?php echo $licoesConcluidas; ?>
                </span>
            </div>

            <div class="card">
                <h3>🎯 Acertos</h3>
                <span>
                    <?php echo $acertos; ?>%
                </span>
            </div>
        </div>

        <section class="historico">
            <h2>
                Histórico de atividades
            </h2>
            <?php if ($progresso): ?>
                <p>
                    Nível atual:
                    <strong>
                        <?php
                        echo (int) $progresso['nivel_atual'];
                        ?>
                    </strong>
                </p>
                <p>
                    Acertos:
                    <strong>
                        <?php echo $acertos; ?>%
                    </strong>
                </p>
                <p>
                    Erros:
                    <strong>
                        <?php
                        echo (int) ($progresso['erros'] ?? 0);
                        ?>
                    </strong>
                </p>
                <p>
                    Tempo de estudo:
                    <strong>
                        <?php
                        echo (int) ($progresso['tempo_estudo'] ?? 0);
                        ?>
                        minutos
                    </strong>
                </p>
            <?php else: ?>
                <p>
                    O progresso aparecerá aqui quando o aluno
                    começar a utilizar o aplicativo.
                </p>
            <?php endif; ?>
        </section>

        <a
            href="dados_aluno.php?id=<?php echo $aluno['id']; ?>"
        >
            <button
                type="button"
                class="acesso"
            >
                🔑 Ver acesso do aluno
            </button>
        </a>
    </main>
</body>
</html>