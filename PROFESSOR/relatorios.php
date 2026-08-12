<?php
declare(strict_types=1);

require_once __DIR__ . '/php/verificar.php';
require_once __DIR__ . '/php/conexao.php';

$professor_id = filter_var(
    $_SESSION['id'] ?? null,
    FILTER_VALIDATE_INT
);

if (!$professor_id) {
    header('Location: index.php');
    exit;
}

$turma_id = filter_input(
    INPUT_GET,
    'turma',
    FILTER_VALIDATE_INT
);

if (!$turma_id) {
    header('Location: turmas.php');
    exit;
}

try {
    $stmt = $conexao->prepare("SELECT id,nome FROM turmas WHERE id = :turma_id AND professor_id = :professor_id LIMIT 1");

    $stmt->execute([
        ':turma_id' => $turma_id,
        ':professor_id' => $professor_id
    ]);

    $turma = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$turma) {
        header('Location: turmas.php');
        exit;
    }

    $stmt = $conexao->prepare("SELECT COUNT(*) FROM alunos WHERE turma_id = :turma_id AND professor_id = :professor_id");

    $stmt->execute([
        ':turma_id' => $turma_id,
        ':professor_id' => $professor_id
    ]);

    $totalAlunos = (int) $stmt->fetchColumn();

    $stmt = $conexao->prepare("SELECT COALESCE(SUM(p.licoes_concluidas),0) FROM progresso p INNER JOIN alunos a ON a.id = p.aluno_id WHERE a.turma_id = :turma_id AND a.professor_id = :professor_id");

    $stmt->execute([
        ':turma_id' => $turma_id,
        ':professor_id' => $professor_id
    ]);

    $totalLicoes = (int) $stmt->fetchColumn();

    $stmt = $conexao->prepare("SELECT COALESCE(AVG(p.acertos),0) FROM progresso p INNER JOIN alunos a ON a.id = p.aluno_id WHERE a.turma_id = :turma_id AND a.professor_id = :professor_id");

    $stmt->execute([
        ':turma_id' => $turma_id,
        ':professor_id' => $professor_id
    ]);

    $media = (float) $stmt->fetchColumn();

    $media = max(
        0,
        min(100, $media)
    );

    $stmt = $conexao->prepare("SELECT a.id,a.nome,COALESCE(p.estrelas, 0) AS estrelas,COALESCE(p.licoes_concluidas, 0) AS licoes_concluidas,COALESCE(p.acertos, 0) AS acertos FROM alunos a LEFT JOIN progresso p ON p.aluno_id = a.id WHERE a.turma_id = :turma_id AND a.professor_id = :professor_id ORDER BY a.nome ASC");

    $stmt->execute([
        ':turma_id' => $turma_id,
        ':professor_id' => $professor_id
    ]);

    $alunos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $nomesAlunos = [];
    $acertosAlunos = [];

    foreach ($alunos as $aluno) {
        $nomesAlunos[] =
            (string) $aluno['nome'];

        $acertos =
            (float) $aluno['acertos'];

        $acertos = max(
            0,
            min(100, $acertos)
        );

        $acertosAlunos[] =
            round($acertos);
    }
} catch (Throwable $e) {
    error_log(
        'Erro no relatorios.php: ' .
        $e->getMessage()
    );

    http_response_code(500);

    echo '
        <!DOCTYPE html>
        <html lang="pt-BR">
        <head>
            <meta charset="UTF-8">
            <title>Erro | Lumi</title>
            <link rel="stylesheet" href="css/style.css">
        </head>
        <body>
            <div class="login-card">
                <h1>Erro</h1>
                <p>
                    Não foi possível carregar o relatório.
                </p>
                <div class="links">
                    <a href="turmas.php">
                        Voltar para as turmas
                    </a>
                </div>
            </div>
        </body>
        </html>
    ';

    exit;
}
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
    Relatório da Turma | Lumi
</title>
<link
    rel="stylesheet"
    href="css/relatorios.css"
>
<link
    href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
    rel="stylesheet"
>
<script
    src="https://cdn.jsdelivr.net/npm/chart.js"
