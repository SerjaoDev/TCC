<?php
require_once __DIR__ . "/conexao.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../cadastro.php");
    exit();
}

$nome = trim($_POST["nome"] ?? "");
$email = trim($_POST["email"] ?? "");
$senha = $_POST["senha"] ?? "";
$confirmar = $_POST["confirmar"] ?? "";

if ($nome === "" || $email === "" || $senha === "" || $confirmar === "") {
    header("Location: ../cadastro.php?erro=preencha");
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: ../cadastro.php?erro=email");
    exit();
}

if ($senha !== $confirmar) {
    header("Location: ../cadastro.php?erro=senhas");
    exit();
}

if (strlen($senha) < 6) {
    header("Location: ../cadastro.php?erro=senha_curta");
    exit();
}

$sqlVerificar = "SELECT id FROM professores WHERE email = :email LIMIT 1";

$stmtVerificar = $conexao->prepare($sqlVerificar);

$stmtVerificar->execute([
    ":email" => $email
]);

if ($stmtVerificar->fetch()) {
    header("Location: ../cadastro.php?erro=email_existente");
    exit();
}

$senhaHash = password_hash( $senha, PASSWORD_DEFAULT);

$sql = "INSERT INTO professores (nome,email,senha) VALUES (:nome,:email,:senha)";

$stmt = $conexao->prepare($sql);

try {
    $stmt->execute([
        ":nome" => $nome,
        ":email" => $email,
        ":senha" => $senhaHash
    ]);
} catch (PDOException $erro) {
    if ($erro->getCode() === "23505") {
        header("Location: ../cadastro.php?erro=email_existente");
        exit();
    }

    http_response_code(500);

    echo "Erro ao cadastrar professor.";

    exit();
}

header("Location: ../index.html?cadastro=sucesso");
exit();
?>