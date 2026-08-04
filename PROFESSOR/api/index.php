<?php
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = ltrim($uri, '/');

if ($uri === '') {
    include __DIR__ . '/../login.php'; 
    exit;
}

$arquivo_solicitado = __DIR__ . '/../' . $uri;

if (file_exists($arquivo_solicitado) && is_file($arquivo_solicitado) && pathinfo($uri, PATHINFO_EXTENSION) === 'php') {
    include $arquivo_solicitado;
    exit;
}

http_response_code(404);
echo "Página não encontrada (404).";
?>