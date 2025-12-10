<?php
require_once __DIR__ . '/auth-guard.php';
require_once __DIR__ . '/config/dz.php';
require_once __DIR__ . '/helpers.php';

const MODULO_ENTRENAMIENTOS_STORE = 'modulo_entrenamientos.json';

$registros = load_json(MODULO_ENTRENAMIENTOS_STORE);

if (($_GET['action'] ?? '') === 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $registros = array_values(array_filter(
        $registros,
        fn($registro) => ($registro['id'] ?? '') !== $id
    ));
    save_json(MODULO_ENTRENAMIENTOS_STORE, $registros);
    flash('entrenamiento_success', 'Sesión eliminada.');
    header('Location: modulo-entrenamientos.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? '');
    $tipo = trim($_POST['tipo'] ?? '');
    $entrenador = trim($_POST['entrenador'] ?? '');
    $fecha = trim($_POST['fecha'] ?? '');
    $intensidad = trim($_POST['intensidad'] ?? '');
    $capacidad = trim($_POST['capacidad'] ?? '');

    if ($titulo === '' || $tipo === '') {
        flash('entrenamiento_error', 'El título y el tipo de entrenamiento son obligatorios.');
        header('Location: modulo-entrenamientos.php');
        exit;
    }

    $registros[] = [
        'id' => uniqid('sesion_', true),
        'titulo' => $titulo,
        'tipo' => $tipo,
        'entrenador' => $entrenador,
        'fecha' => $fecha,
        'intensidad' => $intensidad,
        'capacidad' => $capacidad,
        'created_at' => date(DATE_ATOM),
    ];

    save_json(MODULO_ENTRENAMIENTOS_STORE, $registros);
    flash('entrenamiento_success', 'Entrenamiento planificado.');
    header('Location: modulo-entrenamientos.php');
    exit;
}

$successMessage = flash('entrenamiento_success');
$errorMessage = flash('entrenamiento_error');
?>
<!DOCTYPE html>
<html lang="es">
<head>
        <title>Módulo de entrenamientos | <?php echo $DexignZoneSettings['site_level']['site_title']; ?></title>
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
                                        <li class="breadcrumb-item active">Módulo de entrenamientos</li>
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
                                                        <h4 class="card-title mb-0">Planificar entrenamiento</h4>
                                                </div>
                                                <div class="card-body">
                                                        <form method="post" class="row g-3">
                                                                <div class="col-12">
                                                                        <label class="form-label">Título *</label>
                                                                        <input type="text" name="titulo" class="form-control" required>
                                                                </div>
                                                                <div class="col-md-6">
                                                                        <label class="form-label">Tipo *</label>
                                                                        <select name="tipo" class="form-select" required>
                                                                                <option value="">Seleccionar</option>
                                                                                <option value="Grupal">Grupal</option>
                                                                                <option value="Individual">Individual</option>
                                                                                <option value="Físico">Físico</option>
                                                                                <option value="Técnico">Técnico</option>
                                                                                <option value="Mixto">Mixto</option>
                                                                        </select>
                                                                </div>
                                                                <div class="col-md-6">
                                                                        <label class="form-label">Entrenador responsable</label>
                                                                        <input type="text" name="entrenador" class="form-control" placeholder="Nombre del entrenador">
                                                                </div>
                                                                <div class="col-md-6">
                                                                        <label class="form-label">Fecha y hora</label>
                                                                        <input type="datetime-local" name="fecha" class="form-control">
                                                                </div>
                                                                <div class="col-md-3">
                                                                        <label class="form-label">Intensidad (1-10)</label>
                                                                        <input type="number" min="1" max="10" name="intensidad" class="form-control" placeholder="7">
                                                                </div>
                                                                <div class="col-md-3">
                                                                        <label class="form-label">Capacidad</label>
                                                                        <input type="number" min="1" name="capacidad" class="form-control" placeholder="12">
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
                                                        <h4 class="card-title mb-0">Sesiones planificadas</h4>
                                                        <small class="text-muted">Incluye tipo, intensidad y capacidad</small>
                                                </div>
                                                <div class="card-body p-0">
                                                        <div class="table-responsive">
                                                                <table class="table mb-0">
                                                                        <thead>
                                                                                <tr>
                                                                                        <th>Título</th>
                                                                                        <th>Tipo</th>
                                                                                        <th>Entrenador</th>
                                                                                        <th>Fecha</th>
                                                                                        <th>Cap.</th>
                                                                                        <th></th>
                                                                                </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                                <?php if (empty($registros)): ?>
                                                                                        <tr><td colspan="6" class="text-center py-4">Sin entrenamientos cargados.</td></tr>
                                                                                <?php else: ?>
                                                                                        <?php foreach ($registros as $registro): ?>
                                                                                                <tr>
                                                                                                        <td><?php echo htmlspecialchars($registro['titulo'] ?? ''); ?></td>
                                                                                                        <td><?php echo htmlspecialchars($registro['tipo'] ?? '-'); ?></td>
                                                                                                        <td><?php echo htmlspecialchars($registro['entrenador'] ?? '-'); ?></td>
                                                                                                        <td><?php echo htmlspecialchars($registro['fecha'] ?? '-'); ?></td>
                                                                                                        <td><?php echo htmlspecialchars($registro['capacidad'] ?? '-'); ?></td>
                                                                                                        <td class="text-end">
                                                                                                                <a class="btn btn-sm btn-outline-danger" href="?action=delete&id=<?php echo urlencode($registro['id']); ?>" onclick="return confirm('¿Eliminar entrenamiento?');">Eliminar</a>
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
