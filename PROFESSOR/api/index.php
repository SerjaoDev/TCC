<?php
declare(strict_types=1);

/*
 * Obtém apenas o caminho da URL.
 */
$uri = parse_url(
    $_SERVER['REQUEST_URI'] ?? '/',
    PHP_URL_PATH
);

$uri = '/' . ltrim(
    (string) $uri,
    '/'
);

/*
 * Normaliza barras duplicadas.
 */
$uri = preg_replace(
    '#/+#',
    '/',
    $uri
);

/*
 * Proteção contra path traversal.
 */
if (
    str_contains($uri, '..') ||
    str_contains($uri, "\0")
) {

    http_response_code(400);

    header(
        'Content-Type: application/json; charset=UTF-8'
    );

    echo json_encode(
        [
            'sucesso' => false,
            'mensagem' => 'Requisição inválida.'
        ],
        JSON_UNESCAPED_UNICODE
    );

    exit;
}

/*
 * Página inicial.
 */
if ($uri === '/') {

    $index = dirname(__DIR__) . '/index.html';

    if (is_file($index)) {
        require $index;
        exit;
    }

    http_response_code(404);

    header(
        'Content-Type: text/plain; charset=UTF-8'
    );

    echo 'index.html não encontrado.';

    exit;
}

/*
 * Caminho físico solicitado.
 */
$arquivo = dirname(__DIR__) . $uri;

/*
 * PHP
 */
if (
    is_file($arquivo) &&
    strtolower(
        pathinfo(
            $arquivo,
            PATHINFO_EXTENSION
        )
    ) === 'php'
) {

    require $arquivo;

    exit;
}

/*
 * HTML
 */
if (
    is_file($arquivo) &&
    strtolower(
        pathinfo(
            $arquivo,
            PATHINFO_EXTENSION
        )
    ) === 'html'
) {

    require $arquivo;

    exit;
}

/*
 * Arquivos estáticos.
 */
if (is_file($arquivo)) {

    $extensao = strtolower(
        pathinfo(
            $arquivo,
            PATHINFO_EXTENSION
        )
    );

    $tipos = [
        'css'  => 'text/css; charset=UTF-8',
        'js'   => 'application/javascript; charset=UTF-8',
        'json' => 'application/json; charset=UTF-8',

        'png'  => 'image/png',
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif'  => 'image/gif',
        'svg'  => 'image/svg+xml',
        'webp' => 'image/webp',
        'ico'  => 'image/x-icon',

        'woff'  => 'font/woff',
        'woff2' => 'font/woff2'
    ];

    if (isset($tipos[$extensao])) {

        header(
            'Content-Type: ' .
            $tipos[$extensao]
        );

        readfile($arquivo);

        exit;
    }
}

/*
 * 404.
 */
http_response_code(404);

header(
    'Content-Type: application/json; charset=UTF-8'
);

echo json_encode(
    [
        'sucesso' => false,
        'mensagem' => 'Página não encontrada.'
    ],
    JSON_UNESCAPED_UNICODE
);

exit;