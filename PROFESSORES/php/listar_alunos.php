<?php
include("conexao.php");

// Busca o aluno e traz o total acumulado de progresso
$sql = "SELECT 
            a.id, 
            a.nome, 
            COALESCE(p.licoes_concluidas, 0) AS licoes_concluidas,
            COALESCE(p.acertos, 0) AS acertos
        FROM alunos a
        LEFT JOIN progresso p ON a.id = p.aluno_id
        ORDER BY a.nome";

$resultado = $conexao->query($sql);
?>