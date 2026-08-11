<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (
    !isset($_SESSION['id']) ||
    !is_numeric($_SESSION['id']) ||
    (int) $_SESSION['id'] <= 0
) {
    header('Location: ../index.php');
    exit;
}

$_SESSION['id'] = (int) $_SESSION['id'];