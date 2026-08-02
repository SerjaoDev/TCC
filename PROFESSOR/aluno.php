<?php

include("php/verificar.php");
include("php/conexao.php");

$id = $_GET["id"];
$sql = "SELECT * FROM alunos 
WHERE id='$id'";
$resultado = $conexao->query($sql);
$aluno = $resultado->fetch_assoc();

if(!$aluno){
    echo "Aluno não encontrado.";
    exit();
}

$sqlProgresso = "SELECT * FROM progresso
WHERE aluno_id='$id'";
$dados = $conexao->query($sqlProgresso);
$progresso = $dados->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Aluno | Lumi</title>
<link rel="stylesheet" href="css/aluno.css">
</head>
<body>

<div class="conteudo">
<h1>
<?php echo $aluno["nome"]; ?>
</h1>

<div class="cards">
<div class="card">

<h3>⭐ Estrelas</h3>

<span>
<?php echo $progresso["estrelas"] ?? 0; ?>
</span>

</div>

<div class="card">
<h3>🪙 Moedas</h3>

<span>
<?php echo $progresso["moedas"] ?? 0; ?>
</span>

</div>

<div class="card">

<h3>📚 Lições</h3>

<span>
<?php echo $progresso["licoes_concluidas"] ?? 0; ?>
</span>

</div>

<div class="card">

<h3>🎯 Acertos</h3>

<span>
<?php echo $progresso["acertos"] ?? 0; ?>%
</span>

</div>

</div>

<div class="historico">

<h2>
Histórico de atividades
</h2>

<p>
O progresso aparecerá aqui quando o aluno usar o aplicativo.
</p>

</div>

<a href="dados_aluno.php?id=<?php echo $aluno['id']; ?>">
<button class="acesso">
🔑 Ver acesso do aluno
</button>
</a>

</div>

</body>
</html>