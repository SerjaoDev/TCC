<?php
session_start();

require_once __DIR__ . "/conexao.php";

if (!isset($_SESSION["professor_id"])) {
    header("Location: ../index.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../turmas.php?erro=metodo");
    exit();
}

$professorId = $_SESSION["professor_id"];
$alunoId = $_POST["id"] ?? "";

if (!filter_var($alunoId, FILTER_VALIDATE_INT)) {
    header("Location: ../turmas.php?erro=aluno");
    exit();
}

$sql = "DELETE FROM alunos WHERE id = :aluno_id AND professor_id = :professor_id";

try {
    $stmt = $conexao->prepare($sql);

    $stmt->execute([
        ":aluno_id" => $alunoId,
        ":professor_id" => $professorId
    ]);

    if ($stmt->rowCount() === 0) {
        header("Location: ../turmas.php?erro=aluno_nao_encontrado");
        exit();
    }

    header("Location: ../turmas.php?sucesso=aluno_excluido");
    exit();
} catch (PDOException $erro) {
    http_response_code(500);

    echo "Erro ao excluir aluno.";

    exit();
}
?>