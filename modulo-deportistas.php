<?php
require_once __DIR__ . '/auth-guard.php';
require_once __DIR__ . '/config/dz.php';
require_once __DIR__ . '/helpers.php';

const MODULO_DEPORTISTAS_STORE = 'modulo_deportistas.json';

$registros = load_json(MODULO_DEPORTISTAS_STORE);

if (($_GET['action'] ?? '') === 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $registros = array_values(array_filter(
        $registros,
        fn($registro) => ($registro['id'] ?? '') !== $id
    ));
    save_json(MODULO_DEPORTISTAS_STORE, $registros);
    flash('deportista_success', 'Ficha eliminada correctamente.');
    header('Location: modulo-deportistas.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $categoria = trim($_POST['categoria'] ?? '');
    $mano = trim($_POST['mano'] ?? '');
    $estilo = trim($_POST['estilo'] ?? '');
    $estado = trim($_POST['estado'] ?? '');
    $entrenador = trim($_POST['entrenador'] ?? '');
    $plan = trim($_POST['plan'] ?? '');

    if ($nombre === '' || $categoria === '') {
        flash('deportista_error', 'Nombre y categoría son obligatorios.');
        header('Location: modulo-deportistas.php');
        exit;
    }

    $registros[] = [
        'id' => uniqid('dep_', true),
        'nombre' => $nombre,
        'categoria' => $categoria,
        'mano' => $mano,
        'estilo' => $estilo,
        'estado' => $estado ?: 'Activo',
        'entrenador' => $entrenador,
        'plan' => $plan,
        'created_at' => date(DATE_ATOM),
    ];

    save_json(MODULO_DEPORTISTAS_STORE, $registros);
    flash('deportista_success', 'Ficha de deportista guardada.');
    header('Location: modulo-deportistas.php');
    exit;
}

$successMessage = flash('deportista_success');
$errorMessage = flash('deportista_error');
?>
<!DOCTYPE html>
<html lang="es">
<head>
        <title>Módulo de deportistas | <?php echo $DexignZoneSettings['site_level']['site_title']; ?></title>
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
                                        <li class="breadcrumb-item active">Módulo de deportistas</li>
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
                                                        <h4 class="card-title mb-0">Registrar deportista</h4>
                                                </div>
                                                <div class="card-body">
                                                        <form method="post" class="row g-3">
                                                                <div class="col-12">
                                                                        <label class="form-label">Nombre completo *</label>
                                                                        <input type="text" name="nombre" class="form-control" required>
                                                                </div>
                                                                <div class="col-md-6">
                                                                        <label class="form-label">Categoría *</label>
                                                                        <input type="text" name="categoria" class="form-control" placeholder="Sub-13, Adulto" required>
                                                                </div>
                                                                <div class="col-md-6">
                                                                        <label class="form-label">Estado</label>
                                                                        <select name="estado" class="form-select">
                                                                                <option value="Activo">Activo</option>
                                                                                <option value="Inactivo">Inactivo</option>
                                                                                <option value="En prueba">En prueba</option>
                                                                        </select>
                                                                </div>
                                                                <div class="col-md-6">
                                                                        <label class="form-label">Mano hábil</label>
                                                                        <select name="mano" class="form-select">
                                                                                <option value="">Seleccionar</option>
                                                                                <option value="Diestro">Diestro</option>
                                                                                <option value="Zurdo">Zurdo</option>
                                                                        </select>
                                                                </div>
                                                                <div class="col-md-6">
                                                                        <label class="form-label">Estilo de juego</label>
                                                                        <input type="text" name="estilo" class="form-control" placeholder="Ofensivo, defensivo">
                                                                </div>
                                                                <div class="col-md-6">
                                                                        <label class="form-label">Entrenador principal</label>
                                                                        <input type="text" name="entrenador" class="form-control" placeholder="Nombre del entrenador">
                                                                </div>
                                                                <div class="col-md-6">
                                                                        <label class="form-label">Plan contratado</label>
                                                                        <input type="text" name="plan" class="form-control" placeholder="Mensual, individual, etc.">
                                                                </div>
                                                                <div class="col-12">
                                                                        <button type="submit" class="btn btn-primary">Guardar deportista</button>
                                                                </div>
                                                        </form>
                                                </div>
                                        </div>
                                </div>
                                <div class="col-xl-7">
                                        <div class="card">
                                                <div class="card-header">
                                                        <h4 class="card-title mb-0">Fichas registradas</h4>
                                                </div>
                                                <div class="card-body p-0">
                                                        <div class="table-responsive">
                                                                <table class="table mb-0">
                                                                        <thead>
                                                                                <tr>
                                                                                        <th>Nombre</th>
                                                                                        <th>Categoría</th>
                                                                                        <th>Entrenador</th>
                                                                                        <th>Plan</th>
                                                                                        <th>Estado</th>
                                                                                        <th></th>
                                                                                </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                                <?php if (empty($registros)): ?>
                                                                                        <tr><td colspan="6" class="text-center py-4">Sin deportistas registrados.</td></tr>
                                                                                <?php else: ?>
                                                                                        <?php foreach ($registros as $registro): ?>
                                                                                                <tr>
                                                                                                        <td><?php echo htmlspecialchars($registro['nombre'] ?? ''); ?></td>
                                                                                                        <td><?php echo htmlspecialchars($registro['categoria'] ?? ''); ?></td>
                                                                                                        <td><?php echo htmlspecialchars($registro['entrenador'] ?? '-'); ?></td>
                                                                                                        <td><?php echo htmlspecialchars($registro['plan'] ?? '-'); ?></td>
                                                                                                        <td><span class="badge bg-<?php echo (($registro['estado'] ?? '') === 'Activo') ? 'success' : 'secondary'; ?>">
                                                                                                                <?php echo htmlspecialchars($registro['estado'] ?? ''); ?></span></td>
                                                                                                        <td class="text-end">
                                                                                                                <a class="btn btn-sm btn-outline-danger" href="?action=delete&id=<?php echo urlencode($registro['id']); ?>" onclick="return confirm('¿Eliminar la ficha?');">Eliminar</a>
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
