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
<link rel="stylesheet" href="css/turmas.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

<div class="sidebar">

    <img src="img/logo.png" class="logo">

    <h2>Lumi</h2>

    <ul>
        <li><a href="painel.php">🏠 Dashboard</a></li>
        <li class="ativo">📚 Turmas</li>
        <li><a href="logout.php">🚪 Sair</a></li>
    </ul>

</div>

<div class="conteudo">

    <h1>Turmas</h1>

    <a href="adicionar_turma.php">
        <button class="novo">
            + Nova Turma
        </button>
    </a>

    <?php while($turma = $turmas->fetch_assoc()){ ?>

    <div class="card">

        <h2>
            <?php echo $turma["nome"]; ?>
        </h2>

        <p>
            👨‍🎓 <?php echo $turma["quantidade_alunos"]; ?> alunos
        </p>

        <a href="relatorios.php?turma=<?php echo $turma['id']; ?>">
    <button>
        Abrir Turma
    </button>
</a>


<a href="php/excluir_turma.php?id=<?php echo $turma['id']; ?>" 
onclick="return confirm('Tem certeza que deseja excluir essa turma?');">
    <button class="excluir">
        Excluir
    </button>
</a>

    </div>
    <?php } ?>
</div>

</body>
</html>