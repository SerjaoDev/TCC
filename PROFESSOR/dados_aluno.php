<?php

include("php/verificar.php");
include("php/conexao.php");

$id = $_GET["id"];
$sql = "SELECT * FROM alunos WHERE id='$id'";
$resultado = $conexao->query($sql);
$aluno = $resultado->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Acesso do Aluno | Lumi</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="login-card">

<h1>
Acesso do Aluno
</h1>

<p>
Entregue essas informações para o aluno acessar o aplicativo.
</p>

<h3>
Nome:
</h3>

<p>
<?php echo $aluno["nome"]; ?>
</p>

<h3>
Usuário:
</h3>

<p>
<?php echo $aluno["usuario"]; ?>
</p>

<h3>
Senha:
</h3>

<p>
<?php echo $aluno["senha_visivel"]; ?>
</p>

<a href="javascript:history.back()">

<button class="entrar">
Voltar
</button>

</a>

</div>

</body>
</html>