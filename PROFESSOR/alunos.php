<!DOCTYPE html>
<html lang="pt-BR">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Alunos | Lumi</title>

<link rel="stylesheet" href="css/alunos.css">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

</head>

<body>

<div class="sidebar">

    <img src="img/logo.png" class="logo">

    <h2>Lumi</h2>

    <ul>

        <li><a href="painel.php">🏠 Dashboard</a></li>

        <li class="ativo">👨‍🎓 Alunos</li>

        <li><a href="turmas.php">📚 Turmas</a></li>

        <li><a href="relatorios.php">📊 Relatórios</a></li>

        <li><a href="logout.php">🚪 Sair</a></li>

    </ul>

</div>

<div class="conteudo">

<h1>Alunos</h1>

<button class="novo">

+ Novo Aluno

</button>

<table>

<thead>

<tr>

<th>Foto</th>

<th>Nome</th>

<th>Turma</th>

<th>Idade</th>

<th>Desempenho</th>

<th>Ações</th>

</tr>

</thead>

<tbody>

<tr>

<td>

<img src="img/user.png" class="foto">

</td>

<td>Maria Silva</td>

<td>1° Ano A</td>

<td>7</td>

<td>95%</td>

<td>

<button class="editar">Editar</button>

<button class="excluir">Excluir</button>

</td>

</tr>

</tbody>

</table>

</div>

</body>

</html>