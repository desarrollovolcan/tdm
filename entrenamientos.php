<?php
require_once __DIR__ . '/auth-guard.php';
require_once __DIR__ . '/config/dz.php';
require_once __DIR__ . '/helpers.php';

const CLUB_STORE = 'clubes.json';
const SOCIO_STORE = 'socios.json';
const TRAINING_STORE = 'entrenamientos.json';
const PLAN_STORE = 'planes_entrenamiento.json';

$clubes = load_json(CLUB_STORE);
$socios = load_json(SOCIO_STORE);
$planes = load_json(PLAN_STORE);
$entrenamientos = load_json(TRAINING_STORE);

$entrenamientoEditando = null;
$busqueda = trim($_GET['q'] ?? '');
$filtroClub = trim($_GET['club'] ?? '');
$filtroTipo = trim($_GET['tipo'] ?? '');
$filtroFecha = trim($_GET['fecha'] ?? '');
$filtroEstado = trim($_GET['estado'] ?? '');
$filtroSocio = trim($_GET['socio'] ?? '');

if (($_GET['action'] ?? '') === 'editar' && isset($_GET['id'])) {
    $entrenamientoEditando = find_by_id($entrenamientos, $_GET['id']);
}

function sanitizarParticipantes(array $ids, array $socios, string $clubId): array
{
    return array_values(array_filter(
        $ids,
        function ($id) use ($socios, $clubId) {
            foreach ($socios as $socio) {
                if (($socio['id'] ?? '') === $id && ($socio['club_id'] ?? '') === $clubId) {
                    return true;
                }
            }

            return false;
        }
    ));
}

function actualizarHistorialPlanes(array $socios, array $entrenamiento, array $planes): array
{
    $entrenamientoId = $entrenamiento['id'];
    $planId = $entrenamiento['plan_id'] ?? '';
    $plan = $planId ? find_by_id($planes, $planId) : null;
    $integrantes = $entrenamiento['integrantes'] ?? [];

    foreach ($socios as &$socio) {
        $historial = $socio['plan_historial'] ?? [];
        $historial = array_values(array_filter($historial, fn($registro) => ($registro['entrenamiento_id'] ?? '') !== $entrenamientoId));

        if (in_array($socio['id'], $integrantes, true) && $plan) {
            $historial[] = [
                'entrenamiento_id' => $entrenamientoId,
                'plan_id' => $planId,
                'plan_nombre' => $plan['nombre'] ?? '',
                'fecha' => $entrenamiento['inicio'] ?? date(DATE_ATOM),
            ];
        }

        $socio['plan_historial'] = $historial;
    }
    unset($socio);

    return $socios;
}

