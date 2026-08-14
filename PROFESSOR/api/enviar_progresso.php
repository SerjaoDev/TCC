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
     * Aceita JSON
     */
    $dados = [];

    $conteudo = file_get_contents('php://input');

    if ($conteudo !== false && trim($conteudo) !== '') {

        $json = json_decode(
            $conteudo,
            true
        );

        if (is_array($json)) {
            $dados = $json;
        }
    }

    /*
     * Se não veio JSON,
     * usa POST tradicional.
     */
    if (empty($dados)) {
        $dados = $_POST;
    }

    $alunoId = filter_var(
        $dados['aluno_id'] ?? null,
        FILTER_VALIDATE_INT
    );

    $licaoId = filter_var(
        $dados['licao_id'] ?? null,
        FILTER_VALIDATE_INT
    );

    $resultado = $dados['resultado'] ?? null;

    $pontuacao = (float) (
        $dados['pontuacao'] ?? 0
    );

    $tempo = (int) (
        $dados['tempo_gasto'] ?? 0
    );

    if (!$alunoId || !$licaoId) {

        http_response_code(400);

        responder(
            false,
            'Aluno ou lição não informado.'
        );
    }

    /*
     * Verifica se o aluno existe.
     */
    $stmt = $conexao->prepare(
        'SELECT id FROM alunos WHERE id = :id LIMIT 1'
    );

    $stmt->execute([
        ':id' => $alunoId
    ]);

    if (!$stmt->fetch()) {

        http_response_code(404);

        responder(
            false,
            'Aluno não encontrado.'
        );
    }

    /*
     * Verifica se a lição existe.
     */
    $stmt = $conexao->prepare(
        'SELECT id FROM licoes WHERE id = :id LIMIT 1'
    );

    $stmt->execute([
        ':id' => $licaoId
    ]);

    if (!$stmt->fetch()) {

        http_response_code(404);

        responder(
            false,
            'Lição não encontrada.'
        );
    }

    $conexao->beginTransaction();

    /*
     * Registra o desempenho.
     */
    $sql = "
        INSERT INTO desempenho
        (
            aluno_id,
            licao_id,
            resultado,
            pontuacao,
            tempo_gasto
        )
        VALUES
        (
            :aluno,
            :licao,
            :resultado,
            :pontuacao,
            :tempo
        )
    ";

    $stmt = $conexao->prepare($sql);

    $stmt->execute([
        ':aluno' => $alunoId,
        ':licao' => $licaoId,
        ':resultado' => $resultado,
        ':pontuacao' => $pontuacao,
        ':tempo' => $tempo
    ]);

    /*
     * Atualiza o progresso.
     *
     * Se ainda não existir registro,
     * cria um.
     */
    $sql = "
        INSERT INTO progresso
        (
            aluno_id,
            licoes_concluidas,
            acertos,
            estrelas,
            moedas
        )
        VALUES
        (
            :aluno,
            1,
            :acertos,
            0,
            0
        )
        ON CONFLICT (aluno_id)
        DO UPDATE SET
            licoes_concluidas =
                progresso.licoes_concluidas + 1,

            acertos =
                progresso.acertos + EXCLUDED.acertos
    ";

    $stmt = $conexao->prepare($sql);

    $stmt->execute([
        ':aluno' => $alunoId,
        ':acertos' => $pontuacao
    ]);

    $conexao->commit();

    responder(
        true,
        'Progresso salvo com sucesso.'
    );

} catch (Throwable $e) {

    if (
        isset($conexao) &&
        $conexao instanceof PDO &&
        $conexao->inTransaction()
    ) {
        $conexao->rollBack();
    }

    error_log(
        'Erro enviar_progresso.php: ' .
        $e->getMessage()
    );

    http_response_code(500);

    responder(
        false,
        'Erro ao salvar o progresso.'
    );
}