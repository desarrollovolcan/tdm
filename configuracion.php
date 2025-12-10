<?php
require_once __DIR__ . '/auth-guard.php';
require_once __DIR__ . '/config/dz.php';
require_once __DIR__ . '/helpers.php';

const CONFIG_STORE = 'configuracion.json';

$config = load_json(CONFIG_STORE);
$config = $config ?: [
    'organizacion' => 'Federación local de tenis de mesa',
    'correo' => 'contacto@example.com',
    'telefono' => '+00 000 0000',
    'reglamento' => 'https://',
    'mensaje_portal' => 'Bienvenido al sistema de campeonatos.',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $config['organizacion'] = trim($_POST['organizacion'] ?? $config['organizacion']);
    $config['correo'] = trim($_POST['correo'] ?? $config['correo']);
    $config['telefono'] = trim($_POST['telefono'] ?? $config['telefono']);
    $config['reglamento'] = trim($_POST['reglamento'] ?? $config['reglamento']);
    $config['mensaje_portal'] = trim($_POST['mensaje_portal'] ?? $config['mensaje_portal']);

    if (save_json(CONFIG_STORE, $config)) {
        $_SESSION['config_success'] = 'Configuración guardada correctamente.';
    } else {
        $_SESSION['config_success'] = null;
        $_SESSION['config_error'] = 'No se pudo guardar la configuración. Verifica la conexión con la base de datos.';
    }
    header('Location: configuracion.php');
    exit;
}

$successMessage = flash('config_success');
$errorMessage = flash('config_error');
?>
<!DOCTYPE html>
<html lang="es">

<head>
        <title>Configuración</title>
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
                                                <li class="breadcrumb-item"><a href="panel-principal.php">Panel</a></li>
                                                <li class="breadcrumb-item active"><a href="javascript:void(0)">Administración</a></li>
                                        </ol>
                </div>
                                <div class="row">
                                        <div class="col-xl-7">
                                                <div class="card">
                                                        <div class="card-header d-sm-flex d-block border-0 pb-0">
                                                                <div>
                                                                        <h4 class="fs-20 mb-1">Datos generales</h4>
                                                                        <span class="fs-14 text-muted">Personaliza el portal</span>
                                                                </div>
                                                        </div>
                                                        <div class="card-body">
                                                            <?php if ($successMessage): ?>
                                                                <div class="alert alert-success alert-dismissible fade show">
                                                                    <?php echo htmlspecialchars($successMessage); ?>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                                                </div>
                                                            <?php endif; ?>
                                                            <?php if ($errorMessage): ?>
                                                                <div class="alert alert-danger alert-dismissible fade show">
                                                                    <?php echo htmlspecialchars($errorMessage); ?>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                                                </div>
                                                            <?php endif; ?>

                                                            <form method="post" class="row g-3">
                                                                <div class="col-12">
                                                                    <label class="form-label">Organización</label>
                                                                    <input type="text" name="organizacion" class="form-control" value="<?php echo htmlspecialchars($config['organizacion']); ?>" required>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label class="form-label">Correo de contacto</label>
                                                                    <input type="email" name="correo" class="form-control" value="<?php echo htmlspecialchars($config['correo']); ?>" required>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label class="form-label">Teléfono</label>
                                                                    <input type="text" name="telefono" class="form-control" value="<?php echo htmlspecialchars($config['telefono']); ?>">
                                                                </div>
                                                                <div class="col-12">
                                                                    <label class="form-label">URL reglamento</label>
                                                                    <input type="url" name="reglamento" class="form-control" value="<?php echo htmlspecialchars($config['reglamento']); ?>">
                                                                </div>
                                                                <div class="col-12">
                                                                    <label class="form-label">Mensaje de portada</label>
                                                                    <textarea name="mensaje_portal" class="form-control" rows="3"><?php echo htmlspecialchars($config['mensaje_portal']); ?></textarea>
                                                                </div>
                                                                <div class="col-12 text-end">
                                                                    <button type="submit" class="btn btn-primary">Guardar cambios</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                </div>
                                        </div>
                                        <div class="col-xl-5">
                                                <div class="card">
                                                        <div class="card-header border-0 pb-0">
                                                            <div>
                                                                <h4 class="fs-20 mb-1">Resumen</h4>
                                                                <span class="fs-14 text-muted">Datos que verán los usuarios</span>
                                                            </div>
                                                        </div>
                                                        <div class="card-body">
                                                            <ul class="list-unstyled mb-0">
                                                                <li class="mb-3"><strong>Organización:</strong> <?php echo htmlspecialchars($config['organizacion']); ?></li>
                                                                <li class="mb-3"><strong>Contacto:</strong> <?php echo htmlspecialchars($config['correo']); ?></li>
                                                                <li class="mb-3"><strong>Teléfono:</strong> <?php echo htmlspecialchars($config['telefono']); ?></li>
                                                                <li class="mb-3"><strong>Reglamento:</strong> <a href="<?php echo htmlspecialchars($config['reglamento']); ?>" target="_blank" rel="noreferrer">Ver enlace</a></li>
                                                                <li><strong>Mensaje portada:</strong><br><?php echo nl2br(htmlspecialchars($config['mensaje_portal'])); ?></li>
                                                            </ul>
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
