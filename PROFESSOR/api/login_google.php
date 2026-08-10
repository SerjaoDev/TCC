<?php
declare(strict_types=1);

session_start();

header('Content-Type: application/json; charset=UTF-8');

require_once __DIR__ . '/conexao.php';
require_once __DIR__ . '/firebase.php';

function responder(bool $sucesso, string $mensagem, int $status = 200, array $extra = []): never
{
    http_response_code($status);

    echo json_encode(
        array_merge([
            'sucesso' => $sucesso,
            'mensagem' => $mensagem
        ], $extra),
        JSON_UNESCAPED_UNICODE
    );

    exit;
}

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        responder(
            false,
            'Método não permitido.',
            405
        );
    }

    $conteudo = file_get_contents('php://input');

    if ($conteudo === false || trim($conteudo) === '') {
        responder(
            false,
            'Nenhum dado foi recebido.',
            400
        );
    }

    $dados = json_decode($conteudo, true);

    if (!is_array($dados)) {
        responder(
            false,
            'JSON enviado pelo navegador é inválido.',
            400
        );
    }

    $idToken = trim((string)($dados['idToken'] ?? ''));

    if ($idToken === '') {
        responder(
            false,
            'Token do Firebase não informado.',
            400
        );
    }

    $auth = firebaseAuth();
    $tokenVerificado = $auth->verifyIdToken($idToken);
    $claims = $tokenVerificado->claims();
    $firebaseUid = $claims->get('sub');
    $email = $claims->get('email');
    $nome = $claims->get('name');

    if (!$firebaseUid || !$email) {
        responder(
            false,
            'O token do Firebase não possui os dados necessários.',
            401
        );
    }

    $firebaseUid = (string)$firebaseUid;
    $email = (string)$email;
    $nome = trim((string)($nome ?? ''));

    $sql = "SELECT id, nome, email, foto FROM professores WHERE firebase_uid = :firebase_uid LIMIT 1";

    $stmt = $conexao->prepare($sql);

    $stmt->execute([
        ':firebase_uid' => $firebaseUid
    ]);

    $professor = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$professor) {
        $sql = "SELECT id, nome, email, foto FROM professores WHERE email = :email LIMIT 1";

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
        $nomeFinal = $nome !== ''
            ? $nome
            : 'Professor';

        $senhaAleatoria = password_hash(
            bin2hex(random_bytes(32)),
            PASSWORD_DEFAULT
        );

        $sql = "INSERT INTO professores (nome,email,senha,foto,firebase_uid) VALUES (:nome,:email,:senha,:foto,:firebase_uid) RETURNING id, nome, email, foto";

        $stmt = $conexao->prepare($sql);

        $stmt->execute([
            ':nome' => $nomeFinal,
            ':email' => $email,
            ':senha' => $senhaAleatoria,
            ':foto' => 'padrao.png',
            ':firebase_uid' => $firebaseUid
        ]);

        $professor = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$professor) {
            throw new RuntimeException(
                'Não foi possível criar o professor.'
            );
        }
    }

    session_regenerate_id(true);

    $_SESSION['id'] = $professor['id'];
    $_SESSION['nome'] = $professor['nome'];
    $_SESSION['email'] = $professor['email'];
    $_SESSION['login_google'] = true;

    responder(
        true,
        'Login realizado com sucesso.',
        200,
        [
            'redirecionar' => '/painel.php'
        ]
    );
} catch (Throwable $e) {
    error_log(
        'LOGIN GOOGLE - ' .
        $e->getFile() .
        ':' .
        $e->getLine() .
        ' - ' .
        $e->getMessage()
    );

    responder(
        false,
        'Não foi possível concluir o login com o Google.',
        500
    );
}