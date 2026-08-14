<?php
declare(strict_types=1);

require_once __DIR__ . '/conexao.php';

if (!isset($_SESSION['id'])) {
    header('Location: ../index.php');
    exit;
}

$professor_id = filter_var(
    $_SESSION['id'],
    FILTER_VALIDATE_INT
);

if (!$professor_id) {
    header('Location: ../index.php');
    exit;
}

try {
    $stmt = $conexao->prepare("SELECT t.id,t.nome,COUNT(a.id) AS quantidade_alunos FROM turmas t LEFT JOIN alunos a ON a.turma_id = t.id AND a.professor_id = t.professor_id WHERE t.professor_id = :professor_id GROUP BY t.id,t.nome ORDER BY t.nome ASC");

    $stmt->execute([
        ':professor_id' => $professor_id
    ]);

    $turmas = $stmt->fetchAll(
        PDO::FETCH_ASSOC
    );
} catch (Throwable $e) {
    error_log(
        'Erro ao listar turmas: ' .
        $e->getMessage()
    );

    $turmas = [];
}
?>