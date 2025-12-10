<?php
require_once __DIR__ . '/auth-guard.php';
require_once __DIR__ . '/config/dz.php';
require_once __DIR__ . '/helpers.php';

const MODULO_FINANZAS_STORE = 'modulo_finanzas.json';

$registros = load_json(MODULO_FINANZAS_STORE);

if (($_GET['action'] ?? '') === 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $registros = array_values(array_filter(
        $registros,
        fn($registro) => ($registro['id'] ?? '') !== $id
    ));
    save_json(MODULO_FINANZAS_STORE, $registros);
    flash('finanzas_success', 'Movimiento eliminado.');
    header('Location: modulo-finanzas.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $concepto = trim($_POST['concepto'] ?? '');
    $tipo = trim($_POST['tipo'] ?? '');
    $monto = trim($_POST['monto'] ?? '');
    $estado = trim($_POST['estado'] ?? '');
    $vencimiento = trim($_POST['vencimiento'] ?? '');
    $responsable = trim($_POST['responsable'] ?? '');

    if ($concepto === '' || $monto === '') {
        flash('finanzas_error', 'Concepto y monto son obligatorios.');
        header('Location: modulo-finanzas.php');
        exit;
    }

    $registros[] = [
        'id' => uniqid('fin_', true),
        'concepto' => $concepto,
        'tipo' => $tipo ?: 'Plan',
        'monto' => (float) $monto,
        'estado' => $estado ?: 'Pendiente',
        'vencimiento' => $vencimiento,
        'responsable' => $responsable,
        'created_at' => date(DATE_ATOM),
    ];

    save_json(MODULO_FINANZAS_STORE, $registros);
    flash('finanzas_success', 'Movimiento guardado.');
    header('Location: modulo-finanzas.php');
    exit;
}

$successMessage = flash('finanzas_success');
$errorMessage = flash('finanzas_error');
?>
<!DOCTYPE html>
<html lang="es">
<head>
        <title>Módulo de finanzas | <?php echo $DexignZoneSettings['site_level']['site_title']; ?></title>
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
                                        <li class="breadcrumb-item active">Módulo de finanzas</li>
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
                                                        <h4 class="card-title mb-0">Agregar movimiento</h4>
                                                </div>
                                                <div class="card-body">
                                                        <form method="post" class="row g-3">
                                                                <div class="col-12">
                                                                        <label class="form-label">Concepto *</label>
                                                                        <input type="text" name="concepto" class="form-control" required>
                                                                </div>
                                                                <div class="col-md-6">
                                                                        <label class="form-label">Tipo</label>
                                                                        <select name="tipo" class="form-select">
                                                                                <option value="Plan">Plan</option>
                                                                                <option value="Membresía">Membresía</option>
                                                                                <option value="Producto">Producto</option>
                                                                                <option value="Servicio">Servicio</option>
                                                                        </select>
                                                                </div>
                                                                <div class="col-md-6">
                                                                        <label class="form-label">Monto *</label>
                                                                        <input type="number" step="0.01" name="monto" class="form-control" required>
                                                                </div>
                                                                <div class="col-md-6">
                                                                        <label class="form-label">Estado</label>
                                                                        <select name="estado" class="form-select">
                                                                                <option value="Pendiente">Pendiente</option>
                                                                                <option value="Pagado">Pagado</option>
                                                                                <option value="Vencido">Vencido</option>
                                                                                <option value="En negociación">En negociación</option>
                                                                        </select>
                                                                </div>
                                                                <div class="col-md-6">
                                                                        <label class="form-label">Vencimiento</label>
                                                                        <input type="date" name="vencimiento" class="form-control">
                                                                </div>
                                                                <div class="col-12">
                                                                        <label class="form-label">Responsable / asociado</label>
                                                                        <input type="text" name="responsable" class="form-control" placeholder="Jugador o apoderado">
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
                                                        <h4 class="card-title mb-0">Planes, membresías y pagos</h4>
                                                        <small class="text-muted">Control rápido de cobranzas</small>
                                                </div>
                                                <div class="card-body p-0">
                                                        <div class="table-responsive">
                                                                <table class="table mb-0">
                                                                        <thead>
                                                                                <tr>
                                                                                        <th>Concepto</th>
                                                                                        <th>Tipo</th>
                                                                                        <th>Monto</th>
                                                                                        <th>Estado</th>
                                                                                        <th>Vence</th>
                                                                                        <th></th>
                                                                                </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                                <?php if (empty($registros)): ?>
                                                                                        <tr><td colspan="6" class="text-center py-4">Sin movimientos registrados.</td></tr>
                                                                                <?php else: ?>
                                                                                        <?php foreach ($registros as $registro): ?>
                                                                                                <tr>
                                                                                                        <td><?php echo htmlspecialchars($registro['concepto'] ?? ''); ?></td>
                                                                                                        <td><?php echo htmlspecialchars($registro['tipo'] ?? '-'); ?></td>
                                                                                                        <td>$<?php echo number_format((float) ($registro['monto'] ?? 0), 2, ',', '.'); ?></td>
                                                                                                        <td><span class="badge bg-<?php echo (($registro['estado'] ?? '') === 'Pagado') ? 'success' : 'warning'; ?>"><?php echo htmlspecialchars($registro['estado'] ?? ''); ?></span></td>
                                                                                                        <td><?php echo htmlspecialchars($registro['vencimiento'] ?? '-'); ?></td>
                                                                                                        <td class="text-end">
                                                                                                                <a class="btn btn-sm btn-outline-danger" href="?action=delete&id=<?php echo urlencode($registro['id']); ?>" onclick="return confirm('¿Eliminar movimiento?');">Eliminar</a>
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
