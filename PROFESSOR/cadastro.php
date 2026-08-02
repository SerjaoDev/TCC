<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro | Lumi</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="background"></div>

<div class="login-card">

    <img src="img/logo.png" class="logo">

    <h1>Criar Conta</h1>
    <p>Cadastre-se para acessar o sistema.</p>

    <form action="php/cadastrar.php" method="POST">

        <input
            type="text"
            name="nome"
            placeholder="Nome completo"
            required>

        <input
            type="email"
            name="email"
            placeholder="E-mail"
            required>

        <input
            type="password"
            name="senha"
            placeholder="Senha"
            required>

        <input
            type="password"
            name="confirmar"
            placeholder="Confirmar senha"
            required>

        <button class="entrar">
            Criar Conta
        </button>
    </form>

    <div class="links">

        <a href="index.php">
            Já tenho uma conta
        </a>
    </div>
</div>

</body>
</html>