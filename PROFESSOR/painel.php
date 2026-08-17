<?php

declare(strict_types=1);

require_once __DIR__ . '/php/verificar.php';
require_once __DIR__ . '/php/conexao.php';

$professorId = filter_var(
    $_SESSION['id'] ?? null,
    FILTER_VALIDATE_INT
);

if (!$professorId) {
    session_unset();
    session_destroy();

    header('Location: index.php');
    exit;
}

$nomeProfessor =
    $_SESSION['nome'] ?? 'Professor';

try {
    $stmt = $conexao->prepare("
        SELECT COUNT(*)
        FROM alunos
        WHERE professor_id = :professor_id
    ");

    $stmt->execute([
        ':professor_id' => $professorId
    ]);

    $totalAlunos =
        (int) $stmt->fetchColumn();

    $stmt = $conexao->prepare("
        SELECT COUNT(*)
        FROM turmas
        WHERE professor_id = :professor_id
    ");

    $stmt->execute([
        ':professor_id' => $professorId
    ]);

    $totalTurmas =
        (int) $stmt->fetchColumn();

    $stmt = $conexao->prepare("
        SELECT
            COALESCE(
                SUM(p.licoes_concluidas),
                0
            )
        FROM progresso p
        INNER JOIN alunos a
            ON a.id = p.aluno_id
        WHERE a.professor_id = :professor_id
    ");

    $stmt->execute([
        ':professor_id' => $professorId
    ]);

    $totalLicoes =
        (int) $stmt->fetchColumn();

    $stmt = $conexao->prepare("
        SELECT
            COALESCE(
                AVG(p.acertos),
                0
            )
        FROM progresso p
        INNER JOIN alunos a
            ON a.id = p.aluno_id
        WHERE a.professor_id = :professor_id
    ");

    $stmt->execute([
        ':professor_id' => $professorId
    ]);

    $media =
        (float) $stmt->fetchColumn();

    $media =
        max(0, min(100, $media));

    $stmt = $conexao->prepare("
        SELECT
            t.id,
            t.nome,
            COALESCE(
                AVG(p.acertos),
                0
            ) AS media
        FROM turmas t
        LEFT JOIN alunos a
            ON a.turma_id = t.id
        LEFT JOIN progresso p
            ON p.aluno_id = a.id
        WHERE t.professor_id = :professor_id
        GROUP BY
            t.id,
            t.nome
        ORDER BY
            t.nome ASC
    ");

    $stmt->execute([
        ':professor_id' => $professorId
    ]);

    $nomesTurmas = [];
    $mediasTurmas = [];

    while (
        $linha =
        $stmt->fetch(PDO::FETCH_ASSOC)
    ) {
        $nomesTurmas[] =
            (string) $linha['nome'];

        $mediaTurma =
            (float) $linha['media'];

        $mediaTurma =
            max(
                0,
                min(100, $mediaTurma)
            );

        $mediasTurmas[] =
            round($mediaTurma);
    }
} catch (Throwable $e) {
    error_log(
        'Erro no painel.php: ' .
            $e->getMessage()
    );

    http_response_code(500);

    exit('Não foi possível carregar o painel.');
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
        Painel do Professor | Lumi
    </title>
    <link
        rel="stylesheet"
        href="css/painel.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
</head>

<body>
    <div class="sidebar">
        <img
            src="img/logo.png"
            class="logo"
            alt="Lumi">
        <h2>
            Lumi
        </h2>
        <ul>
            <li class="ativo">
                🏠 Dashboard
            </li>
            <li>
                <a href="alunos.php">
                    👨‍🎓 Alunos
                </a>
            </li>
            <li>
                <a href="turmas.php">
                    📚 Turmas
                </a>
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
            Bem-vindo,
            <?= htmlspecialchars(
                (string) $nomeProfessor,
                ENT_QUOTES,
                'UTF-8'
            ) ?>!
        </h1>
        <p>
            Tenha uma ótima aula hoje.
        </p>
        <div class="cards">
            <div class="card">
                <h3>
                    👨‍🎓 Alunos
                </h3>
                <span>
                    <?= $totalAlunos ?>
                </span>
            </div>
            <div class="card">
                <h3>
                    📚 Turmas
                </h3>
                <span>
                    <?= $totalTurmas ?>
                </span>
            </div>
            <div class="card">
                <h3>
                    ⭐ Média
                </h3>
                <span>
                    <?= round($media) ?>%
                </span>
            </div>
            <div class="card">
                <h3>
                    🏆 Atividades
                </h3>
                <span>
                    <?= $totalLicoes ?>
                </span>
            </div>
        </div>
        <div class="grafico">
            <h2>
                Desempenho das turmas
            </h2>
            <canvas id="grafico"></canvas>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const nomesTurmas =
            <?= json_encode(
                $nomesTurmas,
                JSON_UNESCAPED_UNICODE |
                    JSON_UNESCAPED_SLASHES |
                    JSON_HEX_TAG |
                    JSON_HEX_AMP |
                    JSON_HEX_APOS |
                    JSON_HEX_QUOT
            ) ?>;

        const mediasTurmas =
            <?= json_encode(
                $mediasTurmas,
                JSON_UNESCAPED_UNICODE |
                    JSON_UNESCAPED_SLASHES
            ) ?>;
    </script>

    <script src="js/painel.js"></script>
</body>

</html>