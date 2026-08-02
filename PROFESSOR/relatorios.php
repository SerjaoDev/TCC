<?php

include("php/verificar.php");
include("php/conexao.php");

$professor_id = $_SESSION["id"];

if(!isset($_GET["turma"])){

    header("Location: turmas.php");
    exit();
}

$turma_id = $_GET["turma"];

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

$sql = "SELECT AVG(p.acertos) media
FROM progresso p
INNER JOIN alunos a
ON a.id=p.aluno_id
WHERE a.turma_id='$turma_id'";

$media = $conexao->query($sql)->fetch_assoc()["media"] ?? 0;

$sqlAlunos = "SELECT
a.id,
a.nome,
p.estrelas,
p.licoes_concluidas,
p.acertos

FROM alunos a
LEFT JOIN progresso p
ON p.aluno_id = a.id
WHERE a.turma_id='$turma_id'
ORDER BY a.nome";

$alunos = $conexao->query($sqlAlunos);

$sqlGrafico = "SELECT 
a.nome,
p.acertos
FROM alunos a
LEFT JOIN progresso p
ON p.aluno_id = a.id
WHERE a.turma_id='$turma_id'
ORDER BY a.nome";

$resultadoGrafico = $conexao->query($sqlGrafico);
$nomesAlunos = [];
$acertosAlunos = [];

while($linha = $resultadoGrafico->fetch_assoc()){
    $nomesAlunos[] = $linha["nome"];
    $acertosAlunos[] = $linha["acertos"] ?? 0;
}

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Relatório da Turma | Lumi</title>
<link rel="stylesheet" href="css/relatorios.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<div class="sidebar">

<img src="img/logo.png" class="logo">

<h2>Lumi</h2>

<ul>
<li><a href="painel.php">🏠 Dashboard</a></li>
<li><a href="turmas.php">📚 Turmas</a></li>
<li class="ativo">📊 Relatório da Turma</li>
<li><a href="logout.php">🚪 Sair</a></li>
</ul>

</div>

<div class="conteudo">

<h1>Relatório - <?php echo $turma["nome"]; ?></h1>

<div class="cards">

<div class="card">
<h3>Média Geral</h3>
<span><?php echo round($media); ?>%</span>
</div>

<div class="card">
<h3>Atividades</h3>
<span><?php echo $totalLicoes; ?></span>
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

<h2>Desempenho da Turma</h2>

<canvas id="grafico"></canvas>

</div>

<div class="lista-alunos">
<div class="topo-alunos">

<h2>Alunos da Turma</h2>

<a href="novo_aluno.php?turma=<?php echo $turma_id; ?>">
<button class="novo-aluno">
+ Adicionar Aluno
</button>
</a>

</div>
<table>
<thead>
<tr>

<th>Aluno</th>
<th>⭐ Estrelas</th>
<th>📚 Lições</th>
<th>🎯 Acertos</th>
<th>Ações</th>

</tr>

</thead>
<tbody>

<?php while($aluno = $alunos->fetch_assoc()){ ?>

<tr>
<td><?php echo $aluno["nome"]; ?></td>
<td><?php echo $aluno["estrelas"] ?? 0; ?></td>
<td><?php echo $aluno["licoes_concluidas"] ?? 0; ?></td>
<td><?php echo $aluno["acertos"] ?? 0; ?>%</td>

<td>

<a href="aluno.php?id=<?php echo $aluno["id"]; ?>">
<button class="abrir">Abrir</button>
</a>

</td>
</tr>

<?php } ?>

</tbody>
</table>
</div>
</div>
<script>

<script>

const ctx = document.getElementById('grafico');

new Chart(ctx,{
type:'bar',
data:{
labels: <?php echo json_encode($nomesAlunos); ?>,
datasets:[{
label:'Acertos (%)',
data: <?php echo json_encode($acertosAlunos); ?>,
borderWidth:2
}]
}
});

</script>
</script>

</body>
</html>