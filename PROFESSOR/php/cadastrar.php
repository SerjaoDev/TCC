<?php
declare(strict_types=1);

require_once __DIR__ . '/conexao.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../cadastro.php');
    exit;
}

$nome = trim(
    (string) ($_POST['nome'] ?? '')
);

$email = trim(
    (string) ($_POST['email'] ?? '')
);

$senha = (string) ($_POST['senha'] ?? '');

$confirmar = (string) (
    $_POST['confirmar'] ?? ''
);

if (
    $nome === '' ||
    $email === '' ||
    $senha === '' ||
    $confirmar === ''
) {
    header(
        'Location: ../cadastro.php?erro=preencha'
    );
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header(
        'Location: ../cadastro.php?erro=email'
    );
    exit;
}

if (strlen($senha) < 6) {
    header(
        'Location: ../cadastro.php?erro=senha_curta'
    );
    exit;
}

if ($senha !== $confirmar) {
    header(
        'Location: ../cadastro.php?erro=senhas'
    );
    exit;
}

try {
    $stmt = $conexao->prepare(
        "
        SELECT id
        FROM professores
        WHERE LOWER(email) = LOWER(:email)
        LIMIT 1
        "
    );

    $stmt->execute([
        ':email' => $email
    ]);

    if ($stmt->fetch()) {
        header(
            'Location: ../cadastro.php?erro=email_existente'
        );
        exit;
    }

    $senhaHash = password_hash(
        $senha,
        PASSWORD_DEFAULT
    );

    $stmt = $conexao->prepare(
        "
        INSERT INTO professores
        (
            nome,
            email,
            senha,
            foto
        )
        VALUES
        (
            :nome,
            :email,
            :senha,
            :foto
        )
        "
    );

    $stmt->execute([
        ':nome' => $nome,
        ':email' => $email,
        ':senha' => $senhaHash,
        ':foto' => 'padrao.png'
    ]);

    header(
        'Location: ../index.php?cadastro=sucesso'
    );
    exit;
} catch (PDOException $e) {
    error_log(
        'ERRO CADASTRO PROFESSOR: ' .
        $e->getMessage()
    );

    if ($e->getCode() === '23505') {
        header(
            'Location: ../cadastro.php?erro=email_existente'
        );
        exit;
    }

    http_response_code(500);

    exit(
        'Erro ao cadastrar professor.'
    );
}