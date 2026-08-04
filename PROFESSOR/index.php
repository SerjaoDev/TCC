<?php
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = ltrim($uri, '/');

if ($uri === '') {
    include __DIR__ . '/login.php'; 
    exit;
}

if (file_exists(__DIR__ . '/' . $uri) && is_file(__DIR__ . '/' . $uri) && pathinfo($uri, PATHINFO_EXTENSION) === 'php') {
    include __DIR__ . '/' . $uri;
    exit;
}

http_response_code(404);
echo "Página não encontrada (404).";
?>