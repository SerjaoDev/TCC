<?php
session_start();

if (!isset($_SESSION["id"])) {
    header("Location: ../index.php");
    exit();
}

include("conexao.php");

$professor_id = $_SESSION["id"];

// Recebe e sanitiza os dados do formulário
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$nome = trim($_POST["nome"] ?? '');
$usuario = trim($_POST["usuario"] ?? '');
$data_nascimento = !empty($_POST["data_nascimento"]) ? $_POST["data_nascimento"] : null;
$turma_id = filter_input(INPUT_POST, 'turma_id', FILTER_VALIDATE_INT);
$senha_nova = $_POST["senha"] ?? '';

// Validação dos dados obrigatórios
if (!$id || empty($nome) || empty($usuario) || !$turma_id) {
    header("Location: ../alunos.php?erro=dados_invalidos");
    exit();
}

// 1. Verifica se a senha foi alterada
if (!empty($senha_nova)) {
    $senha_hash = password_hash($senha_nova, PASSWORD_DEFAULT);
    
    // Query com atualização de senha
    $sql = "UPDATE alunos 
            SET nome = ?, usuario = ?, senha = ?, senha_visivel = ?, data_nascimento = ?, turma_id = ? 
            WHERE id = ? AND professor_id = ?";
    
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("sssssiii", $nome, $usuario, $senha_hash, $senha_nova, $data_nascimento, $turma_id, $id, $professor_id);
} else {
    // Query sem alterar a senha existente
    $sql = "UPDATE alunos 
            SET nome = ?, usuario = ?, data_nascimento = ?, turma_id = ? 
            WHERE id = ? AND professor_id = ?";
    
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("sssiii", $nome, $usuario, $data_nascimento, $turma_id, $id, $professor_id);
}

// 2. Executa a atualização
if ($stmt->execute()) {
    header("Location: ../alunos.php?sucesso=editado");
    exit();
} else {
    echo "Erro ao atualizar aluno: " . $conexao->error;
}
?>