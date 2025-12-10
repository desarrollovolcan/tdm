<?php
require_once __DIR__ . '/config/dz.php';
session_start();

if (!empty($_SESSION['user'])) {
    header('Location: panel-principal.php');
    exit;
}

header('Location: page-login.php');
exit;
