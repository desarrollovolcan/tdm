<?php
         require_once __DIR__ . '/config/dz.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
        <title>Reservas y recursos | <?php echo $DexignZoneSettings['site_level']['site_title']; ?></title>
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
                                        <li class="breadcrumb-item active">Reservas y recursos</li>
                                </ol>
                        </div>
                        <div class="row">
                                <div class="col-xl-12">
                                        <div class="card">
                                                <div class="card-header">
                                                        <h4 class="card-title mb-0">Calendario de mesas y entrenadores</h4>
                                                </div>
                                                <div class="card-body">
                                                        <p class="mb-4">Reserva mesas y sesiones individuales con entrenadores, aplicando reglas de ocupación, cancelaciones y penalizaciones.</p>
                                                        <div class="row g-4">
                                                                <div class="col-lg-6">
                                                                        <div class="h-100 p-4 bg-light rounded">
                                                                                <h5>Reservas de mesas</h5>
                                                                                <ul class="mb-0">
                                                                                        <li>Disponibilidad por sede, mesa y franja horaria.</li>
                                                                                        <li>Reglas de máximo de reservas simultáneas por deportista.</li>
                                                                                        <li>Políticas de cancelación y penalizaciones.</li>
                                                                                </ul>
                                                                        </div>
                                                                </div>
                                                                <div class="col-lg-6">
                                                                        <div class="h-100 p-4 bg-light rounded">
                                                                                <h5>Entrenadores</h5>
                                                                                <ul class="mb-0">
                                                                                        <li>Reserva de sesiones individuales con agenda del entrenador.</li>
                                                                                        <li>Visibilidad para deportistas, entrenadores y administradores.</li>
                                                                                        <li>Alertas por solapamientos y bloqueos por eventos.</li>
                                                                                </ul>
                                                                        </div>
                                                                </div>
                                                                <div class="col-lg-6">
                                                                        <div class="h-100 p-4 bg-light rounded">
                                                                                <h5>Calendario</h5>
                                                                                <ul class="mb-0">
                                                                                        <li>Vista mensual/semanal por usuario, entrenador o sede.</li>
                                                                                        <li>Integración con entrenamientos y partidos confirmados.</li>
                                                                                        <li>Filtro por categoría, tipo de sesión y estado.</li>
                                                                                </ul>
                                                                        </div>
                                                                </div>
                                                                <div class="col-lg-6">
                                                                        <div class="h-100 p-4 bg-light rounded">
                                                                                <h5>Controles adicionales</h5>
                                                                                <ul class="mb-0">
                                                                                        <li>Pagos asociados a reservas premium o fuera de plan.</li>
                                                                                        <li>Bloqueo por morosidad o inasistencias reiteradas.</li>
                                                                                        <li>Historial de reservas y exportes a reportes.</li>
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
