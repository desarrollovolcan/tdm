<?php
require_once __DIR__ . '/auth-guard.php';
require_once __DIR__ . '/config/dz.php';
require_once __DIR__ . '/helpers.php';

const MODULO_RESERVAS_STORE = 'modulo_reservas.json';

$registros = load_json(MODULO_RESERVAS_STORE);

if (($_GET['action'] ?? '') === 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $registros = array_values(array_filter(
        $registros,
        fn($registro) => ($registro['id'] ?? '') !== $id
    ));
    save_json(MODULO_RESERVAS_STORE, $registros);
    flash('reserva_success', 'Reserva eliminada.');
    header('Location: modulo-reservas.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $recurso = trim($_POST['recurso'] ?? '');
    $fecha = trim($_POST['fecha'] ?? '');
    $hora = trim($_POST['hora'] ?? '');
    $usuario = trim($_POST['usuario'] ?? '');
    $estado = trim($_POST['estado'] ?? '');
    $sede = trim($_POST['sede'] ?? '');

    if ($recurso === '' || $fecha === '' || $hora === '') {
        flash('reserva_error', 'Recurso, fecha y hora son obligatorios.');
        header('Location: modulo-reservas.php');
        exit;
    }

    $registros[] = [
        'id' => uniqid('res_', true),
        'recurso' => $recurso,
        'fecha' => $fecha,
        'hora' => $hora,
        'usuario' => $usuario,
        'estado' => $estado ?: 'Reservado',
        'sede' => $sede,
        'created_at' => date(DATE_ATOM),
    ];

    save_json(MODULO_RESERVAS_STORE, $registros);
    flash('reserva_success', 'Reserva guardada.');
    header('Location: modulo-reservas.php');
    exit;
}

$successMessage = flash('reserva_success');
$errorMessage = flash('reserva_error');
?>
<!DOCTYPE html>
<html lang="es">
<head>
        <title>Módulo de reservas | <?php echo $DexignZoneSettings['site_level']['site_title']; ?></title>
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
                                        <li class="breadcrumb-item active">Módulo de reservas</li>
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
                                                        <h4 class="card-title mb-0">Crear reserva</h4>
                                                </div>
                                                <div class="card-body">
                                                        <form method="post" class="row g-3">
                                                                <div class="col-12">
                                                                        <label class="form-label">Recurso *</label>
                                                                        <input type="text" name="recurso" class="form-control" placeholder="Mesa 3, Entrenador Juan" required>
                                                                </div>
                                                                <div class="col-md-6">
                                                                        <label class="form-label">Fecha *</label>
                                                                        <input type="date" name="fecha" class="form-control" required>
                                                                </div>
                                                                <div class="col-md-6">
                                                                        <label class="form-label">Hora *</label>
                                                                        <input type="time" name="hora" class="form-control" required>
                                                                </div>
                                                                <div class="col-md-6">
                                                                        <label class="form-label">Estado</label>
                                                                        <select name="estado" class="form-select">
                                                                                <option value="Reservado">Reservado</option>
                                                                                <option value="Confirmado">Confirmado</option>
                                                                                <option value="Cancelado">Cancelado</option>
                                                                        </select>
                                                                </div>
                                                                <div class="col-md-6">
                                                                        <label class="form-label">Sede</label>
                                                                        <input type="text" name="sede" class="form-control" placeholder="Sede principal">
                                                                </div>
                                                                <div class="col-12">
                                                                        <label class="form-label">Usuario / deportista</label>
                                                                        <input type="text" name="usuario" class="form-control" placeholder="Nombre o email">
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
                                                <div class="card-header d-flex justify-content-between align-items-center">
                                                        <h4 class="card-title mb-0">Reservas creadas</h4>
                                                        <small class="text-muted">Mesas y entrenadores</small>
                                                </div>
                                                <div class="card-body p-0">
                                                        <div class="table-responsive">
                                                                <table class="table mb-0">
                                                                        <thead>
                                                                                <tr>
                                                                                        <th>Recurso</th>
                                                                                        <th>Fecha</th>
                                                                                        <th>Hora</th>
                                                                                        <th>Estado</th>
                                                                                        <th></th>
                                                                                </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                                <?php if (empty($registros)): ?>
                                                                                        <tr><td colspan="5" class="text-center py-4">Sin reservas registradas.</td></tr>
                                                                                <?php else: ?>
                                                                                        <?php foreach ($registros as $registro): ?>
                                                                                                <tr>
                                                                                                        <td><?php echo htmlspecialchars($registro['recurso'] ?? ''); ?></td>
                                                                                                        <td><?php echo htmlspecialchars($registro['fecha'] ?? '-'); ?></td>
                                                                                                        <td><?php echo htmlspecialchars($registro['hora'] ?? '-'); ?></td>
                                                                                                        <td><span class="badge bg-<?php echo (($registro['estado'] ?? '') === 'Cancelado') ? 'danger' : 'primary'; ?>">
                                                                                                                <?php echo htmlspecialchars($registro['estado'] ?? ''); ?></span></td>
                                                                                                        <td class="text-end">
                                                                                                                <a class="btn btn-sm btn-outline-danger" href="?action=delete&id=<?php echo urlencode($registro['id']); ?>" onclick="return confirm('¿Eliminar reserva?');">Eliminar</a>
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
