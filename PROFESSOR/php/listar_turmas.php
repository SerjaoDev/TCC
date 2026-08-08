<?php
session_start();

require_once __DIR__ . "/conexao.php";

if (!isset($_SESSION["professor_id"])) {
    header("Location: ../index.html");
    exit();
}

$professorId = $_SESSION["professor_id"];

$sql = "SELECT t.id,t.nome,t.descricao,t.professor_id,COUNT(a.id) AS quantidade_alunos FROM turmas t LEFT JOIN alunos a ON a.turma_id = t.id WHERE t.professor_id = :professor_id GROUP BY t.id,t.nome,t.descricao,t.professor_id ORDER BY t.nome ASC";

try {
    $stmt = $conexao->prepare($sql);

    $stmt->execute([
        ":professor_id" => $professorId
    ]);

    $turmas = $stmt;
} catch (PDOException $erro) {
    http_response_code(500);

    echo "Erro ao carregar as turmas.";

    exit();
}
?>