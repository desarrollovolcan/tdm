<?php
session_start();

if (empty($_SESSION['user'])) {
    $_SESSION['auth_error'] = 'Debes iniciar sesión para acceder al panel.';
    header('Location: page-login.php');
    exit;
}
