<?php

include("php/verificar.php");

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Novo Aluno | Lumi</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="login-card">

<h1>Novo Aluno</h1>
<p>Cadastre um aluno para acessar o aplicativo.</p>

<form action="php/cadastrar_aluno.php" method="POST">

<input 
type="text" 
name="nome"
placeholder="Nome do aluno"
required>

<input 
type="text" 
name="usuario"
placeholder="Usuário de login"
required>

<input 
type="password"
name="senha"
placeholder="Senha do aluno"
required>

<input 
type="date"
name="data_nascimento">

<input 
type="hidden" 
name="turma_id"
value="<?php echo $_GET['turma']; ?>">

<button class="entrar">
Criar aluno
</button>

</form>

</div>

</body>
</html>