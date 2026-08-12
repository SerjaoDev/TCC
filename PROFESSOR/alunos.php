<?php

declare(strict_types=1);

require_once __DIR__ . '/php/verificar.php';
require_once __DIR__ . '/php/conexao.php';

$professorId = $_SESSION['id'];

try {
    $sql = "SELECT a.id,a.nome,a.data_nascimento,a.turma_id,t.nome AS turma_nome,COALESCE(p.acertos, 0) AS acertos FROM alunos a LEFT JOIN turmas t ON t.id = a.turma_id LEFT JOIN progresso p ON p.aluno_id = a.id WHERE a.professor_id = :professor_id ORDER BY a.nome ASC";

    $stmt = $conexao->prepare($sql);
    $stmt->execute([
        ':professor_id' => $professorId
    ]);

    $alunos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log('Erro ao listar alunos: ' . $e->getMessage());

    $alunos = [];
    $erro = 'Não foi possível carregar os alunos.';
}

function calcularIdade(?string $dataNascimento): string
{
    if (empty($dataNascimento)) {
        return '-';
    }

    try {
        $nascimento = new DateTime($dataNascimento);
        $hoje = new DateTime();

        return (string) $nascimento->diff($hoje)->y;
    } catch (Exception $e) {
        return '-';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alunos | Lumi</title>
    <link rel="stylesheet" href="css/alunos.css">
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
                <a href="relatorios.php">
                    📊 Relatórios
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
                <h1>Alunos</h1>
                <p>
                    Gerencie os alunos cadastrados no sistema.
                </p>
            </div>

            <a href="turmas.php">
                <button type="button" class="novo">
                    + Novo Aluno
                </button>
            </a>
        </div>

        <?php if (isset($erro)): ?>
            <div class="mensagem-erro">
                <?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8'); ?>
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
                            <td colspan="6" class="sem-alunos">
                                Nenhum aluno cadastrado.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($alunos as $aluno): ?>
                            <tr>
                                <td>
                                    <img
                                        src="img/user.png"
                                        class="foto"
                                        alt="Foto do aluno"
                                        onerror="this.src='img/logo.png';">
                                </td>
                                <td>
                                    <?= htmlspecialchars(
                                        $aluno['nome'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ); ?>
                                </td>
                                <td>
                                    <?= htmlspecialchars(
                                        $aluno['turma_nome'] ?? 'Sem turma',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ); ?>
                                </td>
                                <td>
                                    <?php
                                    $idade = calcularIdade(
                                        $aluno['data_nascimento'] ?? null
                                    );

                                    echo $idade === '-'
                                        ? '-'
                                        : $idade . ' anos';
                                    ?>
                                </td>
                                <td>
                                    <?= round(
                                        (float) ($aluno['acertos'] ?? 0)
                                    ); ?>%
                                </td>
                                <td>
                                    <a href="aluno.php?id=<?= (int) $aluno['id']; ?>">
                                        <button
                                            type="button"
                                            class="editar">
                                            Abrir
                                        </button>
                                    </a>
                                    <a href="editar_aluno.php?id=<?= (int) $aluno['id']; ?>">
                                        <button
                                            type="button"
                                            class="editar">
                                            Editar
                                        </button>
                                    </a>
                                    <a
                                        href="php/excluir_aluno.php?id=<?= (int) $aluno['id']; ?>"
                                        onclick="return confirm('Tem certeza que deseja excluir este aluno?');">
                                        <button
                                            type="button"
                                            class="excluir">
                                            Excluir
                                        </button>
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