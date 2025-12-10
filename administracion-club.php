<?php
         require_once __DIR__ . '/config/dz.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
        <title>Administración del club | <?php echo $DexignZoneSettings['site_level']['site_title']; ?></title>
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
                                        <li class="breadcrumb-item active">Administración del club</li>
                                </ol>
                        </div>
                        <div class="row">
                                <div class="col-xl-12">
                                        <div class="card">
                                                <div class="card-header">
                                                        <h4 class="card-title mb-0">Configuración y control del club</h4>
                                                </div>
                                                <div class="card-body">
                                                        <p class="mb-4">Centraliza datos institucionales, sedes, mesas y categorías para mantener la operación del club alineada con los requerimientos legales y deportivos.</p>
                                                        <div class="row g-4">
                                                                <div class="col-lg-6">
                                                                        <div class="h-100 p-4 bg-light rounded">
                                                                                <h5>Datos del club</h5>
                                                                                <ul class="mb-0">
                                                                                        <li>Identidad corporativa: nombre, RUT/ID fiscal, contactos y horarios.</li>
                                                                                        <li>Colores, logotipo y personalización de marca en portales y comunicaciones.</li>
                                                                                        <li>Categorías internas (Sub-11, Sub-13, Sub-15, Sub-19, Todo competidor, Máster, etc.) y reglas por categoría.</li>
                                                                                </ul>
                                                                        </div>
                                                                </div>
                                                                <div class="col-lg-6">
                                                                        <div class="h-100 p-4 bg-light rounded">
                                                                                <h5>Sedes y recursos</h5>
                                                                                <ul class="mb-0">
                                                                                        <li>Gestión multi-sede con dirección, contacto y horarios de atención.</li>
                                                                                        <li>Inventario de mesas, salas de videoanálisis, gimnasio y espacios complementarios.</li>
                                                                                        <li>Disponibilidad de mesas para asignación en entrenamientos y partidos.</li>
                                                                                </ul>
                                                                        </div>
                                                                </div>
                                                                <div class="col-lg-6">
                                                                        <div class="h-100 p-4 bg-light rounded">
                                                                                <h5>Usuarios y permisos</h5>
                                                                                <ul class="mb-0">
                                                                                        <li>Alta y baja lógica de administradores, staff de finanzas y supervisores.</li>
                                                                                        <li>Definición de roles (super administrador, administrador, invitado, etc.) y ACL por módulo.</li>
                                                                                        <li>Bitácora básica de auditoría para cambios críticos.</li>
                                                                                </ul>
                                                                        </div>
                                                                </div>
                                                                <div class="col-lg-6">
                                                                        <div class="h-100 p-4 bg-light rounded">
                                                                                <h5>Documentación y cumplimiento</h5>
                                                                                <ul class="mb-0">
                                                                                        <li>Adjunta reglamentos, políticas de privacidad y manuales operativos.</li>
                                                                                        <li>Indicadores de uso: deportistas activos, retención y ocupación de mesas.</li>
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
