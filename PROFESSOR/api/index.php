<?php
$uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
$uri = trim($uri, "/");

if ($uri === "") {
    require __DIR__ . "/../index.html";
    exit;
}

$arquivo = __DIR__ . "/../" . $uri;

if (
    file_exists($arquivo) && is_file($arquivo)
) {
    require $arquivo;
    exit;
}

http_response_code(404);
header("Content-Type: application/json");

echo json_encode([
    "sucesso" => false,
    "mensagem" => "Página não encontrada"
]);
?>