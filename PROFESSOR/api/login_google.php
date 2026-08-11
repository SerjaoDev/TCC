<?php
declare(strict_types=1);

session_start();

header(
    'Content-Type: application/json; charset=UTF-8'
);

function respostaJson(
    bool $sucesso,
    string $mensagem,
    int $status = 200,
    array $dados = []
): never {

    http_response_code($status);

    echo json_encode(
        array_merge(
            [
                'sucesso' => $sucesso,
                'mensagem' => $mensagem
            ],
            $dados
        ),
        JSON_UNESCAPED_UNICODE
    );

    exit;
}

set_error_handler(
    function (
        int $severity,
        string $message,
        string $file,
        int $line
    ): bool {

        error_log(
            "PHP ERROR: {$message} em {$file}:{$line}"
        );

        throw new ErrorException(
            $message,
            0,
            $severity,
            $file,
            $line
        );
    }
);

try {
    require_once __DIR__ . '/conexao.php';
    require_once __DIR__ . '/firebase.php';

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        respostaJson(
            false,
            'Método não permitido.',
            405
        );
    }

    $conteudo = file_get_contents(
        'php://input'
    );

    if (!$conteudo) {
        respostaJson(
            false,
            'Nenhum dado foi recebido.',
            400
        );
    }

    $dados = json_decode(
        $conteudo,
        true
    );

    if (!is_array($dados)) {
        respostaJson(
            false,
            'JSON inválido.',
            400
        );
    }

    $idToken = trim(
        (string)(
            $dados['idToken'] ?? ''
        )
    );

    if ($idToken === '') {
        respostaJson(
            false,
            'Token do Firebase não informado.',
            400
        );
    }

    $auth = firebaseAuth();

    $token = $auth->verifyIdToken(
        $idToken
    );

    $claims = $token->claims();

    $firebaseUid = (string)(
        $claims->get('sub')
    );

    $email = (string)(
        $claims->get('email')
    );

    $nome = (string)(
        $claims->get('name') ?? ''
    );

    if (
        $firebaseUid === '' ||
        $email === ''
    ) {
        respostaJson(
            false,
            'Token do Google inválido.',
            401
        );
    }

    $sql = "SELECT id,nome,email,foto FROM professores WHERE firebase_uid = :firebase_uid LIMIT 1";

    $stmt = $conexao->prepare($sql);

    $stmt->execute([
        ':firebase_uid' =>
            $firebaseUid
    ]);

    $professor =
        $stmt->fetch(
            PDO::FETCH_ASSOC
        );

    if (!$professor) {
        $sql = "SELECT id,nome,email,foto FROM professores WHERE email = :email LIMIT 1";

        $stmt =
            $conexao->prepare($sql);

        $stmt->execute([
            ':email' => $email
        ]);

        $professor =
            $stmt->fetch(
                PDO::FETCH_ASSOC
            );

        if ($professor) {
            $sql = "UPDATE professores SET firebase_uid = :firebase_uid WHERE id = :id";

            $stmt =
                $conexao->prepare($sql);

            $stmt->execute([
                ':firebase_uid' =>
                    $firebaseUid,

                ':id' =>
                    $professor['id']
            ]);
        }
    }

    if (!$professor) {
        $nomeFinal =
            trim($nome) !== ''
                ? trim($nome)
                : 'Professor';

        $senhaAleatoria =
            password_hash(
                bin2hex(
                    random_bytes(32)
                ),
                PASSWORD_DEFAULT
            );

        $sql = "INSERT INTO professores (nome,email,senha,foto,firebase_uid) VALUES (:nome,:email,:senha,:foto,:firebase_uid) RETURNING id,nome,email,foto";

        $stmt =
            $conexao->prepare($sql);

        $stmt->execute([
            ':nome' =>
                $nomeFinal,

            ':email' =>
                $email,

            ':senha' =>
                $senhaAleatoria,

            ':foto' =>
                'padrao.png',

            ':firebase_uid' =>
                $firebaseUid
        ]);

        $professor =
            $stmt->fetch(
                PDO::FETCH_ASSOC
            );
    }

    session_regenerate_id(true);

    $_SESSION['id'] =
        $professor['id'];

    $_SESSION['nome'] =
        $professor['nome'];

    $_SESSION['email'] =
        $professor['email'];

    $_SESSION['login_google'] =
        true;

    respostaJson(
        true,
        'Login realizado com sucesso.',
        200,
        [
            'redirecionar' =>
                '/painel.php'
        ]
    );
} catch (Throwable $e) {
    error_log(
        'LOGIN GOOGLE: ' .
        $e->getMessage() .
        ' em ' .
        $e->getFile() .
        ':' .
        $e->getLine()
    );

    respostaJson(
        false,
        'Não foi possível realizar o login com Google.',
        500
    );
}