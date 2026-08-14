<?php
declare(strict_types=1);

session_start();

header('Content-Type: application/json; charset=UTF-8');

require_once __DIR__ . '/conexao.php';
require_once __DIR__ . '/firebase.php';

function resposta(
    bool $sucesso,
    string $mensagem,
    array $extra = []
): void {

    $resposta = array_merge(
        [
            'sucesso' => $sucesso,
            'mensagem' => $mensagem
        ],
        $extra
    );

    echo json_encode(
        $resposta,
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );

    exit;
}

$corpo = '';
$dados = [];

$idToken = '';
$firebaseUid = '';
$email = '';
$nome = '';

$nomeFinal = '';
$senhaAleatoria = '';

$professor = false;

try {
    if (
        !isset($_SERVER['REQUEST_METHOD']) ||
        $_SERVER['REQUEST_METHOD'] !== 'POST'
    ) {

        http_response_code(405);

        resposta(
            false,
            'Método de requisição inválido.'
        );
    }

    $corpoRecebido = file_get_contents('php://input');

    if ($corpoRecebido === false) {

        http_response_code(400);

        resposta(
            false,
            'Não foi possível ler os dados enviados.'
        );
    }

    $corpo = trim($corpoRecebido);

    if ($corpo === '') {

        http_response_code(400);

        resposta(
            false,
            'Nenhum dado foi recebido.'
        );
    }

    $dadosDecodificados = json_decode(
        $corpo,
        true
    );

    if (
        !is_array($dadosDecodificados)
    ) {

        http_response_code(400);

        resposta(
            false,
            'JSON inválido.'
        );
    }

    $dados = $dadosDecodificados;

    $idToken = trim(
        (string) (
            $dados['idToken'] ?? ''
        )
    );

    if ($idToken === '') {

        http_response_code(400);

        resposta(
            false,
            'Token do Firebase não informado.'
        );
    }

    $auth = firebaseAuth();

    $token = $auth->verifyIdToken(
        $idToken
    );

    $firebaseUid = (string) (
        $token
            ->claims()
            ->get('sub')
    );

    $email = (string) (
        $token
            ->claims()
            ->get('email')
    );

    $nome = (string) (
        $token
            ->claims()
            ->get('name')
    );

    if (
        $firebaseUid === '' ||
        $email === ''
    ) {

        http_response_code(401);

        resposta(
            false,
            'Token do Firebase inválido ou incompleto.'
        );
    }

    $stmt = $conexao->prepare(
        "
        SELECT
            id,
            nome,
            email,
            foto,
            firebase_uid
        FROM professores
        WHERE firebase_uid = :firebase_uid
        LIMIT 1
        "
    );

    $stmt->execute(
        [
            ':firebase_uid' => $firebaseUid
        ]
    );

    $professor = $stmt->fetch(
        PDO::FETCH_ASSOC
    );

    if ($professor === false) {

        $stmt = $conexao->prepare(
            "
            SELECT
                id,
                nome,
                email,
                foto,
                firebase_uid
            FROM professores
            WHERE LOWER(email) = LOWER(:email)
            LIMIT 1
            "
        );

        $stmt->execute(
            [
                ':email' => $email
            ]
        );

        $professor = $stmt->fetch(
            PDO::FETCH_ASSOC
        );

        if ($professor !== false) {

            $stmt = $conexao->prepare(
                "
                UPDATE professores
                SET firebase_uid = :firebase_uid
                WHERE id = :id
                "
            );

            $stmt->execute(
                [
                    ':firebase_uid' => $firebaseUid,
                    ':id' => $professor['id']
                ]
            );

            $professor['firebase_uid'] = $firebaseUid;
        }
    }

    if ($professor === false) {

        $nomeFinal = trim($nome);

        if ($nomeFinal === '') {
            $nomeFinal = 'Professor';
        }

        $senhaTemporaria = bin2hex(
            random_bytes(32)
        );

        $senhaAleatoria = password_hash(
            $senhaTemporaria,
            PASSWORD_DEFAULT
        );

        $stmt = $conexao->prepare(
            "
            INSERT INTO professores
            (
                nome,
                email,
                senha,
                foto,
                firebase_uid
            )
            VALUES
            (
                :nome,
                :email,
                :senha,
                :foto,
                :firebase_uid
            )
            RETURNING
                id,
                nome,
                email,
                foto,
                firebase_uid
            "
        );


        $stmt->execute(
            [
                ':nome' => $nomeFinal,
                ':email' => $email,
                ':senha' => $senhaAleatoria,
                ':foto' => 'padrao.png',
                ':firebase_uid' => $firebaseUid
            ]
        );


        $professor = $stmt->fetch(
            PDO::FETCH_ASSOC
        );
    }

    if (
        !is_array($professor) ||
        empty($professor['id'])
    ) {

        throw new RuntimeException(
            'Professor não pôde ser localizado ou criado.'
        );
    }

    session_regenerate_id(true);


    $_SESSION['id'] = (int) $professor['id'];

    $_SESSION['nome'] = (string) $professor['nome'];

    $_SESSION['email'] = (string) $professor['email'];

    $_SESSION['foto'] =
        !empty($professor['foto'])
            ? $professor['foto']
            : 'padrao.png';

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
        'Erro no login Google: ' .
        $e->getMessage()
    );
}