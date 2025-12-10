<?php
require_once __DIR__ . '/auth-guard.php';
require_once __DIR__ . '/config/dz.php';
require_once __DIR__ . '/helpers.php';

const CLUB_STORE = 'clubes.json';
const SOCIO_STORE = 'socios.json';
const TRAINING_STORE = 'entrenamientos.json';

$clubes = load_json(CLUB_STORE);
$socios = load_json(SOCIO_STORE);
$entrenamientos = load_json(TRAINING_STORE);

$busqueda = trim($_GET['q'] ?? '');
$filtroClub = trim($_GET['club'] ?? '');
$socioEditando = null;

if (($_GET['action'] ?? '') === 'editar' && isset($_GET['id'])) {
    $socioEditando = find_by_id($socios, $_GET['id']);
}

if (($_GET['action'] ?? '') === 'toggle' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $socios = array_map(
        function ($socio) use ($id) {
            if (($socio['id'] ?? '') !== $id) {
                return $socio;
            }

            $socio['activo'] = !($socio['activo'] ?? true);
            $socio['updated_at'] = date(DATE_ATOM);
            return $socio;
        },
        $socios
    );

    save_json(SOCIO_STORE, $socios);
    $_SESSION['socio_success'] = 'Estado del integrante actualizado.';
    header('Location: socios.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = trim($_POST['id'] ?? '');
    $nombre = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $rut = trim($_POST['rut'] ?? '');
    $nacimiento = trim($_POST['nacimiento'] ?? '');
    $genero = trim($_POST['genero'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $clubId = trim($_POST['club_id'] ?? '');
    $activo = isset($_POST['activo']) ? (bool) $_POST['activo'] : true;

    if ($nombre === '' || $apellido === '' || $rut === '' || $clubId === '') {
        $_SESSION['socio_error'] = 'Los campos nombre, apellido, RUT/ID y club son obligatorios.';
        header('Location: socios.php');
        exit;
    }

    $clubEncontrado = find_by_id($clubes, $clubId);
    if (!$clubEncontrado) {
        $_SESSION['socio_error'] = 'El club seleccionado no existe.';
        header('Location: socios.php');
        exit;
    }

    if ($id !== '') {
        $socios = array_map(
            function ($socio) use ($id, $nombre, $apellido, $rut, $nacimiento, $genero, $email, $telefono, $clubId, $activo) {
                if (($socio['id'] ?? '') !== $id) {
                    return $socio;
                }

                return array_merge($socio, [
                    'nombre' => $nombre,
                    'apellido' => $apellido,
                    'rut' => $rut,
                    'nacimiento' => $nacimiento,
                    'genero' => $genero,
                    'email' => $email,
                    'telefono' => $telefono,
                    'club_id' => $clubId,
                    'activo' => $activo,
                    'updated_at' => date(DATE_ATOM),
                ]);
            },
            $socios
        );
    } else {
        $socios[] = [
            'id' => uniqid('socio_', true),
            'nombre' => $nombre,
            'apellido' => $apellido,
            'rut' => $rut,
            'nacimiento' => $nacimiento,
            'genero' => $genero,
            'email' => $email,
            'telefono' => $telefono,
            'club_id' => $clubId,
            'activo' => true,
            'plan_historial' => [],
            'created_at' => date(DATE_ATOM),
        ];
    }

    save_json(SOCIO_STORE, $socios);
    $_SESSION['socio_success'] = $id !== '' ? 'Integrante actualizado correctamente.' : 'Integrante registrado correctamente.';
    header('Location: socios.php');
    exit;
}

$sociosFiltrados = array_values(array_filter(
    $socios,
    function ($socio) use ($busqueda, $filtroClub) {
        $matchBusqueda = $busqueda === ''
            || stripos($socio['nombre'] ?? '', $busqueda) !== false
            || stripos($socio['apellido'] ?? '', $busqueda) !== false
            || stripos($socio['rut'] ?? '', $busqueda) !== false;

        $matchClub = $filtroClub === '' || ($socio['club_id'] ?? '') === $filtroClub;

        return $matchBusqueda && $matchClub;
    }
));

?>
<!DOCTYPE html>
<html lang="es">

<head>
        <title>Integrantes y socios | Sistema de Campeonatos</title>
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
                        <li class="breadcrumb-item active"><a href="javascript:void(0)">Integrantes / Socios</a></li>
                    </ol>
                </div>

                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-header d-sm-flex d-block border-0 pb-0">
                                <div>
                                    <h4 class="fs-20 mb-1">Registro de integrantes</h4>
                                    <span class="fs-14 text-muted">Asocia miembros a clubes y gestiona su estado.</span>
                                </div>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($_SESSION['socio_success'])): ?>
                                    <div class="alert alert-success alert-dismissible fade show">
                                        <?php echo htmlspecialchars($_SESSION['socio_success']); unset($_SESSION['socio_success']); ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($_SESSION['socio_error'])): ?>
                                    <div class="alert alert-danger alert-dismissible fade show">
                                        <?php echo htmlspecialchars($_SESSION['socio_error']); unset($_SESSION['socio_error']); ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                <?php endif; ?>

                                <form method="post" class="row g-3">
                                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($socioEditando['id'] ?? ''); ?>">
                                    <div class="col-md-4">
                                        <label class="form-label">Nombre</label>
                                        <input type="text" name="nombre" class="form-control" required value="<?php echo htmlspecialchars($socioEditando['nombre'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Apellido</label>
                                        <input type="text" name="apellido" class="form-control" required value="<?php echo htmlspecialchars($socioEditando['apellido'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">RUT / ID</label>
                                        <input type="text" name="rut" class="form-control" required value="<?php echo htmlspecialchars($socioEditando['rut'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Fecha de nacimiento</label>
                                        <input type="date" name="nacimiento" class="form-control" value="<?php echo htmlspecialchars($socioEditando['nacimiento'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Género (opcional)</label>
                                        <select class="form-control" name="genero">
                                            <option value="">No especificar</option>
                                            <?php $generoActual = $socioEditando['genero'] ?? ''; ?>
                                            <option value="femenino" <?php echo $generoActual === 'femenino' ? 'selected' : ''; ?>>Femenino</option>
                                            <option value="masculino" <?php echo $generoActual === 'masculino' ? 'selected' : ''; ?>>Masculino</option>
                                            <option value="otro" <?php echo $generoActual === 'otro' ? 'selected' : ''; ?>>Otro</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Club</label>
                                        <select class="form-control" name="club_id" required>
                                            <option value="">Selecciona un club</option>
                                            <?php foreach ($clubes as $club): ?>
                                                <option value="<?php echo htmlspecialchars($club['id'] ?? ''); ?>" <?php echo (($socioEditando['club_id'] ?? '') === ($club['id'] ?? '')) ? 'selected' : ''; ?>><?php echo htmlspecialchars($club['nombre'] ?? ''); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Correo</label>
                                        <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($socioEditando['email'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Teléfono</label>
                                        <input type="text" name="telefono" class="form-control" value="<?php echo htmlspecialchars($socioEditando['telefono'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-6 d-flex align-items-center">
                                        <div class="form-check mt-4">
                                            <input class="form-check-input" type="checkbox" value="1" id="activo" name="activo" <?php echo ($socioEditando['activo'] ?? true) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="activo">Integrante activo</label>
                                        </div>
                                    </div>
                                    <div class="col-12 text-end">
                                        <button type="submit" class="btn btn-primary"><?php echo $socioEditando ? 'Actualizar integrante' : 'Guardar integrante'; ?></button>
                                        <?php if ($socioEditando): ?>
                                            <a href="socios.php" class="btn btn-outline-secondary">Cancelar edición</a>
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
                                    <h4 class="fs-20 mb-1">Directorio de integrantes</h4>
                                    <span class="fs-14 text-muted">Búsqueda rápida por nombre, club o identificación.</span>
                                </div>
                            </div>
                            <div class="card-body">
                                <form class="row g-3 mb-3" method="get">
                                    <div class="col-md-6 col-lg-4">
                                        <label class="form-label">Buscar por nombre o RUT/ID</label>
                                        <input type="text" name="q" class="form-control" value="<?php echo htmlspecialchars($busqueda); ?>" placeholder="Ej: Ana, 12.345.678-9">
                                    </div>
                                    <div class="col-md-6 col-lg-3">
                                        <label class="form-label">Club</label>
                                        <select name="club" class="form-control">
                                            <option value="">Todos los clubes</option>
                                            <?php foreach ($clubes as $club): ?>
                                                <option value="<?php echo htmlspecialchars($club['id'] ?? ''); ?>" <?php echo $filtroClub === ($club['id'] ?? '') ? 'selected' : ''; ?>><?php echo htmlspecialchars($club['nombre'] ?? ''); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6 col-lg-3 align-self-end">
                                        <button class="btn btn-outline-primary" type="submit">Filtrar</button>
                                        <a class="btn btn-light" href="socios.php">Limpiar</a>
                                    </div>
                                </form>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Integrante</th>
                                                <th>Club</th>
                                                <th>Contacto</th>
                                                <th>Estado</th>
                                                <th class="text-end">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($sociosFiltrados)): ?>
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted">Aún no hay integrantes registrados.</td>
                                                </tr>
                                            <?php else: ?>
                                                <?php foreach ($sociosFiltrados as $socio): ?>
                                                    <?php
                                                        $club = find_by_id($clubes, $socio['club_id'] ?? '');
                                                        $entrenamientosAsignados = array_filter(
                                                            $entrenamientos,
                                                            fn($entrenamiento) => in_array($socio['id'], $entrenamiento['integrantes'] ?? [], true)
                                                        );
                                                    ?>
                                                    <tr>
                                                        <td>
                                                            <div class="d-flex flex-column">
                                                                <span class="fw-bold"><?php echo htmlspecialchars(($socio['nombre'] ?? '') . ' ' . ($socio['apellido'] ?? '')); ?></span>
                                                                <small class="text-muted">RUT/ID: <?php echo htmlspecialchars($socio['rut'] ?? ''); ?></small>
                                                                <?php if (!empty($socio['plan_historial'])): ?>
                                                                    <small class="text-muted">Último plan: <?php echo htmlspecialchars(end($socio['plan_historial'])['plan_nombre'] ?? ''); ?></small>
                                                                <?php endif; ?>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <?php echo htmlspecialchars($club['nombre'] ?? 'Sin club'); ?>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex flex-column">
                                                                <span><?php echo htmlspecialchars($socio['email'] ?? ''); ?></span>
                                                                <small class="text-muted"><?php echo htmlspecialchars($socio['telefono'] ?? ''); ?></small>
                                                                <small class="text-muted">Entrenamientos: <?php echo count($entrenamientosAsignados); ?></small>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <span class="badge <?php echo ($socio['activo'] ?? true) ? 'badge-success' : 'badge-secondary'; ?>"><?php echo ($socio['activo'] ?? true) ? 'Activo' : 'Inactivo'; ?></span>
                                                        </td>
                                                        <td class="text-end">
                                                            <div class="btn-group">
                                                                <a href="socios.php?action=editar&id=<?php echo urlencode($socio['id']); ?>" class="btn btn-outline-primary btn-xs">Editar</a>
                                                                <a href="socios.php?action=toggle&id=<?php echo urlencode($socio['id']); ?>" class="btn btn-outline-warning btn-xs"><?php echo ($socio['activo'] ?? true) ? 'Desactivar' : 'Activar'; ?></a>
                                                            </div>
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
