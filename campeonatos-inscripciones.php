<?php
require_once __DIR__ . '/auth-guard.php';
require_once __DIR__ . '/config/dz.php';
require_once __DIR__ . '/helpers.php';

$campeonatos = load_json('campeonatos.json');
$jugadores = load_json('jugadores.json');
$inscripciones = load_json('inscripciones.json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $campeonato_id = trim($_POST['campeonato_id'] ?? '');
    $jugador_id = trim($_POST['jugador_id'] ?? '');
    $estado = trim($_POST['estado'] ?? 'Pendiente');
    $pago = trim($_POST['pago'] ?? 'No registrado');

    if ($campeonato_id === '' || $jugador_id === '') {
        flash('insc_error', 'Selecciona un campeonato y un jugador.');
        header('Location: campeonatos-inscripciones.php');
        exit;
    }

    $inscripciones[] = [
        'id' => uniqid('insc_', true),
        'campeonato_id' => $campeonato_id,
        'jugador_id' => $jugador_id,
        'estado' => $estado,
        'pago' => $pago,
        'created_at' => date(DATE_ATOM),
    ];

    save_json('inscripciones.json', $inscripciones);
    flash('insc_success', 'Inscripción registrada correctamente.');
    header('Location: campeonatos-inscripciones.php');
    exit;
}

if (($_GET['action'] ?? '') === 'eliminar' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $inscripciones = array_values(array_filter($inscripciones, fn($insc) => ($insc['id'] ?? '') !== $id));
    save_json('inscripciones.json', $inscripciones);
    flash('insc_success', 'Inscripción eliminada.');
    header('Location: campeonatos-inscripciones.php');
    exit;
}

$successMessage = flash('insc_success');
$errorMessage = flash('insc_error');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Inscripciones | Sistema de Campeonatos</title>
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
                    <li class="breadcrumb-item active"><a href="javascript:void(0)">Campeonatos</a></li>
                </ol>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header d-sm-flex d-block border-0 pb-0">
                            <div>
                                <h4 class="fs-20 mb-1">Inscripciones</h4>
                                <span class="fs-14 text-muted">Asigna jugadores inscritos y controla el pago.</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if ($successMessage): ?>
                                <div class="alert alert-success alert-dismissible fade show">
                                    <?php echo htmlspecialchars($successMessage); ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>
                            <?php if ($errorMessage): ?>
                                <div class="alert alert-danger alert-dismissible fade show">
                                    <?php echo htmlspecialchars($errorMessage); ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>

                            <form method="post" class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Campeonato</label>
                                    <select name="campeonato_id" class="form-control" required>
                                        <option value="">Selecciona</option>
                                        <?php foreach ($campeonatos as $camp): ?>
                                            <option value="<?php echo htmlspecialchars($camp['id']); ?>"><?php echo htmlspecialchars($camp['nombre']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Jugador</label>
                                    <select name="jugador_id" class="form-control" required>
                                        <option value="">Selecciona</option>
                                        <?php foreach ($jugadores as $jugador): ?>
                                            <option value="<?php echo htmlspecialchars($jugador['id']); ?>"><?php echo htmlspecialchars(($jugador['nombre'] ?? '') . ' ' . ($jugador['apellido'] ?? '')); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Estado</label>
                                    <select name="estado" class="form-control">
                                        <option>Pendiente</option>
                                        <option>Aprobada</option>
                                        <option>Rechazada</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Pago</label>
                                    <select name="pago" class="form-control">
                                        <option value="No registrado">No registrado</option>
                                        <option value="Pagado">Pagado</option>
                                        <option value="En revisión">En revisión</option>
                                    </select>
                                </div>
                                <div class="col-12 text-end">
                                    <button type="submit" class="btn btn-primary">Registrar inscripción</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header d-sm-flex d-block border-0 pb-0">
                            <div>
                                <h4 class="fs-20 mb-1">Listado de inscritos</h4>
                                <span class="fs-14 text-muted">Control de pagos y estado de verificación.</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Campeonato</th>
                                            <th>Jugador</th>
                                            <th>Estado</th>
                                            <th>Pago</th>
                                            <th class="text-end">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php if (empty($inscripciones)): ?>
                                        <tr><td colspan="5" class="text-center text-muted">No hay inscripciones cargadas.</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($inscripciones as $insc): ?>
                                            <?php $camp = find_by_id($campeonatos, $insc['campeonato_id']); ?>
                                            <?php $jug = find_by_id($jugadores, $insc['jugador_id']); ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($camp['nombre'] ?? 'Campeonato'); ?></td>
                                                <td><?php echo htmlspecialchars(($jug['nombre'] ?? '') . ' ' . ($jug['apellido'] ?? '')); ?></td>
                                                <td><span class="badge badge-primary light"><?php echo htmlspecialchars($insc['estado']); ?></span></td>
                                                <td><?php echo htmlspecialchars($insc['pago']); ?></td>
                                                <td class="text-end">
                                                    <a class="btn btn-outline-danger btn-xs" href="campeonatos-inscripciones.php?action=eliminar&id=<?php echo urlencode($insc['id']); ?>" onclick="return confirm('¿Eliminar inscripción?');">Eliminar</a>
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
