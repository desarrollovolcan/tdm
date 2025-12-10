<?php
require_once __DIR__ . '/auth-guard.php';
require_once __DIR__ . '/config/dz.php';
require_once __DIR__ . '/helpers.php';

const MODULO_COMPETENCIAS_STORE = 'modulo_competencias.json';

$registros = load_json(MODULO_COMPETENCIAS_STORE);

if (($_GET['action'] ?? '') === 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $registros = array_values(array_filter(
        $registros,
        fn($registro) => ($registro['id'] ?? '') !== $id
    ));
    save_json(MODULO_COMPETENCIAS_STORE, $registros);
    flash('competencia_success', 'Competencia eliminada.');
    header('Location: modulo-competencias.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $categoria = trim($_POST['categoria'] ?? '');
    $formato = trim($_POST['formato'] ?? '');
    $fecha = trim($_POST['fecha'] ?? '');
    $sede = trim($_POST['sede'] ?? '');
    $estado = trim($_POST['estado'] ?? '');

    if ($nombre === '') {
        flash('competencia_error', 'El nombre de la competencia es obligatorio.');
        header('Location: modulo-competencias.php');
        exit;
    }

    $registros[] = [
        'id' => uniqid('comp_', true),
        'nombre' => $nombre,
        'categoria' => $categoria,
        'formato' => $formato,
        'fecha' => $fecha,
        'sede' => $sede,
        'estado' => $estado ?: 'Planificada',
        'created_at' => date(DATE_ATOM),
    ];

    save_json(MODULO_COMPETENCIAS_STORE, $registros);
    flash('competencia_success', 'Competencia guardada correctamente.');
    header('Location: modulo-competencias.php');
    exit;
}

$successMessage = flash('competencia_success');
$errorMessage = flash('competencia_error');
?>
<!DOCTYPE html>
<html lang="es">
<head>
        <title>Módulo de competencias | <?php echo $DexignZoneSettings['site_level']['site_title']; ?></title>
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
                                        <li class="breadcrumb-item active">Módulo de competencias</li>
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
                                                        <h4 class="card-title mb-0">Crear competencia</h4>
                                                </div>
                                                <div class="card-body">
                                                        <form method="post" class="row g-3">
                                                                <div class="col-12">
                                                                        <label class="form-label">Nombre *</label>
                                                                        <input type="text" name="nombre" class="form-control" required>
                                                                </div>
                                                                <div class="col-md-6">
                                                                        <label class="form-label">Categoría</label>
                                                                        <input type="text" name="categoria" class="form-control" placeholder="Sub-15, Adultos">
                                                                </div>
                                                                <div class="col-md-6">
                                                                        <label class="form-label">Formato</label>
                                                                        <select name="formato" class="form-select">
                                                                                <option value="Liga">Liga</option>
                                                                                <option value="Eliminación">Eliminación</option>
                                                                                <option value="Grupos + Eliminación">Grupos + Eliminación</option>
                                                                                <option value="Amistoso">Amistoso</option>
                                                                        </select>
                                                                </div>
                                                                <div class="col-md-6">
                                                                        <label class="form-label">Fecha inicio</label>
                                                                        <input type="date" name="fecha" class="form-control">
                                                                </div>
                                                                <div class="col-md-6">
                                                                        <label class="form-label">Sede</label>
                                                                        <input type="text" name="sede" class="form-control" placeholder="Sede o ciudad">
                                                                </div>
                                                                <div class="col-md-6">
                                                                        <label class="form-label">Estado</label>
                                                                        <select name="estado" class="form-select">
                                                                                <option value="Planificada">Planificada</option>
                                                                                <option value="En curso">En curso</option>
                                                                                <option value="Finalizada">Finalizada</option>
                                                                        </select>
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
                                                        <h4 class="card-title mb-0">Competencias activas</h4>
                                                        <small class="text-muted">Torneos internos, ligas y amistosos</small>
                                                </div>
                                                <div class="card-body p-0">
                                                        <div class="table-responsive">
                                                                <table class="table mb-0">
                                                                        <thead>
                                                                                <tr>
                                                                                        <th>Nombre</th>
                                                                                        <th>Categoría</th>
                                                                                        <th>Formato</th>
                                                                                        <th>Fecha</th>
                                                                                        <th>Estado</th>
                                                                                        <th></th>
                                                                                </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                                <?php if (empty($registros)): ?>
                                                                                        <tr><td colspan="6" class="text-center py-4">Sin competencias registradas.</td></tr>
                                                                                <?php else: ?>
                                                                                        <?php foreach ($registros as $registro): ?>
                                                                                                <tr>
                                                                                                        <td><?php echo htmlspecialchars($registro['nombre'] ?? ''); ?></td>
                                                                                                        <td><?php echo htmlspecialchars($registro['categoria'] ?? '-'); ?></td>
                                                                                                        <td><?php echo htmlspecialchars($registro['formato'] ?? '-'); ?></td>
                                                                                                        <td><?php echo htmlspecialchars($registro['fecha'] ?? '-'); ?></td>
                                                                                                        <td><span class="badge bg-info text-dark"><?php echo htmlspecialchars($registro['estado'] ?? ''); ?></span></td>
                                                                                                        <td class="text-end">
                                                                                                                <a class="btn btn-sm btn-outline-danger" href="?action=delete&id=<?php echo urlencode($registro['id']); ?>" onclick="return confirm('¿Eliminar competencia?');">Eliminar</a>
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
