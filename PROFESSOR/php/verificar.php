<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (
    !isset($_SESSION['professor_id']) ||
    !is_numeric($_SESSION['professor_id']) ||
    (int) $_SESSION['professor_id'] <= 0
) {
    header('Location: ../index.php');
    exit;
}

$_SESSION['professor_id'] =
    (int) $_SESSION['professor_id'];

if (!isset($_SESSION['professor_nome'])) {
    $_SESSION['professor_nome'] = '';
}

if (!isset($_SESSION['professor_email'])) {
    $_SESSION['professor_email'] = '';
}

if (!isset($_SESSION['professor_foto'])) {
    $_SESSION['professor_foto'] = 'padrao.png';
}