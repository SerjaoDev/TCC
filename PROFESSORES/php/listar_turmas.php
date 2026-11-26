<?php

include("conexao.php");

$professor_id = $_SESSION["id"];

$sql = "SELECT 
turmas.*,
COUNT(alunos.id) AS quantidade_alunos
FROM turmas
LEFT JOIN alunos
ON turmas.id = alunos.turma_id 
WHERE turmas.professor_id='$professor_id'
GROUP BY turmas.id
ORDER BY turmas.nome ASC";

$turmas = $conexao->query($sql);

?>