<?php
include("php/verificar.php");
include("php/conexao.php");

$professor_id = $_SESSION["id"] ?? null;

if (!$professor_id) {
    header("Location: index.php");
    exit;
}

// Total de fases do app
$TOTAL_FASES_APP = 85;

// 1. Total de alunos do professor (Prepared Statement)
$stmt = $conexao->prepare("SELECT COUNT(*) as total FROM alunos WHERE professor_id = ?");
$stmt->bind_param("i", $professor_id);
$stmt->execute();
$totalAlunos = $stmt->get_result()->fetch_assoc()["total"] ?? 0;

// 2. Total de turmas do professor (Prepared Statement)
$stmt = $conexao->prepare("SELECT COUNT(*) as total FROM turmas WHERE professor_id = ?");
$stmt->bind_param("i", $professor_id);
$stmt->execute();
$totalTurmas = $stmt->get_result()->fetch_assoc()["total"] ?? 0;

// 3. Total de lições concluídas por todos os alunos do professor (Prepared Statement)
$sqlLicoes = "SELECT SUM(p.licoes_concluidas) as total
              FROM progresso p
              INNER JOIN alunos a ON a.id = p.aluno_id
              WHERE a.professor_id = ?";
$stmt = $conexao->prepare($sqlLicoes);
$stmt->bind_param("i", $professor_id);
$stmt->execute();
$totalLicoes = $stmt->get_result()->fetch_assoc()["total"] ?? 0;

// 4. Média geral de progresso do professor (%)
$media = ($totalAlunos > 0) ? round(($totalLicoes / ($totalAlunos * $TOTAL_FASES_APP)) * 100) : 0;

// 5. Consulta para o Gráfico: Progresso Médio de Cada Turma (%) (Prepared Statement)
$sqlGrafico = "SELECT 
    t.nome,
    COUNT(a.id) AS qtd_alunos,
    COALESCE(SUM(p.licoes_concluidas), 0) AS total_fases_turma
FROM turmas t
LEFT JOIN alunos a ON a.turma_id = t.id
LEFT JOIN progresso p ON p.aluno_id = a.id
WHERE t.professor_id = ?
GROUP BY t.id, t.nome
ORDER BY t.nome";

$stmt = $conexao->prepare($sqlGrafico);
$stmt->bind_param("i", $professor_id);
$stmt->execute();
$resultadoGrafico = $stmt->get_result();

$nomesTurmas = [];
$mediasTurmas = [];

while ($linha = $resultadoGrafico->fetch_assoc()) {
    $nomesTurmas[] = $linha["nome"];
    $qtdAlunosTurma = $linha["qtd_alunos"];
    $fasesTurma = $linha["total_fases_turma"];
    
    if ($qtdAlunosTurma > 0) {
        $porcentagemTurma = round(($fasesTurma / ($qtdAlunosTurma * $TOTAL_FASES_APP)) * 100);
    } else {
        $porcentagemTurma = 0;
    }
    
    $mediasTurmas[] = $porcentagemTurma;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Painel do Professor | Lumi</title>
<link rel="stylesheet" href="css/app.css?v=3">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        <a href="painel.php" class="ativo">🏠 Dashboard</a>
        <a href="turmas.php">📚 Turmas</a>
        <a href="logout.php">🚪 Sair</a>
    </nav>
</header>

<div class="conteudo">
    <header class="page-header">
        <div>
            <span class="badge"><i></i> Visão geral</span>
            <h1>Bem-vindo, <?php echo htmlspecialchars($_SESSION['nome'] ?? 'Professor'); ?>!</h1>
            <p>Tenha uma ótima aula hoje.</p>
        </div>
    </header>

    <div class="cards">
        <div class="card">
            <h3>👨‍🎓 Alunos</h3>
            <span><?php echo $totalAlunos; ?></span>
        </div>
        <div class="card">
            <h3>📚 Turmas</h3>
            <span><?php echo $totalTurmas; ?></span>
        </div>
        <div class="card">
            <h3>⭐ Média Geral</h3>
            <span><?php echo $media; ?>%</span>
        </div>
        <div class="card">
            <h3>🏆 Atividades</h3>
            <span><?php echo $totalLicoes; ?></span>
        </div>
    </div>

    <div class="grafico">
        <h2>Desempenho Geral por Turma</h2>
        <canvas id="grafico"></canvas>
    </div>
</div>

<script>
const ctx = document.getElementById('grafico');

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($nomesTurmas); ?>,
        datasets: [{
            label: 'Progresso Médio (%)',
            data: <?php echo json_encode($mediasTurmas); ?>,
            backgroundColor: '#16a37f',
            borderColor: '#0d7d61',
            borderWidth: 1,
            borderRadius: 8,
            borderSkipped: false
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.raw + '% de progresso médio';
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                max: 100,
                ticks: {
                    callback: function(value) {
                        return value + '%';
                    }
                }
            }
        }
    }
});
</script>

</body>
</html>