<?php
session_start();
require_once 'conexao.php';

date_default_timezone_set('America/Sao_Paulo');

if (isset($_SESSION['aluno_id'])) {
    $aluno_id = $_SESSION['aluno_id'];
    
    $stmt = $pdo->prepare("UPDATE alunos SET ultimo_acesso = NOW() WHERE id = :id");
    $stmt->execute([':id' => $aluno_id]);
}

session_unset();
session_destroy();

header("Location: trilha.php");
exit;
?>