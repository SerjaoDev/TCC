<?php
session_start();
require_once 'conexao.php';

$erro = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['usuario'])) {
    $usuario = trim($_POST['usuario']);
    $senha = trim($_POST['senha']);

    $stmt = $pdo->prepare("SELECT * FROM alunos WHERE usuario = :usuario");
    $stmt->execute([':usuario' => $usuario]);
    $aluno = $stmt->fetch(PDO::FETCH_ASSOC);

    $senha_correta = false;
    if ($aluno) {
        if (isset($aluno['senha_visivel']) && $senha === $aluno['senha_visivel']) {
            $senha_correta = true;
        } elseif (isset($aluno['senha']) && (password_verify($senha, $aluno['senha']) || $senha === $aluno['senha'])) {
            $senha_correta = true;
        }
    }

    if ($aluno && $senha_correta) {
        $_SESSION['aluno_id'] = $aluno['id'];
        $_SESSION['aluno_nome'] = $aluno['nome'];
        $_SESSION['aluno_usuario'] = $aluno['usuario'];

        $stmt_acesso = $pdo->prepare("UPDATE alunos SET ultimo_acesso = NOW() WHERE id = :id");
        $stmt_acesso->execute([':id' => $aluno['id']]);

        header("Location: trilha.php");
        exit;
    } else {
        $erro = "Usuário ou senha incorretos!";
    }
}

$esta_logado = isset($_SESSION['aluno_id']);

$ultimo_acesso = null;
if ($esta_logado) {
    $stmt_busca = $pdo->prepare("SELECT ultimo_acesso FROM alunos WHERE id = :id");
    $stmt_busca->execute([':id' => $_SESSION['aluno_id']]);
    $dados_aluno = $stmt_busca->fetch(PDO::FETCH_ASSOC);
    $ultimo_acesso = $dados_aluno['ultimo_acesso'] ?? null;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LUMI - <?= $esta_logado ? 'Meu Perfil' : 'Entrar' ?></title>
    <link rel="icon" type="image/png" href="img/logo.png">
    <link rel="stylesheet" href="css/login.css">
</head>
<body class="login-body">

<div class="login-card">

    <div class="logo-container">
        <img src="img/logo.png" alt="LUMI Logo" class="login-logo">
    </div>

    <?php if ($esta_logado): ?>
        <h2>Olá, <?= htmlspecialchars($_SESSION['aluno_nome']) ?>! 👋</h2>
        <p class="subtitulo">Você já está conectado ao LUMI.</p>

        <div class="perfil-info">
            <p><strong>Usuário:</strong> <?= htmlspecialchars($_SESSION['aluno_usuario'] ?? 'Aluno') ?></p>
            <p><strong>Status:</strong> Conectado 🟢</p>
            
            <p style="margin-top: 10px;">
    ⏱️ <strong>Último acesso:</strong> 
    <?= $ultimo_acesso ? date('d/m/Y \à\s H:i', strtotime($ultimo_acesso)) : 'Nenhum acesso registrado' ?>
</p>
        </div>

        <a href="logout.php" class="btn-sair">Sair / Desconectar 🔴</a>

    <?php else: ?>
        <h2>Área do Aluno</h2>
        <p class="subtitulo">Faça login para acessar seu painel.</p>

        <?php if ($erro): ?>
            <p class="msg-erro"><?php echo $erro; ?></p>
        <?php endif; ?>

        <form method="POST" class="login-form">
            <div class="input-group">
                <input type="text" name="usuario" placeholder="E-mail ou Usuário" required>
            </div>
            
            <div class="input-group">
                <input type="password" name="senha" placeholder="Senha" required>
            </div>

            <button type="submit" class="btn-entrar">Entrar</button>
        </form>
    <?php endif; ?>

    <a href="trilha.php" class="btn-voltar">← Voltar para Trilha</a>
</div>

</body>
</html>