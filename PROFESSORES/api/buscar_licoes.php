<?php

header("Content-Type: application/json");

include("../php/conexao.php");


$sql = "SELECT * FROM licoes ORDER BY nivel ASC";


$resultado = $conexao->query($sql);


$licoes = [];


while($linha = $resultado->fetch_assoc()){

    $licoes[] = $linha;

}


echo json_encode([

    "sucesso" => true,

    "licoes" => $licoes

]);


?>