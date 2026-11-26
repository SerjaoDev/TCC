<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro | Lumi</title>
    <link rel="stylesheet" href="css/style.css?v=4">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

    <div class="background" aria-hidden="true">
        <span class="orb orb-1"></span>
        <span class="orb orb-2"></span>
        <span class="orb orb-3"></span>
        <span class="grid"></span>
    </div>

    <main class="login-card cadastro-card">

        <header class="card-header">
            <div class="logo-wrap">
                <img src="img/logo.png" class="logo" alt="Logo da Lumi">
            </div>

            <span class="badge"><i></i> Acesso educacional</span>

            <h1>Criar Conta</h1>

            <p>Cadastre-se para acessar o sistema.</p>
        </header>

        <form action="php/cadastrar.php" method="POST" class="login-form">

            <div class="field">
                <input type="text" id="nome" name="nome" placeholder=" " required autocomplete="name">
                <label for="nome">Nome completo</label>
            </div>

            <div class="field">
                <input type="email" id="email" name="email" placeholder=" " required autocomplete="email">
                <label for="email">E-mail</label>
            </div>

            <div class="field">
                <input type="password" id="senha" name="senha" placeholder=" " required autocomplete="new-password">
                <label for="senha">Senha</label>

                <button type="button" class="toggle-senha" aria-label="Mostrar senha" aria-pressed="false">
                    <svg class="eye" width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M2 12s3.6-6 10-6 10 6 10 6-3.6 6-10 6-10-6-10-6Z"/>
                        <circle cx="12" cy="12" r="2.6"/>
                    </svg>
                    <svg class="eye-off" width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 3l18 18"/>
                        <path d="M10.6 6.2A10.9 10.9 0 0 1 12 6c6.4 0 10 6 10 6a17 17 0 0 1-3.3 3.9M6.4 7.7A17 17 0 0 0 2 12s3.6 6 10 6a10.8 10.8 0 0 0 4-.7"/>
                        <path d="M9.9 9.9a3 3 0 0 0 4.2 4.2"/>
                    </svg>
                </button>
            </div>

            <div class="field">
                <input type="password" id="confirmar" name="confirmar" placeholder=" " required autocomplete="new-password">
                <label for="confirmar">Confirmar senha</label>

                <button type="button" class="toggle-senha" aria-label="Mostrar senha" aria-pressed="false">
                    <svg class="eye" width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M2 12s3.6-6 10-6 10 6 10 6-3.6 6-10 6-10-6-10-6Z"/>
                        <circle cx="12" cy="12" r="2.6"/>
                    </svg>
                    <svg class="eye-off" width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 3l18 18"/>
                        <path d="M10.6 6.2A10.9 10.9 0 0 1 12 6c6.4 0 10 6 10 6a17 17 0 0 1-3.3 3.9M6.4 7.7A17 17 0 0 0 2 12s3.6 6 10 6a10.8 10.8 0 0 0 4-.7"/>
                        <path d="M9.9 9.9a3 3 0 0 0 4.2 4.2"/>
                    </svg>
                </button>
            </div>

            <button class="entrar" type="submit">
                <span>Criar Conta</span>
                <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 12h13M13 6l6 6-6 6"/>
                </svg>
            </button>

        </form>

        <div class="links centralizado">
            <a href="index.php">Já tenho uma conta</a>
        </div>

    </main>

    <script src="js/script.js?v=4"></script>

</body>
</html>
