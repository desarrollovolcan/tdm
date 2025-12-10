<?php
require_once __DIR__ . '/auth-guard.php';
require_once __DIR__ . '/config/dz.php';
require_once __DIR__ . '/helpers.php';

const ADMIN_CLUB_STORE = 'administracion_club.json';

$registros = load_json(ADMIN_CLUB_STORE);

if (($_GET['action'] ?? '') === 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $registros = array_values(array_filter(
        $registros,
        function ($registro) use ($id) {
            return ($registro['id'] ?? '') !== $id;
        }
    ));

    save_json(ADMIN_CLUB_STORE, $registros);
    flash('admin_success', 'Registro eliminado correctamente.');
    header('Location: administracion-club.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $rut = trim($_POST['rut'] ?? '');
    $ciudad = trim($_POST['ciudad'] ?? '');
    $categoriaBase = trim($_POST['categoria_base'] ?? '');
    $horarioApertura = trim($_POST['horario_apertura'] ?? '');
    $horarioCierre = trim($_POST['horario_cierre'] ?? '');
    $sede = trim($_POST['sede'] ?? '');
    $contacto = trim($_POST['contacto'] ?? '');

    if ($nombre === '' || $rut === '') {
        flash('admin_error', 'Los campos Nombre del club y RUT/ID fiscal son obligatorios.');
        header('Location: administracion-club.php');
        exit;
    }

    $registros[] = [
        'id' => uniqid('club_', true),
        'nombre' => $nombre,
        'rut' => $rut,
        'ciudad' => $ciudad,
        'categoria_base' => $categoriaBase,
        'horario_apertura' => $horarioApertura,
        'horario_cierre' => $horarioCierre,
        'sede' => $sede,
        'contacto' => $contacto,
        'created_at' => date(DATE_ATOM),
    ];

    save_json(ADMIN_CLUB_STORE, $registros);
    flash('admin_success', 'Datos del club guardados correctamente.');
    header('Location: administracion-club.php');
    exit;
}

$successMessage = flash('admin_success');
$errorMessage = flash('admin_error');
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
                        <?php if ($successMessage): ?>
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <?php echo htmlspecialchars($successMessage); ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                        <?php endif; ?>
                        <?php if ($errorMessage): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <?php echo htmlspecialchars($errorMessage); ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                        <?php endif; ?>
                        <div class="row">
                                <div class="col-xl-5">
                                        <div class="card">
                                                <div class="card-header">
                                                        <h4 class="card-title mb-0">Registrar sede y configuración</h4>
                                                </div>
                                                <div class="card-body">
                                                        <form method="post" class="row g-3">
                                                                <div class="col-12">
                                                                        <label class="form-label">Nombre del club *</label>
                                                                        <input type="text" name="nombre" class="form-control" required>
                                                                </div>
                                                                <div class="col-md-6">
                                                                        <label class="form-label">RUT/ID fiscal *</label>
                                                                        <input type="text" name="rut" class="form-control" required>
                                                                </div>
                                                                <div class="col-md-6">
                                                                        <label class="form-label">Ciudad / País</label>
                                                                        <input type="text" name="ciudad" class="form-control" placeholder="Ciudad, País">
                                                                </div>
                                                                <div class="col-md-6">
                                                                        <label class="form-label">Horario de apertura</label>
                                                                        <input type="time" name="horario_apertura" class="form-control">
                                                                </div>
                                                                <div class="col-md-6">
                                                                        <label class="form-label">Horario de cierre</label>
                                                                        <input type="time" name="horario_cierre" class="form-control">
                                                                </div>
                                                                <div class="col-md-6">
                                                                        <label class="form-label">Categoría base</label>
                                                                        <input type="text" name="categoria_base" class="form-control" placeholder="Sub-11, Adultos, etc.">
                                                                </div>
                                                                <div class="col-md-6">
                                                                        <label class="form-label">Sede principal</label>
                                                                        <input type="text" name="sede" class="form-control" placeholder="Nombre de la sede">
                                                                </div>
                                                                <div class="col-12">
                                                                        <label class="form-label">Contacto y teléfonos</label>
                                                                        <input type="text" name="contacto" class="form-control" placeholder="Email o teléfono de contacto">
                                                                </div>
                                                                <div class="col-12">
                                                                        <button type="submit" class="btn btn-primary">Guardar</button>
                                                                </div>
                                                        </form>
                                                </div>
                                        </div>
                                </div>
                                <div class="col-xl-7">
                                        <div class="card">
                                                <div class="card-header">
                                                        <h4 class="card-title mb-0">Sedes registradas</h4>
                                                </div>
                                                <div class="card-body p-0">
                                                        <div class="table-responsive">
                                                                <table class="table mb-0">
                                                                        <thead>
                                                                                <tr>
                                                                                        <th>Club</th>
                                                                                        <th>RUT</th>
                                                                                        <th>Ciudad</th>
                                                                                        <th>Categoría</th>
                                                                                        <th>Horario</th>
                                                                                        <th>Acciones</th>
                                                                                </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                                <?php if (empty($registros)): ?>
                                                                                        <tr><td colspan="6" class="text-center py-4">Sin registros aún.</td></tr>
                                                                                <?php else: ?>
                                                                                        <?php foreach ($registros as $registro): ?>
                                                                                                <tr>
                                                                                                        <td><?php echo htmlspecialchars($registro['nombre'] ?? ''); ?></td>
                                                                                                        <td><?php echo htmlspecialchars($registro['rut'] ?? ''); ?></td>
                                                                                                        <td><?php echo htmlspecialchars($registro['ciudad'] ?? '-'); ?></td>
                                                                                                        <td><?php echo htmlspecialchars($registro['categoria_base'] ?? '-'); ?></td>
                                                                                                        <td><?php echo htmlspecialchars(($registro['horario_apertura'] ?? '-') . ' - ' . ($registro['horario_cierre'] ?? '-')); ?></td>
                                                                                                        <td>
                                                                                                                <a class="btn btn-sm btn-outline-danger" href="?action=delete&id=<?php echo urlencode($registro['id']); ?>" onclick="return confirm('¿Eliminar este registro?');">Eliminar</a>
                                                                                                        </td>
                                                                                                </tr>
                                                                                        <?php endforeach; ?>
                                                                                <?php endif; ?>
                                                                        </tbody>
                                                                </table>
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
