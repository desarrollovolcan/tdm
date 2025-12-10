<?php
         require_once __DIR__ . '/config/dz.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
        <title>Módulo de entrenamientos | <?php echo $DexignZoneSettings['site_level']['site_title']; ?></title>
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
                                        <li class="breadcrumb-item active">Módulo de entrenamientos</li>
                                </ol>
                        </div>
                        <div class="row">
                                <div class="col-xl-12">
                                        <div class="card">
                                                <div class="card-header">
                                                        <h4 class="card-title mb-0">Planificación, registro y análisis</h4>
                                                </div>
                                                <div class="card-body">
                                                        <p class="mb-4">Control integral de sesiones grupales e individuales, asistencia y contenidos técnicos específicos del tenis de mesa.</p>
                                                        <div class="row g-4">
                                                                <div class="col-lg-6">
                                                                        <div class="h-100 p-4 bg-light rounded">
                                                                                <h5>Planificación</h5>
                                                                                <ul class="mb-0">
                                                                                        <li>Sesiones grupales e individuales con capacidad, sede, mesa y entrenador.</li>
                                                                                        <li>Planes asociados, microciclos y clasificación (físico, técnico o mixto).</li>
                                                                                        <li>Asignación de deportistas con límites por categoría y reglas de elegibilidad.</li>
                                                                                </ul>
                                                                        </div>
                                                                </div>
                                                                <div class="col-lg-6">
                                                                        <div class="h-100 p-4 bg-light rounded">
                                                                                <h5>Asistencia y control</h5>
                                                                                <ul class="mb-0">
                                                                                        <li>Marcado rápido de asistencia de deportistas y entrenadores.</li>
                                                                                        <li>Motivos de inasistencia (justificado/no justificado) y alertas.</li>
                                                                                        <li>Reportes por fecha, grupo, entrenador o deportista.</li>
                                                                                </ul>
                                                                        </div>
                                                                </div>
                                                                <div class="col-lg-6">
                                                                        <div class="h-100 p-4 bg-light rounded">
                                                                                <h5>Contenido técnico</h5>
                                                                                <ul class="mb-0">
                                                                                        <li>Tipos de ejercicios: saques, recepciones, drives, loops, bloqueos, multiball.</li>
                                                                                        <li>Volumen estimado de bolas, intensidad (1-10) y enfoque técnico/táctico.</li>
                                                                                        <li>Notas del entrenador y archivos adjuntos para videoanálisis.</li>
                                                                                </ul>
                                                                        </div>
                                                                </div>
                                                                <div class="col-lg-6">
                                                                        <div class="h-100 p-4 bg-light rounded">
                                                                                <h5>Análisis</h5>
                                                                                <ul class="mb-0">
                                                                                        <li>Indicadores de carga: sesiones por semana, volumen de bolas e intensidad promedio.</li>
                                                                                        <li>Tendencias por grupo y evolución de objetivos individuales.</li>
                                                                                        <li>Exportes a reportes y dashboards de rendimiento.</li>
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
