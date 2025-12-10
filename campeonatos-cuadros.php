<?php
require_once __DIR__ . '/auth-guard.php';
require_once __DIR__ . '/config/dz.php';
require_once __DIR__ . '/helpers.php';

const CUADROS_STORE = 'cuadros.json';

$campeonatos = load_json('campeonatos.json');
$cuadros = load_json(CUADROS_STORE);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $campeonato_id = trim($_POST['campeonato_id'] ?? '');
    $nombre = trim($_POST['nombre'] ?? '');
    $tipo = trim($_POST['tipo'] ?? 'Eliminatoria');
    $detalle = trim($_POST['detalle'] ?? '');
    $fase_inicial = trim($_POST['fase_inicial'] ?? 'Octavos');

    if ($campeonato_id === '' || $nombre === '') {
        flash('cuadros_error', 'El nombre del cuadro y el campeonato son obligatorios.');
        header('Location: campeonatos-cuadros.php');
        exit;
    }

    $cuadros[] = [
        'id' => uniqid('cua_', true),
        'campeonato_id' => $campeonato_id,
        'nombre' => $nombre,
        'tipo' => $tipo,
        'fase_inicial' => $fase_inicial,
        'detalle' => $detalle,
        'created_at' => date(DATE_ATOM),
    ];

    save_json(CUADROS_STORE, $cuadros);
    flash('cuadros_success', 'Cuadro creado y listo para definir llaves.');
    header('Location: campeonatos-cuadros.php');
    exit;
}

if (($_GET['action'] ?? '') === 'eliminar' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $cuadros = array_values(array_filter($cuadros, fn($c) => ($c['id'] ?? '') !== $id));
    save_json(CUADROS_STORE, $cuadros);
    flash('cuadros_success', 'Cuadro eliminado.');
    header('Location: campeonatos-cuadros.php');
    exit;
}

$successMessage = flash('cuadros_success');
$errorMessage = flash('cuadros_error');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Cuadros y llaves | Sistema de Campeonatos</title>
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
                                <h4 class="fs-20 mb-1">Cuadros y llaves</h4>
                                <span class="fs-14 text-muted">Define llaves eliminatorias, fases de grupos y cruces.</span>
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
                                <div class="col-md-4">
                                    <label class="form-label">Nombre del cuadro</label>
                                    <input type="text" name="nombre" class="form-control" placeholder="Ej: Eliminatoria principal" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Tipo</label>
                                    <select name="tipo" class="form-control">
                                        <option>Eliminatoria</option>
                                        <option>Fase de grupos</option>
                                        <option>Mixto</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Fase inicial</label>
                                    <select name="fase_inicial" class="form-control">
                                        <option>Final</option>
                                        <option>Semifinales</option>
                                        <option>Cuartos</option>
                                        <option>Octavos</option>
                                        <option>Grupos</option>
                                    </select>
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label">Detalle / reglas</label>
                                    <textarea name="detalle" class="form-control" rows="2" placeholder="Notas de sembrado, clasificación o cruces"></textarea>
                                </div>
                                <div class="col-12 text-end">
                                    <button type="submit" class="btn btn-primary">Guardar cuadro</button>
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
                                <h4 class="fs-20 mb-1">Listado de cuadros</h4>
                                <span class="fs-14 text-muted">Resumen de llaves configuradas por campeonato.</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Campeonato</th>
                                            <th>Nombre</th>
                                            <th>Tipo</th>
                                            <th>Fase inicial</th>
                                            <th>Detalle</th>
                                            <th class="text-end">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($cuadros)): ?>
                                            <tr><td colspan="6" class="text-center text-muted">Aún no hay cuadros configurados.</td></tr>
                                        <?php else: ?>
                                            <?php foreach ($cuadros as $cua): ?>
                                                <?php $camp = find_by_id($campeonatos, $cua['campeonato_id']); ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($camp['nombre'] ?? 'Campeonato'); ?></td>
                                                    <td><?php echo htmlspecialchars($cua['nombre']); ?></td>
                                                    <td><span class="badge badge-primary light"><?php echo htmlspecialchars($cua['tipo']); ?></span></td>
                                                    <td><?php echo htmlspecialchars($cua['fase_inicial']); ?></td>
                                                    <td><?php echo htmlspecialchars($cua['detalle'] ?: 'Sin notas'); ?></td>
                                                    <td class="text-end">
                                                        <a class="btn btn-outline-danger btn-xs" href="campeonatos-cuadros.php?action=eliminar&id=<?php echo urlencode($cua['id']); ?>" onclick="return confirm('¿Eliminar cuadro y llaves asociadas?');">Eliminar</a>
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
