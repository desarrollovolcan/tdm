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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.3/dist/lux/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/main.css">
    <link rel="stylesheet" href="/assets/theme.css">
</head>
<body class="bg-body">
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm mb-4">
  <div class="container-fluid">
    <a class="navbar-brand d-flex align-items-center gap-2" href="/">
        <?php if(!empty($institution['logo_path'])): ?>
            <img src="<?php echo htmlspecialchars($institution['logo_path']); ?>" alt="Logo" style="width:34px; height:34px; object-fit:contain; border-radius:8px; background:#fff; padding:4px;">
        <?php else: ?>
            <span class="logo-chip"><span class="fw-bold text-primary">TDM</span></span>
        <?php endif; ?>
        <span class="fw-bold"><?php echo htmlspecialchars($institution['name'] ?? 'TDM Oficial'); ?></span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <?php if(isset($_SESSION['user'])): ?>
        <li class="nav-item"><a class="nav-link" href="/championships">Campeonatos</a></li>
        <li class="nav-item"><a class="nav-link" href="/players">Jugadores</a></li>
        <?php if($_SESSION['user']['role'] === 'admin'): ?>
        <li class="nav-item"><a class="nav-link" href="/institution">Institución</a></li>
        <?php endif; ?>
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
