<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=UTF-8');

require_once __DIR__ . '/../php/conexao.php';

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

$aluno_id = filter_input(INPUT_POST, 'aluno_id', FILTER_VALIDATE_INT);
$licao_id = filter_input(INPUT_POST, 'licao_id', FILTER_VALIDATE_INT);

$resultado = trim($_POST['resultado'] ?? '');
$pontuacao = (int) ($_POST['pontuacao'] ?? 0);
$tempo = (int) ($_POST['tempo_gasto'] ?? 0);

if (!$aluno_id || !$licao_id) {
    http_response_code(400);

    resposta(
        false,
        'Aluno ou lição não informado.'
    );
}

if ($pontuacao < 0) {
    $pontuacao = 0;
}

if ($tempo < 0) {
    $tempo = 0;
}

try {
    $conexao->beginTransaction();

    $stmt = $conexao->prepare("
        SELECT id
        FROM alunos
        WHERE id = :aluno_id
        LIMIT 1
    ");

    $stmt->execute([
        ':aluno_id' => $aluno_id
    ]);

    if (!$stmt->fetchColumn()) {
        $conexao->rollBack();

        http_response_code(404);

        resposta(
            false,
            'Aluno não encontrado.'
        );
    }

    $stmt = $conexao->prepare("
        SELECT id
        FROM licoes
        WHERE id = :licao_id
        LIMIT 1
    ");

    $stmt->execute([
        ':licao_id' => $licao_id
    ]);

    if (!$stmt->fetchColumn()) {
        $conexao->rollBack();

        http_response_code(404);

        resposta(
            false,
            'Lição não encontrada.'
        );
    }

    $sql = "
        INSERT INTO desempenho (
            aluno_id,
            licao_id,
            resultado,
            pontuacao,
            tempo_gasto
        )
        VALUES (
            :aluno_id,
            :licao_id,
            :resultado,
            :pontuacao,
            :tempo
        )
    ";

    $stmt = $conexao->prepare($sql);

    $stmt->execute([
        ':aluno_id' => $aluno_id,
        ':licao_id' => $licao_id,
        ':resultado' => $resultado,
        ':pontuacao' => $pontuacao,
        ':tempo' => $tempo
    ]);

    $stmt = $conexao->prepare("
        INSERT INTO progresso (
            aluno_id,
            licoes_concluidas,
            acertos,
            tempo_estudo,
            ultimo_acesso
        )
        VALUES (
            :aluno_id,
            0,
            0,
            0,
            CURRENT_TIMESTAMP
        )
        ON CONFLICT (aluno_id) DO NOTHING
    ");

    $stmt->execute([
        ':aluno_id' => $aluno_id
    ]);

    $stmt = $conexao->prepare("
        UPDATE progresso
        SET
            licoes_concluidas = licoes_concluidas + 1,
            acertos = acertos + :pontuacao,
            tempo_estudo = tempo_estudo + :tempo,
            ultimo_acesso = CURRENT_TIMESTAMP
        WHERE aluno_id = :aluno_id
    ");

    $stmt->execute([
        ':pontuacao' => $pontuacao,
        ':tempo' => $tempo,
        ':aluno_id' => $aluno_id
    ]);

    $conexao->commit();

    resposta(
        true,
        'Progresso salvo com sucesso.'
    );
} catch (Throwable $e) {
    if ($conexao->inTransaction()) {
        $conexao->rollBack();
    }

    error_log(
        'Erro enviar_progresso.php: ' .
        $e->getMessage()
    );

    http_response_code(500);

    resposta(
        false,
        'Erro ao salvar progresso.'
    );
}