<?php
require_once __DIR__ . '/auth-guard.php';
require_once __DIR__ . '/config/dz.php';
require_once __DIR__ . '/helpers.php';

const PARTIDOS_STORE = 'partidos.json';

$campeonatos = load_json('campeonatos.json');
$jugadores = load_json('jugadores.json');
$partidos = load_json(PARTIDOS_STORE);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $campeonato_id = trim($_POST['campeonato_id'] ?? '');
    $jugador_local = trim($_POST['jugador_local'] ?? '');
    $jugador_visitante = trim($_POST['jugador_visitante'] ?? '');
    $ronda = trim($_POST['ronda'] ?? '');
    $fecha = trim($_POST['fecha'] ?? '');
    $estado = trim($_POST['estado'] ?? 'Programado');

    if ($campeonato_id === '' || $jugador_local === '' || $jugador_visitante === '') {
        flash('partidos_error', 'Debes elegir campeonato y ambos jugadores.');
        header('Location: partidos.php');
        exit;
    }

    if ($jugador_local === $jugador_visitante) {
        flash('partidos_error', 'Selecciona jugadores diferentes.');
        header('Location: partidos.php');
        exit;
    }

    $partidos[] = [
        'id' => uniqid('par_', true),
        'campeonato_id' => $campeonato_id,
        'jugador_local' => $jugador_local,
        'jugador_visitante' => $jugador_visitante,
        'ronda' => $ronda,
        'fecha' => $fecha,
        'estado' => $estado,
        'created_at' => date(DATE_ATOM),
    ];

    save_json(PARTIDOS_STORE, $partidos);
    flash('partidos_success', 'Partido agregado.');
    header('Location: partidos.php');
    exit;
}

if (($_GET['action'] ?? '') === 'eliminar' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $partidos = array_values(array_filter($partidos, fn($p) => ($p['id'] ?? '') !== $id));
    save_json(PARTIDOS_STORE, $partidos);
    flash('partidos_success', 'Partido eliminado.');
    header('Location: partidos.php');
    exit;
}

$successMessage = flash('partidos_success');
$errorMessage = flash('partidos_error');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Partidos | Sistema de Campeonatos</title>
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
                    <li class="breadcrumb-item active"><a href="javascript:void(0)">Partidos</a></li>
                </ol>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header d-sm-flex d-block border-0 pb-0">
                            <div>
                                <h4 class="fs-20 mb-1">Gestión de partidos</h4>
                                <span class="fs-14 text-muted">Crea enfrentamientos y define su estado.</span>
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
                                    <label class="form-label">Campeonato</label>
                                    <select name="campeonato_id" class="form-control" required>
                                        <option value="">Selecciona</option>
                                        <?php foreach ($campeonatos as $camp): ?>
                                            <option value="<?php echo htmlspecialchars($camp['id'] ?? ''); ?>"><?php echo htmlspecialchars($camp['nombre'] ?? 'Campeonato'); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Jugador local</label>
                                    <select name="jugador_local" class="form-control" required>
                                        <option value="">Selecciona</option>
                                        <?php foreach ($jugadores as $jug): ?>
                                            <option value="<?php echo htmlspecialchars($jug['id'] ?? ''); ?>"><?php echo htmlspecialchars(($jug['nombre'] ?? '') . ' ' . ($jug['apellido'] ?? '')); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Jugador visitante</label>
                                    <select name="jugador_visitante" class="form-control" required>
                                        <option value="">Selecciona</option>
                                        <?php foreach ($jugadores as $jug): ?>
                                            <option value="<?php echo htmlspecialchars($jug['id'] ?? ''); ?>"><?php echo htmlspecialchars(($jug['nombre'] ?? '') . ' ' . ($jug['apellido'] ?? '')); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Ronda</label>
                                    <input type="text" name="ronda" class="form-control" placeholder="Grupo, Cuartos">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Fecha</label>
                                    <input type="date" name="fecha" class="form-control">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Estado</label>
                                    <select name="estado" class="form-control">
                                            <option>Programado</option>
                                            <option>En juego</option>
                                            <option>Finalizado</option>
                                            <option>Suspendido</option>
                                    </select>
                                </div>
                                <div class="col-12 text-end">
                                    <button type="submit" class="btn btn-primary">Crear partido</button>
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
                                <h4 class="fs-20 mb-1">Listado de partidos</h4>
                                <span class="fs-14 text-muted">Enfrentamientos registrados en el sistema.</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Campeonato</th>
                                            <th>Jugadores</th>
                                            <th>Ronda</th>
                                            <th>Fecha</th>
                                            <th>Estado</th>
                                            <th class="text-end">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($partidos)): ?>
                                            <tr><td colspan="6" class="text-center text-muted">Aún no hay partidos cargados.</td></tr>
                                        <?php else: ?>
                                            <?php foreach ($partidos as $par): ?>
                                                <?php $camp = find_by_id($campeonatos, $par['campeonato_id']); ?>
                                                <?php $local = find_by_id($jugadores, $par['jugador_local']); ?>
                                                <?php $visitante = find_by_id($jugadores, $par['jugador_visitante']); ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($camp['nombre'] ?? 'Campeonato'); ?></td>
                                                    <td><?php echo htmlspecialchars(($local['nombre'] ?? '') . ' vs ' . ($visitante['nombre'] ?? '')); ?></td>
                                                    <td><?php echo htmlspecialchars($par['ronda'] ?: ''); ?></td>
                                                    <td><?php echo htmlspecialchars($par['fecha'] ?: ''); ?></td>
                                                    <td><span class="badge badge-primary light"><?php echo htmlspecialchars($par['estado'] ?? 'Programado'); ?></span></td>
                                                    <td class="text-end">
                                                        <a class="btn btn-outline-danger btn-xs" href="partidos.php?action=eliminar&id=<?php echo urlencode($par['id']); ?>" onclick="return confirm('¿Eliminar partido?');">Eliminar</a>
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