if (($_GET['action'] ?? '') === 'cancelar' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $entrenamientos = array_map(
        function ($entrenamiento) use ($id) {
            if (($entrenamiento['id'] ?? '') !== $id) {
                return $entrenamiento;
            }

            $entrenamiento['estado'] = ($entrenamiento['estado'] ?? 'programado') === 'cancelado' ? 'programado' : 'cancelado';
            $entrenamiento['updated_at'] = date(DATE_ATOM);
            return $entrenamiento;
        },
        $entrenamientos
    );

    save_json(TRAINING_STORE, $entrenamientos);
    $_SESSION['entrenamiento_success'] = 'Estado del entrenamiento actualizado.';
    header('Location: entrenamientos.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = trim($_POST['id'] ?? '');
    $clubId = trim($_POST['club_id'] ?? '');
    $tipo = trim($_POST['tipo'] ?? '');
    $titulo = trim($_POST['titulo'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $inicio = trim($_POST['inicio'] ?? '');
    $fin = trim($_POST['fin'] ?? '');
    $lugar = trim($_POST['lugar'] ?? '');
    $entrenador = trim($_POST['entrenador'] ?? '');
    $planId = trim($_POST['plan_id'] ?? '');
    $integrantes = isset($_POST['integrantes']) ? (array) $_POST['integrantes'] : [];

    if ($clubId === '') {
        $_SESSION['entrenamiento_error'] = 'El club es obligatorio.';
        header('Location: entrenamientos.php');
        exit;
    }

    if ($tipo === '') {
        $_SESSION['entrenamiento_error'] = 'El tipo de entrenamiento es obligatorio.';
        header('Location: entrenamientos.php');
        exit;
    }

    if ($titulo === '' || $inicio === '' || $fin === '') {
        $_SESSION['entrenamiento_error'] = 'Título, fecha de inicio y término son obligatorios.';
        header('Location: entrenamientos.php');
        exit;
    }

    if (strtotime($fin) < strtotime($inicio)) {
        $_SESSION['entrenamiento_error'] = 'La fecha de término no puede ser anterior al inicio.';
        header('Location: entrenamientos.php');
        exit;
    }

    $integrantesLimpios = sanitizarParticipantes($integrantes, $socios, $clubId);

    if ($tipo === 'individual' && count($integrantesLimpios) !== 1) {
        $_SESSION['entrenamiento_error'] = 'Los entrenamientos individuales deben tener un solo integrante asignado.';
        header('Location: entrenamientos.php');
        exit;
    }

    if ($tipo === 'grupal' && count($integrantesLimpios) < 1) {
        $_SESSION['entrenamiento_error'] = 'Debes asignar al menos un integrante para entrenamientos grupales.';
        header('Location: entrenamientos.php');
        exit;
    }

    $entrenamientoActual = $id !== '' ? find_by_id($entrenamientos, $id) : null;

    $registro = [
        'id' => $id !== '' ? $id : uniqid('entreno_', true),
        'club_id' => $clubId,
        'tipo' => $tipo,
        'titulo' => $titulo,
        'descripcion' => $descripcion,
        'inicio' => $inicio,
        'fin' => $fin,
        'lugar' => $lugar,
        'entrenador' => $entrenador,
        'plan_id' => $planId,
        'integrantes' => $integrantesLimpios,
        'estado' => $id !== '' ? ($entrenamientoActual['estado'] ?? 'programado') : 'programado',
        'updated_at' => date(DATE_ATOM),
    ];

    $entrenamientos = array_values(array_filter($entrenamientos, fn($item) => ($item['id'] ?? '') !== $registro['id']));
    $entrenamientos[] = $registro;
    usort($entrenamientos, fn($a, $b) => strtotime($a['inicio'] ?? '') <=> strtotime($b['inicio'] ?? ''));

    $socios = actualizarHistorialPlanes($socios, $registro, $planes);

    save_json(TRAINING_STORE, $entrenamientos);
    save_json(SOCIO_STORE, $socios);

    $_SESSION['entrenamiento_success'] = $id !== '' ? 'Entrenamiento actualizado correctamente.' : 'Entrenamiento creado correctamente.';
    header('Location: entrenamientos.php');
    exit;
}

$entrenamientosFiltrados = array_values(array_filter(
    $entrenamientos,
    function ($entrenamiento) use ($busqueda, $filtroClub, $filtroTipo, $filtroFecha, $filtroEstado, $filtroSocio) {
        $matchBusqueda = $busqueda === ''
            || stripos($entrenamiento['titulo'] ?? '', $busqueda) !== false
            || stripos($entrenamiento['descripcion'] ?? '', $busqueda) !== false;

        $matchClub = $filtroClub === '' || ($entrenamiento['club_id'] ?? '') === $filtroClub;
        $matchTipo = $filtroTipo === '' || ($entrenamiento['tipo'] ?? '') === $filtroTipo;
        $matchEstado = $filtroEstado === '' || ($entrenamiento['estado'] ?? 'programado') === $filtroEstado;

        $matchFecha = true;
        if ($filtroFecha !== '') {
            $fecha = date('Y-m-d', strtotime($entrenamiento['inicio'] ?? ''));
            $matchFecha = $fecha === $filtroFecha;
        }

        $matchSocio = true;
        if ($filtroSocio !== '') {
            $matchSocio = in_array($filtroSocio, $entrenamiento['integrantes'] ?? [], true);
        }

        return $matchBusqueda && $matchClub && $matchTipo && $matchFecha && $matchEstado && $matchSocio;
    }
));

function agruparPorFecha(array $entrenamientos): array
{
    $grupos = [];
    foreach ($entrenamientos as $entrenamiento) {
        $fecha = date('Y-m-d', strtotime($entrenamiento['inicio'] ?? ''));
        $grupos[$fecha][] = $entrenamiento;
    }

    ksort($grupos);
    return $grupos;
}

$entrenamientosPorDia = agruparPorFecha($entrenamientosFiltrados);

?>
<!DOCTYPE html>
<html lang="es">

<head>
        <title>Entrenamientos | Sistema de Campeonatos</title>
        <?php include 'elements/meta.php';?>
        <link rel="shortcut icon" type="image/png" href="<?php echo ['site_level']['favicon']?>">
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
                        <li class="breadcrumb-item active"><a href="javascript:void(0)">Entrenamientos</a></li>
                    </ol>
                </div>

                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-header d-sm-flex d-block border-0 pb-0">
                                <div>
                                    <h4 class="fs-20 mb-1">Programar entrenamiento</h4>
                                    <span class="fs-14 text-muted">Crea sesiones grupales o individuales con validaciones básicas.</span>
                                </div>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($_SESSION['entrenamiento_success'])): ?>
                                    <div class="alert alert-success alert-dismissible fade show">
                                        <?php echo htmlspecialchars($_SESSION['entrenamiento_success']); unset($_SESSION['entrenamiento_success']); ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($_SESSION['entrenamiento_error'])): ?>
                                    <div class="alert alert-danger alert-dismissible fade show">
                                        <?php echo htmlspecialchars($_SESSION['entrenamiento_error']); unset($_SESSION['entrenamiento_error']); ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                <?php endif; ?>

                                <form method="post" class="row g-3">
                                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($entrenamientoEditando['id'] ?? ''); ?>">
                                    <div class="col-md-4">
                                        <label class="form-label">Club</label>
                                        <select class="form-control" name="club_id" required>
                                            <option value="">Selecciona un club</option>
                                            <?php foreach ($clubes as $club): ?>
                                                <option value="<?php echo htmlspecialchars($club['id'] ?? ''); ?>" <?php echo (($entrenamientoEditando['club_id'] ?? '') === ($club['id'] ?? '')) ? 'selected' : ''; ?>><?php echo htmlspecialchars($club['nombre'] ?? ''); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Tipo</label>
                                        <?php $tipoActual = $entrenamientoEditando['tipo'] ?? ''; ?>
                                        <select name="tipo" class="form-control" required>
                                            <option value="">Selecciona</option>
                                            <option value="grupal" <?php echo $tipoActual === 'grupal' ? 'selected' : ''; ?>>Grupal</option>
                                            <option value="individual" <?php echo $tipoActual === 'individual' ? 'selected' : ''; ?>>Individual</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Título del entrenamiento</label>
                                        <input type="text" name="titulo" class="form-control" required value="<?php echo htmlspecialchars($entrenamientoEditando['titulo'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-8">
                                        <label class="form-label">Descripción</label>
                                        <textarea name="descripcion" class="form-control" rows="3" placeholder="Objetivo, ejercicios, notas..."><?php echo htmlspecialchars($entrenamientoEditando['descripcion'] ?? ''); ?></textarea>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Lugar</label>
                                        <input type="text" name="lugar" class="form-control" placeholder="Cancha, pista, gimnasio" value="<?php echo htmlspecialchars($entrenamientoEditando['lugar'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Fecha y hora de inicio</label>
                                        <input type="datetime-local" name="inicio" class="form-control" required value="<?php echo htmlspecialchars($entrenamientoEditando['inicio'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Fecha y hora de término</label>
                                        <input type="datetime-local" name="fin" class="form-control" required value="<?php echo htmlspecialchars($entrenamientoEditando['fin'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Entrenador / responsable</label>
                                        <input type="text" name="entrenador" class="form-control" placeholder="Nombre del entrenador" value="<?php echo htmlspecialchars($entrenamientoEditando['entrenador'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Plan de entrenamiento (opcional)</label>
                                        <select name="plan_id" class="form-control">
                                            <option value="">Sin plan asociado</option>
                                            <?php foreach ($planes as $plan): ?>
                                                <option value="<?php echo htmlspecialchars($plan['id'] ?? ''); ?>" <?php echo (($entrenamientoEditando['plan_id'] ?? '') === ($plan['id'] ?? '')) ? 'selected' : ''; ?>><?php echo htmlspecialchars($plan['nombre'] ?? ''); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label">Integrantes asignados</label>
                                        <select name="integrantes[]" class="form-control" multiple size="6" required>
                                            <?php foreach ($socios as $socio): ?>
                                                <option value="<?php echo htmlspecialchars($socio['id']); ?>" <?php echo in_array($socio['id'], $entrenamientoEditando['integrantes'] ?? [], true) ? 'selected' : ''; ?>><?php echo htmlspecialchars(($socio['nombre'] ?? '') . ' ' . ($socio['apellido'] ?? '') . ' - ' . (find_by_id($clubes, $socio['club_id'] ?? '')['nombre'] ?? '')); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <small class="text-muted">Solo se considerarán integrantes del club seleccionado. Para entrenamientos individuales se validará que haya un único integrante.</small>
                                    </div>
                                    <div class="col-12 text-end">
                                        <button type="submit" class="btn btn-primary"><?php echo $entrenamientoEditando ? 'Actualizar entrenamiento' : 'Guardar entrenamiento'; ?></button>
                                        <?php if ($entrenamientoEditando): ?>
                                            <a href="entrenamientos.php" class="btn btn-outline-secondary">Cancelar edición</a>
                                        <?php endif; ?>
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
                                    <h4 class="fs-20 mb-1">Agenda y calendario</h4>
                                    <span class="fs-14 text-muted">Filtra por club, tipo, fecha o integrante.</span>
                                </div>
                            </div>
                            <div class="card-body">
                                <form class="row g-3 mb-3" method="get">
                                    <div class="col-md-4 col-lg-3">
                                        <label class="form-label">Buscar</label>
                                        <input type="text" name="q" class="form-control" placeholder="Título o descripción" value="<?php echo htmlspecialchars($busqueda); ?>">
                                    </div>
                                    <div class="col-md-4 col-lg-2">
                                        <label class="form-label">Club</label>
                                        <select name="club" class="form-control">
                                            <option value="">Todos</option>
                                            <?php foreach ($clubes as $club): ?>
                                                <option value="<?php echo htmlspecialchars($club['id'] ?? ''); ?>" <?php echo $filtroClub === ($club['id'] ?? '') ? 'selected' : ''; ?>><?php echo htmlspecialchars($club['nombre'] ?? ''); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4 col-lg-2">
                                        <label class="form-label">Tipo</label>
                                        <select name="tipo" class="form-control">
                                            <option value="">Todos</option>
                                            <option value="grupal" <?php echo $filtroTipo === 'grupal' ? 'selected' : ''; ?>>Grupal</option>
                                            <option value="individual" <?php echo $filtroTipo === 'individual' ? 'selected' : ''; ?>>Individual</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 col-lg-2">
                                        <label class="form-label">Fecha</label>
                                        <input type="date" name="fecha" class="form-control" value="<?php echo htmlspecialchars($filtroFecha); ?>">
                                    </div>
                                    <div class="col-md-4 col-lg-2">
                                        <label class="form-label">Estado</label>
                                        <select name="estado" class="form-control">
                                            <option value="">Todos</option>
                                            <option value="programado" <?php echo $filtroEstado === 'programado' ? 'selected' : ''; ?>>Programado</option>
                                            <option value="cancelado" <?php echo $filtroEstado === 'cancelado' ? 'selected' : ''; ?>>Cancelado</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 col-lg-3">
                                        <label class="form-label">Integrante</label>
                                        <select name="socio" class="form-control">
                                            <option value="">Todos</option>
                                            <?php foreach ($socios as $socio): ?>
                                                <option value="<?php echo htmlspecialchars($socio['id'] ?? ''); ?>" <?php echo $filtroSocio === ($socio['id'] ?? '') ? 'selected' : ''; ?>><?php echo htmlspecialchars(($socio['nombre'] ?? '') . ' ' . ($socio['apellido'] ?? '')); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-12 text-end">
                                        <button class="btn btn-outline-primary" type="submit">Aplicar filtros</button>
                                        <a class="btn btn-light" href="entrenamientos.php">Limpiar</a>
                                    </div>
                                </form>

                                <div class="row">
                                    <?php if (empty($entrenamientosPorDia)): ?>
                                        <div class="col-12"><p class="text-muted text-center mb-0">No hay entrenamientos con los filtros seleccionados.</p></div>
                                    <?php else: ?>
                                        <?php foreach ($entrenamientosPorDia as $fecha => $items): ?>
                                            <div class="col-md-6 col-lg-4">
                                                <div class="border rounded p-3 mb-3">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <h5 class="mb-0"><?php echo date('d M Y', strtotime($fecha)); ?></h5>
                                                        <span class="badge badge-light"><?php echo count($items); ?> sesión(es)</span>
                                                    </div>
                                                    <?php foreach ($items as $item): ?>
                                                        <?php
                                                            $club = find_by_id($clubes, $item['club_id'] ?? '');
                                                            $plan = find_by_id($planes, $item['plan_id'] ?? '');
                                                            $participantes = array_filter($socios, fn($socio) => in_array($socio['id'] ?? '', $item['integrantes'] ?? [], true));
                                                        ?>
                                                        <div class="border rounded p-2 mb-2 <?php echo ($item['estado'] ?? 'programado') === 'cancelado' ? 'bg-light' : ''; ?>">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div>
                                                                    <span class="badge badge-<?php echo ($item['tipo'] ?? '') === 'individual' ? 'info' : 'primary'; ?> text-uppercase me-1"><?php echo htmlspecialchars($item['tipo'] ?? ''); ?></span>
                                                                    <span class="badge badge-<?php echo ($item['estado'] ?? 'programado') === 'cancelado' ? 'secondary' : 'success'; ?>"><?php echo htmlspecialchars($item['estado'] ?? 'programado'); ?></span>
                                                                </div>
                                                                <div class="text-end">
                                                                    <small class="d-block">Inicio: <?php echo date('H:i', strtotime($item['inicio'] ?? '')); ?></small>
                                                                    <small class="d-block">Fin: <?php echo date('H:i', strtotime($item['fin'] ?? '')); ?></small>
                                                                </div>
                                                            </div>
                                                            <h6 class="mt-2 mb-1"><?php echo htmlspecialchars($item['titulo'] ?? ''); ?></h6>
                                                            <p class="mb-1 text-muted small">Club: <?php echo htmlspecialchars($club['nombre'] ?? ''); ?> | Lugar: <?php echo htmlspecialchars($item['lugar'] ?? ''); ?></p>
                                                            <?php if ($plan): ?><p class="mb-1 text-muted small">Plan: <?php echo htmlspecialchars($plan['nombre'] ?? ''); ?></p><?php endif; ?>
                                                            <p class="mb-1 text-muted small">Entrenador: <?php echo htmlspecialchars($item['entrenador'] ?? ''); ?></p>
                                                            <p class="mb-2 small">Integrantes:
                                                                <?php foreach ($participantes as $participante): ?>
                                                                    <span class="badge badge-light text-dark me-1 mb-1"><?php echo htmlspecialchars(($participante['nombre'] ?? '') . ' ' . ($participante['apellido'] ?? '')); ?></span>
                                                                <?php endforeach; ?>
                                                            </p>
                                                            <div class="text-end">
                                                                <a class="btn btn-outline-primary btn-xs" href="entrenamientos.php?action=editar&id=<?php echo urlencode($item['id']); ?>">Editar</a>
                                                                <a class="btn btn-outline-warning btn-xs" href="entrenamientos.php?action=cancelar&id=<?php echo urlencode($item['id']); ?>"><?php echo ($item['estado'] ?? 'programado') === 'cancelado' ? 'Reactivar' : 'Cancelar'; ?></a>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>

                                <div class="table-responsive mt-3">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Título</th>
                                                <th>Club</th>
                                                <th>Tipo</th>
                                                <th>Fechas</th>
                                                <th>Participantes</th>
                                                <th>Estado</th>
                                                <th class="text-end">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($entrenamientosFiltrados)): ?>
                                                <tr><td colspan="7" class="text-center text-muted">No hay entrenamientos programados.</td></tr>
                                            <?php else: ?>
                                                <?php foreach ($entrenamientosFiltrados as $item): ?>
                                                    <?php
                                                        $club = find_by_id($clubes, $item['club_id'] ?? '');
                                                        $participantes = array_filter($socios, fn($socio) => in_array($socio['id'] ?? '', $item['integrantes'] ?? [], true));
                                                    ?>
                                                    <tr>
                                                        <td>
                                                            <div class="d-flex flex-column">
                                                                <span class="fw-bold"><?php echo htmlspecialchars($item['titulo'] ?? ''); ?></span>
                                                                <small class="text-muted"><?php echo htmlspecialchars($item['descripcion'] ?? ''); ?></small>
                                                            </div>
                                                        </td>
                                                        <td><?php echo htmlspecialchars($club['nombre'] ?? ''); ?></td>
                                                        <td class="text-capitalize"><?php echo htmlspecialchars($item['tipo'] ?? ''); ?></td>
                                                        <td>
                                                            <small>Inicio: <?php echo date('d/m H:i', strtotime($item['inicio'] ?? '')); ?></small><br>
                                                            <small>Fin: <?php echo date('d/m H:i', strtotime($item['fin'] ?? '')); ?></small>
                                                        </td>
                                                        <td>
                                                            <?php foreach ($participantes as $participante): ?>
                                                                <span class="badge badge-light text-dark me-1 mb-1"><?php echo htmlspecialchars(($participante['nombre'] ?? '') . ' ' . ($participante['apellido'] ?? '')); ?></span>
                                                            <?php endforeach; ?>
                                                        </td>
                                                        <td><span class="badge <?php echo ($item['estado'] ?? 'programado') === 'cancelado' ? 'badge-secondary' : 'badge-success'; ?>"><?php echo htmlspecialchars($item['estado'] ?? 'programado'); ?></span></td>
                                                        <td class="text-end">
                                                            <div class="btn-group">
                                                                <a href="entrenamientos.php?action=editar&id=<?php echo urlencode($item['id']); ?>" class="btn btn-outline-primary btn-xs">Editar</a>
                                                                <a href="entrenamientos.php?action=cancelar&id=<?php echo urlencode($item['id']); ?>" class="btn btn-outline-warning btn-xs"><?php echo ($item['estado'] ?? 'programado') === 'cancelado' ? 'Reactivar' : 'Cancelar'; ?></a>
                                                            </div>
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
