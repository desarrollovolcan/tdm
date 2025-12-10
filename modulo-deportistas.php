<?php
         require_once __DIR__ . '/config/dz.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
        <title>Módulo de deportistas | <?php echo $DexignZoneSettings['site_level']['site_title']; ?></title>
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
                                        <li class="breadcrumb-item active">Módulo de deportistas</li>
                                </ol>
                        </div>
                        <div class="row">
                                <div class="col-xl-12">
                                        <div class="card">
                                                <div class="card-header">
                                                        <h4 class="card-title mb-0">Panel del deportista y gestión de fichas</h4>
                                                </div>
                                                <div class="card-body">
                                                        <p class="mb-4">Fichas individuales, seguimiento de objetivos, estado de pagos y acceso al panel personal de rendimiento.</p>
                                                        <div class="row g-4">
                                                                <div class="col-lg-6">
                                                                        <div class="h-100 p-4 bg-light rounded">
                                                                                <h5>Ficha y elegibilidad</h5>
                                                                                <ul class="mb-0">
                                                                                        <li>Datos personales, categoría, mano hábil, estilo de juego y salud.</li>
                                                                                        <li>Estado: activo, inactivo o en prueba con vínculos a planes contratados.</li>
                                                                                        <li>Asociación de apoderados para menores de edad.</li>
                                                                                </ul>
                                                                        </div>
                                                                </div>
                                                                <div class="col-lg-6">
                                                                        <div class="h-100 p-4 bg-light rounded">
                                                                                <h5>Rendimiento y objetivos</h5>
                                                                                <ul class="mb-0">
                                                                                        <li>Dashboard de rendimiento: % de partidos ganados, rating interno y ranking.</li>
                                                                                        <li>Metas técnicas, tácticas y físicas con seguimiento por entrenador.</li>
                                                                                        <li>Histórico de entrenamientos y notas del entrenador.</li>
                                                                                </ul>
                                                                        </div>
                                                                </div>
                                                                <div class="col-lg-6">
                                                                        <div class="h-100 p-4 bg-light rounded">
                                                                                <h5>Agenda y asistencia</h5>
                                                                                <ul class="mb-0">
                                                                                        <li>Próximos entrenamientos, partidos y reservas confirmadas.</li>
                                                                                        <li>Control de asistencia con justificaciones y alertas.</li>
                                                                                        <li>Disponibilidad para sesiones individuales.</li>
                                                                                </ul>
                                                                        </div>
                                                                </div>
                                                                <div class="col-lg-6">
                                                                        <div class="h-100 p-4 bg-light rounded">
                                                                                <h5>Finanzas y comunicaciones</h5>
                                                                                <ul class="mb-0">
                                                                                        <li>Planes vigentes, facturas, pagos realizados y pendientes.</li>
                                                                                        <li>Chat directo con entrenadores y recepción de notificaciones.</li>
                                                                                        <li>Descarga de comprobantes y recordatorios automáticos.</li>
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
