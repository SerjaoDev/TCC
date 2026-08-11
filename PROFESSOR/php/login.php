<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/conexao.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

$email = trim($_POST['email'] ?? '');
$senha = $_POST['senha'] ?? '';

if ($email === '' || $senha === '') {
    header('Location: ../index.php?erro=preencha');
    exit;
}

try {
    $sql = "SELECT id, nome, email, senha, foto FROM professores WHERE email = :email LIMIT 1";

    $stmt = $conexao->prepare($sql);

    $stmt->execute([
        ':email' => $email
    ]);

    $professor = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$professor) {
        header('Location: ../index.php?erro=usuario');
        exit;
    }

    if (!password_verify($senha, $professor['senha'])) {
        header('Location: ../index.php?erro=senha');
        exit;
    }

    session_regenerate_id(true);

    $_SESSION['id'] = (int) $professor['id'];
    $_SESSION['nome'] = $professor['nome'];
    $_SESSION['email'] = $professor['email'];
    $_SESSION['foto'] = $professor['foto'] ?? 'padrao.png';
    $_SESSION['login_google'] = false;

    header('Location: ../painel.php');
    exit;
} catch (Throwable $e) {
    error_log('Erro no login: ' . $e->getMessage());

    http_response_code(500);

    echo 'Erro interno ao realizar o login.';
    exit;
}