<?php
require_once __DIR__ . '/auth-guard.php';
require_once __DIR__ . '/config/dz.php';
require_once __DIR__ . '/helpers.php';

const MODULO_COMUNICACIONES_STORE = 'modulo_comunicaciones.json';

$registros = load_json(MODULO_COMUNICACIONES_STORE);

if (($_GET['action'] ?? '') === 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $registros = array_values(array_filter(
        $registros,
        fn($registro) => ($registro['id'] ?? '') !== $id
    ));
    save_json(MODULO_COMUNICACIONES_STORE, $registros);
    flash('comunicacion_success', 'Mensaje eliminado.');
    header('Location: modulo-comunicaciones.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $canal = trim($_POST['canal'] ?? '');
    $asunto = trim($_POST['asunto'] ?? '');
    $destinatarios = trim($_POST['destinatarios'] ?? '');
    $prioridad = trim($_POST['prioridad'] ?? '');
    $resumen = trim($_POST['resumen'] ?? '');

    if ($asunto === '') {
        flash('comunicacion_error', 'El asunto es obligatorio.');
        header('Location: modulo-comunicaciones.php');
        exit;
    }

    $registros[] = [
        'id' => uniqid('msg_', true),
        'canal' => $canal ?: 'Chat',
        'asunto' => $asunto,
        'destinatarios' => $destinatarios,
        'prioridad' => $prioridad ?: 'Normal',
        'resumen' => $resumen,
        'created_at' => date(DATE_ATOM),
    ];

    save_json(MODULO_COMUNICACIONES_STORE, $registros);
    flash('comunicacion_success', 'Mensaje registrado.');
    header('Location: modulo-comunicaciones.php');
    exit;
}

$successMessage = flash('comunicacion_success');
$errorMessage = flash('comunicacion_error');
?>
<!DOCTYPE html>
<html lang="es">
<head>
        <title>Módulo de comunicaciones | <?php echo $DexignZoneSettings['site_level']['site_title']; ?></title>
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
                                        <li class="breadcrumb-item active">Módulo de comunicaciones</li>
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
                                                        <h4 class="card-title mb-0">Registrar comunicación</h4>
                                                </div>
                                                <div class="card-body">
                                                        <form method="post" class="row g-3">
                                                                <div class="col-md-6">
                                                                        <label class="form-label">Canal</label>
                                                                        <select name="canal" class="form-select">
                                                                                <option value="Chat">Chat</option>
                                                                                <option value="Email">Email</option>
                                                                                <option value="Notificación">Notificación</option>
                                                                                <option value="Anuncio">Anuncio</option>
                                                                        </select>
                                                                </div>
                                                                <div class="col-md-6">
                                                                        <label class="form-label">Prioridad</label>
                                                                        <select name="prioridad" class="form-select">
                                                                                <option value="Normal">Normal</option>
                                                                                <option value="Alta">Alta</option>
                                                                                <option value="Crítica">Crítica</option>
                                                                        </select>
                                                                </div>
                                                                <div class="col-12">
                                                                        <label class="form-label">Asunto *</label>
                                                                        <input type="text" name="asunto" class="form-control" required>
                                                                </div>
                                                                <div class="col-12">
                                                                        <label class="form-label">Destinatarios</label>
                                                                        <input type="text" name="destinatarios" class="form-control" placeholder="Grupo, categoría o jugador">
                                                                </div>
                                                                <div class="col-12">
                                                                        <label class="form-label">Resumen / mensaje</label>
                                                                        <textarea name="resumen" rows="4" class="form-control" placeholder="Escribe el mensaje o nota clave"></textarea>
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
                                                        <h4 class="card-title mb-0">Mensajes registrados</h4>
                                                        <small class="text-muted">Chat interno, email y anuncios</small>
                                                </div>
                                                <div class="card-body p-0">
                                                        <div class="table-responsive">
                                                                <table class="table mb-0">
                                                                        <thead>
                                                                                <tr>
                                                                                        <th>Canal</th>
                                                                                        <th>Asunto</th>
                                                                                        <th>Destinatarios</th>
                                                                                        <th>Prioridad</th>
                                                                                        <th></th>
                                                                                </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                                <?php if (empty($registros)): ?>
                                                                                        <tr><td colspan="5" class="text-center py-4">Sin comunicaciones registradas.</td></tr>
                                                                                <?php else: ?>
                                                                                        <?php foreach ($registros as $registro): ?>
                                                                                                <tr>
                                                                                                        <td><?php echo htmlspecialchars($registro['canal'] ?? ''); ?></td>
                                                                                                        <td><?php echo htmlspecialchars($registro['asunto'] ?? ''); ?></td>
                                                                                                        <td><?php echo htmlspecialchars($registro['destinatarios'] ?? '-'); ?></td>
                                                                                                        <td><span class="badge bg-<?php echo (($registro['prioridad'] ?? '') === 'Crítica') ? 'danger' : ((($registro['prioridad'] ?? '') === 'Alta') ? 'warning' : 'secondary'); ?>">
                                                                                                                <?php echo htmlspecialchars($registro['prioridad'] ?? ''); ?></span></td>
                                                                                                        <td class="text-end">
                                                                                                                <a class="btn btn-sm btn-outline-danger" href="?action=delete&id=<?php echo urlencode($registro['id']); ?>" onclick="return confirm('¿Eliminar comunicación?');">Eliminar</a>
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
