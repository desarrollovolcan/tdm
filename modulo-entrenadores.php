<?php
         require_once __DIR__ . '/config/dz.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
        <title>Módulo de entrenadores | <?php echo $DexignZoneSettings['site_level']['site_title']; ?></title>
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
                                        <li class="breadcrumb-item active">Módulo de entrenadores</li>
                                </ol>
                        </div>
                        <div class="row">
                                <div class="col-xl-12">
                                        <div class="card">
                                                <div class="card-header">
                                                        <h4 class="card-title mb-0">Gestión de entrenadores y staff técnico</h4>
                                                </div>
                                                <div class="card-body">
                                                        <p class="mb-4">Organiza perfiles de entrenadores, certificaciones, disponibilidad y comunicación directa con deportistas y apoderados.</p>
                                                        <div class="row g-4">
                                                                <div class="col-lg-6">
                                                                        <div class="h-100 p-4 bg-light rounded">
                                                                                <h5>Perfiles y certificaciones</h5>
                                                                                <ul class="mb-0">
                                                                                        <li>Datos personales, especialidades (niños, alto rendimiento, preparación física).</li>
                                                                                        <li>Certificaciones y niveles de la federación o programas internos.</li>
                                                                                        <li>Documentos adjuntos y vencimientos de credenciales.</li>
                                                                                </ul>
                                                                        </div>
                                                                </div>
                                                                <div class="col-lg-6">
                                                                        <div class="h-100 p-4 bg-light rounded">
                                                                                <h5>Agenda y asignaciones</h5>
                                                                                <ul class="mb-0">
                                                                                        <li>Disponibilidad semanal y reglas de carga máxima.</li>
                                                                                        <li>Asignación a grupos, sesiones individuales y torneos.</li>
                                                                                        <li>Alertas por solapamientos y gestión de reemplazos.</li>
                                                                                </ul>
                                                                        </div>
                                                                </div>
                                                                <div class="col-lg-6">
                                                                        <div class="h-100 p-4 bg-light rounded">
                                                                                <h5>Seguimiento y evaluaciones</h5>
                                                                                <ul class="mb-0">
                                                                                        <li>Registro de asistencia de entrenadores por sesión.</li>
                                                                                        <li>Evaluaciones de desempeño y feedback de deportistas.</li>
                                                                                        <li>Indicadores: mejora de deportistas, volumen de sesiones dirigidas.</li>
                                                                                </ul>
                                                                        </div>
                                                                </div>
                                                                <div class="col-lg-6">
                                                                        <div class="h-100 p-4 bg-light rounded">
                                                                                <h5>Comunicaciones</h5>
                                                                                <ul class="mb-0">
                                                                                        <li>Chat directo con deportistas y apoderados.</li>
                                                                                        <li>Envío de anuncios por categoría o grupo.</li>
                                                                                        <li>Historial de mensajes vinculados a entrenamientos específicos.</li>
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
