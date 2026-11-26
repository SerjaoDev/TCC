<?php

include("php/verificar.php");
include("php/listar_turmas.php");

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Turmas | Lumi</title>
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
        <a href="turmas.php" class="ativo">📚 Turmas</a>
        <a href="logout.php">🚪 Sair</a>
    </nav>
</header>

<div class="conteudo">
    <header class="page-header">
        <div>
            <span class="badge"><i></i> Gestão</span>
            <h1>Turmas</h1>
            <p>Organize suas turmas e acompanhe o progresso de cada uma.</p>
        </div>
    </header>

    <div class="page-actions">
        <a href="adicionar_turma.php" class="btn">+ Nova Turma</a>
    </div>

    <?php while($turma = $turmas->fetch_assoc()){ ?>

    <div class="card">

        <h2>
            <?php echo $turma["nome"]; ?>
        </h2>

        <p>
            👨‍🎓 <?php echo $turma["quantidade_alunos"]; ?> alunos
        </p>

        <div class="acoes">
            <a href="relatorios.php?turma=<?php echo $turma['id']; ?>" class="btn abrir">Abrir Turma</a>
            <a href="php/excluir_turma.php?id=<?php echo $turma['id']; ?>"
               class="btn excluir"
               onclick="return confirm('Tem certeza que deseja excluir essa turma?');">Excluir</a>
        </div>

    </div>
    <?php } ?>
</div>

</body>
</html>