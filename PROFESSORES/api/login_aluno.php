<?php

include("../php/conexao.php");


header("Content-Type: application/json");


$usuario = $_POST["usuario"];
$senha = $_POST["senha"];



$sql = "SELECT * FROM alunos WHERE usuario='$usuario'";


$resultado = $conexao->query($sql);



if($resultado->num_rows > 0){


    $aluno = $resultado->fetch_assoc();



    if(password_verify($senha, $aluno["senha"])){


        $sqlProgresso = "SELECT * FROM progresso 
        WHERE aluno_id='".$aluno["id"]."'";


        $resultadoProgresso = $conexao->query($sqlProgresso);


        $progresso = $resultadoProgresso->fetch_assoc();



        echo json_encode([

            "sucesso"=>true,

            "aluno"=>[

                "id"=>$aluno["id"],

                "nome"=>$aluno["nome"],

                "turma"=>$aluno["turma_id"],

                "estrelas"=>$progresso["estrelas"] ?? 0,

                "moedas"=>$progresso["moedas"] ?? 0,

                "licoes"=>$progresso["licoes_concluidas"] ?? 0,

                "acertos"=>$progresso["acertos"] ?? 0

            ]

        ]);



    }else{


        echo json_encode([

            "sucesso"=>false,

            "mensagem"=>"Senha incorreta"

        ]);


    }



}else{


    echo json_encode([

        "sucesso"=>false,

        "mensagem"=>"Aluno não encontrado"

    ]);


}


?>