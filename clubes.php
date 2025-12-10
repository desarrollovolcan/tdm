<?php
session_start();
require_once __DIR__ . '/config/dz.php';

const CLUB_STORE = __DIR__ . '/database/clubes.json';

function loadClubStore(): array
{
    if (!file_exists(CLUB_STORE)) {
        return [];
    }

    $json = file_get_contents(CLUB_STORE);
    $decoded = json_decode($json, true);

    return is_array($decoded) ? $decoded : [];
}

function persistClubStore(array $clubes): void
{
    $dir = dirname(CLUB_STORE);
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    file_put_contents(CLUB_STORE, json_encode($clubes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

$clubes = loadClubStore();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $ciudad = trim($_POST['ciudad'] ?? '');
    $contacto = trim($_POST['contacto'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $afiliacion = trim($_POST['afiliacion'] ?? '');

    if ($nombre === '' || $ciudad === '') {
        $_SESSION['club_error'] = 'El nombre y la ciudad son obligatorios.';
        header('Location: clubes.php');
        exit;
    }

    $clubes[] = [
        'id' => uniqid('club_', true),
        'nombre' => $nombre,
        'ciudad' => $ciudad,
        'contacto' => $contacto,
        'correo' => $correo,
        'telefono' => $telefono,
        'afiliacion' => $afiliacion,
        'created_at' => date(DATE_ATOM),
    ];

    persistClubStore($clubes);
    $_SESSION['club_success'] = 'Club registrado correctamente.';
    header('Location: clubes.php');
    exit;
}

if (($_GET['action'] ?? '') === 'eliminar' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $clubes = array_values(array_filter($clubes, fn($club) => ($club['id'] ?? '') !== $id));
    persistClubStore($clubes);
    $_SESSION['club_success'] = 'Club eliminado correctamente.';
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
                                    <div class="col-md-6">
                                        <label class="form-label">Nombre del club</label>
                                        <input type="text" name="nombre" class="form-control" placeholder="Club / Asociación" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Ciudad</label>
                                        <input type="text" name="ciudad" class="form-control" placeholder="Ciudad" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Contacto principal</label>
                                        <input type="text" name="contacto" class="form-control" placeholder="Nombre del responsable">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Correo</label>
                                        <input type="email" name="correo" class="form-control" placeholder="contacto@club.com">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Teléfono</label>
                                        <input type="text" name="telefono" class="form-control" placeholder="Opcional">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Afiliación</label>
                                        <input type="text" name="afiliacion" class="form-control" placeholder="Liga / Federación">
                                    </div>
                                    <div class="col-12 text-end">
                                        <button type="submit" class="btn btn-primary">Guardar club</button>
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
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Nombre</th>
                                                <th>Ciudad</th>
                                                <th>Contacto</th>
                                                <th>Afiliación</th>
                                                <th class="text-end">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php if (empty($clubes)): ?>
                                            <tr>
                                                <td colspan="5" class="text-center text-muted">Aún no hay clubes registrados.</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($clubes as $club): ?>
                                                <tr>
                                                    <td class="fw-bold"><?php echo htmlspecialchars($club['nombre'] ?? ''); ?></td>
                                                    <td><?php echo htmlspecialchars($club['ciudad'] ?? ''); ?></td>
                                                    <td>
                                                        <div class="d-flex flex-column">
                                                            <span><?php echo htmlspecialchars($club['contacto'] ?? ''); ?></span>
                                                            <small class="text-muted"><?php echo htmlspecialchars($club['correo'] ?? $club['telefono'] ?? ''); ?></small>
                                                        </div>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($club['afiliacion'] ?? ''); ?></td>
                                                    <td class="text-end">
                                                        <a href="clubes.php?action=eliminar&id=<?php echo urlencode($club['id']); ?>" class="btn btn-outline-danger btn-xs" onclick="return confirm('¿Eliminar club?');">Eliminar</a>
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
