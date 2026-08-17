<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=UTF-8');

require_once __DIR__ . '/../php/conexao.php';

$usuario = trim($_POST['usuario'] ?? '');
$senha = $_POST['senha'] ?? '';

if ($usuario === '' || $senha === '') {
    http_response_code(400);

    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Preencha usuário e senha.'
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

try {
    $stmt = $conexao->prepare("
        SELECT
            id,
            professor_id,
            nome,
            usuario,
            senha,
            foto,
            turma_id
        FROM alunos
        WHERE usuario = :usuario
        LIMIT 1
    ");

    $stmt->execute([
        ':usuario' => $usuario
    ]);

    $aluno = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$aluno) {
        http_response_code(401);

        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'Usuário ou senha incorretos.'
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    if (!password_verify($senha, $aluno['senha'])) {
        http_response_code(401);

        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'Usuário ou senha incorretos.'
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    $stmt = $conexao->prepare("
        SELECT
            nivel_atual,
            estrelas,
            moedas,
            licoes_concluidas,
            acertos,
            erros,
            tempo_estudo,
            ultimo_acesso
        FROM progresso
        WHERE aluno_id = :aluno_id
        LIMIT 1
    ");

    $stmt->execute([
        ':aluno_id' => $aluno['id']
    ]);

    $progresso = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$progresso) {
        $stmt = $conexao->prepare("
            INSERT INTO progresso (aluno_id)
            VALUES (:aluno_id)
            ON CONFLICT (aluno_id) DO NOTHING
        ");

        $stmt->execute([
            ':aluno_id' => $aluno['id']
        ]);

        $stmt = $conexao->prepare("
            SELECT
                nivel_atual,
                estrelas,
                moedas,
                licoes_concluidas,
                acertos,
                erros,
                tempo_estudo,
                ultimo_acesso
            FROM progresso
            WHERE aluno_id = :aluno_id
            LIMIT 1
        ");

        $stmt->execute([
            ':aluno_id' => $aluno['id']
        ]);

        $progresso = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    $stmt = $conexao->prepare("
        UPDATE progresso
        SET ultimo_acesso = CURRENT_TIMESTAMP
        WHERE aluno_id = :aluno_id
    ");

    $stmt->execute([
        ':aluno_id' => $aluno['id']
    ]);

    echo json_encode([
        'sucesso' => true,
        'aluno' => [
            'id' => (int) $aluno['id'],
            'nome' => $aluno['nome'],
            'turma' => $aluno['turma_id'] !== null
                ? (int) $aluno['turma_id']
                : null,
            'foto' => $aluno['foto'] ?? 'padrao.png',
            'nivel' => (int) ($progresso['nivel_atual'] ?? 1),
            'estrelas' => (int) ($progresso['estrelas'] ?? 0),
            'moedas' => (int) ($progresso['moedas'] ?? 0),
            'licoes' => (int) ($progresso['licoes_concluidas'] ?? 0),
            'acertos' => (int) ($progresso['acertos'] ?? 0),
            'erros' => (int) ($progresso['erros'] ?? 0),
            'tempo_estudo' => (int) ($progresso['tempo_estudo'] ?? 0)
        ]
    ], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    error_log(
        'Erro login_aluno.php: ' .
        $e->getMessage()
    );

    http_response_code(500);

    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Erro interno ao realizar login.'
    ], JSON_UNESCAPED_UNICODE);
}

exit;