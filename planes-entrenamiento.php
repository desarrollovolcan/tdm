<?php
require_once __DIR__ . '/auth-guard.php';
require_once __DIR__ . '/config/dz.php';
require_once __DIR__ . '/helpers.php';

const CLUB_STORE = 'clubes.json';
const PLAN_STORE = 'planes_entrenamiento.json';
const SOCIO_STORE = 'socios.json';

$clubes = load_json(CLUB_STORE);
$planes = load_json(PLAN_STORE);
$socios = load_json(SOCIO_STORE);

$planEditando = null;
$busqueda = trim($_GET['q'] ?? '');
$filtroClub = trim($_GET['club'] ?? '');

if (($_GET['action'] ?? '') === 'editar' && isset($_GET['id'])) {
    $planEditando = find_by_id($planes, $_GET['id']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = trim($_POST['id'] ?? '');
    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $tipo = trim($_POST['tipo'] ?? '');
    $duracion = trim($_POST['duracion'] ?? '');
    $valor = trim($_POST['valor'] ?? '');
    $moneda = trim($_POST['moneda'] ?? 'CLP');
    $clubId = trim($_POST['club_id'] ?? '');

    if ($nombre === '' || $tipo === '' || $valor === '') {
        $_SESSION['plan_error'] = 'Nombre, tipo y valor del plan son obligatorios.';
        header('Location: planes-entrenamiento.php');
        exit;
    }

    if ($id !== '') {
        $planes = array_map(
            function ($plan) use ($id, $nombre, $descripcion, $tipo, $duracion, $valor, $moneda, $clubId) {
                if (($plan['id'] ?? '') !== $id) {
                    return $plan;
                }

                return array_merge($plan, [
                    'nombre' => $nombre,
                    'descripcion' => $descripcion,
                    'tipo' => $tipo,
                    'duracion' => $duracion,
                    'valor' => $valor,
                    'moneda' => $moneda,
                    'club_id' => $clubId,
                    'updated_at' => date(DATE_ATOM),
                ]);
            },
            $planes
        );
    } else {
        $planes[] = [
            'id' => uniqid('plan_', true),
            'nombre' => $nombre,
            'descripcion' => $descripcion,
            'tipo' => $tipo,
            'duracion' => $duracion,
            'valor' => $valor,
            'moneda' => $moneda,
            'club_id' => $clubId,
            'created_at' => date(DATE_ATOM),
        ];
    }

    save_json(PLAN_STORE, $planes);
    $_SESSION['plan_success'] = $id !== '' ? 'Plan actualizado correctamente.' : 'Plan creado correctamente.';
    header('Location: planes-entrenamiento.php');
    exit;
}

$planesFiltrados = array_values(array_filter(
    $planes,
    function ($plan) use ($busqueda, $filtroClub) {
        $matchBusqueda = $busqueda === ''
            || stripos($plan['nombre'] ?? '', $busqueda) !== false
            || stripos($plan['descripcion'] ?? '', $busqueda) !== false;

        $matchClub = $filtroClub === '' || ($plan['club_id'] ?? '') === $filtroClub;

        return $matchBusqueda && $matchClub;
    }
));

function sociosUsandoPlan(array $socios, string $planId): int
{
    $count = 0;
    foreach ($socios as $socio) {
        $historial = $socio['plan_historial'] ?? [];
        foreach ($historial as $registro) {
            if (($registro['plan_id'] ?? '') === $planId) {
                $count++;
                break;
            }
        }
    }

    return $count;
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
        <title>Planes de entrenamiento | Sistema de Campeonatos</title>
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
                        <li class="breadcrumb-item active"><a href="javascript:void(0)">Planes de entrenamiento</a></li>
                    </ol>
                </div>

                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-header d-sm-flex d-block border-0 pb-0">
                                <div>
                                    <h4 class="fs-20 mb-1">Nuevo plan de entrenamiento</h4>
                                    <span class="fs-14 text-muted">Define precios, alcance y tipo (grupal, individual o mixto).</span>
                                </div>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($_SESSION['plan_success'])): ?>
                                    <div class="alert alert-success alert-dismissible fade show">
                                        <?php echo htmlspecialchars($_SESSION['plan_success']); unset($_SESSION['plan_success']); ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($_SESSION['plan_error'])): ?>
                                    <div class="alert alert-danger alert-dismissible fade show">
                                        <?php echo htmlspecialchars($_SESSION['plan_error']); unset($_SESSION['plan_error']); ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                <?php endif; ?>

                                <form method="post" class="row g-3">
                                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($planEditando['id'] ?? ''); ?>">
                                    <div class="col-md-6">
                                        <label class="form-label">Nombre del plan</label>
                                        <input type="text" name="nombre" class="form-control" required value="<?php echo htmlspecialchars($planEditando['nombre'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Tipo de plan</label>
                                        <?php $tipoActual = $planEditando['tipo'] ?? ''; ?>
                                        <select name="tipo" class="form-control" required>
                                            <option value="">Selecciona una opción</option>
                                            <option value="grupal" <?php echo $tipoActual === 'grupal' ? 'selected' : ''; ?>>Grupal</option>
                                            <option value="individual" <?php echo $tipoActual === 'individual' ? 'selected' : ''; ?>>Individual</option>
                                            <option value="mixto" <?php echo $tipoActual === 'mixto' ? 'selected' : ''; ?>>Mixto</option>
                                        </select>
                                    </div>
                                    <div class="col-md-8">
                                        <label class="form-label">Descripción</label>
                                        <textarea name="descripcion" class="form-control" rows="3" placeholder="En qué consiste el plan..."><?php echo htmlspecialchars($planEditando['descripcion'] ?? ''); ?></textarea>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Duración (semanas o sesiones)</label>
                                        <input type="text" name="duracion" class="form-control" placeholder="Ej: 8 semanas" value="<?php echo htmlspecialchars($planEditando['duracion'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Valor</label>
                                        <div class="input-group">
                                            <select class="form-select" name="moneda">
                                                <?php $monedaActual = $planEditando['moneda'] ?? 'CLP'; ?>
                                                <option value="CLP" <?php echo $monedaActual === 'CLP' ? 'selected' : ''; ?>>CLP</option>
                                                <option value="USD" <?php echo $monedaActual === 'USD' ? 'selected' : ''; ?>>USD</option>
                                                <option value="EUR" <?php echo $monedaActual === 'EUR' ? 'selected' : ''; ?>>EUR</option>
                                            </select>
                                            <input type="number" step="0.01" min="0" name="valor" class="form-control" required value="<?php echo htmlspecialchars($planEditando['valor'] ?? ''); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Club (opcional)</label>
                                        <select name="club_id" class="form-control">
                                            <option value="">Plan genérico</option>
                                            <?php foreach ($clubes as $club): ?>
                                                <option value="<?php echo htmlspecialchars($club['id'] ?? ''); ?>" <?php echo (($planEditando['club_id'] ?? '') === ($club['id'] ?? '')) ? 'selected' : ''; ?>><?php echo htmlspecialchars($club['nombre'] ?? ''); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-12 text-end">
                                        <button type="submit" class="btn btn-primary"><?php echo $planEditando ? 'Actualizar plan' : 'Guardar plan'; ?></button>
                                        <?php if ($planEditando): ?>
                                            <a href="planes-entrenamiento.php" class="btn btn-outline-secondary">Cancelar</a>
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
                                    <h4 class="fs-20 mb-1">Planes disponibles</h4>
                                    <span class="fs-14 text-muted">Filtra por club, nombre o descripción.</span>
                                </div>
                            </div>
                            <div class="card-body">
                                <form class="row g-3 mb-3" method="get">
                                    <div class="col-md-6 col-lg-4">
                                        <label class="form-label">Buscar</label>
                                        <input type="text" name="q" class="form-control" placeholder="Nombre o descripción" value="<?php echo htmlspecialchars($busqueda); ?>">
                                    </div>
                                    <div class="col-md-6 col-lg-3">
                                        <label class="form-label">Club</label>
                                        <select name="club" class="form-control">
                                            <option value="">Todos</option>
                                            <?php foreach ($clubes as $club): ?>
                                                <option value="<?php echo htmlspecialchars($club['id'] ?? ''); ?>" <?php echo $filtroClub === ($club['id'] ?? '') ? 'selected' : ''; ?>><?php echo htmlspecialchars($club['nombre'] ?? ''); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6 col-lg-3 align-self-end">
                                        <button class="btn btn-outline-primary" type="submit">Filtrar</button>
                                        <a class="btn btn-light" href="planes-entrenamiento.php">Limpiar</a>
                                    </div>
                                </form>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Plan</th>
                                                <th>Tipo</th>
                                                <th>Duración</th>
                                                <th>Valor</th>
                                                <th>Uso</th>
                                                <th class="text-end">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($planesFiltrados)): ?>
                                                <tr><td colspan="6" class="text-center text-muted">No hay planes registrados.</td></tr>
                                            <?php else: ?>
                                                <?php foreach ($planesFiltrados as $plan): ?>
                                                    <?php $club = find_by_id($clubes, $plan['club_id'] ?? ''); ?>
                                                    <tr>
                                                        <td>
                                                            <div class="d-flex flex-column">
                                                                <span class="fw-bold"><?php echo htmlspecialchars($plan['nombre'] ?? ''); ?></span>
                                                                <small class="text-muted"><?php echo htmlspecialchars($plan['descripcion'] ?? ''); ?></small>
                                                                <small class="text-muted">Club: <?php echo htmlspecialchars($club['nombre'] ?? 'Genérico'); ?></small>
                                                            </div>
                                                        </td>
                                                        <td class="text-capitalize"><?php echo htmlspecialchars($plan['tipo'] ?? ''); ?></td>
                                                        <td><?php echo htmlspecialchars($plan['duracion'] ?? ''); ?></td>
                                                        <td><?php echo htmlspecialchars(($plan['moneda'] ?? '') . ' ' . ($plan['valor'] ?? '')); ?></td>
                                                        <td><?php echo sociosUsandoPlan($socios, $plan['id']); ?> integrante(s)</td>
                                                        <td class="text-end">
                                                            <a href="planes-entrenamiento.php?action=editar&id=<?php echo urlencode($plan['id']); ?>" class="btn btn-outline-primary btn-xs">Editar</a>
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
