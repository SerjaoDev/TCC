<?php

include("../php/conexao.php");


header("Content-Type: application/json");



$sql = "SELECT * FROM licoes ORDER BY nivel ASC";


$resultado = $conexao->query($sql);



$licoes = [];



while($licao = $resultado->fetch_assoc()){


    $licoes[] = [

        "id"=>$licao["id"],

        "titulo"=>$licao["titulo"],

        "descricao"=>$licao["descricao"],

        "nivel"=>$licao["nivel"],

        "categoria"=>$licao["categoria"]

    ];


}



echo json_encode([

    "sucesso"=>true,

    "licoes"=>$licoes

]);



?>