<?php

session_start();

if(!isset($_SESSION["id"])){

    header("Location: ../index.php");
    exit();
}

include("conexao.php");

$professor_id = $_SESSION["id"];
$nome = $_POST["nome"];
$usuario = $_POST["usuario"];
$senha_original = $_POST["senha"];
$senha = password_hash($senha_original, PASSWORD_DEFAULT);
$data = $_POST["data_nascimento"];
$turma_id = $_POST["turma_id"];

$sql = "INSERT INTO alunos
(professor_id,nome,usuario,senha,senha_visivel,data_nascimento,turma_id)

VALUES
('$professor_id','$nome','$usuario','$senha','$senha_original','$data','$turma_id')";

if($conexao->query($sql)){
    $aluno_id = $conexao->insert_id;
    $sqlProgresso = "INSERT INTO progresso
    (aluno_id)
    VALUES
    ('$aluno_id')";
    $conexao->query($sqlProgresso);
header("Location: ../turmas.php");
}else{
echo "Erro ao cadastrar aluno: ".$conexao->error;
}

?>