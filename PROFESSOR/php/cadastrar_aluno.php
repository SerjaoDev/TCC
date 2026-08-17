<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/conexao.php';

if (
    !isset($_SESSION['professor_id']) ||
    !is_numeric($_SESSION['professor_id'])
) {
    header('Location: ../index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../turmas.php');
    exit;
}

$professorId =
    (int) $_SESSION['professor_id'];

$nome = trim(
    (string) ($_POST['nome'] ?? '')
);

$usuario = trim(
    (string) ($_POST['usuario'] ?? '')
);

$senha = (string) (
    $_POST['senha'] ?? ''
);

$dataNascimento = trim(
    (string) (
        $_POST['data_nascimento'] ?? ''
    )
);

$turmaId = trim(
    (string) (
        $_POST['turma_id'] ?? ''
    )
);

if (
    $nome === '' ||
    $usuario === '' ||
    $senha === ''
) {
    header(
        'Location: ../novo_aluno.php?erro=preencha&turma=' .
        urlencode($turmaId)
    );

    exit;
}

if (mb_strlen($nome) > 150) {
    header(
        'Location: ../novo_aluno.php?erro=nome_longo&turma=' .
        urlencode($turmaId)
    );

    exit;
}

if (mb_strlen($usuario) < 3) {
    header(
        'Location: ../novo_aluno.php?erro=usuario_curto&turma=' .
        urlencode($turmaId)
    );

    exit;
}

if (strlen($senha) < 4) {
    header(
        'Location: ../novo_aluno.php?erro=senha_curta&turma=' .
        urlencode($turmaId)
    );

    exit;
}

$turmaIdBanco = null;

if ($turmaId !== '') {
    if (
        !filter_var(
            $turmaId,
            FILTER_VALIDATE_INT
        )
    ) {
        header(
            'Location: ../turmas.php?erro=turma'
        );

        exit;
    }

    $stmt = $conexao->prepare(
        "
        SELECT id
        FROM turmas
        WHERE id = :turma_id
        AND professor_id = :professor_id
        LIMIT 1
        "
    );

    $stmt->execute([
        ':turma_id' => (int) $turmaId,
        ':professor_id' => $professorId
    ]);

    if (!$stmt->fetch()) {
        header(
            'Location: ../turmas.php?erro=turma'
        );

        exit;
    }

    $turmaIdBanco = (int) $turmaId;
}

try {
    $stmt = $conexao->prepare(
        "
        SELECT id
        FROM alunos
        WHERE LOWER(usuario) = LOWER(:usuario)
        LIMIT 1
        "
    );

    $stmt->execute([
        ':usuario' => $usuario
    ]);

    if ($stmt->fetch()) {
        header(
            'Location: ../novo_aluno.php?erro=usuario_existente&turma=' .
            urlencode($turmaId)
        );

        exit;
    }

    $senhaHash = password_hash(
        $senha,
        PASSWORD_DEFAULT
    );

    $dataNascimentoBanco = null;

    if ($dataNascimento !== '') {
        $data = DateTime::createFromFormat(
            'Y-m-d',
            $dataNascimento
        );

        if (
            !$data ||
            $data->format('Y-m-d') !==
            $dataNascimento
        ) {
            header(
                'Location: ../novo_aluno.php?erro=data&turma=' .
                urlencode($turmaId)
            );

            exit;
        }

        $dataNascimentoBanco =
            $dataNascimento;
    }

    $conexao->beginTransaction();

    $stmt = $conexao->prepare(
        "
        INSERT INTO alunos
        (
            professor_id,
            nome,
            usuario,
            senha,
            foto,
            data_nascimento,
            turma_id
        )
        VALUES
        (
            :professor_id,
            :nome,
            :usuario,
            :senha,
            :foto,
            :data_nascimento,
            :turma_id
        )
        RETURNING id
        "
    );

    $stmt->execute([
        ':professor_id' => $professorId,
        ':nome' => $nome,
        ':usuario' => $usuario,
        ':senha' => $senhaHash,
        ':foto' => 'padrao.png',
        ':data_nascimento' =>
            $dataNascimentoBanco,
        ':turma_id' => $turmaIdBanco
    ]);

    $alunoId =
        $stmt->fetchColumn();

    if (!$alunoId) {
        throw new RuntimeException(
            'Aluno não pôde ser criado.'
        );
    }

    $stmt = $conexao->prepare(
        "
        INSERT INTO progresso
        (
            aluno_id,
            nivel_atual,
            estrelas,
            moedas,
            licoes_concluidas,
            acertos,
            erros,
            tempo_estudo
        )
        VALUES
        (
            :aluno_id,
            1,
            0,
            0,
            0,
            0,
            0,
            0
        )
        "
    );

    $stmt->execute([
        ':aluno_id' => $alunoId
    ]);

    $conexao->commit();

    if ($turmaIdBanco !== null) {
        header(
            'Location: ../relatorios.php?turma=' .
            urlencode((string) $turmaIdBanco) .
            '&sucesso=aluno_criado'
        );
    } else {
        header(
            'Location: ../turmas.php?sucesso=aluno_criado'
        );
    }

    exit;
} catch (PDOException $e) {
    if ($conexao->inTransaction()) {
        $conexao->rollBack();
    }

    error_log(
        'ERRO CADASTRAR ALUNO: ' .
        $e->getMessage()
    );

    if ($e->getCode() === '23505') {
        header(
            'Location: ../novo_aluno.php?erro=usuario_existente&turma=' .
            urlencode($turmaId)
        );

        exit;
    }

    http_response_code(500);

    exit(
        'Erro ao cadastrar aluno.'
    );
} catch (Throwable $e) {
    if ($conexao->inTransaction()) {
        $conexao->rollBack();
    }

    error_log(
        'ERRO CADASTRAR ALUNO: ' .
        $e->getMessage()
    );

    http_response_code(500);

    exit(
        'Erro ao cadastrar aluno.'
    );
}