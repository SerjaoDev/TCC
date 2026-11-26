<?php

include("php/verificar.php");
include("php/conexao.php");

$professor_id = $_SESSION["id"];

if(!isset($_GET["turma"])){
    header("Location: turmas.php");
    exit();
}

$turma_id = $_GET["turma"];

// 🎯 TOTAL DE FASES DO SEU APP (13 + 20 + 25 + 15 + 12 = 85)
$TOTAL_FASES_APP = 85; 

$sqlTurma = "SELECT * FROM turmas
WHERE id='$turma_id'
AND professor_id='$professor_id'";

$turma = $conexao->query($sqlTurma)->fetch_assoc();

$sql = "SELECT COUNT(*) total
FROM alunos
WHERE turma_id='$turma_id'";

$totalAlunos = $conexao->query($sql)->fetch_assoc()["total"];

$sql = "SELECT SUM(p.licoes_concluidas) total
FROM progresso p
INNER JOIN alunos a
ON a.id=p.aluno_id
WHERE a.turma_id='$turma_id'";

$totalLicoes = $conexao->query($sql)->fetch_assoc()["total"] ?? 0;

// Média geral do progresso da turma (%)
$media = $totalAlunos > 0 ? round(($totalLicoes / ($totalAlunos * $TOTAL_FASES_APP)) * 100) : 0;

// CONSULTA ALUNOS: Busca as lições e porcentagem concluída
$sqlAlunos = "SELECT
a.id,
a.nome,
COALESCE(SUM(p.licoes_concluidas), 0) AS licoes_concluidas,
COALESCE(ROUND((SUM(p.licoes_concluidas) / $TOTAL_FASES_APP) * 100, 0), 0) AS progresso_porcentagem

FROM alunos a
LEFT JOIN progresso p
ON p.aluno_id = a.id
WHERE a.turma_id='$turma_id'
GROUP BY a.id, a.nome
ORDER BY a.nome";

$alunos = $conexao->query($sqlAlunos);

// CONSULTA GRÁFICO: Pega a porcentagem de progresso para o gráfico
$sqlGrafico = "SELECT 
a.nome,
COALESCE(ROUND((SUM(p.licoes_concluidas) / $TOTAL_FASES_APP) * 100, 0), 0) AS progresso_porcentagem
FROM alunos a
LEFT JOIN progresso p
ON p.aluno_id = a.id
WHERE a.turma_id='$turma_id'
GROUP BY a.id, a.nome
ORDER BY a.nome";

$resultadoGrafico = $conexao->query($sqlGrafico);
$nomesAlunos = [];
$acertosAlunos = [];

while($linha = $resultadoGrafico->fetch_assoc()){
    $nomesAlunos[] = $linha["nome"];
    $acertosAlunos[] = $linha["progresso_porcentagem"] ?? 0;
}

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Relatório da Turma | Lumi</title>
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
        <a href="painel.php">🏠 Dashboard</a>
        <a href="turmas.php">📚 Turmas</a>
        <a href="#" class="ativo">📊 Relatório da Turma</a>
        <a href="logout.php">🚪 Sair</a>
    </nav>
</header>

<div class="conteudo">
    <header class="page-header">
        <div>
            <span class="badge"><i></i> Desempenho</span>
            <h1>Relatório - <?php echo $turma["nome"]; ?></h1>
            <p>Acompanhe a evolução dos alunos desta turma.</p>
        </div>
    </header>

<div class="cards">

<div class="card">
<h3>Média Geral</h3>
<span><?php echo $media; ?>%</span>
</div>


<div class="card">
<h3>Alunos</h3>
<span><?php echo $totalAlunos; ?></span>
</div>

<div class="card">
<h3>Turma</h3>
<span><?php echo $turma["nome"]; ?></span>
</div>

</div>

<div class="grafico">

<h2>Progresso Geral da Turma</h2>

<canvas id="grafico"></canvas>

</div>

<div class="lista-alunos">
<div class="topo-alunos">

<h2>Alunos da Turma</h2>

<a href="novo_aluno.php?turma=<?php echo $turma_id; ?>" class="btn novo-aluno">+ Adicionar Aluno</a>

</div>
<div class="table-wrap">
<table>
<thead>
<tr>

<th>Aluno</th>
<th>⭐ Estrelas</th>
<th>📚 Lições</th>
<th>📊 Progresso Total</th>
<th>Ações</th>

</tr>

</thead>
<tbody>

<?php 
while($aluno = $alunos->fetch_assoc()){ 
    $fases = $aluno["licoes_concluidas"];

    // Lógica das estrelas conforme os marcos exatos de cada nível
    if ($fases >= 85) {
        $estrelas = 5; // Nível 5 completo
    } elseif ($fases >= 73) {
        $estrelas = 4; // Nível 4 completo
    } elseif ($fases >= 58) {
        $estrelas = 3; // Nível 3 completo
    } elseif ($fases >= 33) {
        $estrelas = 2; // Nível 2 completo
    } elseif ($fases >= 13) {
        $estrelas = 1; // Nível 1 completo
    } else {
        $estrelas = 0;
    }
?>

<tr>
<td><?php echo $aluno["nome"]; ?></td>
<td><?php echo $estrelas; ?></td>
<td><?php echo $aluno["licoes_concluidas"]; ?></td>
<td><?php echo $aluno["progresso_porcentagem"]; ?>%</td>

<td>

<a href="aluno.php?id=<?php echo $aluno["id"]; ?>" class="btn abrir">Abrir</a>

</td>
</tr>

<?php } ?>

</tbody>
</table>
</div>
</div>
</div>

<script>
const ctx = document.getElementById('grafico');

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($nomesAlunos); ?>,
        datasets: [{
            label: 'Progresso Total (%)',
            data: <?php echo json_encode($acertosAlunos); ?>,
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
                        return context.raw + '% concluído';
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