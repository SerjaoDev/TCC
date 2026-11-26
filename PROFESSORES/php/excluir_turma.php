<?php

session_start();

include("conexao.php");

$id = $_GET["id"];
$professor_id = $_SESSION["id"];

$sql = "DELETE p FROM progresso p
INNER JOIN alunos a
ON a.id = p.aluno_id
WHERE a.turma_id='$id'";

$conexao->query($sql);

$sql = "DELETE FROM alunos
WHERE turma_id='$id'
AND professor_id='$professor_id'";

$conexao->query($sql);

$sql = "DELETE FROM turmas
WHERE id='$id'
AND professor_id='$professor_id'";

if($conexao->query($sql)){
    header("Location: ../turmas.php");
    exit();
}else{
    echo "Erro ao excluir turma.";
}

?>