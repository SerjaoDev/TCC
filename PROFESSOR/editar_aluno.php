<?php

include("php/verificar.php");
include("php/conexao.php");

$id = $_GET["id"];
$professor_id = $_SESSION["id"];
$sql = "SELECT * FROM alunos 
        WHERE id='$id' 
        AND professor_id='$professor_id'";
$resultado = $conexao->query($sql);
$aluno = $resultado->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Editar Aluno | Lumi</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="login-card">

<h1>Editar Aluno</h1>

<form action="php/editar_aluno.php" method="POST">

<input 
type="hidden" 
name="id"
value="<?php echo $aluno['id']; ?>">

<input 
type="text"
name="nome"
value="<?php echo $aluno['nome']; ?>" required>

<input 
type="text"
name="usuario"
value="<?php echo $aluno['usuario']; ?>" required>

<input 
type="date"
name="data_nascimento"
value="<?php echo $aluno['data_nascimento']; ?>" required>

<input 
type="text"
name="turma"
value="<?php echo $aluno['turma']; ?>" required>

<button class="entrar">
Salvar Alterações
</button>

</form>

<div class="links">

<a href="javascript:history.back()">
Voltar
</a>

</div>

</div>

</body>
</html>