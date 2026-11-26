<?php

session_start();

include("conexao.php");

$professor_id = $_SESSION["id"];
$nome = $_POST["nome"];

$sql = "INSERT INTO turmas
(professor_id,nome)

VALUES
('$professor_id','$nome')";

if($conexao->query($sql)){
header("Location: ../turmas.php");
}else{
echo "Erro ao criar turma.";
}
?>