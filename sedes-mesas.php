<?php
require_once __DIR__ . '/auth-guard.php';
require_once __DIR__ . '/config/dz.php';
require_once __DIR__ . '/helpers.php';

const SEDES_STORE = 'sedes.json';

$sedes = load_json(SEDES_STORE);
$sedeEditando = null;

if (($_GET['action'] ?? '') === 'editar' && isset($_GET['id'])) {
    $sedeEditando = find_by_id($sedes, $_GET['id']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = trim($_POST['id'] ?? '');
    $nombre = trim($_POST['nombre'] ?? '');
    $ciudad = trim($_POST['ciudad'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    $capacidad = trim($_POST['capacidad'] ?? '');
    $mesas = array_filter(array_map('trim', explode("\n", $_POST['mesas'] ?? '')));

    if ($nombre === '' || $ciudad === '') {
        $_SESSION['sede_error'] = 'El nombre y la ciudad son obligatorios.';
    } else {
        if ($id !== '') {
            $sedes = array_map(function ($sede) use ($id, $nombre, $ciudad, $direccion, $capacidad, $mesas) {
                if (($sede['id'] ?? '') !== $id) {
                    return $sede;
                }

                return array_merge($sede, [
                    'nombre' => $nombre,
                    'ciudad' => $ciudad,
                    'direccion' => $direccion,
                    'capacidad' => $capacidad,
                    'mesas' => $mesas,
                    'updated_at' => date(DATE_ATOM),
                ]);
            }, $sedes);
        } else {
            $sedes[] = [
                'id' => uniqid('sede_', true),
                'nombre' => $nombre,
                'ciudad' => $ciudad,
                'direccion' => $direccion,
                'capacidad' => $capacidad,
                'mesas' => $mesas,
                'created_at' => date(DATE_ATOM),
            ];
        }

        if (save_json(SEDES_STORE, $sedes)) {
            $_SESSION['sede_success'] = $id !== '' ? 'Sede actualizada correctamente.' : 'Sede registrada correctamente.';
        } else {
            $_SESSION['sede_error'] = 'No se pudo guardar la sede. Revisa la base de datos o permisos de escritura.';
        }
    }

    header('Location: sedes-mesas.php');
    exit;
}

if (($_GET['action'] ?? '') === 'eliminar' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $sedes = array_values(array_filter($sedes, fn($sede) => ($sede['id'] ?? '') !== $id));
    if (save_json(SEDES_STORE, $sedes)) {
        $_SESSION['sede_success'] = 'Sede eliminada correctamente.';
    } else {
        $_SESSION['sede_error'] = 'No se pudo eliminar la sede en el almacenamiento.';
    }
    header('Location: sedes-mesas.php');
    exit;
}

$successMessage = flash('sede_success');
$errorMessage = flash('sede_error');
?>
<!DOCTYPE html>
<html lang="es">

<head>
        <title>Sedes y mesas</title>
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
                                        <div class="col-xl-4">
                                                <div class="card">
                                                        <div class="card-header d-sm-flex d-block border-0 pb-0">
                                                                <div>
                                                                        <h4 class="fs-20 mb-1">Registrar sede</h4>
                                                                        <span class="fs-14 text-muted">Incluye mesas disponibles</span>
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
                                                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($sedeEditando['id'] ?? ''); ?>">
                                                                <div class="col-12">
                                                                    <label class="form-label">Nombre de la sede</label>
                                                                    <input type="text" name="nombre" class="form-control" required value="<?php echo htmlspecialchars($sedeEditando['nombre'] ?? ''); ?>">
                                                                </div>
                                                                <div class="col-12">
                                                                    <label class="form-label">Ciudad</label>
                                                                    <input type="text" name="ciudad" class="form-control" required value="<?php echo htmlspecialchars($sedeEditando['ciudad'] ?? ''); ?>">
                                                                </div>
                                                                <div class="col-12">
                                                                    <label class="form-label">Dirección</label>
                                                                    <input type="text" name="direccion" class="form-control" placeholder="Opcional" value="<?php echo htmlspecialchars($sedeEditando['direccion'] ?? ''); ?>">
                                                                </div>
                                                                <div class="col-12">
                                                                    <label class="form-label">Capacidad estimada</label>
                                                                    <input type="number" name="capacidad" class="form-control" placeholder="Ej. 200" value="<?php echo htmlspecialchars($sedeEditando['capacidad'] ?? ''); ?>">
                                                                </div>
                                                                <div class="col-12">
                                                                    <label class="form-label">Mesas disponibles (una por línea)</label>
                                                                    <textarea name="mesas" class="form-control" rows="3" placeholder="Mesa 1\nMesa 2\nMesa 3"><?php echo htmlspecialchars(isset($sedeEditando['mesas']) ? implode("\n", $sedeEditando['mesas']) : ''); ?></textarea>
                                                                </div>
                                                                <div class="col-12 text-end">
                                                                    <button type="submit" class="btn btn-primary"><?php echo $sedeEditando ? 'Actualizar sede' : 'Guardar sede'; ?></button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                </div>
                                        </div>
                                        <div class="col-xl-8">
                                                <div class="card">
                                                        <div class="card-header border-0 pb-0">
                                                            <div>
                                                                <h4 class="fs-20 mb-1">Sedes y mesas</h4>
                                                                <span class="fs-14 text-muted">Administra la infraestructura</span>
                                                            </div>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="table-responsive">
                                                                <table class="table table-striped align-middle">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Nombre</th>
                                                                            <th>Ciudad</th>
                                                                            <th>Dirección</th>
                                                                            <th>Capacidad</th>
                                                                            <th>Mesas</th>
                                                                            <th></th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <?php if (empty($sedes)): ?>
                                                                            <tr><td colspan="6" class="text-center text-muted">Sin registros</td></tr>
                                                                        <?php else: ?>
                                                                            <?php foreach ($sedes as $sede): ?>
                                                                                <tr>
                                                                                    <td><?php echo htmlspecialchars($sede['nombre'] ?? ''); ?></td>
                                                                                    <td><?php echo htmlspecialchars($sede['ciudad'] ?? ''); ?></td>
                                                                                    <td><?php echo htmlspecialchars($sede['direccion'] ?? ''); ?></td>
                                                                                    <td><?php echo htmlspecialchars($sede['capacidad'] ?? ''); ?></td>
                                                                                    <td><?php echo htmlspecialchars(implode(', ', $sede['mesas'] ?? [])); ?></td>
                                                                                    <td class="text-end">
                                                                                        <a href="?action=editar&id=<?php echo urlencode($sede['id']); ?>" class="btn btn-sm btn-outline-primary me-1">Editar</a>
                                                                                        <a href="?action=eliminar&id=<?php echo urlencode($sede['id']); ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar sede?');">Eliminar</a>
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
