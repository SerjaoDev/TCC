<?php

include("../php/conexao.php");


header("Content-Type: application/json");



$aluno_id = $_POST["aluno_id"];

$licao_id = $_POST["licao_id"];

$resultado = $_POST["resultado"];

$pontuacao = $_POST["pontuacao"];

$tempo = $_POST["tempo_gasto"];



// salva histórico da lição

$sql = "INSERT INTO desempenho

(aluno_id, licao_id, resultado, pontuacao, tempo_gasto)

VALUES

('$aluno_id',
'$licao_id',
'$resultado',
'$pontuacao',
'$tempo')";



if($conexao->query($sql)){



    // atualiza progresso geral


    $sqlAtualizar = "UPDATE progresso SET

    licoes_concluidas = licoes_concluidas + 1,

    acertos = acertos + '$pontuacao'

    WHERE aluno_id='$aluno_id'";



    $conexao->query($sqlAtualizar);



    echo json_encode([

        "sucesso"=>true,

        "mensagem"=>"Progresso salvo"

    ]);



}else{


    echo json_encode([

        "sucesso"=>false,

        "mensagem"=>"Erro ao salvar"

    ]);

}



?>