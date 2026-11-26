<?php

include("php/verificar.php");

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nova Turma | Lumi</title>
    <link rel="stylesheet" href="css/app.css?v=3">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="form-page">

    <div class="background" aria-hidden="true">
        <span class="orb orb-1"></span>
        <span class="orb orb-2"></span>
        <span class="orb orb-3"></span>
        <span class="grid"></span>
    </div>

    <main class="login-card">

        <header class="card-header">
            <div class="logo-wrap">
                <img src="img/logo.png" class="logo" alt="Logo da Lumi">
            </div>
            <span class="badge"><i></i> Gestão</span>
            <h1>Nova Turma</h1>
            <p>Crie uma turma para organizar seus alunos.</p>
        </header>

        <form action="php/cadastrar_turma.php" method="POST" class="login-form">

            <div class="field">
                <input type="text" id="nome" name="nome" placeholder=" " required>
                <label for="nome">Nome da turma</label>
            </div>

            <button class="entrar" type="submit">
                <span>Criar Turma</span>
                <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 12h13M13 6l6 6-6 6"/>
                </svg>
            </button>

        </form>

        <div class="links">
            <a href="turmas.php">Voltar</a>
        </div>

    </main>

    <script src="js/script.js?v=3"></script>

</body>
</html>
