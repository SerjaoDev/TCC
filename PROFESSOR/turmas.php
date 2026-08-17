<?php

declare(strict_types=1);

require_once __DIR__ . '/php/verificar.php';
require_once __DIR__ . '/php/listar_turmas.php';
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">
    <title>
        Turmas | Lumi
    </title>
    <link
        rel="stylesheet"
        href="css/turmas.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
</head>

<body>
    <div class="sidebar">
        <img
            src="img/logo.png"
            class="logo"
            alt="Lumi">
        <h2>
            Lumi
        </h2>
        <ul>
            <li>
                <a href="painel.php">
                    🏠 Dashboard
                </a>
            </li>
            <li>
                <a href="alunos.php">
                    👨‍🎓 Alunos
                </a>
            </li>
            <li class="ativo">
                📚 Turmas
            </li>
            <li>
                <a href="logout.php">
                    🚪 Sair
                </a>
            </li>
        </ul>
    </div>
    <div class="conteudo">
        <div class="cabecalho">
            <div>
                <h1>
                    Turmas
                </h1>
                <p>
                    Organize seus alunos por turma.
                </p>
            </div>
            <a
                href="adicionar_turma.php"
                class="novo">
                + Nova Turma
            </a>
        </div>
        <div class="turmas-container">
            <?php if (empty($turmas)): ?>
                <div class="sem-turmas">
                    <h2>
                        Nenhuma turma cadastrada
                    </h2>
                    <p>
                        Crie sua primeira turma para
                        começar a cadastrar alunos.
                    </p>
                    <a
                        href="adicionar_turma.php"
                        class="novo">
                        + Criar primeira turma
                    </a>
                </div>

            <?php else: ?>
                <?php foreach ($turmas as $turma): ?>
                    <?php
                    $quantidadeAlunos =
                        (int) (
                            $turma['quantidade_alunos']
                            ?? 0
                        );
                    ?>
                    <div class="card">
                        <h2>
                            <?= htmlspecialchars(
                                (string) $turma['nome'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </h2>
                        <p>
                            👨‍🎓
                            <?= $quantidadeAlunos ?>
                            aluno<?= $quantidadeAlunos === 1
                                        ? ''
                                        : 's' ?>
                        </p>
                        <div class="acoes">
                            <a
                                href="relatorios.php?turma=<?= (int) $turma['id'] ?>"
                                class="abrir">
                                Abrir Turma
                            </a>
                            <a
                                href="php/excluir_turma.php?id=<?= (int) $turma['id'] ?>"
                                class="excluir"
                                onclick="return confirmarExclusao();">
                                Excluir
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function confirmarExclusao() {
            return confirm(
                'Tem certeza que deseja excluir esta turma?\n\n' +
                'Os alunos vinculados a ela também poderão ser afetados.'
            );
        }
    </script>
</body>

</html>