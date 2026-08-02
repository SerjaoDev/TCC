<?php

session_start();

include("conexao.php");
 
$email = $_POST["email"];
$senha = $_POST["senha"];

$sql = "SELECT * FROM professores WHERE email='$email'";

$resultado = $conexao->query($sql);

if($resultado->num_rows > 0){
    $professor = $resultado->fetch_assoc();

    if(password_verify($senha, $professor["senha"])){
        $_SESSION["id"] = $professor["id"];
        $_SESSION["nome"] = $professor["nome"];
        $_SESSION["email"] = $professor["email"];

        header("Location: ../painel.php");
        exit();
    }else{
        echo "Senha incorreta.";
    }
}else{
    echo "Professor não encontrado.";
}

?>