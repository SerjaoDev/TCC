<?php
include("php/verificar.php");
include("php/conexao.php");

date_default_timezone_set('America/Sao_Paulo');

// Busca alunos via MySQLi ($conexao)
$sql = "SELECT * FROM alunos ORDER BY id DESC";
$resultado = $conexao->query($sql);

$alunos = [];
if ($resultado && $resultado->num_rows > 0) {
    while ($row = $resultado->fetch_assoc()) {
        $alunos[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Lista de Alunos - Lumi</title>
    <link rel="stylesheet" href="css/app.css?v=3">
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

    <header class="topbar">
        <a href="painel.php" class="topbar-brand">
            <img src="img/logo.png" class="topbar-logo" alt="Logo Lumi">
            <span>Lumi</span>
        </a>

        <nav class="topbar-nav">
            <a href="painel.php">🏠 Dashboard</a>
            <a href="turmas.php">📚 Turmas</a>
            <a href="logout.php">🚪 Sair</a>
        </nav>
    </header>

    <main class="conteudo">
        <header class="page-header">
            <div>
                <span class="badge"><i></i> Gestão</span>
                <h1>Alunos Cadastrados</h1>
                <p>Veja os alunos e acompanhe o progresso de cada um.</p>
            </div>
        </header>

        <div class="page-actions">
            <a href="novo_aluno.php" class="btn">+ Cadastrar Novo Aluno</a>
        </div>

        <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Usuário</th>
                    <th>Último Acesso ao App</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($alunos)): ?>
                    <tr>
                        <td colspan="4">Nenhum aluno encontrado.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($alunos as $aluno): ?>
                        <tr>
                            <td><?= htmlspecialchars($aluno['nome'] ?? '') ?></td>
                            <td><?= htmlspecialchars($aluno['usuario'] ?? '') ?></td>
                            <td>
                                ⏱️ <?= (!empty($aluno['ultimo_acesso']) && $aluno['ultimo_acesso'] !== '0000-00-00 00:00:00') 
                                    ? date('d/m/Y \à\s H:i', strtotime($aluno['ultimo_acesso'])) 
                                    : 'Nenhum acesso registrado' ?>
                            </td>
                            <td>
                                <div class="acoes">
                                    <a href="aluno.php?id=<?= $aluno['id'] ?>" class="btn abrir">Ver Progresso</a>
                                    <a href="dados_aluno.php?id=<?= $aluno['id'] ?>" class="btn">Ver Senha</a>
                                    <a href="editar_aluno.php?id=<?= $aluno['id'] ?>" class="btn">Editar</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        </div>
    </main>
</body>
</html>