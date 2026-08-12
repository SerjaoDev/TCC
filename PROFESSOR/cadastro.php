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
        <img
            src="img/logo.png"
            class="logo"
            alt="Logo Lumi">

        <h1>Criar Conta</h1>

        <p>
            Cadastre-se para acessar o sistema.
        </p>

        <form
            action="php/cadastrar.php"
            method="POST"
            id="formCadastro">

            <input
                type="text"
                name="nome"
                placeholder="Nome completo"
                autocomplete="name"
                minlength="3"
                maxlength="150"
                required>

            <input
                type="email"
                name="email"
                placeholder="E-mail"
                autocomplete="email"
                maxlength="150"
                required>

            <input
                type="password"
                name="senha"
                id="senha"
                placeholder="Senha"
                autocomplete="new-password"
                minlength="6"
                required>

            <input
                type="password"
                name="confirmar"
                id="confirmar"
                placeholder="Confirmar senha"
                autocomplete="new-password"
                minlength="6"
                required>

            <p
                id="mensagemSenha"
                style="
            display: none;
            color: #d9534f;
            font-size: 14px;
            margin-top: -5px;
            margin-bottom: 15px;
        ">
                As senhas não coincidem.
            </p>

            <button
                type="submit"
                class="entrar"
                id="botaoCadastro">
                Criar Conta
            </button>
        </form>

        <div class="links">
            <a href="index.php">
                Já tenho uma conta
            </a>
        </div>
    </div>

    <script>
        const formulario = document.getElementById('formCadastro');
        const senha = document.getElementById('senha');
        const confirmar = document.getElementById('confirmar');
        const mensagemSenha = document.getElementById('mensagemSenha');
        const botaoCadastro = document.getElementById('botaoCadastro');

        function validarSenhas() {
            if (
                confirmar.value.length > 0 &&
                senha.value !== confirmar.value
            ) {
                mensagemSenha.style.display = 'block';
                confirmar.style.borderColor = '#d9534f';

                return false;
            }
            mensagemSenha.style.display = 'none';
            confirmar.style.borderColor = '';

            return true;
        }

        senha.addEventListener('input', validarSenhas);
        confirmar.addEventListener('input', validarSenhas);

        formulario.addEventListener('submit', function(event) {
            if (!validarSenhas()) {
                event.preventDefault();
                confirmar.focus();

                return;
            }

            botaoCadastro.disabled = true;
            botaoCadastro.textContent = 'Criando conta...';
        });
    </script>
</body>

</html>