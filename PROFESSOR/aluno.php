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
            a.foto,
            a.data_nascimento,
            a.turma_id,
            t.nome AS turma_nome
        FROM alunos a
        LEFT JOIN turmas t
            ON t.id = a.turma_id
        WHERE
            a.id = :aluno_id
            AND a.professor_id = :professor_id
        LIMIT 1
    ";

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

    $sqlProgresso = "
        SELECT
            nivel_atual,
            estrelas,
            moedas,
            licoes_concluidas,
            acertos,
            erros,
            tempo_estudo,
            ultimo_acesso
        FROM progresso
        WHERE aluno_id = :aluno_id
        LIMIT 1
    ";

    $stmtProgresso = $conexao->prepare($sqlProgresso);

    $stmtProgresso->execute([
        ':aluno_id' => $alunoId
    ]);

    $progresso = $stmtProgresso->fetch(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    error_log(
        'Erro ao carregar aluno: ' . $e->getMessage()
    );

    http_response_code(500);

    exit('Não foi possível carregar os dados do aluno.');
}

$estrelas = (int) ($progresso['estrelas'] ?? 0);

$moedas = (int) ($progresso['moedas'] ?? 0);

$licoesConcluidas =
    (int) ($progresso['licoes_concluidas'] ?? 0);

$acertos =
    (float) ($progresso['acertos'] ?? 0);

$acertos = max(
    0,
    min(100, $acertos)
);

$erros =
    (int) ($progresso['erros'] ?? 0);

$nivelAtual =
    (int) ($progresso['nivel_atual'] ?? 0);

$tempoEstudo =
    (int) ($progresso['tempo_estudo'] ?? 0);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">
    <title>
        <?= htmlspecialchars(
            (string) $aluno['nome'],
            ENT_QUOTES,
            'UTF-8'
        ) ?> | Lumi
    </title>
    <link
        rel="stylesheet"
        href="css/aluno.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
</head>

<body>
    <main class="conteudo">
        <div class="topo-aluno">
            <div>
                <h1>
                    <?= htmlspecialchars(
                        (string) $aluno['nome'],
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </h1>
                <?php if (!empty($aluno['turma_nome'])): ?>
                    <p>
                        Turma:
                        <strong>
                            <?= htmlspecialchars(
                                (string) $aluno['turma_nome'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </strong>
                    </p>
                <?php endif; ?>
            </div>
        </div>
        <div class="cards">
            <div class="card">
                <h3>⭐ Estrelas</h3>
                <span>
                    <?= $estrelas ?>
                </span>
            </div>
            <div class="card">
                <h3>🪙 Moedas</h3>
                <span>
                    <?= $moedas ?>
                </span>
            </div>
            <div class="card">
                <h3>📚 Lições</h3>
                <span>
                    <?= $licoesConcluidas ?>
                </span>
            </div>
            <div class="card">
                <h3>🎯 Acertos</h3>
                <span>
                    <?= round($acertos) ?>%
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
                        <?= $nivelAtual ?>
                    </strong>
                </p>
                <p>
                    Acertos:
                    <strong>
                        <?= round($acertos) ?>%
                    </strong>
                </p>
                <p>
                    Erros:
                    <strong>
                        <?= $erros ?>
                    </strong>
                </p>
                <p>
                    Tempo de estudo:
                    <strong>
                        <?= $tempoEstudo ?>
                        minutos
                    </strong>
                </p>
                <?php if (!empty($progresso['ultimo_acesso'])): ?>
                    <p>
                        Último acesso:
                        <strong>
                            <?= htmlspecialchars(
                                (string) $progresso['ultimo_acesso'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </strong>
                    </p>
                <?php endif; ?>
            <?php else: ?>
                <p>
                    O progresso aparecerá aqui quando o aluno
                    começar a utilizar o aplicativo.
                </p>
            <?php endif; ?>
        </section>
        <div class="acoes-aluno">
            <a
                href="dados_aluno.php?id=<?= (int) $aluno['id'] ?>"
                class="acesso">
                🔑 Ver acesso do aluno
            </a>
            <a
                href="editar_aluno.php?id=<?= (int) $aluno['id'] ?>"
                class="acesso">
                ✏️ Editar aluno
            </a>
            <a
                href="alunos.php"
                class="acesso">
                ← Voltar para alunos
            </a>
        </div>
    </main>
</body>

</html>