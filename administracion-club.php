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
                                                <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3">
                                                        <div>
                                                                <h4 class="card-title mb-1">Configuración y control del club</h4>
                                                                <p class="mb-0">Centraliza datos institucionales, sedes, mesas y categorías para mantener la operación del club alineada con los requerimientos legales y deportivos.</p>
                                                        </div>
                                                        <a class="btn btn-outline-primary" href="configuracion.php">
                                                                <i class="fa fa-cog me-2"></i> Ajustes generales
                                                        </a>
                                                </div>
                                                <div class="card-body">
                                                        <div class="row g-4">
                                                                <div class="col-lg-6 col-xl-3">
                                                                        <div class="h-100 p-4 bg-light rounded">
                                                                                <div class="d-flex align-items-center mb-3">
                                                                                        <div class="bg-primary rounded-circle text-white d-flex align-items-center justify-content-center me-3" style="width:42px;height:42px;">
                                                                                                <i class="fa fa-id-badge"></i>
                                                                                        </div>
                                                                                        <div>
                                                                                                <h5 class="mb-1">Datos del club</h5>
                                                                                                <small class="text-muted">Identidad y branding</small>
                                                                                        </div>
                                                                                </div>
                                                                                <div class="list-group mb-3">
                                                                                        <a class="list-group-item list-group-item-action d-flex align-items-center" href="configuracion.php">
                                                                                                <i class="fa fa-building me-2 text-primary"></i> Identidad corporativa
                                                                                        </a>
                                                                                        <a class="list-group-item list-group-item-action d-flex align-items-center" href="setting.php">
                                                                                                <i class="fa fa-paint-brush me-2 text-primary"></i> Colores y logotipo
                                                                                        </a>
                                                                                        <a class="list-group-item list-group-item-action d-flex align-items-center" href="category-table.php">
                                                                                                <i class="fa fa-sitemap me-2 text-primary"></i> Categorías internas
                                                                                        </a>
                                                                                </div>
                                                                                <a class="btn btn-primary w-100" href="add-categary.php">
                                                                                        <i class="fa fa-plus me-2"></i> Crear o editar categorías
                                                                                </a>
                                                                        </div>
                                                                </div>
                                                                <div class="col-lg-6 col-xl-3">
                                                                        <div class="h-100 p-4 bg-light rounded">
                                                                                <div class="d-flex align-items-center mb-3">
                                                                                        <div class="bg-success rounded-circle text-white d-flex align-items-center justify-content-center me-3" style="width:42px;height:42px;">
                                                                                                <i class="fa fa-map-marker"></i>
                                                                                        </div>
                                                                                        <div>
                                                                                                <h5 class="mb-1">Sedes y recursos</h5>
                                                                                                <small class="text-muted">Infraestructura</small>
                                                                                        </div>
                                                                                </div>
                                                                                <div class="list-group mb-3">
                                                                                        <a class="list-group-item list-group-item-action d-flex align-items-center" href="sedes-mesas.php">
                                                                                                <i class="fa fa-map-signs me-2 text-success"></i> Gestión multi-sede
                                                                                        </a>
                                                                                        <a class="list-group-item list-group-item-action d-flex align-items-center" href="sedes-mesas.php">
                                                                                                <i class="fa fa-table-tennis me-2 text-success"></i> Inventario de mesas y salas
                                                                                        </a>
                                                                                        <a class="list-group-item list-group-item-action d-flex align-items-center" href="modulo-reservas.php">
                                                                                                <i class="fa fa-calendar-check me-2 text-success"></i> Disponibilidad y reservas
                                                                                        </a>
                                                                                </div>
                                                                                <a class="btn btn-success w-100" href="modulo-reservas.php">
                                                                                        <i class="fa fa-calendar-plus me-2"></i> Asignar mesas
                                                                                </a>
                                                                        </div>
                                                                </div>
                                                                <div class="col-lg-6 col-xl-3">
                                                                        <div class="h-100 p-4 bg-light rounded">
                                                                                <div class="d-flex align-items-center mb-3">
                                                                                        <div class="bg-warning rounded-circle text-white d-flex align-items-center justify-content-center me-3" style="width:42px;height:42px;">
                                                                                                <i class="fa fa-users-cog"></i>
                                                                                        </div>
                                                                                        <div>
                                                                                                <h5 class="mb-1">Usuarios y permisos</h5>
                                                                                                <small class="text-muted">Roles y auditoría</small>
                                                                                        </div>
                                                                                </div>
                                                                                <div class="list-group mb-3">
                                                                                        <a class="list-group-item list-group-item-action d-flex align-items-center" href="usuarios.php">
                                                                                                <i class="fa fa-user-check me-2 text-warning"></i> Administradores y staff
                                                                                        </a>
                                                                                        <a class="list-group-item list-group-item-action d-flex align-items-center" href="usuarios.php">
                                                                                                <i class="fa fa-shield-alt me-2 text-warning"></i> Roles y ACL
                                                                                        </a>
                                                                                        <a class="list-group-item list-group-item-action d-flex align-items-center" href="reportes.php">
                                                                                                <i class="fa fa-clipboard-list me-2 text-warning"></i> Bitácora básica
                                                                                        </a>
                                                                                </div>
                                                                                <a class="btn btn-warning w-100" href="usuarios.php">
                                                                                        <i class="fa fa-user-plus me-2"></i> Alta y baja lógica
                                                                                </a>
                                                                        </div>
                                                                </div>
                                                                <div class="col-lg-6 col-xl-3">
                                                                        <div class="h-100 p-4 bg-light rounded">
                                                                                <div class="d-flex align-items-center mb-3">
                                                                                        <div class="bg-info rounded-circle text-white d-flex align-items-center justify-content-center me-3" style="width:42px;height:42px;">
                                                                                                <i class="fa fa-file-alt"></i>
                                                                                        </div>
                                                                                        <div>
                                                                                                <h5 class="mb-1">Documentación y cumplimiento</h5>
                                                                                                <small class="text-muted">Normativas y seguimiento</small>
                                                                                        </div>
                                                                                </div>
                                                                                <div class="list-group mb-3">
                                                                                        <a class="list-group-item list-group-item-action d-flex align-items-center" href="reportes-descargables.php">
                                                                                                <i class="fa fa-book me-2 text-info"></i> Reglamentos y manuales
                                                                                        </a>
                                                                                        <a class="list-group-item list-group-item-action d-flex align-items-center" href="reportes.php">
                                                                                                <i class="fa fa-chart-line me-2 text-info"></i> Indicadores de uso
                                                                                        </a>
                                                                                        <a class="list-group-item list-group-item-action d-flex align-items-center" href="reportes-descargables.php">
                                                                                                <i class="fa fa-lock me-2 text-info"></i> Políticas de privacidad
                                                                                        </a>
                                                                                </div>
                                                                                <a class="btn btn-info w-100" href="reportes.php">
                                                                                        <i class="fa fa-download me-2"></i> Ver reportes y actas
                                                                                </a>
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
