<?php
include("php/verificar.php");
include("php/conexao.php");

$professor_id = $_SESSION["id"] ?? null;

if (!$professor_id) {
    header("Location: index.php");
    exit;
}

// Recebe o ID da turma via GET (caso venha da página de uma turma específica)
$turma_id = filter_input(INPUT_GET, 'turma', FILTER_VALIDATE_INT);

// Busca todas as turmas do professor para preencher o select
$stmt_turmas = $conexao->prepare("SELECT id, nome FROM turmas WHERE professor_id = ? ORDER BY nome ASC");
$stmt_turmas->bind_param("i", $professor_id);
$stmt_turmas->execute();
$res_turmas = $stmt_turmas->get_result();
$turmas = $res_turmas ? $res_turmas->fetch_all(MYSQLI_ASSOC) : [];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Aluno | Lumi</title>
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

    <main class="login-card cadastro-card">

        <header class="card-header">
            <div class="logo-wrap">
                <img src="img/logo.png" class="logo" alt="Logo da Lumi">
            </div>
            <span class="badge"><i></i> Gestão</span>
            <h1>Novo Aluno</h1>
            <p>Cadastre um aluno para acessar o aplicativo.</p>
        </header>

        <form action="php/cadastrar_aluno.php" method="POST" class="login-form">

            <div class="field">
                <input type="text" id="nome" name="nome" placeholder=" " required autocomplete="name">
                <label for="nome">Nome do aluno</label>
            </div>

            <div class="field">
                <input type="text" id="usuario" name="usuario" placeholder=" " required autocomplete="username">
                <label for="usuario">Usuário de login</label>
            </div>

            <?php if (isset($_GET['erro']) && $_GET['erro'] === 'usuario_indisponivel'): ?>
                <span class="erro">Nome de usuário indisponível</span>
            <?php endif; ?>

            <div class="field">
                <input type="password" id="senha" name="senha" placeholder=" " required autocomplete="new-password">
                <label for="senha">Senha do aluno</label>
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
                <input type="date" id="data_nascimento" name="data_nascimento">
                <label for="data_nascimento">Data de nascimento</label>
            </div>

            <?php if ($turma_id): ?>
                <input type="hidden" name="turma_id" value="<?php echo htmlspecialchars($turma_id); ?>">
            <?php else: ?>
                <div class="field">
                    <select id="turma_id" name="turma_id" required>
                        <option value="">Selecione a Turma</option>
                        <?php foreach ($turmas as $turma): ?>
                            <option value="<?php echo $turma['id']; ?>">
                                <?php echo htmlspecialchars($turma['nome']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <label for="turma_id">Turma</label>
                </div>
            <?php endif; ?>

            <button class="entrar" type="submit">
                <span>Criar aluno</span>
                <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 12h13M13 6l6 6-6 6"/>
                </svg>
            </button>

        </form>

        <div class="links">
            <a href="javascript:history.back()">Voltar</a>
        </div>

    </main>

    <script src="js/script.js?v=3"></script>

</body>
</html>
