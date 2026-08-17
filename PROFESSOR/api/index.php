<?php
declare(strict_types=1);

$uri = parse_url(
    $_SERVER['REQUEST_URI'] ?? '/',
    PHP_URL_PATH
);

$uri = '/' . ltrim(
    (string) $uri,
    '/'
);

$uri = preg_replace(
    '#/+#',
    '/',
    $uri
);

if ($uri === '/') {
    $index = __DIR__ . '/../index.html';

    if (is_file($index)) {
        require $index;
        exit;
    }
}

if (
    str_contains($uri, '..') ||
    str_contains($uri, "\0")
) {
    http_response_code(400);

    header(
        'Content-Type: application/json; charset=UTF-8'
    );

    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Requisição inválida.'
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

$arquivo = realpath(
    __DIR__ . '/..' . $uri
);

$raiz = realpath(
    __DIR__ . '/..'
);

if (
    $arquivo === false ||
    $raiz === false ||
    !str_starts_with($arquivo, $raiz)
) {
    http_response_code(404);

    header(
        'Content-Type: application/json; charset=UTF-8'
    );

    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Página não encontrada.'
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

if (
    is_file($arquivo) &&
    strtolower(pathinfo($arquivo, PATHINFO_EXTENSION)) === 'php'
) {
    require $arquivo;
    exit;
}

if (
    is_file($arquivo) &&
    strtolower(pathinfo($arquivo, PATHINFO_EXTENSION)) === 'html'
) {
    require $arquivo;
    exit;
}

$tipos = [
    'css'  => 'text/css; charset=UTF-8',
    'js'   => 'application/javascript; charset=UTF-8',
    'png'  => 'image/png',
    'jpg'  => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'gif'  => 'image/gif',
    'svg'  => 'image/svg+xml',
    'webp' => 'image/webp',
    'ico'  => 'image/x-icon'
];

$extensao = strtolower(
    pathinfo($arquivo, PATHINFO_EXTENSION)
);

if (
    is_file($arquivo) &&
    isset($tipos[$extensao])
) {
    header(
        'Content-Type: ' . $tipos[$extensao]
    );

    readfile($arquivo);
    exit;
}

http_response_code(404);

header(
    'Content-Type: application/json; charset=UTF-8'
);

echo json_encode([
    'sucesso' => false,
    'mensagem' => 'Página não encontrada.'
], JSON_UNESCAPED_UNICODE);

exit;