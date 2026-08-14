<?php
declare(strict_types=1);

/*
 * Não exibir warnings/notices diretamente.
 * Eles quebrariam o JSON.
 */
ini_set('display_errors', '0');
ini_set('log_errors', '1');

error_reporting(E_ALL);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

header(
    'Content-Type: application/json; charset=UTF-8'
);

require_once __DIR__ . '/../php/conexao.php';
require_once __DIR__ . '/firebase.php';

function responder(
    bool $sucesso,
    string $mensagem,
    array $extra = [],
    int $status = 200
): never {

    http_response_code($status);

    /*
     * Limpa qualquer saída acidental.
     */
    if (ob_get_level() > 0) {
        ob_clean();
    }

    echo json_encode(
        array_merge(
            [
                'sucesso' => $sucesso,
                'mensagem' => $mensagem
            ],
            $extra
        ),
        JSON_UNESCAPED_UNICODE |
        JSON_UNESCAPED_SLASHES
    );

    exit;
}

try {

    if (
        $_SERVER['REQUEST_METHOD'] !== 'POST'
    ) {

        responder(
            false,
            'Método de requisição inválido.',
            [],
            405
        );
    }

    $corpo = file_get_contents(
        'php://input'
    );

    if (
        $corpo === false ||
        trim($corpo) === ''
    ) {

        responder(
            false,
            'Nenhum dado foi recebido.',
            [],
            400
        );
    }

    $dados = json_decode(
        $corpo,
        true
    );

    if (!is_array($dados)) {

        responder(
            false,
            'JSON inválido.',
            [],
            400
        );
    }

    $idToken = trim(
        (string) (
            $dados['idToken'] ?? ''
        )
    );

    if ($idToken === '') {

        responder(
            false,
            'Token do Firebase não informado.',
            [],
            400
        );
    }

    /*
     * Firebase.
     */
    $auth = firebaseAuth();

    $token = $auth->verifyIdToken(
        $idToken
    );

    $claims = $token->claims();

    $firebaseUid = (string) (
        $claims->get('sub')
    );

    $email = (string) (
        $claims->get('email')
    );

    $nome = (string) (
        $claims->get('name', 'Professor')
    );

    if (
        $firebaseUid === '' ||
        $email === ''
    ) {

        responder(
            false,
            'Token do Firebase inválido ou incompleto.',
            [],
            401
        );
    }

    /*
     * Procura pelo Firebase UID.
     */
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

    $stmt->execute([
        ':firebase_uid' => $firebaseUid
    ]);

    $professor = $stmt->fetch(
        PDO::FETCH_ASSOC
    );

    /*
     * Caso ainda não exista pelo UID,
     * procura pelo e-mail.
     */
    if (!$professor) {

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

        $stmt->execute([
            ':email' => $email
        ]);

        $professor = $stmt->fetch(
            PDO::FETCH_ASSOC
        );

        /*
         * Vincula a conta existente ao Firebase.
         */
        if ($professor) {

            $stmt = $conexao->prepare(
                "
                UPDATE professores
                SET firebase_uid = :firebase_uid
                WHERE id = :id
                "
            );

            $stmt->execute([
                ':firebase_uid' => $firebaseUid,
                ':id' => $professor['id']
            ]);

            $professor['firebase_uid'] =
                $firebaseUid;
        }
    }

    /*
     * Se ainda não existe professor,
     * cria automaticamente.
     */
    if (!$professor) {

        $nomeFinal = trim($nome);

        if ($nomeFinal === '') {
            $nomeFinal = 'Professor';
        }

        $senhaAleatoria = password_hash(
            bin2hex(
                random_bytes(32)
            ),
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

        $stmt->execute([
            ':nome' => $nomeFinal,
            ':email' => $email,
            ':senha' => $senhaAleatoria,
            ':foto' => 'padrao.png',
            ':firebase_uid' => $firebaseUid
        ]);

        $professor = $stmt->fetch(
            PDO::FETCH_ASSOC
        );
    }

    /*
     * Validação final.
     */
    if (
        !$professor ||
        empty($professor['id'])
    ) {

        throw new RuntimeException(
            'Professor não pôde ser localizado ou criado.'
        );
    }

    /*
     * Cria a sessão do professor.
     */
    if (
        session_status() !==
        PHP_SESSION_ACTIVE
    ) {
        session_start();
    }

    session_regenerate_id(true);

    $_SESSION['id'] =
        (int) $professor['id'];

    $_SESSION['nome'] =
        $professor['nome'];

    $_SESSION['email'] =
        $professor['email'];

    $_SESSION['foto'] =
        $professor['foto'] ??
        'padrao.png';

    $_SESSION['login_google'] = true;

    responder(
        true,
        'Login realizado com sucesso.',
        [
            'redirecionar' =>
                'painel.php'
        ]
    );

} catch (
    Kreait\Firebase\Exception\AuthException |
    Kreait\Firebase\Exception\FirebaseException $e
) {

    error_log(
        'Firebase login error: ' .
        $e->getMessage()
    );

    responder(
        false,
        'Não foi possível validar sua conta Google.',
        [],
        401
    );

} catch (PDOException $e) {

    error_log(
        'Database login Google error: ' .
        $e->getMessage()
    );

    responder(
        false,
        'Erro ao acessar o banco de dados.',
        [],
        500
    );

} catch (Throwable $e) {

    error_log(
        'Login Google error: ' .
        $e->getMessage()
    );

    responder(
        false,
        'Não foi possível realizar o login com Google.',
        [],
        500
    );
}