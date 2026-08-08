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
$turmaId = $_POST["id"] ?? "";

if (!filter_var($turmaId, FILTER_VALIDATE_INT)) {
    header("Location: ../turmas.php?erro=turma");
    exit();
}

$sql = "DELETE FROM turmas WHERE id = :turma_id AND professor_id = :professor_id";

try {
    $stmt = $conexao->prepare($sql);

    $stmt->execute([
        ":turma_id" => $turmaId,
        ":professor_id" => $professorId
    ]);

    if ($stmt->rowCount() === 0) {
        header(
            "Location: ../turmas.php?erro=turma_nao_encontrada"
        );

        exit();
    }

    header(
        "Location: ../turmas.php?sucesso=turma_excluida"
    );

    exit();
} catch (PDOException $erro) {
    http_response_code(500);

    echo "Erro ao excluir turma.";

    exit();
}
?>