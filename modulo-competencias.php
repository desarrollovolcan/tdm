<?php
         require_once __DIR__ . '/config/dz.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
        <title>Módulo de competencias y partidos | <?php echo $DexignZoneSettings['site_level']['site_title']; ?></title>
        <?php include 'elements/meta.php';?>
        <link rel="shortcut icon" type="image/png" href="<?php echo $DexignZoneSettings['site_level']['favicon']?>">
        <?php include 'elements/page-css.php'; ?>
</head>
<body>
<?php include 'elements/preloader.php'; ?>
<div id="main-wrapper">
        <?php include 'elements/nav-header.php'; ?>
        <?php include 'elements/chatbox.php'; ?>
        <?php include 'elements/header.php'; ?>
        <?php include 'elements/sidebar.php'; ?>
        <div class="content-body">
                <div class="container-fluid">
                        <div class="row page-titles">
                                <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="panel-principal.php">Inicio</a></li>
                                        <li class="breadcrumb-item active">Competencias y partidos</li>
                                </ol>
                        </div>
                        <div class="row">
                                <div class="col-xl-12">
                                        <div class="card">
                                                <div class="card-header">
                                                        <h4 class="card-title mb-0">Torneos, ligas y estadísticas de juego</h4>
                                                </div>
                                                <div class="card-body">
                                                        <p class="mb-4">Gestiona torneos internos y federados, registra resultados por set y analiza estadísticas específicas de tenis de mesa.</p>
                                                        <div class="row g-4">
                                                                <div class="col-lg-6">
                                                                        <div class="h-100 p-4 bg-light rounded">
                                                                                <h5>Organización de competencias</h5>
                                                                                <ul class="mb-0">
                                                                                        <li>Creación de torneos por categoría, formato (liga, eliminación, grupos) y sede.</li>
                                                                                        <li>Cuadros de juego, cronogramas y asignación de mesas.</li>
                                                                                        <li>Inscripciones, validación de elegibilidad y arbitraje.</li>
                                                                                </ul>
                                                                        </div>
                                                                </div>
                                                                <div class="col-lg-6">
                                                                        <div class="h-100 p-4 bg-light rounded">
                                                                                <h5>Gestión de partidos</h5>
                                                                                <ul class="mb-0">
                                                                                        <li>Resultado por set y resultado final (3-0, 3-2, etc.).</li>
                                                                                        <li>Registro de rivales internos o externos y tipo de competencia.</li>
                                                                                        <li>Control de árbitros y actas descargables.</li>
                                                                                </ul>
                                                                        </div>
                                                                </div>
                                                                <div class="col-lg-6">
                                                                        <div class="h-100 p-4 bg-light rounded">
                                                                                <h5>Estadísticas avanzadas</h5>
                                                                                <ul class="mb-0">
                                                                                        <li>% puntos ganados con saque propio y con saque rival.</li>
                                                                                        <li>Errores no forzados, forzados y winners por tipo de golpe.</li>
                                                                                        <li>Zonas de la mesa (corto/medio/largo, revés/centro/derecha) opcionales.</li>
                                                                                </ul>
                                                                        </div>
                                                                </div>
                                                                <div class="col-lg-6">
                                                                        <div class="h-100 p-4 bg-light rounded">
                                                                                <h5>Reportes y rankings</h5>
                                                                                <ul class="mb-0">
                                                                                        <li>Ranking interno por categoría y rating ELO interno.</li>
                                                                                        <li>Historial contra rivales específicos y resultados por período.</li>
                                                                                        <li>Exportación a PDF/Excel de tablas, medalleros y cronogramas.</li>
                                                                                </ul>
                                                                        </div>
                                                                </div>
                                                        </div>
                                                </div>
                                        </div>
                                </div>
                        </div>
                </div>
        </div>
        <?php include 'elements/footer.php'; ?>
</div>
<?php include 'elements/page-js.php'; ?>
</body>
</html>
