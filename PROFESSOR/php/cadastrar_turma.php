<?php
session_start();

require_once __DIR__ . "/conexao.php";

if (!isset($_SESSION["professor_id"])) {
    header("Location: ../index.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../turmas.php");
    exit();
}

$professorId = $_SESSION["professor_id"];
$nome = trim($_POST["nome"] ?? "");
$descricao = trim($_POST["descricao"] ?? "");

if ($nome === "") {
    header("Location: ../adicionar_turma.php?erro=nome");
    exit();
}

$sql = "INSERT INTO turmas (professor_id,nome,descricao) VALUES (:professor_id,:nome,:descricao)";

try {
    $stmt = $conexao->prepare($sql);

    $stmt->execute([
        ":professor_id" => $professorId,
        ":nome" => $nome,
        ":descricao" => $descricao !== "" ? $descricao : null
    ]);
    
    header("Location: ../turmas.php?sucesso=turma_criada");
    exit();
} catch (PDOException $erro) {
    http_response_code(500);

    echo "Erro ao criar a turma.";

    exit();
}
?>