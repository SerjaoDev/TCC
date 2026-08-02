<?php

include("php/verificar.php");
include("php/conexao.php");

$professor_id = $_SESSION["id"];

$sql = "SELECT COUNT(*) total
FROM alunos
WHERE professor_id='$professor_id'";

$totalAlunos = $conexao->query($sql)->fetch_assoc()["total"];

$sql = "SELECT COUNT(*) total
FROM turmas
WHERE professor_id='$professor_id'";

$totalTurmas = $conexao->query($sql)->fetch_assoc()["total"];

$sql = "SELECT SUM(p.licoes_concluidas) total
FROM progresso p
INNER JOIN alunos a
ON a.id=p.aluno_id
WHERE a.professor_id='$professor_id'";

$totalLicoes = $conexao->query($sql)->fetch_assoc()["total"] ?? 0;

$sql = "SELECT AVG(p.acertos) media
FROM progresso p
INNER JOIN alunos a
ON a.id=p.aluno_id
WHERE a.professor_id='$professor_id'";

$media = $conexao->query($sql)->fetch_assoc()["media"] ?? 0;

$sqlGrafico = "SELECT 
t.nome,
AVG(p.acertos) media
FROM turmas t
LEFT JOIN alunos a
ON a.turma_id = t.id
LEFT JOIN progresso p
ON p.aluno_id = a.id
WHERE t.professor_id='$professor_id'
GROUP BY t.id";

$resultadoGrafico = $conexao->query($sqlGrafico);
$nomesTurmas = [];
$mediasTurmas = [];
while($linha = $resultadoGrafico->fetch_assoc()){
    $nomesTurmas[] = $linha["nome"];
    $mediasTurmas[] = round($linha["media"] ?? 0);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Painel do Professor</title>
<link rel="stylesheet" href="css/painel.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>

<div class="sidebar">
    <img src="img/logo.png" class="logo">
    <h2>Lumi</h2>

  <ul>
<li class="ativo">
    🏠 Dashboard
</li>

<li>
    <a href="turmas.php">📚 Turmas</a>
</li>

<li>
    <a href="logout.php">🚪 Sair</a>
</li>
</ul>

</div>
<div class="conteudo">
    <h1>Bem-vindo, <?php echo $_SESSION['nome']; ?>!</h1>
    <p>Tenha uma ótima aula hoje.</p>
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
            <h3>⭐ Média</h3>
            <span><?php echo round($media); ?>%</span>
        </div>
        <div class="card">
            <h3>🏆 Atividades</h3>
            <span><?php echo $totalLicoes; ?></span>
        </div>
    </div>
    <div class="grafico">
        <h2>Desempenho dos alunos</h2>
        <canvas id="grafico"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const nomesTurmas = <?php echo json_encode($nomesTurmas); ?>;
const mediasTurmas = <?php echo json_encode($mediasTurmas); ?>;
</script>

<script src="js/painel.js"></script>

</body>
</html>