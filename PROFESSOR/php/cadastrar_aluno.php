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
$usuario = trim($_POST["usuario"] ?? "");
$senhaOriginal = $_POST["senha"] ?? "";
$dataNascimento = $_POST["data_nascimento"] ?? "";
$turmaId = $_POST["turma_id"] ?? "";

if ($nome === "" || $usuario === "" || $senhaOriginal === "") {
    header("Location: ../novo_aluno.php?erro=preencha&turma=" . urlencode($turmaId));
    exit();
}

if (strlen($senhaOriginal) < 4) {
    header("Location: ../novo_aluno.php?erro=senha_curta&turma=" . urlencode($turmaId));
    exit();
}

if ($turmaId !== "") {
    if (!filter_var($turmaId, FILTER_VALIDATE_INT)) {
        header("Location: ../turmas.php?erro=turma");
        exit();
    }

    $sqlTurma = "SELECT id FROM turmas WHERE id = :turma_id AND professor_id = :professor_id LIMIT 1";

    $stmtTurma = $conexao->prepare($sqlTurma);

    $stmtTurma->execute([
        ":turma_id" => $turmaId,
        ":professor_id" => $professorId
    ]);

    if (!$stmtTurma->fetch()) {
        header("Location: ../turmas.php?erro=turma");
        exit();
    }
}

$sqlUsuario = "SELECT id FROM alunos WHERE usuario = :usuario LIMIT 1";

$stmtUsuario = $conexao->prepare($sqlUsuario);

$stmtUsuario->execute([
    ":usuario" => $usuario
]);

if ($stmtUsuario->fetch()) {
    header(
        "Location: ../novo_aluno.php?erro=usuario_existente&turma="
        . urlencode($turmaId)
    );

    exit();
}

$senhaHash = password_hash(
    $senhaOriginal,
    PASSWORD_DEFAULT
);

$dataNascimentoBanco = null;

if ($dataNascimento !== "") {
    $dataNascimentoBanco = $dataNascimento;
}

$sqlAluno = "INSERT INTO alunos (professor_id,nome,usuario,senha,foto,data_nascimento,turma_id,senha_visivel) VALUES (:professor_id,:nome,:usuario,:senha,:foto,:data_nascimento,:turma_id,:senha_visivel) RETURNING id";

try {
    $conexao->beginTransaction();
    $stmtAluno = $conexao->prepare($sqlAluno);

    $stmtAluno->execute([
        ":professor_id" => $professorId,
        ":nome" => $nome,
        ":usuario" => $usuario,
        ":senha" => $senhaHash,
        ":foto" => "padrao.png",
        ":data_nascimento" => $dataNascimentoBanco,
        ":turma_id" => $turmaId !== ""
            ? $turmaId
            : null,
        ":senha_visivel" => $senhaOriginal
    ]);

    $alunoId = $stmtAluno->fetchColumn();

    $sqlProgresso = "INSERT INTO progresso (aluno_id,nivel_atual,estrelas,moedas,licoes_concluidas,acertos,erros,tempo_estudo) VALUES (:aluno_id,1,0,0,0,0,0,0)";

    $stmtProgresso = $conexao->prepare($sqlProgresso);

    $stmtProgresso->execute([
        ":aluno_id" => $alunoId
    ]);

    $conexao->commit();

    if ($turmaId !== "") {
        header(
            "Location: ../relatorios.php?turma="
            . urlencode($turmaId)
            . "&sucesso=aluno_criado"
        );
    } else {
        header(
            "Location: ../turmas.php?sucesso=aluno_criado"
        );
    }
    exit();
} catch (PDOException $erro) {
    if ($conexao->inTransaction()) {
        $conexao->rollBack();
    }

    if ($erro->getCode() === "23505") {
        header(
            "Location: ../novo_aluno.php?erro=usuario_existente&turma="
            . urlencode($turmaId)
        );
        exit();
    }

    http_response_code(500);

    echo "Erro ao cadastrar aluno.";

    exit();
}
?>