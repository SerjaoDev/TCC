<?php

declare(strict_types=1);

require_once __DIR__ . '/php/verificar.php';
require_once __DIR__ . '/php/conexao.php';

$professorId = filter_var(
    $_SESSION['id'] ?? null,
    FILTER_VALIDATE_INT
);

if (!$professorId) {
    header('Location: index.php');
    exit;
}

$alunos = [];
$erro = null;

try {
    $sql = "
        SELECT
            a.id,
            a.nome,
            a.data_nascimento,
            a.turma_id,
            t.nome AS turma_nome,
            COALESCE(p.acertos, 0) AS acertos
        FROM alunos a
        LEFT JOIN turmas t
            ON t.id = a.turma_id
        LEFT JOIN progresso p
            ON p.aluno_id = a.id
        WHERE a.professor_id = :professor_id
        ORDER BY a.nome ASC
    ";

    $stmt = $conexao->prepare($sql);

    $stmt->execute([
        ':professor_id' => $professorId
    ]);

    $alunos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    error_log(
        'Erro ao listar alunos: ' . $e->getMessage()
    );

    $erro =
        'Não foi possível carregar os alunos.';
}

function calcularIdade(?string $dataNascimento): string
{
    if (!$dataNascimento) {
        return '-';
    }

    try {
        $nascimento =
            new DateTime($dataNascimento);

        $hoje =
            new DateTime();

        return (string)
        $nascimento->diff($hoje)->y;
    } catch (Throwable $e) {
        return '-';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">
    <title>Alunos | Lumi</title>
    <link
        rel="stylesheet"
        href="css/alunos.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
</head>

<body>
    <div class="sidebar">
        <img
            src="img/logo.png"
            class="logo"
            alt="Logo Lumi">
        <h2>Lumi</h2>
        <ul>
            <li>
                <a href="painel.php">
                    🏠 Dashboard
                </a>
            </li>
            <li class="ativo">
                👨‍🎓 Alunos
            </li>
            <li>
                <a href="turmas.php">
                    📚 Turmas
                </a>
            </li>
            <li>
                <a href="logout.php">
                    🚪 Sair
                </a>
            </li>
        </ul>
    </div>
    <div class="conteudo">
        <div class="topo-alunos">
            <div>
                <h1>
                    Alunos
                </h1>
                <p>
                    Gerencie os alunos cadastrados no sistema.
                </p>
            </div>
            <a
                href="turmas.php"
                class="novo">
                + Novo Aluno
            </a>
        </div>
        <?php if ($erro): ?>
            <div class="mensagem-erro">
                <?= htmlspecialchars(
                    $erro,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </div>
        <?php endif; ?>
        <div class="tabela-container">
            <table>
                <thead>
                    <tr>
                        <th>Foto</th>
                        <th>Nome</th>
                        <th>Turma</th>
                        <th>Idade</th>
                        <th>Desempenho</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($alunos)): ?>
                        <tr>
                            <td
                                colspan="6"
                                class="sem-alunos">
                                Nenhum aluno cadastrado.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($alunos as $aluno): ?>
                            <?php
                            $idade =
                                calcularIdade(
                                    $aluno['data_nascimento'] ?? null
                                );

                            $acertos =
                                (float) ($aluno['acertos'] ?? 0);

                            $acertos =
                                max(0, min(100, $acertos));
                            ?>
                            <tr>
                                <td>
                                    <img
                                        src="<?= !empty($aluno['foto'])
                                                    ? htmlspecialchars(
                                                        (string) $aluno['foto'],
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    )
                                                    : 'img/logo.png' ?>"
                                        class="foto"
                                        alt="Foto do aluno"
                                        onerror="this.src='img/logo.png';">
                                </td>
                                <td>
                                    <?= htmlspecialchars(
                                        (string) $aluno['nome'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </td>
                                <td>
                                    <?= htmlspecialchars(
                                        (string) (
                                            $aluno['turma_nome']
                                            ?? 'Sem turma'
                                        ),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </td>
                                <td>
                                    <?= $idade === '-'
                                        ? '-'
                                        : $idade . ' anos' ?>
                                </td>
                                <td>
                                    <?= round($acertos) ?>%
                                </td>
                                <td class="acoes">
                                    <a
                                        href="aluno.php?id=<?= (int) $aluno['id'] ?>"
                                        class="editar">
                                        Abrir
                                    </a>
                                    <a
                                        href="editar_aluno.php?id=<?= (int) $aluno['id'] ?>"
                                        class="editar">
                                        Editar
                                    </a>
                                    <a
                                        href="php/excluir_aluno.php?id=<?= (int) $aluno['id'] ?>"
                                        class="excluir"
                                        onclick="return confirm('Tem certeza que deseja excluir este aluno?');">
                                        Excluir
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>