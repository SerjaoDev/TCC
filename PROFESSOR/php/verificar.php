<?php
session_start();

if (!isset($_SESSION["professor_id"])) {
    header("Location: ../index.html");
    exit();
}

if (!filter_var($_SESSION["professor_id"], FILTER_VALIDATE_INT)) {
    session_unset();
    session_destroy();

    header("Location: ../index.html");
    exit();
}
?>