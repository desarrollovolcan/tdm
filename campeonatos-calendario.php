<?php
require_once __DIR__ . '/auth-guard.php';
require_once __DIR__ . '/config/dz.php';
require_once __DIR__ . '/helpers.php';

$campeonatos = load_json('campeonatos.json');
$sedes = load_json('sedes.json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $categoria = trim($_POST['categoria'] ?? '');
    $sede = trim($_POST['sede'] ?? '');
    $fecha_inicio = trim($_POST['fecha_inicio'] ?? '');
    $fecha_fin = trim($_POST['fecha_fin'] ?? '');

    if ($nombre === '' || $fecha_inicio === '') {
        flash('camp_error', 'El nombre y la fecha de inicio son obligatorios.');
        header('Location: campeonatos-calendario.php');
        exit;
    }

    $campeonatos[] = [
        'id' => uniqid('camp_', true),
        'nombre' => $nombre,
        'categoria' => $categoria,
        'sede' => $sede,
        'fecha_inicio' => $fecha_inicio,
        'fecha_fin' => $fecha_fin,
        'created_at' => date(DATE_ATOM),
    ];

    save_json('campeonatos.json', $campeonatos);
    flash('camp_success', 'Campeonato agregado al calendario.');
    header('Location: campeonatos-calendario.php');
    exit;
}

if (($_GET['action'] ?? '') === 'eliminar' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $campeonatos = array_values(array_filter($campeonatos, fn($c) => ($c['id'] ?? '') !== $id));
    save_json('campeonatos.json', $campeonatos);
    flash('camp_success', 'Campeonato eliminado.');
    header('Location: campeonatos-calendario.php');
    exit;
}

$successMessage = flash('camp_success');
$errorMessage = flash('camp_error');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Calendario | Sistema de Campeonatos</title>
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
                                <h4 class="fs-20 mb-1">Calendario de campeonatos</h4>
                                <span class="fs-14 text-muted">Registra fechas, sedes y categorías para cada torneo.</span>
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
                                <div class="col-md-6">
                                    <label class="form-label">Nombre del campeonato</label>
                                    <input type="text" name="nombre" class="form-control" placeholder="Abierto nacional" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Categoría</label>
                                    <select name="categoria" class="form-control">
                                        <option value="">Libre</option>
                                        <option>Juvenil</option>
                                        <option>Adulto</option>
                                        <option>Senior</option>
                                        <option>Mixto</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Sede</label>
                                    <select name="sede" class="form-control">
                                        <option value="">Por definir</option>
                                        <?php foreach ($sedes as $sedeItem): ?>
                                            <option value="<?php echo htmlspecialchars($sedeItem['nombre'] ?? ''); ?>"><?php echo htmlspecialchars($sedeItem['nombre'] ?? ''); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Fecha de inicio</label>
                                    <input type="date" name="fecha_inicio" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Fecha de cierre</label>
                                    <input type="date" name="fecha_fin" class="form-control">
                                </div>
                                <div class="col-12 text-end">
                                    <button type="submit" class="btn btn-primary">Agregar al calendario</button>
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
                                <h4 class="fs-20 mb-1">Programación vigente</h4>
                                <span class="fs-14 text-muted">Listado cronológico de campeonatos creados.</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Nombre</th>
                                            <th>Categoría</th>
                                            <th>Sede</th>
                                            <th>Inicio</th>
                                            <th>Fin</th>
                                            <th class="text-end">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php if (empty($campeonatos)): ?>
                                        <tr><td colspan="6" class="text-center text-muted">Aún no hay campeonatos cargados.</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($campeonatos as $camp): ?>
                                            <tr>
                                                <td class="fw-bold"><?php echo htmlspecialchars($camp['nombre'] ?? ''); ?></td>
                                                <td><?php echo htmlspecialchars($camp['categoria'] ?: 'Libre'); ?></td>
                                                <td><?php echo htmlspecialchars($camp['sede'] ?: 'Por confirmar'); ?></td>
                                                <td><?php echo htmlspecialchars($camp['fecha_inicio'] ?: ''); ?></td>
                                                <td><?php echo htmlspecialchars($camp['fecha_fin'] ?: ''); ?></td>
                                                <td class="text-end">
                                                    <a class="btn btn-outline-danger btn-xs" href="campeonatos-calendario.php?action=eliminar&id=<?php echo urlencode($camp['id']); ?>" onclick="return confirm('¿Eliminar campeonato?');">Eliminar</a>
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
