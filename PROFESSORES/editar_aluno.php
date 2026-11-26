<?php
include("php/verificar.php");
include("php/conexao.php");

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$professor_id = $_SESSION["id"] ?? null;

if (!$id || !$professor_id) {
    header("Location: alunos.php");
    exit;
}

// 1. Busca os dados do aluno com segurança
$stmt = $conexao->prepare("SELECT * FROM alunos WHERE id = ? AND professor_id = ?");
$stmt->bind_param("ii", $id, $professor_id);
$stmt->execute();
$aluno = $stmt->get_result()->fetch_assoc();

if (!$aluno) {
    header("Location: alunos.php");
    exit;
}

// 2. Busca as turmas do professor para preencher o select
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
    <title>Editar Aluno | Lumi</title>
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
            <h1>Editar Aluno</h1>
            <p>Atualize os dados de acesso do aluno.</p>
        </header>

        <form action="php/editar_aluno.php" method="POST" class="login-form">

            <input type="hidden" name="id" value="<?php echo htmlspecialchars($aluno['id']); ?>">

            <div class="field">
                <input type="text" id="nome" name="nome" placeholder=" " value="<?php echo htmlspecialchars($aluno['nome'] ?? ''); ?>" required>
                <label for="nome">Nome do aluno</label>
            </div>

            <div class="field">
                <input type="text" id="usuario" name="usuario" placeholder=" " value="<?php echo htmlspecialchars($aluno['usuario'] ?? ''); ?>" required>
                <label for="usuario">Usuário de acesso</label>
            </div>

            <div class="field">
                <input type="date" id="data_nascimento" name="data_nascimento" value="<?php echo htmlspecialchars($aluno['data_nascimento'] ?? ''); ?>" required>
                <label for="data_nascimento">Data de nascimento</label>
            </div>

            <div class="field">
                <select id="turma_id" name="turma_id" required>
                    <option value="">Selecione a Turma</option>
                    <?php foreach ($turmas as $turma): ?>
                        <option value="<?php echo $turma['id']; ?>" <?php echo ($turma['id'] == ($aluno['turma_id'] ?? null)) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($turma['nome']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <label for="turma_id">Turma</label>
            </div>

            <button class="entrar" type="submit">
                <span>Salvar Alterações</span>
                <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 12h13M13 6l6 6-6 6"/>
                </svg>
            </button>

        </form>

        <div class="links">
            <a href="alunos.php">Voltar</a>
        </div>

    </main>

    <script src="js/script.js?v=3"></script>

</body>
</html>
