<?php
require_once __DIR__ . '/auth-guard.php';
require_once __DIR__ . '/config/dz.php';
require_once __DIR__ . '/helpers.php';

const MODULO_ENTRENADORES_STORE = 'modulo_entrenadores.json';

$registros = load_json(MODULO_ENTRENADORES_STORE);

if (($_GET['action'] ?? '') === 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $registros = array_values(array_filter(
        $registros,
        fn($registro) => ($registro['id'] ?? '') !== $id
    ));
    save_json(MODULO_ENTRENADORES_STORE, $registros);
    flash('entrenador_success', 'Registro de entrenador eliminado.');
    header('Location: modulo-entrenadores.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $especialidad = trim($_POST['especialidad'] ?? '');
    $certificacion = trim($_POST['certificacion'] ?? '');
    $disponibilidad = trim($_POST['disponibilidad'] ?? '');
    $grupos = trim($_POST['grupos'] ?? '');

    if ($nombre === '') {
        flash('entrenador_error', 'El nombre del entrenador es obligatorio.');
        header('Location: modulo-entrenadores.php');
        exit;
    }

    $registros[] = [
        'id' => uniqid('coach_', true),
        'nombre' => $nombre,
        'especialidad' => $especialidad,
        'certificacion' => $certificacion,
        'disponibilidad' => $disponibilidad,
        'grupos' => $grupos,
        'created_at' => date(DATE_ATOM),
    ];

    save_json(MODULO_ENTRENADORES_STORE, $registros);
    flash('entrenador_success', 'Entrenador guardado correctamente.');
    header('Location: modulo-entrenadores.php');
    exit;
}

$successMessage = flash('entrenador_success');
$errorMessage = flash('entrenador_error');

function collect_unique_values(array $source, string $key): array
{
    $values = array_map(fn($registro) => trim($registro[$key] ?? ''), $source);
    $values = array_values(array_filter(array_unique($values), fn($value) => $value !== ''));
    sort($values, SORT_NATURAL | SORT_FLAG_CASE);

    return $values;
}

$especialidadesGuardadas = collect_unique_values($registros, 'especialidad');
$certificacionesGuardadas = collect_unique_values($registros, 'certificacion');
$disponibilidadesGuardadas = collect_unique_values($registros, 'disponibilidad');
$gruposGuardados = collect_unique_values($registros, 'grupos');
?>
<!DOCTYPE html>
<html lang="es">
<head>
        <title>Módulo de entrenadores | <?php echo $DexignZoneSettings['site_level']['site_title']; ?></title>
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
                                        <li class="breadcrumb-item active">Módulo de entrenadores</li>
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
                                                        <h4 class="card-title mb-0">Registrar entrenador</h4>
                                                        <p class="mb-0 text-muted">Usa los valores existentes para evitar escribir varias veces la misma información.</p>
                                                </div>
                                                <div class="card-body">
                                                        <form method="post" class="row g-3">
                                                                <div class="col-12">
                                                                        <label class="form-label">Nombre completo *</label>
                                                                        <input type="text" name="nombre" class="form-control" required>
                                                                </div>
                                                                <div class="col-md-6">
                                                                        <label class="form-label">Especialidad</label>
                                                                        <input type="text" name="especialidad" class="form-control" placeholder="Niños, alto rendimiento, físico" list="especialidad-sugerida">
                                                                        <datalist id="especialidad-sugerida">
                                                                                <?php foreach ($especialidadesGuardadas as $valor): ?>
                                                                                        <option value="<?php echo htmlspecialchars($valor); ?>"></option>
                                                                                <?php endforeach; ?>
                                                                        </datalist>
                                                                </div>
                                                                <div class="col-md-6">
                                                                        <label class="form-label">Certificación</label>
                                                                        <input type="text" name="certificacion" class="form-control" placeholder="ITTF nivel 1, etc." list="certificacion-sugerida">
                                                                        <datalist id="certificacion-sugerida">
                                                                                <?php foreach ($certificacionesGuardadas as $valor): ?>
                                                                                        <option value="<?php echo htmlspecialchars($valor); ?>"></option>
                                                                                <?php endforeach; ?>
                                                                        </datalist>
                                                                </div>
                                                                <div class="col-md-6">
                                                                        <label class="form-label">Disponibilidad</label>
                                                                        <input type="text" name="disponibilidad" class="form-control" placeholder="Lunes a viernes, 9-18h" list="disponibilidad-sugerida">
                                                                        <datalist id="disponibilidad-sugerida">
                                                                                <?php foreach ($disponibilidadesGuardadas as $valor): ?>
                                                                                        <option value="<?php echo htmlspecialchars($valor); ?>"></option>
                                                                                <?php endforeach; ?>
                                                                        </datalist>
                                                                </div>
                                                                <div class="col-md-6">
                                                                        <label class="form-label">Grupos asignados</label>
                                                                        <input type="text" name="grupos" class="form-control" placeholder="Sub-13 A, Adultos B" list="grupos-sugeridos">
                                                                        <datalist id="grupos-sugeridos">
                                                                                <?php foreach ($gruposGuardados as $valor): ?>
                                                                                        <option value="<?php echo htmlspecialchars($valor); ?>"></option>
                                                                                <?php endforeach; ?>
                                                                        </datalist>
                                                                </div>
                                                                <div class="col-12">
                                                                        <button type="submit" class="btn btn-primary">Guardar entrenador</button>
                                                                </div>
                                                        </form>
                                                </div>
                                        </div>
                                </div>
                                <div class="col-xl-7">
                                        <div class="card">
                                                <div class="card-header">
                                                        <h4 class="card-title mb-0">Staff técnico</h4>
                                                </div>
                                                <div class="card-body p-0">
                                                        <div class="table-responsive">
                                                                <table class="table mb-0">
                                                                        <thead>
                                                                                <tr>
                                                                                        <th>Nombre</th>
                                                                                        <th>Especialidad</th>
                                                                                        <th>Certificación</th>
                                                                                        <th>Disponibilidad</th>
                                                                                        <th></th>
                                                                                </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                                <?php if (empty($registros)): ?>
                                                                                        <tr><td colspan="5" class="text-center py-4">Sin entrenadores cargados.</td></tr>
                                                                                <?php else: ?>
                                                                                        <?php foreach ($registros as $registro): ?>
                                                                                                <tr>
                                                                                                        <td><?php echo htmlspecialchars($registro['nombre'] ?? ''); ?></td>
                                                                                                        <td><?php echo htmlspecialchars($registro['especialidad'] ?? '-'); ?></td>
                                                                                                        <td><?php echo htmlspecialchars($registro['certificacion'] ?? '-'); ?></td>
                                                                                                        <td><?php echo htmlspecialchars($registro['disponibilidad'] ?? '-'); ?></td>
                                                                                                        <td class="text-end">
                                                                                                                <a class="btn btn-sm btn-outline-danger" href="?action=delete&id=<?php echo urlencode($registro['id']); ?>" onclick="return confirm('¿Eliminar este entrenador?');">Eliminar</a>
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
