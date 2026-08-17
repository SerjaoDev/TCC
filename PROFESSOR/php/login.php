<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/conexao.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

$email = trim(
    (string) ($_POST['email'] ?? '')
);

$senha = (string) ($_POST['senha'] ?? '');

if ($email === '' || $senha === '') {
    header('Location: ../index.php?erro=preencha');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: ../index.php?erro=email');
    exit;
}

try {
    $sql = "
        SELECT
            id,
            nome,
            email,
            senha,
            foto
        FROM professores
        WHERE LOWER(email) = LOWER(:email)
        LIMIT 1
    ";

    $stmt = $conexao->prepare($sql);

    $stmt->execute([
        ':email' => $email
    ]);

    $professor = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$professor) {
        header('Location: ../index.php?erro=usuario');
        exit;
    }

    if (
        empty($professor['senha']) ||
        !password_verify(
            $senha,
            $professor['senha']
        )
    ) {
        header('Location: ../index.php?erro=senha');
        exit;
    }

    session_regenerate_id(true);

    $_SESSION['professor_id'] =
        (int) $professor['id'];

    $_SESSION['professor_nome'] =
        (string) $professor['nome'];

    $_SESSION['professor_email'] =
        (string) $professor['email'];

    $_SESSION['professor_foto'] =
        !empty($professor['foto'])
            ? (string) $professor['foto']
            : 'padrao.png';

    $_SESSION['login_google'] = false;

    header('Location: ../painel.php');
    exit;
} catch (Throwable $e) {
    error_log(
        'ERRO LOGIN: ' .
        $e->getMessage()
    );

    http_response_code(500);

    exit(
        'Erro interno ao realizar o login.'
    );
}