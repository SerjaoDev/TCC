<?php
header("Content-Type: application/json; charset=utf-8");

require_once __DIR__ . "/../php/conexao.php";

$usuario = $_POST["usuario"] ?? "";
$senha = $_POST["senha"] ?? "";

if (empty($usuario) || empty($senha)) {
    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Preencha usuário e senha"
    ]);
    exit;
}

try {
    $sql = "SELECT * FROM alunos WHERE usuario = :usuario LIMIT 1";

    $stmt = $conexao->prepare($sql);
    $stmt->bindValue(":usuario", $usuario);
    $stmt->execute();

    $aluno = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$aluno) {
        echo json_encode([
            "sucesso" => false,
            "mensagem" => "Aluno não encontrado"
        ]);
        exit;
    }

    if (!password_verify($senha, $aluno["senha"])) {
        echo json_encode([
            "sucesso" => false,
            "mensagem" => "Senha incorreta"
        ]);
        exit;
    }

    $sql = "SELECT * FROM progresso WHERE aluno_id=:id";

    $stmt = $conexao->prepare($sql);
    $stmt->bindValue(":id", $aluno["id"]);
    $stmt->execute();

    $progresso = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        "sucesso" => true,
        "aluno" => [
            "id" => $aluno["id"],
            "nome" => $aluno["nome"],
            "turma" => $aluno["turma_id"],
            "estrelas" => $progresso["estrelas"] ?? 0,
            "moedas" => $progresso["moedas"] ?? 0,
            "licoes" => $progresso["licoes_concluidas"] ?? 0,
            "acertos" => $progresso["acertos"] ?? 0
        ]
    ]);
} catch (PDOException $erro) {
    http_response_code(500);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Erro interno"
    ]);
}
?>