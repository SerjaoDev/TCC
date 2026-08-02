<?php

session_start();

include("conexao.php");


if(!isset($_SESSION["id"])){
    header("Location: ../index.php");
    exit();
}

$id = $_GET["id"];
$professor_id = $_SESSION["id"];

$sql = "DELETE FROM alunos 
        WHERE id='$id' 
        AND professor_id='$professor_id'";

if($conexao->query($sql)){
    header("Location: ../turmas.php");
}else{
    echo "Erro ao excluir aluno.";
}

?>