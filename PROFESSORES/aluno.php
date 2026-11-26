<?php
include("php/verificar.php");
include("php/conexao.php");

date_default_timezone_set('America/Sao_Paulo');

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$professor_id = $_SESSION["id"] ?? null;

if (!$id || !$professor_id) {
    header("Location: turmas.php");
    exit;
}

// Busca dados do aluno
$stmt = $conexao->prepare("SELECT * FROM alunos WHERE id = ? AND professor_id = ?");
$stmt->bind_param("ii", $id, $professor_id);
$stmt->execute();
$aluno = $stmt->get_result()->fetch_assoc();

if (!$aluno) {
    header("Location: turmas.php");
    exit;
}

// 2. Busca a SOMA TOTAL de progresso do aluno
$stmt_totais = $conexao->prepare("
    SELECT 
        COALESCE(SUM(estrelas), 0) as total_estrelas,
        COALESCE(SUM(licoes_concluidas), 0) as total_licoes,
        MAX(ultimo_acesso) as ultimo_acesso_prog
    FROM progresso 
    WHERE aluno_id = ?
");
$stmt_totais->bind_param("i", $id);
$stmt_totais->execute();
$totais = $stmt_totais->get_result()->fetch_assoc();

$estrelas = (int)$totais['total_estrelas'];
$licoes   = (int)$totais['total_licoes'];

// Cálculo do Progresso Geral (%)
$TOTAL_FASES_APP = 85;
$porcentagem_total = ($TOTAL_FASES_APP > 0) ? round(($licoes / $TOTAL_FASES_APP) * 100) : 0;

$ultimo_acesso = $totais['ultimo_acesso_prog'] ?? $aluno['ultimo_acesso'] ?? null;

// Configuração dos Níveis
$niveis_config = [
    1 => ['nome' => 'Nível 1: O Alfabeto', 'total' => 13, 'icone' => '🔤'],
    2 => ['nome' => 'Nível 2: As Sílabas Fáceis', 'total' => 20, 'icone' => '🧩'],
    3 => ['nome' => 'Nível 3: As Sílabas Complexas', 'total' => 25, 'icone' => '🧠'],
    4 => ['nome' => 'Nível 4: Palavras e Pequenas Frases', 'total' => 20, 'icone' => '📖'],
    5 => ['nome' => 'Nível 5: Leitura Fluente', 'total' => 15, 'icone' => '🏆']
];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($aluno['nome'] ?? '') ?> - Lumi</title>
    <link rel="stylesheet" href="css/app.css?v=3">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

    <div class="background" aria-hidden="true">
        <span class="orb orb-1"></span>
        <span class="orb orb-2"></span>
        <span class="orb orb-3"></span>
        <span class="grid"></span>
    </div>

    <header class="topbar">
        <a href="painel.php" class="topbar-brand">
            <img src="img/logo.png" class="topbar-logo" alt="Logo Lumi">
            <span>Lumi</span>
        </a>

        <nav class="topbar-nav">
            <a href="painel.php">🏠 Dashboard</a>
            <a href="turmas.php">📚 Turmas</a>
            <a href="logout.php">🚪 Sair</a>
        </nav>
    </header>

    <main class="conteudo">
        <a href="javascript:history.back()" class="voltar">← Voltar</a>

        <header class="page-header">
            <div>
                <span class="badge"><i></i> Progresso do aluno</span>
                <h1><?= htmlspecialchars($aluno['nome'] ?? '') ?></h1>
                <p>
                    ⏱️ Último acesso ao app:
                    <?= (!empty($ultimo_acesso) && $ultimo_acesso !== '0000-00-00 00:00:00')
                        ? date('d/m/Y \à\s H:i', strtotime($ultimo_acesso))
                        : 'Nenhum acesso registrado' ?>
                </p>
            </div>
        </header>

        <div class="info-grid">
            <div class="info-item">
                <span>Usuário de login</span>
                <strong><?= htmlspecialchars($aluno['usuario'] ?? 'N/A') ?></strong>
            </div>
            <div class="info-item">
                <span>Senha</span>
                <strong><?= htmlspecialchars($aluno['senha_visivel'] ?? 'N/A') ?></strong>
            </div>
        </div>

        <div class="cards">
            <div class="card">
                <h3>⭐ Estrelas</h3>
                <span><?= $estrelas ?>/5</span>
            </div>
            <div class="card">
                <h3>🎴 Lições</h3>
                <span><?= $licoes ?>/85</span>
            </div>
            <div class="card">
                <h3>📊 Progresso</h3>
                <span><?= $porcentagem_total ?>%</span>
            </div>
        </div>

        <section class="historico">
            <h2>Progresso por Nível</h2>

            <?php foreach ($niveis_config as $n_id => $n_info):
                if ($n_id === 1) {
                    $fases_no_nivel = min($licoes, $n_info['total']);
                } else {
                    $fases_no_nivel = 0;
                }

                $porcentagem_nivel = round(($fases_no_nivel / $n_info['total']) * 100);
            ?>
                <div class="nivel">
                    <div class="nivel-topo">
                        <span><?= $n_info['icone'] ?> <?= $n_info['nome'] ?></span>
                        <span><?= $fases_no_nivel ?> / <?= $n_info['total'] ?> fases (<?= $porcentagem_nivel ?>%)</span>
                    </div>
                    <div class="barra">
                        <span style="width: <?= $porcentagem_nivel ?>%"></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </section>
    </main>

</body>
</html>
