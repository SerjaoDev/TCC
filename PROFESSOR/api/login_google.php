<?php
declare(strict_types=1);

session_start();

header('Content-Type: application/json; charset=UTF-8');

require_once __DIR__ . '/conexao.php';
require_once __DIR__ . '/firebase.php';

function resposta(bool $sucesso, string $mensagem, array $extra = []): never
{
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
        http_response_code(405);

        resposta(
            false,
            'Método de requisição inválido.'
        );
    }

    $corpo = file_get_contents('php://input');

    if ($corpo === false || trim($corpo) === '') {
        http_response_code(400);

        resposta(
            false,
            'Nenhum dado foi recebido.'
        );
    }

    $dados = json_decode($corpo, true);

    if (!is_array($dados)) {
        http_response_code(400);

        resposta(
            false,
            'JSON inválido.'
        );
    }

    $idToken = trim($dados['idToken'] ?? '');

    if ($idToken === '') {
        http_response_code(400);

        resposta(
            false,
            'Token do Firebase não informado.'
        );
    }

    $auth = firebaseAuth();

    $tokenVerificado = $auth->verifyIdToken($idToken);

    $firebaseUid = $tokenVerificado
        ->claims()
        ->get('sub');

    $email = $tokenVerificado
        ->claims()
        ->get('email');

    $nome = $tokenVerificado
        ->claims()
        ->get('name');

    if (!$firebaseUid || !$email) {
        http_response_code(401);

        resposta(
            false,
            'Token do Firebase inválido ou incompleto.'
        );
    }

    $stmt = $conexao->prepare("SELECT id,nome,email,foto,firebase_uid FROM professores WHERE firebase_uid = :firebase_uid LIMIT 1");

    $stmt->execute([
        ':firebase_uid' => $firebaseUid
    ]);

    $professor = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$professor) {
        $stmt = $conexao->prepare("SELECT id,nome,email,foto,firebase_uid FROM professores WHERE LOWER(email) = LOWER(:email) LIMIT 1");

        $stmt->execute([
            ':email' => $email
        ]);

        $professor = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($professor) {
            $stmt = $conexao->prepare("UPDATE professores SET firebase_uid = :firebase_uid WHERE id = :id");

            $stmt->execute([
                ':firebase_uid' => $firebaseUid,
                ':id' => $professor['id']
            ]);
        }
    }

    if (!$professor) {
        $nomeFinal = trim($nome ?: 'Professor');

        $senhaAleatoria = password_hash(
            bin2hex(random_bytes(32)),
            PASSWORD_DEFAULT
        );

        $stmt = $conexao->prepare("INSERT INTO professores (nome,email,senha,foto,firebase_uid) VALUES (:nome,:email,:senha,:foto,:firebase_uid) RETURNING id,nome,email,foto,firebase_uid");

        $stmt->execute([
            ':nome' => $nomeFinal,
            ':email' => $email,
            ':senha' => $senhaAleatoria,
            ':foto' => 'padrao.png',
            ':firebase_uid' => $firebaseUid
        ]);

        $professor = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    if (!$professor || empty($professor['id'])) {
        throw new RuntimeException(
            'Professor não pôde ser localizado ou criado.'
        );
    }

    session_regenerate_id(true);

    $_SESSION['id'] = (int) $professor['id'];
    $_SESSION['nome'] = $professor['nome'];
    $_SESSION['email'] = $professor['email'];
    $_SESSION['foto'] = $professor['foto'] ?? 'padrao.png';
    $_SESSION['login_google'] = true;

    resposta(
        true,
        'Login realizado com sucesso.',
        [
            'redirecionar' => 'painel.php'
        ]
    );
} catch (Throwable $e) {
    error_log(
        'LOGIN GOOGLE ERROR: ' .
        $e->getMessage()
    );

    http_response_code(500);

    resposta(
        false,
        'Erro no login Google: ' . $e->getMessage()
    );
}