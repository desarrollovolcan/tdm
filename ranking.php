<?php
require_once __DIR__ . '/auth-guard.php';
require_once __DIR__ . '/config/dz.php';
require_once __DIR__ . '/helpers.php';

const RANKING_STORE = 'rankings.json';

$campeonatos = load_json('campeonatos.json');
$jugadores = load_json('jugadores.json');
$rankings = load_json(RANKING_STORE);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $campeonato_id = trim($_POST['campeonato_id'] ?? '');
    $jugador_id = trim($_POST['jugador_id'] ?? '');
    $categoria = trim($_POST['categoria'] ?? '');
    $posicion = (int) ($_POST['posicion'] ?? 0);
    $puntos = (int) ($_POST['puntos'] ?? 0);

    if ($campeonato_id === '' || $jugador_id === '' || $posicion <= 0) {
        flash('ranking_error', 'Campeonato, jugador y posición son obligatorios.');
        header('Location: ranking.php');
        exit;
    }

    $rankings[] = [
        'id' => uniqid('rk_', true),
        'campeonato_id' => $campeonato_id,
        'jugador_id' => $jugador_id,
        'categoria' => $categoria,
        'posicion' => $posicion,
        'puntos' => $puntos,
        'fecha_actualizacion' => date('Y-m-d'),
    ];

    save_json(RANKING_STORE, $rankings);
    flash('ranking_success', 'Ranking actualizado.');
    header('Location: ranking.php');
    exit;
}

if (($_GET['action'] ?? '') === 'eliminar' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $rankings = array_values(array_filter($rankings, fn($r) => ($r['id'] ?? '') !== $id));
    save_json(RANKING_STORE, $rankings);
    flash('ranking_success', 'Registro eliminado del ranking.');
    header('Location: ranking.php');
    exit;
}

usort($rankings, fn($a, $b) => ($a['posicion'] ?? 0) <=> ($b['posicion'] ?? 0));

$successMessage = flash('ranking_success');
$errorMessage = flash('ranking_error');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Ranking | Sistema de Campeonatos</title>
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
                    <li class="breadcrumb-item active"><a href="javascript:void(0)">Estadísticas</a></li>
                </ol>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header d-sm-flex d-block border-0 pb-0">
                            <div>
                                <h4 class="fs-20 mb-1">Ranking general</h4>
                                <span class="fs-14 text-muted">Registra posiciones, puntos y categoría.</span>
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
                                    <label class="form-label">Jugador</label>
                                    <select name="jugador_id" class="form-control" required>
                                        <option value="">Selecciona</option>
                                        <?php foreach ($jugadores as $jug): ?>
                                            <option value="<?php echo htmlspecialchars($jug['id'] ?? ''); ?>"><?php echo htmlspecialchars(($jug['nombre'] ?? '') . ' ' . ($jug['apellido'] ?? '')); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Categoría</label>
                                    <input type="text" name="categoria" class="form-control" placeholder="Adulto, juvenil">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Posición</label>
                                    <input type="number" name="posicion" class="form-control" min="1" required>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Puntos</label>
                                    <input type="number" name="puntos" class="form-control" min="0" value="0">
                                </div>
                                <div class="col-12 text-end">
                                    <button type="submit" class="btn btn-primary">Actualizar ranking</button>
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
                                <h4 class="fs-20 mb-1">Tabla de posiciones</h4>
                                <span class="fs-14 text-muted">Ordenada por puesto ascendente.</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Posición</th>
                                            <th>Jugador</th>
                                            <th>Campeonato</th>
                                            <th>Categoría</th>
                                            <th>Puntos</th>
                                            <th>Actualizado</th>
                                            <th class="text-end">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($rankings)): ?>
                                            <tr><td colspan="7" class="text-center text-muted">No hay datos de ranking.</td></tr>
                                        <?php else: ?>
                                            <?php foreach ($rankings as $rk): ?>
                                                <?php $camp = find_by_id($campeonatos, $rk['campeonato_id']); ?>
                                                <?php $jug = find_by_id($jugadores, $rk['jugador_id']); ?>
                                                <tr>
                                                    <td><strong>#<?php echo htmlspecialchars((string) ($rk['posicion'] ?? 0)); ?></strong></td>
                                                    <td><?php echo htmlspecialchars(($jug['nombre'] ?? '') . ' ' . ($jug['apellido'] ?? '')); ?></td>
                                                    <td><?php echo htmlspecialchars($camp['nombre'] ?? 'Campeonato'); ?></td>
                                                    <td><?php echo htmlspecialchars($rk['categoria'] ?: 'General'); ?></td>
                                                    <td><?php echo htmlspecialchars((string) ($rk['puntos'] ?? 0)); ?></td>
                                                    <td><?php echo htmlspecialchars($rk['fecha_actualizacion'] ?? ''); ?></td>
                                                    <td class="text-end">
                                                        <a class="btn btn-outline-danger btn-xs" href="ranking.php?action=eliminar&id=<?php echo urlencode($rk['id']); ?>" onclick="return confirm('¿Eliminar del ranking?');">Eliminar</a>
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
