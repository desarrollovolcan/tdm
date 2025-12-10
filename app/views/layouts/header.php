<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tenis de Mesa - Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/main.css">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
  <div class="container-fluid">
    <a class="navbar-brand" href="/">TDM Oficial</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <?php if(isset($_SESSION['user'])): ?>
        <li class="nav-item"><a class="nav-link" href="/championships">Campeonatos</a></li>
        <li class="nav-item"><a class="nav-link" href="/players">Jugadores</a></li>
        <?php endif; ?>
        <li class="nav-item"><a class="nav-link" href="/register">Inscripción pública</a></li>
      </ul>
      <ul class="navbar-nav">
        <?php if(isset($_SESSION['user'])): ?>
            <li class="nav-item text-white nav-link">Hola, <?php echo htmlspecialchars($_SESSION['user']['name']); ?></li>
            <li class="nav-item"><a class="nav-link" href="/logout">Salir</a></li>
        <?php else: ?>
            <li class="nav-item"><a class="nav-link" href="/login">Ingresar</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
<div class="container">
