<?php
declare(strict_types=1);

session_start();

header('Content-Type: application/json; charset=UTF-8');

require_once __DIR__ . '/../php/conexao.php';
require_once __DIR__ . '/firebase.php';

try {
    $dados = json_decode(
        file_get_contents('php://input'),
        true
    );

    if (!is_array($dados)) {
        http_response_code(400);

        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'Dados inválidos.'
        ]);

        exit;
    }

    $idToken = trim($dados['idToken'] ?? '');

    if ($idToken === '') {
        http_response_code(400);

        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'Token do Firebase não informado.'
        ]);

        exit;
    }

    $auth = firebaseAuth();
    $tokenVerificado = $auth->verifyIdToken($idToken);
    $firebaseUid = $tokenVerificado->claims()->get('sub');
    $email = $tokenVerificado->claims()->get('email');
    $nome = $tokenVerificado->claims()->get('name');

    if (!$firebaseUid || !$email) {
        http_response_code(401);

        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'Token do Firebase inválido.'
        ]);

        exit;
    }

    $sql = "SELECT id, nome, email, foto, firebase_uid FROM professores WHERE firebase_uid = :firebase_uid LIMIT 1";

    $stmt = $conexao->prepare($sql);

    $stmt->execute([
        ':firebase_uid' => $firebaseUid
    ]);

    $professor = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$professor) {
        $sql = "SELECT id, nome, email, foto, firebase_uid FROM professores WHERE LOWER(email) = LOWER(:email) LIMIT 1";

        $stmt = $conexao->prepare($sql);

        $stmt->execute([
            ':email' => $email
        ]);

        $professor = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($professor) {
            $sql = "UPDATE professores SET firebase_uid = :firebase_uid WHERE id = :id";

            $stmt = $conexao->prepare($sql);

            $stmt->execute([
                ':firebase_uid' => $firebaseUid,
                ':id' => $professor['id']
            ]);
        }
    }

    if (!$professor) {
        $nomeFinal = trim((string) ($nome ?: 'Professor'));

        $senhaAleatoria = password_hash(
            bin2hex(random_bytes(32)),
            PASSWORD_DEFAULT
        );

        $sql = "INSERT INTO professores (nome, email, senha, foto, firebase_uid) VALUES (:nome, :email, :senha, :foto, :firebase_uid) RETURNING id, nome, email, foto, firebase_uid";

        $stmt = $conexao->prepare($sql);

        $stmt->execute([
            ':nome' => $nomeFinal,
            ':email' => $email,
            ':senha' => $senhaAleatoria,
            ':foto' => 'padrao.png',
            ':firebase_uid' => $firebaseUid
        ]);

        $professor = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    session_regenerate_id(true);

    $_SESSION['id'] = $professor['id'];
    $_SESSION['nome'] = $professor['nome'];
    $_SESSION['email'] = $professor['email'];
    $_SESSION['login_google'] = true;

    echo json_encode([
        'sucesso' => true,
        'mensagem' => 'Login realizado com sucesso.',
        'redirecionar' => '/painel.php'
    ]);

    exit;
} catch (Throwable $e) {
    error_log(
        'Erro no login Google: ' . $e->getMessage()
    );

    http_response_code(401);

    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Não foi possível validar o login com o Google.'
    ]);

    exit;
}
?>