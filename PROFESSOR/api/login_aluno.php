<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=UTF-8');

require_once __DIR__ . '/../php/conexao.php';

function responder(
    bool $sucesso,
    string $mensagem,
    array $extra = []
): never {

    echo json_encode(
        array_merge(
            [
                'sucesso' => $sucesso,
                'mensagem' => $mensagem
            ],
            $extra
        ),
        JSON_UNESCAPED_UNICODE
    );

    exit;
}

try {

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

        http_response_code(405);

        responder(
            false,
            'Método de requisição inválido.'
        );
    }

    /*
     * Primeiro tenta JSON.
     */
    $dados = [];

    $corpo = file_get_contents(
        'php://input'
    );

    if (
        $corpo !== false &&
        trim($corpo) !== ''
    ) {

        $json = json_decode(
            $corpo,
            true
        );

        if (is_array($json)) {
            $dados = $json;
        }
    }

    /*
     * Caso seja formulário.
     */
    if (empty($dados)) {
        $dados = $_POST;
    }

    $usuario = trim(
        (string) (
            $dados['usuario'] ?? ''
        )
    );

    $senha = (string) (
        $dados['senha'] ?? ''
    );

    if (
        $usuario === '' ||
        $senha === ''
    ) {

        http_response_code(400);

        responder(
            false,
            'Preencha usuário e senha.'
        );
    }

    $stmt = $conexao->prepare(
        "
        SELECT
            id,
            nome,
            usuario,
            senha,
            turma_id
        FROM alunos
        WHERE usuario = :usuario
        LIMIT 1
        "
    );

    $stmt->execute([
        ':usuario' => $usuario
    ]);

    $aluno = $stmt->fetch(
        PDO::FETCH_ASSOC
    );

    if (!$aluno) {

        http_response_code(401);

        responder(
            false,
            'Aluno não encontrado.'
        );
    }

    if (
        empty($aluno['senha']) ||
        !password_verify(
            $senha,
            $aluno['senha']
        )
    ) {

        http_response_code(401);

        responder(
            false,
            'Senha incorreta.'
        );
    }

    /*
     * Busca progresso.
     */
    $stmt = $conexao->prepare(
        "
        SELECT
            estrelas,
            moedas,
            licoes_concluidas,
            acertos
        FROM progresso
        WHERE aluno_id = :aluno_id
        LIMIT 1
        "
    );

    $stmt->execute([
        ':aluno_id' => $aluno['id']
    ]);

    $progresso = $stmt->fetch(
        PDO::FETCH_ASSOC
    );

    responder(
        true,
        'Login realizado com sucesso.',
        [
            'aluno' => [
                'id' => (int) $aluno['id'],
                'nome' => $aluno['nome'],
                'turma' => $aluno['turma_id'],
                'estrelas' =>
                    (int) (
                        $progresso['estrelas'] ?? 0
                    ),
                'moedas' =>
                    (int) (
                        $progresso['moedas'] ?? 0
                    ),
                'licoes' =>
                    (int) (
                        $progresso['licoes_concluidas'] ?? 0
                    ),
                'acertos' =>
                    (float) (
                        $progresso['acertos'] ?? 0
                    )
            ]
        ]
    );

} catch (Throwable $e) {

    error_log(
        'Erro login_aluno.php: ' .
        $e->getMessage()
    );

    http_response_code(500);

    responder(
        false,
        'Erro interno ao realizar login.'
    );
}