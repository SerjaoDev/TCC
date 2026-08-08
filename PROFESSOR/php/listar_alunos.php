<?php
session_start();

require_once __DIR__ . "/conexao.php";

if (!isset($_SESSION["professor_id"])) {
    header("Location: ../index.html");
    exit();
}

$professorId = $_SESSION["professor_id"];

$sql = "SELECT a.id,a.nome,a.usuario,a.foto,a.data_nascimento,a.turma_id,t.nome AS turma_nome,a.data_cadastro FROM alunos a LEFT JOIN turmas t ON t.id = a.turma_id WHERE a.professor_id = :professor_id ORDER BY a.nome ASC";

try {
    $stmt = $conexao->prepare($sql);

    $stmt->execute([
        ":professor_id" => $professorId
    ]);
    
    $alunos = $stmt;
} catch (PDOException $erro) {
    http_response_code(500);

    echo "Erro ao carregar os alunos.";

    exit();
}
?>