<?php
declare(strict_types=1);

require_once __DIR__ . '/php/verificar.php';

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">
    <title>Nova Turma | Lumi</title>
    <link
        rel="stylesheet"
        href="css/style.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
</head>

<body>
    <div class="background"></div>
    <main class="login-card">
        <h1>Nova Turma</h1>
        <p>
            Cadastre uma nova turma para organizar seus alunos.
        </p>

        <form
            action="php/cadastrar_turma.php"
            method="POST">
            <input
                type="text"
                name="nome"
                placeholder="Nome da turma"
                maxlength="100"
                autocomplete="off"
                required>

            <button
                type="submit"
                class="entrar">
                Criar Turma
            </button>
        </form>

        <div class="links">
            <a href="turmas.php">
                Voltar para turmas
            </a>
        </div>
    </main>
</body>

</html>