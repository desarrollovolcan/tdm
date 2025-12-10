<?php
require_once __DIR__ . '/auth-guard.php';
require_once __DIR__ . '/config/dz.php';
require_once __DIR__ . '/helpers.php';

const PROGRAMACION_STORE = 'programacion.json';

$campeonatos = load_json('campeonatos.json');
$programaciones = load_json(PROGRAMACION_STORE);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $campeonato_id = trim($_POST['campeonato_id'] ?? '');
    $fecha = trim($_POST['fecha'] ?? '');
    $hora = trim($_POST['hora'] ?? '');
    $mesa = trim($_POST['mesa'] ?? '');
    $detalle = trim($_POST['detalle'] ?? '');
    $fase = trim($_POST['fase'] ?? 'Ronda preliminar');

    if ($campeonato_id === '' || $fecha === '') {
        flash('programacion_error', 'Selecciona un campeonato y una fecha.');
        header('Location: programacion-partidos.php');
        exit;
    }

    $programaciones[] = [
        'id' => uniqid('prog_', true),
        'campeonato_id' => $campeonato_id,
        'fecha' => $fecha,
        'hora' => $hora,
        'mesa' => $mesa,
        'fase' => $fase,
        'detalle' => $detalle,
        'created_at' => date(DATE_ATOM),
    ];

    save_json(PROGRAMACION_STORE, $programaciones);
    flash('programacion_success', 'Partido programado correctamente.');
    header('Location: programacion-partidos.php');
    exit;
}

if (($_GET['action'] ?? '') === 'eliminar' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $programaciones = array_values(array_filter($programaciones, fn($p) => ($p['id'] ?? '') !== $id));
    save_json(PROGRAMACION_STORE, $programaciones);
    flash('programacion_success', 'Programación eliminada.');
    header('Location: programacion-partidos.php');
    exit;
}

$successMessage = flash('programacion_success');
$errorMessage = flash('programacion_error');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Programación de partidos | Sistema de Campeonatos</title>
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
                                <h4 class="fs-20 mb-1">Programación de partidos</h4>
                                <span class="fs-14 text-muted">Agenda mesas, horarios y fases.</span>
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
                                            <option value="<?php echo htmlspecialchars($camp['id'] ?? ''); ?>"><?php echo htmlspecialchars($camp['nombre'] ?? 'Campeonato'); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Fecha</label>
                                    <input type="date" name="fecha" class="form-control" required>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Hora</label>
                                    <input type="time" name="hora" class="form-control">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Mesa / cancha</label>
                                    <input type="text" name="mesa" class="form-control" placeholder="Mesa 1">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Fase</label>
                                    <select name="fase" class="form-control">
                                        <option>Ronda preliminar</option>
                                        <option>Grupo</option>
                                        <option>Cuartos</option>
                                        <option>Semifinal</option>
                                        <option>Final</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Detalle</label>
                                    <textarea name="detalle" class="form-control" rows="2" placeholder="Jugadores, equipos o notas del emparejamiento"></textarea>
                                </div>
                                <div class="col-12 text-end">
                                    <button type="submit" class="btn btn-primary">Agregar a la programación</button>
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
                                <h4 class="fs-20 mb-1">Partidos programados</h4>
                                <span class="fs-14 text-muted">Vista rápida de fecha, fase y mesa.</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Campeonato</th>
                                            <th>Fecha</th>
                                            <th>Hora</th>
                                            <th>Fase</th>
                                            <th>Mesa</th>
                                            <th>Detalle</th>
                                            <th class="text-end">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($programaciones)): ?>
                                            <tr><td colspan="7" class="text-center text-muted">Sin partidos programados.</td></tr>
                                        <?php else: ?>
                                            <?php foreach ($programaciones as $prog): ?>
                                                <?php $camp = find_by_id($campeonatos, $prog['campeonato_id']); ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($camp['nombre'] ?? 'Campeonato'); ?></td>
                                                    <td><?php echo htmlspecialchars($prog['fecha']); ?></td>
                                                    <td><?php echo htmlspecialchars($prog['hora'] ?: '-'); ?></td>
                                                    <td><span class="badge badge-secondary light"><?php echo htmlspecialchars($prog['fase']); ?></span></td>
                                                    <td><?php echo htmlspecialchars($prog['mesa'] ?: 'Por asignar'); ?></td>
                                                    <td><?php echo htmlspecialchars($prog['detalle'] ?: 'Sin detalle'); ?></td>
                                                    <td class="text-end">
                                                        <a class="btn btn-outline-danger btn-xs" href="programacion-partidos.php?action=eliminar&id=<?php echo urlencode($prog['id']); ?>" onclick="return confirm('¿Eliminar este enfrentamiento?');">Eliminar</a>
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
