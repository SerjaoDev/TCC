<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Professor | Lumi</title>
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

    <main class="login-card">

        <header class="card-header">
            <div class="logo-wrap">
                <img src="img/logo.png" class="logo" alt="Logo da Lumi">
            </div>

            <span class="badge"><i></i> Acesso educacional</span>

            <h1>Área do Professor</h1>

            <p>Faça login para acessar seu painel.</p>
        </header>

        <form action="php/login.php" method="POST" class="login-form">

            <div class="field">
                <input type="email" id="email" name="email" placeholder=" " required autocomplete="email">
                <label for="email">E-mail</label>
            </div>

            <div class="field">
                <input type="password" id="senha" name="senha" placeholder=" " required autocomplete="current-password">
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

            <button class="entrar" type="submit">
                <span>Entrar</span>
                <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 12h13M13 6l6 6-6 6"/>
                </svg>
            </button>

        </form>

        <div class="divider"><span>ou continue com</span></div>

        <button class="google" type="button">
            <svg width="20" height="20" viewBox="0 0 48 48" aria-hidden="true">
                <path fill="#EA4335" d="M24 9.5c3.5 0 6.6 1.2 9 3.6l6.7-6.7C35.6 2.5 30.2 0 24 0 14.6 0 6.5 5.4 2.6 13.3l7.8 6c1.9-5.7 7.2-9.8 13.6-9.8Z"/>
                <path fill="#4285F4" d="M46.1 24.6c0-1.6-.1-3.1-.4-4.6H24v9.1h12.4a10.6 10.6 0 0 1-4.6 7l7.2 5.5c4.2-3.9 6.6-9.7 6.6-16.6l-.5-.4Z"/>
                <path fill="#FBBC05" d="M10.4 28.7a14.4 14.4 0 0 1 0-9.4l-7.8-6a24 24 0 0 0 0 21.4l7.8-6Z"/>
                <path fill="#34A853" d="M24 48c6.5 0 11.9-2.1 15.9-5.8l-7.2-5.5c-2 1.3-4.5 2.1-8.7 2.1-6.4 0-11.7-4.1-13.6-9.8l-7.8 6C6.5 42.6 14.6 48 24 48Z"/>
            </svg>
            <span>Entrar com Google</span>
        </button>

        <div class="links">
            <a href="cadastro.php">Criar conta</a>
            <a href="recuperar.php">Esqueci minha senha</a>
        </div>

    </main>

    <script src="js/script.js?v=4"></script>

</body>
</html>
