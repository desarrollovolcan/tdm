<?php
         require_once __DIR__ . '/config/dz.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
        <title>Comunicación y chat | <?php echo $DexignZoneSettings['site_level']['site_title']; ?></title>
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
                                        <li class="breadcrumb-item active">Comunicación y chat</li>
                                </ol>
                        </div>
                        <div class="row">
                                <div class="col-xl-12">
                                        <div class="card">
                                                <div class="card-header">
                                                        <h4 class="card-title mb-0">Mensajería interna y notificaciones</h4>
                                                </div>
                                                <div class="card-body">
                                                        <p class="mb-4">Chats 1 a 1, grupales y notificaciones vinculadas a entrenamientos, pagos y anuncios del club.</p>
                                                        <div class="row g-4">
                                                                <div class="col-lg-6">
                                                                        <div class="h-100 p-4 bg-light rounded">
                                                                                <h5>Chats</h5>
                                                                                <ul class="mb-0">
                                                                                        <li>Entrenador ↔ Deportista / Apoderado y administrador ↔ usuarios.</li>
                                                                                        <li>Grupos por categoría, grupo de entrenamiento o anuncios generales.</li>
                                                                                        <li>Adjuntos de video, imagen y documentos.</li>
                                                                                </ul>
                                                                        </div>
                                                                </div>
                                                                <div class="col-lg-6">
                                                                        <div class="h-100 p-4 bg-light rounded">
                                                                                <h5>Mensajes asociados</h5>
                                                                                <ul class="mb-0">
                                                                                        <li>Comentarios en sesiones de entrenamiento específicas.</li>
                                                                                        <li>Feedback post-partido y seguimiento de objetivos.</li>
                                                                                        <li>Historial en el panel del deportista y del entrenador.</li>
                                                                                </ul>
                                                                        </div>
                                                                </div>
                                                                <div class="col-lg-6">
                                                                        <div class="h-100 p-4 bg-light rounded">
                                                                                <h5>Notificaciones</h5>
                                                                                <ul class="mb-0">
                                                                                        <li>Alertas por nuevos entrenamientos, cambios de horario o cancelaciones.</li>
                                                                                        <li>Recordatorios de asistencia y pagos próximos a vencer.</li>
                                                                                        <li>Preferencias por canal (email, in-app, WhatsApp).</li>
                                                                                </ul>
                                                                        </div>
                                                                </div>
                                                                <div class="col-lg-6">
                                                                        <div class="h-100 p-4 bg-light rounded">
                                                                                <h5>Moderación y seguridad</h5>
                                                                                <ul class="mb-0">
                                                                                        <li>Políticas de privacidad y retención de mensajes.</li>
                                                                                        <li>Bloqueo de usuarios y reportes de actividad.</li>
                                                                                        <li>Registros básicos para auditoría.</li>
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
