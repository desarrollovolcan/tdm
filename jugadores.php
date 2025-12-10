<?php
session_start();
require_once __DIR__ . '/config/dz.php';

const PLAYER_STORE = __DIR__ . '/database/jugadores.json';
const CLUB_STORE = __DIR__ . '/database/clubes.json';

function loadStore(string $path): array
{
    if (!file_exists($path)) {
        return [];
    }

    $json = file_get_contents($path);
    $data = json_decode($json, true);

    return is_array($data) ? $data : [];
}

function persistStore(string $path, array $payload): void
{
    $dir = dirname($path);
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    file_put_contents($path, json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

$jugadores = loadStore(PLAYER_STORE);
$clubes = loadStore(CLUB_STORE);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $nacimiento = trim($_POST['nacimiento'] ?? '');
    $club = trim($_POST['club'] ?? '');
    $categoria = trim($_POST['categoria'] ?? '');
    $ranking = trim($_POST['ranking'] ?? '');
    $mano = trim($_POST['mano'] ?? '');

    if ($nombre === '' || $apellido === '' || $correo === '') {
        $_SESSION['jugadores_error'] = 'Nombre, apellido y correo son obligatorios.';
        header('Location: jugadores.php');
        exit;
    }

    $jugadores[] = [
        'id' => uniqid('jug_', true),
        'nombre' => $nombre,
        'apellido' => $apellido,
        'correo' => $correo,
        'telefono' => $telefono,
        'nacimiento' => $nacimiento,
        'club' => $club,
        'categoria' => $categoria,
        'ranking' => $ranking,
        'mano' => $mano,
        'created_at' => date(DATE_ATOM),
    ];

    persistStore(PLAYER_STORE, $jugadores);
    $_SESSION['jugadores_success'] = 'Jugador registrado correctamente.';
    header('Location: jugadores.php');
    exit;
}

if (($_GET['action'] ?? '') === 'eliminar' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $jugadores = array_values(array_filter($jugadores, fn($jugador) => ($jugador['id'] ?? '') !== $id));
    persistStore(PLAYER_STORE, $jugadores);
    $_SESSION['jugadores_success'] = 'Jugador eliminado correctamente.';
    header('Location: jugadores.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
        <title>Jugadores | Sistema de Campeonatos</title>
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
                        <li class="breadcrumb-item active"><a href="javascript:void(0)">Jugadores</a></li>
                    </ol>
                </div>

                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-header d-sm-flex d-block border-0 pb-0">
                                <div>
                                    <h4 class="fs-20 mb-1">Gestión de jugadores</h4>
                                    <span class="fs-14 text-muted">Registra fichas, datos de contacto y asignación a clubes.</span>
                                </div>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($_SESSION['jugadores_success'])): ?>
                                    <div class="alert alert-success alert-dismissible fade show">
                                        <?php echo htmlspecialchars($_SESSION['jugadores_success']); unset($_SESSION['jugadores_success']); ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($_SESSION['jugadores_error'])): ?>
                                    <div class="alert alert-danger alert-dismissible fade show">
                                        <?php echo htmlspecialchars($_SESSION['jugadores_error']); unset($_SESSION['jugadores_error']); ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                <?php endif; ?>

                                <form method="post" class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Nombre</label>
                                        <input type="text" name="nombre" class="form-control" placeholder="Nombre" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Apellido</label>
                                        <input type="text" name="apellido" class="form-control" placeholder="Apellido" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Correo electrónico</label>
                                        <input type="email" name="correo" class="form-control" placeholder="correo@dominio.com" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Teléfono</label>
                                        <input type="text" name="telefono" class="form-control" placeholder="Opcional">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Fecha de nacimiento</label>
                                        <input type="date" name="nacimiento" class="form-control">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Club</label>
                                        <select name="club" class="form-control">
                                            <option value="">Sin club</option>
                                            <?php foreach ($clubes as $club): ?>
                                                <option value="<?php echo htmlspecialchars($club['nombre'] ?? ''); ?>"><?php echo htmlspecialchars($club['nombre'] ?? ''); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Categoría</label>
                                        <select name="categoria" class="form-control">
                                            <option value="">Selecciona</option>
                                            <option>Infantil</option>
                                            <option>Juvenil</option>
                                            <option>Adulto</option>
                                            <option>Senior</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Ranking / NIV</label>
                                        <input type="text" name="ranking" class="form-control" placeholder="Ej. 1240">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Mano hábil</label>
                                        <select name="mano" class="form-control">
                                            <option value="">Selecciona</option>
                                            <option>Derecha</option>
                                            <option>Izquierda</option>
                                        </select>
                                    </div>
                                    <div class="col-12 text-end">
                                        <button type="submit" class="btn btn-primary">Guardar jugador</button>
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
                                    <h4 class="fs-20 mb-1">Listado de jugadores</h4>
                                    <span class="fs-14 text-muted">Control rápido de fichas activas.</span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Nombre</th>
                                                <th>Club</th>
                                                <th>Categoría</th>
                                                <th>Ranking</th>
                                                <th>Contacto</th>
                                                <th class="text-end">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php if (empty($jugadores)): ?>
                                            <tr>
                                                <td colspan="6" class="text-center text-muted">Aún no hay jugadores registrados.</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($jugadores as $jugador): ?>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div>
                                                                <div class="fw-bold"><?php echo htmlspecialchars(($jugador['nombre'] ?? '') . ' ' . ($jugador['apellido'] ?? '')); ?></div>
                                                                <small class="text-muted"><?php echo htmlspecialchars($jugador['mano'] ?: ''); ?></small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($jugador['club'] ?: 'Libre'); ?></td>
                                                    <td><?php echo htmlspecialchars($jugador['categoria'] ?: ''); ?></td>
                                                    <td><span class="badge badge-primary light"><?php echo htmlspecialchars($jugador['ranking'] ?: 'N/D'); ?></span></td>
                                                    <td>
                                                        <div class="d-flex flex-column">
                                                            <span><?php echo htmlspecialchars($jugador['correo'] ?? ''); ?></span>
                                                            <?php if (!empty($jugador['telefono'])): ?>
                                                                <small class="text-muted"><?php echo htmlspecialchars($jugador['telefono']); ?></small>
                                                            <?php endif; ?>
                                                        </div>
                                                    </td>
                                                    <td class="text-end">
                                                        <a href="jugadores.php?action=eliminar&id=<?php echo urlencode($jugador['id']); ?>" class="btn btn-outline-danger btn-xs" onclick="return confirm('¿Eliminar jugador?');">Eliminar</a>
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