></script>
</head>
<body>
<div class="sidebar">
<img
    src="img/logo.png"
    class="logo"
    alt="Lumi"
>

<h2>
    Lumi
</h2>

<ul>
    <li>
        <a href="painel.php">
            🏠 Dashboard
        </a>
    </li>

    <li>
        <a href="turmas.php">
            📚 Turmas
        </a>
    </li>

    <li class="ativo">
        📊 Relatório da Turma
    </li>

    <li>
        <a href="logout.php">
            🚪 Sair
        </a>
    </li>
</ul>
</div>

<div class="conteudo">
<h1>
    Relatório -
    <?= htmlspecialchars(
        $turma['nome'],
        ENT_QUOTES,
        'UTF-8'
    ) ?>
</h1>

<div class="cards">
    <div class="card">
        <h3>
            Média Geral
        </h3>
        <span>
            <?= round($media) ?>%
        </span>
    </div>

    <div class="card">
        <h3>
            Atividades
        </h3>
        <span>
            <?= $totalLicoes ?>
        </span>
    </div>

    <div class="card">
        <h3>
            Alunos
        </h3>
        <span>
            <?= $totalAlunos ?>
        </span>
    </div>

    <div class="card">
        <h3>
            Turma
        </h3>
        <span>
            <?= htmlspecialchars(
                $turma['nome'],
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </span>
    </div>
</div>

<div class="grafico">
    <h2>
        Desempenho da Turma
    </h2>
    <canvas id="grafico"></canvas>
</div>

<div class="lista-alunos">
    <div class="topo-alunos">
        <h2>
            Alunos da Turma
        </h2>

        <a
            href="novo_aluno.php?turma=<?= $turma_id ?>"
        >

            <button
                type="button"
                class="novo-aluno"
            >
                + Adicionar Aluno
            </button>
        </a>
    </div>

    <table>
        <thead>
            <tr>
                <th>
                    Aluno
                </th>

                <th>
                    ⭐ Estrelas
                </th>

                <th>
                    📚 Lições
                </th>

                <th>
                    🎯 Acertos
                </th>

                <th>
                    Ações
                </th>
            </tr>
        </thead>
        <tbody>

        <?php if (empty($alunos)): ?>
            <tr>
                <td
                    colspan="5"
                    style="text-align:center;"
                >
                    Nenhum aluno cadastrado nesta turma.
                </td>
            </tr>

        <?php else: ?>
            <?php foreach ($alunos as $aluno): ?>
                <tr>
                    <td>
                        <?= htmlspecialchars(
                            $aluno['nome'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </td>

                    <td>
                        <?= (int) $aluno['estrelas'] ?>
                    </td>

                    <td>
                        <?= (int) $aluno['licoes_concluidas'] ?>
                    </td>

                    <td>
                        <?= round(
                            (float) $aluno['acertos']
                        ) ?>%
                    </td>

                    <td>
                        <a
                            href="aluno.php?id=<?= (int) $aluno['id'] ?>"
                        >
                            <button
                                type="button"
                                class="abrir"
                            >
                                Abrir
                            </button>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>
</div>

<script>
    const nomesAlunos =
        <?= json_encode(
            $nomesAlunos,
            JSON_UNESCAPED_UNICODE |
            JSON_UNESCAPED_SLASHES |
            JSON_HEX_TAG |
            JSON_HEX_AMP |
            JSON_HEX_APOS |
            JSON_HEX_QUOT
        ) ?>;

    const acertosAlunos =
        <?= json_encode(
            $acertosAlunos
        ) ?>;

    const canvas =
        document.getElementById(
            'grafico'
        );

    if (canvas) {
        new Chart(
            canvas,
            {
                type: 'bar',

                data: {
                    labels:
                        nomesAlunos,

                    datasets: [
                        {
                            label:
                                'Acertos (%)',

                            data:
                                acertosAlunos,

                            borderWidth:
                                2
                        }
                    ]
                },

                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            ticks: {
                                callback:
                                    function(value) {
                                        return value + '%';
                                    }
                            }
                        }
                    }
                }
            }
        );
    }
</script>
</body>
</html>