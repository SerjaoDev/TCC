<?php
include("php/verificar.php");
include("php/conexao.php");

date_default_timezone_set('America/Sao_Paulo');

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$professor_id = $_SESSION["id"] ?? null;

if (!$id || !$professor_id) {
    header("Location: alunos.php");
    exit;
}

// 1. Busca os dados do aluno no banco (Prepared Statement com trava do professor)
$stmt = $conexao->prepare("SELECT * FROM alunos WHERE id = ? AND professor_id = ?");
$stmt->bind_param("ii", $id, $professor_id);
$stmt->execute();
$aluno = $stmt->get_result()->fetch_assoc();

if (!$aluno) {
    header("Location: alunos.php");
    exit;
}

// 2. Trata o último acesso do aluno
$ultimo_acesso = $aluno['ultimo_acesso'] ?? null;

// Se estiver vazio no banco, busca a última atividade entregue pelo aluno (Prepared Statement)
if (empty($ultimo_acesso) || $ultimo_acesso == '0000-00-00 00:00:00') {
    try {
        $stmt_prog = $conexao->prepare("SELECT MAX(data_conclusao) as ultima_data FROM progresso_licoes WHERE aluno_id = ?");
        $stmt_prog->bind_param("i", $id);
        $stmt_prog->execute();
        $res_prog = $stmt_prog->get_result();

        if ($res_prog && $prog = $res_prog->fetch_assoc()) {
            if (!empty($prog['ultima_data'])) {
                $ultimo_acesso = $prog['ultima_data'];
            }
        }
    } catch (Throwable $e) {
        // Tabela opcional ainda não existe: mantém "Nenhum acesso registrado".
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acesso do Aluno | Lumi</title>
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
            <span class="badge"><i></i> Acesso</span>
            <h1>Acesso do Aluno</h1>
            <p>Entregue essas informações para o aluno acessar o aplicativo.</p>
        </header>

        <div class="info-grid">
            <div class="info-item">
                <span>Nome</span>
                <strong><?php echo htmlspecialchars($aluno["nome"] ?? ''); ?></strong>
            </div>
            <div class="info-item">
                <span>Usuário</span>
                <strong><?php echo htmlspecialchars($aluno["usuario"] ?? ''); ?></strong>
            </div>
            <div class="info-item">
                <span>Senha</span>
                <strong><?php echo htmlspecialchars($aluno["senha_visivel"] ?? 'Não informada'); ?></strong>
            </div>
            <div class="info-item">
                <span>⏱️ Último acesso ao app</span>
                <strong>
                <?php
                if (!empty($ultimo_acesso) && $ultimo_acesso !== '0000-00-00 00:00:00') {
                    echo date('d/m/Y \à\s H:i', strtotime($ultimo_acesso));
                } else {
                    echo "Nenhum acesso registrado";
                }
                ?>
                </strong>
            </div>
        </div>

        <div class="links">
            <a href="alunos.php">Voltar</a>
        </div>

    </main>

    <script src="js/script.js?v=3"></script>

</body>
</html>
