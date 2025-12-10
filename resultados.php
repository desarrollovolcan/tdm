<?php
require_once __DIR__ . '/auth-guard.php';
require_once __DIR__ . '/config/dz.php';
require_once __DIR__ . '/helpers.php';

const RESULTADOS_STORE = 'resultados.json';

$campeonatos = load_json('campeonatos.json');
$jugadores = load_json('jugadores.json');
$partidos = load_json('partidos.json');
$resultados = load_json(RESULTADOS_STORE);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $partido_id = trim($_POST['partido_id'] ?? '');
    $ganador_id = trim($_POST['ganador_id'] ?? '');
    $marcador = trim($_POST['marcador'] ?? '');
    $detalle = trim($_POST['detalle'] ?? '');
    $fecha = trim($_POST['fecha'] ?? date('Y-m-d'));

    if ($partido_id === '' || $ganador_id === '') {
        flash('resultados_error', 'Selecciona partido y jugador ganador.');
        header('Location: resultados.php');
        exit;
    }

    $resultados[] = [
        'id' => uniqid('res_', true),
        'partido_id' => $partido_id,
        'ganador_id' => $ganador_id,
        'marcador' => $marcador,
        'detalle' => $detalle,
        'fecha' => $fecha,
        'created_at' => date(DATE_ATOM),
    ];

    save_json(RESULTADOS_STORE, $resultados);
    flash('resultados_success', 'Resultado registrado.');
    header('Location: resultados.php');
    exit;
}

if (($_GET['action'] ?? '') === 'eliminar' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $resultados = array_values(array_filter($resultados, fn($r) => ($r['id'] ?? '') !== $id));
    save_json(RESULTADOS_STORE, $resultados);
    flash('resultados_success', 'Resultado eliminado.');
    header('Location: resultados.php');
    exit;
}

$successMessage = flash('resultados_success');
$errorMessage = flash('resultados_error');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Resultados | Sistema de Campeonatos</title>
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
                    <li class="breadcrumb-item active"><a href="javascript:void(0)">Resultados</a></li>
                </ol>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header d-sm-flex d-block border-0 pb-0">
                            <div>
                                <h4 class="fs-20 mb-1">Registro de resultados</h4>
                                <span class="fs-14 text-muted">Asigna ganador, marcador y notas.</span>
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
                                    <label class="form-label">Partido</label>
                                    <select name="partido_id" class="form-control" required>
                                        <option value="">Selecciona</option>
                                        <?php foreach ($partidos as $par): ?>
                                            <?php $local = find_by_id($jugadores, $par['jugador_local'] ?? ''); ?>
                                            <?php $visit = find_by_id($jugadores, $par['jugador_visitante'] ?? ''); ?>
                                            <option value="<?php echo htmlspecialchars($par['id'] ?? ''); ?>">
                                                <?php echo htmlspecialchars(($local['nombre'] ?? 'Jugador A') . ' vs ' . ($visit['nombre'] ?? 'Jugador B')); ?> (<?php echo htmlspecialchars($par['ronda'] ?: ''); ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Ganador</label>
                                    <select name="ganador_id" class="form-control" required>
                                        <option value="">Selecciona</option>
                                        <?php foreach ($jugadores as $jug): ?>
                                            <option value="<?php echo htmlspecialchars($jug['id'] ?? ''); ?>"><?php echo htmlspecialchars(($jug['nombre'] ?? '') . ' ' . ($jug['apellido'] ?? '')); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Marcador</label>
                                    <input type="text" name="marcador" class="form-control" placeholder="3-1">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Fecha</label>
                                    <input type="date" name="fecha" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Detalle</label>
                                    <textarea name="detalle" class="form-control" rows="2" placeholder="Sets, incidencias o comentarios"></textarea>
                                </div>
                                <div class="col-12 text-end">
                                    <button type="submit" class="btn btn-primary">Guardar resultado</button>
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
                                <h4 class="fs-20 mb-1">Resultados cargados</h4>
                                <span class="fs-14 text-muted">Cruce de jugadores y campeonatos.</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Partido</th>
                                            <th>Ganador</th>
                                            <th>Marcador</th>
                                            <th>Fecha</th>
                                            <th>Detalle</th>
                                            <th class="text-end">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($resultados)): ?>
                                            <tr><td colspan="6" class="text-center text-muted">Sin resultados registrados.</td></tr>
                                        <?php else: ?>
                                            <?php foreach ($resultados as $res): ?>
                                                <?php $par = find_by_id($partidos, $res['partido_id']); ?>
                                                <?php $local = find_by_id($jugadores, $par['jugador_local'] ?? ''); ?>
                                                <?php $visit = find_by_id($jugadores, $par['jugador_visitante'] ?? ''); ?>
                                                <?php $ganador = find_by_id($jugadores, $res['ganador_id']); ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars(($local['nombre'] ?? 'Jugador A') . ' vs ' . ($visit['nombre'] ?? 'Jugador B')); ?></td>
                                                    <td><?php echo htmlspecialchars(($ganador['nombre'] ?? '') . ' ' . ($ganador['apellido'] ?? '')); ?></td>
                                                    <td><?php echo htmlspecialchars($res['marcador'] ?: '-'); ?></td>
                                                    <td><?php echo htmlspecialchars($res['fecha'] ?? ''); ?></td>
                                                    <td><?php echo htmlspecialchars($res['detalle'] ?: ''); ?></td>
                                                    <td class="text-end">
                                                        <a class="btn btn-outline-danger btn-xs" href="resultados.php?action=eliminar&id=<?php echo urlencode($res['id']); ?>" onclick="return confirm('¿Eliminar resultado?');">Eliminar</a>
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
