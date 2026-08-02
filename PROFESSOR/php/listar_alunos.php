<?php

include("conexao.php");

$sql="SELECT * FROM alunos ORDER BY nome";

$resultado=$conexao->query($sql);

?>