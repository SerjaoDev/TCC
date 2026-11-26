<?php
session_start();

if (file_exists("verificar.php")) {
    include("verificar.php");
}
if (file_exists("conexao.php")) {
    include("conexao.php");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $usuario = trim($_POST['usuario'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $turma_id = $_POST['turma_id'] ?? null;
    $professor_id = $_SESSION['id'] ?? null;

    // Descobre a página do formulário de onde o professor veio
    $pagina_origem = $_SERVER['HTTP_REFERER'] ?? '../index.php';

    // Remove parâmetros antigos da URL (como erros anteriores)
    $pagina_origem = strtok($pagina_origem, '?');

    if (empty($usuario)) {
        header("Location: " . $pagina_origem . "?erro=usuario_vazio");
        exit;
    }

    // 1. Checa se o nome de usuário já está em uso no banco
    $stmt_check = $conexao->prepare("SELECT id FROM alunos WHERE usuario = ?");
    $stmt_check->bind_param("s", $usuario);
    $stmt_check->execute();
    $res_check = $stmt_check->get_result();

    if ($res_check->num_rows > 0) {
        // Recarrega a exata mesma página exibindo o aviso em vermelho
        $url_retorno = $pagina_origem . "?erro=usuario_indisponivel";
        if ($turma_id) {
            $url_retorno .= "&turma=" . $turma_id;
        }
        header("Location: " . $url_retorno);
        exit;
    }

    // 2. Se estiver livre, realiza o cadastro
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
    
    $stmt_insert = $conexao->prepare("
        INSERT INTO alunos (professor_id, nome, usuario, senha, senha_visivel, turma_id) 
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt_insert->bind_param("issssi", $professor_id, $nome, $usuario, $senha_hash, $senha, $turma_id);

    if ($stmt_insert->execute()) {
        header("Location: ../turmas.php?sucesso=1");
        exit;
    } else {
        header("Location: " . $pagina_origem . "?erro=falha_cadastro");
        exit;
    }
}
?>