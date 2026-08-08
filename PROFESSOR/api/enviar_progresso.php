<?php

header("Content-Type: application/json; charset=utf-8");

require_once __DIR__ . "/../php/conexao.php";

$aluno_id = $_POST["aluno_id"] ?? null;
$licao_id = $_POST["licao_id"] ?? null;
$resultado = $_POST["resultado"] ?? null;
$pontuacao = $_POST["pontuacao"] ?? 0;
$tempo = $_POST["tempo_gasto"] ?? 0;

if (!$aluno_id || !$licao_id) {
    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Dados incompletos"
    ]);
    exit;
}

try {
    $conexao->beginTransaction();

    $sql = "INSERT INTO desempenho(aluno_id, licao_id, resultado, pontuacao, tempo_gasto) VALUES (:id :licao, :resultado, :pontuacao, :tempo)";

    $stmt = $conexao->prepare($sql);
    $stmt->execute([
        ":id" => $aluno_id,
        ":licao" => $licao_id,
        ":resultado" => $resultado,
        ":pontuacao" => $pontuacao,
        ":tempo" => $tempo
    ]);

    $sql = "UPDATE progresso SET licoes_concluidas = licoes_concluidas + 1, acertos = acertos + :pontos WHERE aluno_id=:aluno";

    $stmt = $conexao->prepare($sql);
    $stmt->execute([
        ":pontos" => $pontuacao,
        ":aluno" => $aluno_id
    ]);

    $conexao->commit();

    echo json_encode([
        "sucesso" => true,
        "mensagem" => "Progresso salvo"
    ]);
} catch (Exception $erro) {
    $conexao->rollBack();

    http_response_code(500);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Erro ao salvar progresso"
    ]);
}
?>