<?php
require_once __DIR__ . '/auth-guard.php';
require_once __DIR__ . '/config/dz.php';
require_once __DIR__ . '/helpers.php';

const ARBITROS_STORE = 'arbitros.json';

$arbitros = load_json(ARBITROS_STORE);
$sedeStore = load_json('sedes.json');
$arbitroEditando = null;

if (($_GET['action'] ?? '') === 'editar' && isset($_GET['id'])) {
    $arbitroEditando = find_by_id($arbitros, $_GET['id']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = trim($_POST['id'] ?? '');
    $nombre = trim($_POST['nombre'] ?? '');
    $rol = trim($_POST['rol'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $mesa = trim($_POST['mesa'] ?? '');
    $disponibilidad = trim($_POST['disponibilidad'] ?? '');
    $licencia = trim($_POST['licencia'] ?? '');

    if ($nombre === '' || $rol === '') {
        $_SESSION['arbitro_error'] = 'El nombre y el rol son obligatorios.';
    } else {
        if ($id !== '') {
            $arbitros = array_map(function ($item) use ($id, $nombre, $rol, $telefono, $mesa, $disponibilidad, $licencia) {
                if (($item['id'] ?? '') !== $id) {
                    return $item;
                }

                return array_merge($item, [
                    'nombre' => $nombre,
                    'rol' => $rol,
                    'telefono' => $telefono,
                    'mesa' => $mesa,
                    'disponibilidad' => $disponibilidad,
                    'licencia' => $licencia,
                    'updated_at' => date(DATE_ATOM),
                ]);
            }, $arbitros);
        } else {
            $arbitros[] = [
                'id' => uniqid('arb_', true),
                'nombre' => $nombre,
                'rol' => $rol,
                'telefono' => $telefono,
                'mesa' => $mesa,
                'disponibilidad' => $disponibilidad,
                'licencia' => $licencia,
                'created_at' => date(DATE_ATOM),
            ];
        }

        if (save_json(ARBITROS_STORE, $arbitros)) {
            $_SESSION['arbitro_success'] = $id !== '' ? 'Registro actualizado correctamente.' : 'Registro guardado correctamente.';
        } else {
            $_SESSION['arbitro_error'] = 'No fue posible guardar el registro. Verifica la conexión a la base de datos.';
        }
    }

    header('Location: arbitros.php');
    exit;
}

if (($_GET['action'] ?? '') === 'eliminar' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $arbitros = array_values(array_filter($arbitros, fn($item) => ($item['id'] ?? '') !== $id));
    if (save_json(ARBITROS_STORE, $arbitros)) {
        $_SESSION['arbitro_success'] = 'Registro eliminado correctamente.';
    } else {
        $_SESSION['arbitro_error'] = 'No se pudo eliminar el registro en el almacenamiento.';
    }
    header('Location: arbitros.php');
    exit;
}

$successMessage = flash('arbitro_success');
$errorMessage = flash('arbitro_error');
?>
<!DOCTYPE html>
<html lang="es">

<head>
        <title>Árbitros y mesas</title>
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
                                                <li class="breadcrumb-item active"><a href="javascript:void(0)">Administración</a></li>
                                        </ol>
                </div>
                                <div class="row">
                                        <div class="col-xl-4">
                                                <div class="card">
                                                        <div class="card-header d-sm-flex d-block border-0 pb-0">
                                                                <div>
                                                                        <h4 class="fs-20 mb-1">Registrar árbitro/anotador</h4>
                                                                        <span class="fs-14 text-muted">Asigna datos de contacto y mesa</span>
                                                                </div>
                                                        </div>
                                                        <div class="card-body">
                                                            <?php if ($successMessage): ?>
                                                                <div class="alert alert-success alert-dismissible fade show">
                                                                    <?php echo htmlspecialchars($successMessage); ?>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                                                </div>
                                                            <?php endif; ?>
                                                            <?php if ($errorMessage): ?>
                                                                <div class="alert alert-danger alert-dismissible fade show">
                                                                    <?php echo htmlspecialchars($errorMessage); ?>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                                                </div>
                                                            <?php endif; ?>

                                                            <form method="post" class="row g-3">
                                                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($arbitroEditando['id'] ?? ''); ?>">
                                                                <div class="col-12">
                                                                    <label class="form-label">Nombre completo</label>
                                                                    <input type="text" name="nombre" class="form-control" required value="<?php echo htmlspecialchars($arbitroEditando['nombre'] ?? ''); ?>">
                                                                </div>
                                                                <div class="col-12">
                                                                    <label class="form-label">Rol</label>
                                                                    <select name="rol" class="form-control" required>
                                                                        <option value="">Selecciona</option>
                                                                        <?php
                                                                        $roles = ['Árbitro', 'Anotador', 'Coordinador'];
                                                                        foreach ($roles as $rolOpcion):
                                                                            $selected = ($arbitroEditando['rol'] ?? '') === $rolOpcion ? 'selected' : '';
                                                                            echo "<option value=\"" . htmlspecialchars($rolOpcion) . "\" $selected>" . htmlspecialchars($rolOpcion) . "</option>";
                                                                        endforeach;
                                                                        ?>
                                                                    </select>
                                                                </div>
                                                                <div class="col-12">
                                                                    <label class="form-label">Licencia / Certificación</label>
                                                                    <input type="text" name="licencia" class="form-control" placeholder="Ej. ITTF Nivel 1" value="<?php echo htmlspecialchars($arbitroEditando['licencia'] ?? ''); ?>">
                                                                </div>
                                                                <div class="col-12">
                                                                    <label class="form-label">Mesa asignada</label>
                                                                    <select name="mesa" class="form-control">
                                                                        <option value="">Sin asignar</option>
                                                                        <?php foreach ($sedeStore as $sede): ?>
                                                                            <?php foreach (($sede['mesas'] ?? []) as $mesa): ?>
                                                                                <?php $selectedMesa = ($arbitroEditando['mesa'] ?? '') === $mesa ? 'selected' : ''; ?>
                                                                                <option value="<?php echo htmlspecialchars($mesa); ?>" <?php echo $selectedMesa; ?>><?php echo htmlspecialchars(($sede['nombre'] ?? 'Sede') . ' - ' . $mesa); ?></option>
                                                                            <?php endforeach; ?>
                                                                        <?php endforeach; ?>
                                                                    </select>
                                                                </div>
                                                                <div class="col-12">
                                                                    <label class="form-label">Teléfono</label>
                                                                    <input type="text" name="telefono" class="form-control" placeholder="Opcional" value="<?php echo htmlspecialchars($arbitroEditando['telefono'] ?? ''); ?>">
                                                                </div>
                                                                <div class="col-12">
                                                                    <label class="form-label">Disponibilidad</label>
                                                                    <textarea name="disponibilidad" class="form-control" rows="2" placeholder="Horarios o días disponibles"><?php echo htmlspecialchars($arbitroEditando['disponibilidad'] ?? ''); ?></textarea>
                                                                </div>
                                                                <div class="col-12 text-end">
                                                                    <button type="submit" class="btn btn-primary"><?php echo $arbitroEditando ? 'Actualizar árbitro' : 'Guardar árbitro'; ?></button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                </div>
                                        </div>
                                        <div class="col-xl-8">
                                                <div class="card">
                                                        <div class="card-header border-0 pb-0">
                                                            <div>
                                                                <h4 class="fs-20 mb-1">Listado de árbitros/anotadores</h4>
                                                                <span class="fs-14 text-muted">Gestiona asignaciones y disponibilidad</span>
                                                            </div>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="table-responsive">
                                                                <table class="table table-striped align-middle">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Nombre</th>
                                                                            <th>Rol</th>
                                                                            <th>Licencia</th>
                                                                            <th>Mesa</th>
                                                                            <th>Teléfono</th>
                                                                            <th>Disponibilidad</th>
                                                                            <th></th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <?php if (empty($arbitros)): ?>
                                                                            <tr><td colspan="7" class="text-center text-muted">Sin registros</td></tr>
                                                                        <?php else: ?>
                                                                            <?php foreach ($arbitros as $item): ?>
                                                                                <tr>
                                                                                    <td><?php echo htmlspecialchars($item['nombre'] ?? ''); ?></td>
                                                                                    <td><?php echo htmlspecialchars($item['rol'] ?? ''); ?></td>
                                                                                    <td><?php echo htmlspecialchars($item['licencia'] ?? ''); ?></td>
                                                                                    <td><?php echo htmlspecialchars($item['mesa'] ?? ''); ?></td>
                                                                                    <td><?php echo htmlspecialchars($item['telefono'] ?? ''); ?></td>
                                                                                    <td><?php echo htmlspecialchars($item['disponibilidad'] ?? ''); ?></td>
                                                                                    <td class="text-end">
                                                                                        <a href="?action=editar&id=<?php echo urlencode($item['id']); ?>" class="btn btn-sm btn-outline-primary me-1">Editar</a>
                                                                                        <a href="?action=eliminar&id=<?php echo urlencode($item['id']); ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar registro?');">Eliminar</a>
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
