<?php
session_start();

require_once __DIR__ . "/conexao.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../index.html");
    exit();
}

$email = trim($_POST["email"] ?? "");
$senha = $_POST["senha"] ?? "";

if ($email === "" || $senha === "") {
    header("Location: ../index.html?erro=preencha");
    exit();
}

$sql = "SELECT id,nome,email,senha,foto FROM professores WHERE email = :email LIMIT 1";

$stmt = $conexao->prepare($sql);
$stmt->execute([
    ":email" => $email
]);

$professor = $stmt->fetch();

if (!$professor) {
    header("Location: ../index.html?erro=login");
    exit();
}

if (!password_verify($senha, $professor["senha"])) {
    header("Location: ../index.html?erro=login");
    exit();
}

session_regenerate_id(true);

$_SESSION["professor_id"] = $professor["id"];
$_SESSION["professor_nome"] = $professor["nome"];
$_SESSION["professor_email"] = $professor["email"];
$_SESSION["professor_foto"] = $professor["foto"];

header("Location: ../painel.php");
exit();
?>