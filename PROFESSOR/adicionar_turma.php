<?php

include("php/verificar.php");

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Nova Turma | Lumi</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="login-card">
<h1>Nova Turma</h1>

<form action="php/cadastrar_turma.php" method="POST">

<input 
type="text"
name="nome"
placeholder="Nome da turma" required>

<button class="entrar">
Criar Turma
</button>

</form>

<div class="links">
<a href="turmas.php">
Voltar
</a>
</div>

</div>

</body>
</html>