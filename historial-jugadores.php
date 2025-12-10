<?php
require_once __DIR__ . '/auth-guard.php';
require_once __DIR__ . '/config/dz.php';
require_once __DIR__ . '/helpers.php';

const HISTORIAL_STORE = 'historial_jugadores.json';

$jugadores = load_json('jugadores.json');
$campeonatos = load_json('campeonatos.json');
$historial = load_json(HISTORIAL_STORE);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jugador_id = trim($_POST['jugador_id'] ?? '');
    $campeonato_id = trim($_POST['campeonato_id'] ?? '');
    $resultado = trim($_POST['resultado'] ?? 'Ganado');
    $ronda = trim($_POST['ronda'] ?? 'Fase de grupos');
    $detalle = trim($_POST['detalle'] ?? '');
    $fecha = trim($_POST['fecha'] ?? date('Y-m-d'));

    if ($jugador_id === '' || $campeonato_id === '') {
        flash('historial_error', 'Selecciona jugador y campeonato.');
        header('Location: historial-jugadores.php');
        exit;
    }

    $historial[] = [
        'id' => uniqid('hist_', true),
        'jugador_id' => $jugador_id,
        'campeonato_id' => $campeonato_id,
        'resultado' => $resultado,
        'ronda' => $ronda,
        'detalle' => $detalle,
        'fecha' => $fecha,
        'created_at' => date(DATE_ATOM),
    ];

    save_json(HISTORIAL_STORE, $historial);
    flash('historial_success', 'Historial actualizado para el jugador.');
    header('Location: historial-jugadores.php');
    exit;
}

if (($_GET['action'] ?? '') === 'eliminar' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $historial = array_values(array_filter($historial, fn($h) => ($h['id'] ?? '') !== $id));
    save_json(HISTORIAL_STORE, $historial);
    flash('historial_success', 'Registro eliminado.');
    header('Location: historial-jugadores.php');
    exit;
}

$successMessage = flash('historial_success');
$errorMessage = flash('historial_error');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Historial de jugadores | Sistema de Campeonatos</title>
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
                    <li class="breadcrumb-item active"><a href="javascript:void(0)">Reportes</a></li>
                </ol>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header d-sm-flex d-block border-0 pb-0">
                            <div>
                                <h4 class="fs-20 mb-1">Historial de jugadores</h4>
                                <span class="fs-14 text-muted">Resultados, rondas y notas clave.</span>
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
                                <div class="col-md-3">
                                    <label class="form-label">Jugador</label>
                                    <select name="jugador_id" class="form-control" required>
                                        <option value="">Selecciona</option>
                                        <?php foreach ($jugadores as $jug): ?>
                                            <option value="<?php echo htmlspecialchars($jug['id'] ?? ''); ?>"><?php echo htmlspecialchars(($jug['nombre'] ?? '') . ' ' . ($jug['apellido'] ?? '')); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Campeonato</label>
                                    <select name="campeonato_id" class="form-control" required>
                                        <option value="">Selecciona</option>
                                        <?php foreach ($campeonatos as $camp): ?>
                                            <option value="<?php echo htmlspecialchars($camp['id'] ?? ''); ?>"><?php echo htmlspecialchars($camp['nombre'] ?? 'Campeonato'); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Ronda</label>
                                    <input type="text" name="ronda" class="form-control" placeholder="Fase de grupos">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Resultado</label>
                                    <select name="resultado" class="form-control">
                                        <option>Ganado</option>
                                        <option>Perdido</option>
                                        <option>Retirado</option>
                                        <option>Walkover</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Fecha</label>
                                    <input type="date" name="fecha" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Detalle</label>
                                    <textarea name="detalle" class="form-control" rows="2" placeholder="Marcador, incidente o comentario"></textarea>
                                </div>
                                <div class="col-12 text-end">
                                    <button type="submit" class="btn btn-primary">Agregar al historial</button>
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
                                <h4 class="fs-20 mb-1">Últimos registros</h4>
                                <span class="fs-14 text-muted">Seguimiento por jugador y campeonato.</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Jugador</th>
                                            <th>Campeonato</th>
                                            <th>Ronda</th>
                                            <th>Resultado</th>
                                            <th>Fecha</th>
                                            <th>Detalle</th>
                                            <th class="text-end">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($historial)): ?>
                                            <tr><td colspan="7" class="text-center text-muted">Aún no hay historial registrado.</td></tr>
                                        <?php else: ?>
                                            <?php foreach ($historial as $item): ?>
                                                <?php $jug = find_by_id($jugadores, $item['jugador_id']); ?>
                                                <?php $camp = find_by_id($campeonatos, $item['campeonato_id']); ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars(($jug['nombre'] ?? '') . ' ' . ($jug['apellido'] ?? '')); ?></td>
                                                    <td><?php echo htmlspecialchars($camp['nombre'] ?? 'Campeonato'); ?></td>
                                                    <td><?php echo htmlspecialchars($item['ronda'] ?? ''); ?></td>
                                                    <td><span class="badge badge-info light"><?php echo htmlspecialchars($item['resultado']); ?></span></td>
                                                    <td><?php echo htmlspecialchars($item['fecha'] ?? ''); ?></td>
                                                    <td><?php echo htmlspecialchars($item['detalle'] ?: 'Sin detalle'); ?></td>
                                                    <td class="text-end">
                                                        <a class="btn btn-outline-danger btn-xs" href="historial-jugadores.php?action=eliminar&id=<?php echo urlencode($item['id']); ?>" onclick="return confirm('¿Eliminar registro del historial?');">Eliminar</a>
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
