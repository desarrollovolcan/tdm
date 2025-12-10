<?php
require_once __DIR__ . '/auth-guard.php';
require_once __DIR__ . '/config/dz.php';
require_once __DIR__ . '/helpers.php';

const TEAM_STORE = 'equipos.json';
const PLAYER_STORE = 'jugadores.json';
const CLUB_STORE = 'clubes.json';

function loadData(string $path): array
{
    if (!file_exists($path)) {
        return [];
    }

    $json = file_get_contents($path);
    $decoded = json_decode($json, true);

    return is_array($decoded) ? $decoded : [];
}

function saveData(string $path, array $payload): void
{
    $dir = dirname($path);
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    file_put_contents($path, json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

$equipos = load_json(TEAM_STORE);
$jugadores = load_json(PLAYER_STORE);
$clubes = load_json(CLUB_STORE);
$jugadoresIndex = [];
foreach ($jugadores as $jugador) {
    if (!empty($jugador['id'])) {
        $jugadoresIndex[$jugador['id']] = $jugador;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $club = trim($_POST['club'] ?? '');
    $modalidad = trim($_POST['modalidad'] ?? '');
    $categoria = trim($_POST['categoria'] ?? '');
    $integrantes = array_filter($_POST['integrantes'] ?? [], 'strlen');

    if ($nombre === '' || $modalidad === '') {
        $_SESSION['equipos_error'] = 'El nombre del equipo y la modalidad son obligatorios.';
        header('Location: equipos.php');
        exit;
    }

    $equipos[] = [
        'id' => uniqid('eq_', true),
        'nombre' => $nombre,
        'club' => $club,
        'modalidad' => $modalidad,
        'categoria' => $categoria,
        'integrantes' => array_values($integrantes),
        'created_at' => date(DATE_ATOM),
    ];

    saveData(TEAM_STORE, $equipos);
    $_SESSION['equipos_success'] = 'Equipo registrado correctamente.';
    header('Location: equipos.php');
    exit;
}

if (($_GET['action'] ?? '') === 'eliminar' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $equipos = array_values(array_filter($equipos, fn($equipo) => ($equipo['id'] ?? '') !== $id));
    saveData(TEAM_STORE, $equipos);
    $_SESSION['equipos_success'] = 'Equipo eliminado correctamente.';
    header('Location: equipos.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
        <title>Equipos | Sistema de Campeonatos</title>
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
                                    <h4 class="fs-20 mb-1">Equipos y parejas</h4>
                                    <span class="fs-14 text-muted">Organiza equipos para ligas y parejas para dobles.</span>
                                </div>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($_SESSION['equipos_success'])): ?>
                                    <div class="alert alert-success alert-dismissible fade show">
                                        <?php echo htmlspecialchars($_SESSION['equipos_success']); unset($_SESSION['equipos_success']); ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($_SESSION['equipos_error'])): ?>
                                    <div class="alert alert-danger alert-dismissible fade show">
                                        <?php echo htmlspecialchars($_SESSION['equipos_error']); unset($_SESSION['equipos_error']); ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                <?php endif; ?>

                                <form method="post" class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Nombre del equipo</label>
                                        <input type="text" name="nombre" class="form-control" placeholder="Nombre oficial" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Club</label>
                                        <select name="club" class="form-control">
                                            <option value="">Independiente</option>
                                            <?php foreach ($clubes as $club): ?>
                                                <option value="<?php echo htmlspecialchars($club['nombre'] ?? ''); ?>"><?php echo htmlspecialchars($club['nombre'] ?? ''); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Modalidad</label>
                                        <select name="modalidad" class="form-control" required>
                                            <option value="">Selecciona</option>
                                            <option>Liga por equipos</option>
                                            <option>Dobles masculino</option>
                                            <option>Dobles femenino</option>
                                            <option>Dobles mixto</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Categoría</label>
                                        <select name="categoria" class="form-control">
                                            <option value="">General</option>
                                            <option>Infantil</option>
                                            <option>Juvenil</option>
                                            <option>Absoluto</option>
                                            <option>Senior</option>
                                        </select>
                                    </div>
                                    <div class="col-md-8">
                                        <label class="form-label">Integrantes</label>
                                        <select name="integrantes[]" class="form-control" multiple size="6">
                                            <?php foreach ($jugadores as $jugador): ?>
                                                <option value="<?php echo htmlspecialchars($jugador['id'] ?? ''); ?>"><?php echo htmlspecialchars(($jugador['nombre'] ?? '') . ' ' . ($jugador['apellido'] ?? '')); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <small class="text-muted">Mantén Ctrl/Command para seleccionar varios jugadores.</small>
                                    </div>
                                    <div class="col-12 text-end">
                                        <button type="submit" class="btn btn-primary">Guardar equipo</button>
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
                                    <h4 class="fs-20 mb-1">Listado de equipos</h4>
                                    <span class="fs-14 text-muted">Control rápido de equipos inscritos.</span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Equipo</th>
                                                <th>Club</th>
                                                <th>Modalidad</th>
                                                <th>Categoría</th>
                                                <th>Integrantes</th>
                                                <th class="text-end">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php if (empty($equipos)): ?>
                                            <tr>
                                                <td colspan="6" class="text-center text-muted">Aún no hay equipos registrados.</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($equipos as $equipo): ?>
                                                <tr>
                                                    <td class="fw-bold"><?php echo htmlspecialchars($equipo['nombre'] ?? ''); ?></td>
                                                    <td><?php echo htmlspecialchars($equipo['club'] ?: 'Libre'); ?></td>
                                                    <td><?php echo htmlspecialchars($equipo['modalidad'] ?? ''); ?></td>
                                                    <td><?php echo htmlspecialchars($equipo['categoria'] ?? ''); ?></td>
                                                    <td>
                                                        <?php if (empty($equipo['integrantes'])): ?>
                                                            <span class="text-muted">Por asignar</span>
                                                        <?php else: ?>
                                                            <ul class="list-unstyled mb-0">
                                                                <?php foreach ($equipo['integrantes'] as $integranteId): ?>
                                                                    <li><?php echo htmlspecialchars(($jugadoresIndex[$integranteId]['nombre'] ?? 'Jugador') . ' ' . ($jugadoresIndex[$integranteId]['apellido'] ?? '')); ?></li>
                                                                <?php endforeach; ?>
                                                            </ul>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="text-end">
                                                        <a href="equipos.php?action=eliminar&id=<?php echo urlencode($equipo['id']); ?>" class="btn btn-outline-danger btn-xs" onclick="return confirm('¿Eliminar equipo?');">Eliminar</a>
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
