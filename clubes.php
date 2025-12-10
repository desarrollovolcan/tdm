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
$clubEditando = null;
$busqueda = trim($_GET['q'] ?? '');

if (($_GET['action'] ?? '') === 'editar' && isset($_GET['id'])) {
    $clubEditando = find_by_id($clubes, $_GET['id']);
}

$clubesFiltrados = array_values(array_filter(
    $clubes,
    function ($club) use ($busqueda) {
        if ($busqueda === '') {
            return true;
        }

        return stripos($club['nombre'] ?? '', $busqueda) !== false
            || stripos($club['codigo'] ?? '', $busqueda) !== false
            || stripos($club['ciudad'] ?? '', $busqueda) !== false;
    }
));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = trim($_POST['id'] ?? '');
    $nombre = trim($_POST['nombre'] ?? '');
    $codigo = trim($_POST['codigo'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    $ciudad = trim($_POST['ciudad'] ?? '');
    $pais = trim($_POST['pais'] ?? '');
    $contacto = trim($_POST['contacto'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $afiliacion = trim($_POST['afiliacion'] ?? '');
    $activo = isset($_POST['activo']) ? (bool) $_POST['activo'] : true;

    if ($nombre === '' || $ciudad === '' || $pais === '') {
        $_SESSION['club_error'] = 'El nombre, la ciudad y el país son obligatorios.';
        header('Location: clubes.php');
        exit;
    }

    if ($id !== '') {
        $clubes = array_map(
            function ($club) use ($id, $nombre, $codigo, $direccion, $ciudad, $pais, $contacto, $correo, $telefono, $afiliacion, $activo) {
                if (($club['id'] ?? '') !== $id) {
                    return $club;
                }

                return array_merge($club, [
                    'nombre' => $nombre,
                    'codigo' => $codigo,
                    'direccion' => $direccion,
                    'ciudad' => $ciudad,
                    'pais' => $pais,
                    'contacto' => $contacto,
                    'correo' => $correo,
                    'telefono' => $telefono,
                    'afiliacion' => $afiliacion,
                    'activo' => $activo,
                    'updated_at' => date(DATE_ATOM),
                ]);
            },
            $clubes
        );
    } else {
        $clubes[] = [
            'id' => uniqid('club_', true),
            'nombre' => $nombre,
            'codigo' => $codigo,
            'direccion' => $direccion,
            'ciudad' => $ciudad,
            'pais' => $pais,
            'contacto' => $contacto,
            'correo' => $correo,
            'telefono' => $telefono,
            'afiliacion' => $afiliacion,
            'activo' => true,
            'created_at' => date(DATE_ATOM),
        ];
    }

    save_json(CLUB_STORE, $clubes);
    $_SESSION['club_success'] = $id !== '' ? 'Club actualizado correctamente.' : 'Club registrado correctamente.';
    header('Location: clubes.php');
    exit;
}

if (($_GET['action'] ?? '') === 'eliminar' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $clubes = array_values(array_filter($clubes, fn($club) => ($club['id'] ?? '') !== $id));
    save_json(CLUB_STORE, $clubes);
    $_SESSION['club_success'] = 'Club eliminado correctamente.';
    header('Location: clubes.php');
    exit;
}

if (($_GET['action'] ?? '') === 'toggle' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $clubes = array_map(
        function ($club) use ($id) {
            if (($club['id'] ?? '') !== $id) {
                return $club;
            }

            $club['activo'] = !($club['activo'] ?? true);
            $club['updated_at'] = date(DATE_ATOM);

            return $club;
        },
        $clubes
    );

    save_json(CLUB_STORE, $clubes);
    $_SESSION['club_success'] = 'Estado del club actualizado.';
    header('Location: clubes.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
        <title>Clubes | Sistema de Campeonatos</title>
        <?php include 'elements/meta.php';?>
        <!-- FAVICONS ICON -->
        <link rel="shortcut icon" type="image/png" href="<?php echo ['site_level']['favicon']?>">
        <?php include 'elements/page-css.php'; ?>

</head>
<body>

    <!--*******************
        Preloader start
    ********************-->
   <?php include 'elements/preloader.php'; ?>
    <!--*******************
        Preloader end
    ********************-->

    <!--**********************************
        Main wrapper start
    ***********************************-->
    <div id="main-wrapper">

        <!--**********************************
            Nav header start
        ***********************************-->
      <?php include 'elements/nav-header.php'; ?>
        <!--**********************************
            Nav header end
        ***********************************-->

                <!--**********************************
            Chat box start
        ***********************************-->
                <?php include 'elements/chatbox.php'; ?>
                <!--**********************************
            Chat box End
        ***********************************-->

                <!--**********************************
            Header start
        ***********************************-->
                        <?php include 'elements/header.php'; ?>


        <!--**********************************
            Header end ti-comment-alt
        ***********************************-->

        <!--**********************************
            Sidebar start
        ***********************************-->
                <?php include 'elements/sidebar.php'; ?>
        <!--**********************************
            Sidebar end
        ***********************************-->

                <!--**********************************
            Content body start
        ***********************************-->
        <div class="content-body">
            <div class="container-fluid">
                <div class="row page-titles">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="panel-principal.php">Panel</a></li>
                        <li class="breadcrumb-item active"><a href="javascript:void(0)">Clubes y equipos</a></li>
                    </ol>
                </div>

                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-header d-sm-flex d-block border-0 pb-0">
                                <div>
                                    <h4 class="fs-20 mb-1">Clubes y asociaciones</h4>
                                    <span class="fs-14 text-muted">Registra clubes, asociaciones y contactos oficiales.</span>
                                </div>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($_SESSION['club_success'])): ?>
                                    <div class="alert alert-success alert-dismissible fade show">
                                        <?php echo htmlspecialchars($_SESSION['club_success']); unset($_SESSION['club_success']); ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($_SESSION['club_error'])): ?>
                                    <div class="alert alert-danger alert-dismissible fade show">
                                        <?php echo htmlspecialchars($_SESSION['club_error']); unset($_SESSION['club_error']); ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                <?php endif; ?>

                                <form method="post" class="row g-3">
                                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($clubEditando['id'] ?? ''); ?>">
                                    <div class="col-md-6">
                                        <label class="form-label">Nombre del club</label>
                                        <input type="text" name="nombre" class="form-control" placeholder="Club / Asociación" required value="<?php echo htmlspecialchars($clubEditando['nombre'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">RUT / Código interno</label>
                                        <input type="text" name="codigo" class="form-control" placeholder="RUT / ID interno" value="<?php echo htmlspecialchars($clubEditando['codigo'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-8">
                                        <label class="form-label">Dirección</label>
                                        <input type="text" name="direccion" class="form-control" placeholder="Dirección completa" value="<?php echo htmlspecialchars($clubEditando['direccion'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Ciudad</label>
                                        <input type="text" name="ciudad" class="form-control" placeholder="Ciudad" required value="<?php echo htmlspecialchars($clubEditando['ciudad'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">País</label>
                                        <input type="text" name="pais" class="form-control" placeholder="País" required value="<?php echo htmlspecialchars($clubEditando['pais'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Afiliación</label>
                                        <input type="text" name="afiliacion" class="form-control" placeholder="Liga / Federación" value="<?php echo htmlspecialchars($clubEditando['afiliacion'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Contacto principal</label>
                                        <input type="text" name="contacto" class="form-control" placeholder="Nombre del responsable" value="<?php echo htmlspecialchars($clubEditando['contacto'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Correo</label>
                                        <input type="email" name="correo" class="form-control" placeholder="contacto@club.com" value="<?php echo htmlspecialchars($clubEditando['correo'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Teléfono</label>
                                        <input type="text" name="telefono" class="form-control" placeholder="Opcional" value="<?php echo htmlspecialchars($clubEditando['telefono'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-6 d-flex align-items-center">
                                        <div class="form-check mt-4">
                                            <input class="form-check-input" type="checkbox" value="1" id="activo" name="activo" <?php echo ($clubEditando['activo'] ?? true) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="activo">
                                                Club activo
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-12 text-end">
                                        <button type="submit" class="btn btn-primary"><?php echo $clubEditando ? 'Actualizar club' : 'Guardar club'; ?></button>
                                        <?php if ($clubEditando): ?>
                                            <a href="clubes.php" class="btn btn-outline-secondary">Cancelar edición</a>
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
                                    <h4 class="fs-20 mb-1">Directorio de clubes</h4>
                                    <span class="fs-14 text-muted">Listado de asociaciones registradas.</span>
                                </div>
                            </div>
                            <div class="card-body">
                                <form class="row g-3 mb-3" method="get">
                                    <div class="col-md-6 col-lg-4">
                                        <label class="form-label">Buscar por nombre, ciudad o código</label>
                                        <input type="text" name="q" class="form-control" placeholder="Ej: Club Central" value="<?php echo htmlspecialchars($busqueda); ?>">
                                    </div>
                                    <div class="col-md-6 col-lg-4 align-self-end">
                                        <button class="btn btn-outline-primary" type="submit">Filtrar</button>
                                        <a class="btn btn-light" href="clubes.php">Limpiar</a>
                                    </div>
                                </form>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Club</th>
                                                <th>Ubicación</th>
                                                <th>Contacto</th>
                                                <th>Actividad</th>
                                                <th class="text-end">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php if (empty($clubesFiltrados)): ?>
                                            <tr>
                                                <td colspan="5" class="text-center text-muted">Aún no hay clubes registrados.</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($clubesFiltrados as $club): ?>
                                                <?php
                                                    $sociosClub = array_filter($socios, fn($socio) => ($socio['club_id'] ?? '') === ($club['id'] ?? ''));
                                                    $entrenamientosClub = array_filter($entrenamientos, fn($entrenamiento) => ($entrenamiento['club_id'] ?? '') === ($club['id'] ?? ''));
                                                ?>
                                                <tr>
                                                    <td class="fw-bold">
                                                        <div class="d-flex flex-column">
                                                            <span><?php echo htmlspecialchars($club['nombre'] ?? ''); ?></span>
                                                            <small class="text-muted">ID: <?php echo htmlspecialchars($club['codigo'] ?? 'N/A'); ?></small>
                                                            <small class="text-muted">Afiliación: <?php echo htmlspecialchars($club['afiliacion'] ?? ''); ?></small>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex flex-column">
                                                            <span><?php echo htmlspecialchars($club['ciudad'] ?? ''); ?>, <?php echo htmlspecialchars($club['pais'] ?? ''); ?></span>
                                                            <small class="text-muted"><?php echo htmlspecialchars($club['direccion'] ?? ''); ?></small>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex flex-column">
                                                            <span><?php echo htmlspecialchars($club['contacto'] ?? ''); ?></span>
                                                            <small class="text-muted"><?php echo htmlspecialchars($club['correo'] ?? $club['telefono'] ?? ''); ?></small>
                                                            <small class="text-muted"><?php echo htmlspecialchars($club['telefono'] ?? ''); ?></small>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="badge <?php echo ($club['activo'] ?? true) ? 'badge-success' : 'badge-secondary'; ?> mb-1"><?php echo ($club['activo'] ?? true) ? 'Activo' : 'Inactivo'; ?></span>
                                                        <div class="text-muted small">Socios: <?php echo count($sociosClub); ?> | Entrenamientos: <?php echo count($entrenamientosClub); ?></div>
                                                    </td>
                                                    <td class="text-end">
                                                        <div class="btn-group">
                                                            <a href="clubes.php?action=editar&id=<?php echo urlencode($club['id']); ?>" class="btn btn-outline-primary btn-xs">Editar</a>
                                                            <a href="clubes.php?action=toggle&id=<?php echo urlencode($club['id']); ?>" class="btn btn-outline-warning btn-xs"><?php echo ($club['activo'] ?? true) ? 'Desactivar' : 'Activar'; ?></a>
                                                            <a href="clubes.php?action=eliminar&id=<?php echo urlencode($club['id']); ?>" class="btn btn-outline-danger btn-xs" onclick="return confirm('¿Eliminar club?');">Eliminar</a>
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
        <!--**********************************
            Content body end
        ***********************************-->
                <!-- Button trigger modal -->



                <!--**********************************
            Footer start
        ***********************************-->
                <?php include 'elements/footer.php'; ?>




        </div>


        <!--**********************************
        Scripts
    ***********************************-->

        <!-- Required vendors -->
<?php include 'elements/page-js.php'; ?>


</body>

</html>
