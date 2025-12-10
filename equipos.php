<?php
require_once __DIR__ . '/auth-guard.php';
require_once __DIR__ . '/config/dz.php';
require_once __DIR__ . '/helpers.php';

const TEAM_STORE = 'equipos.json';
const PLAYER_STORE = 'jugadores.json';
const CLUB_STORE = 'clubes.json';

$equipos = load_json(TEAM_STORE);
$jugadores = load_json(PLAYER_STORE);
$clubes = load_json(CLUB_STORE);
$jugadoresIndex = [];
foreach ($jugadores as $jugador) {
    if (!empty($jugador['id'])) {
        $jugadoresIndex[$jugador['id']] = $jugador;
    }
}

$equipoEditando = null;
if (($_GET['action'] ?? '') === 'editar' && isset($_GET['id'])) {
    $equipoEditando = find_by_id($equipos, $_GET['id']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = trim($_POST['id'] ?? '');
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

    if ($id !== '') {
        $equipos = array_map(function ($equipo) use ($id, $nombre, $club, $modalidad, $categoria, $integrantes) {
            if (($equipo['id'] ?? '') !== $id) {
                return $equipo;
            }

            return array_merge($equipo, [
                'nombre' => $nombre,
                'club' => $club,
                'modalidad' => $modalidad,
                'categoria' => $categoria,
                'integrantes' => array_values($integrantes),
                'updated_at' => date(DATE_ATOM),
            ]);
        }, $equipos);
    } else {
        $equipos[] = [
            'id' => uniqid('eq_', true),
            'nombre' => $nombre,
            'club' => $club,
            'modalidad' => $modalidad,
            'categoria' => $categoria,
            'integrantes' => array_values($integrantes),
            'created_at' => date(DATE_ATOM),
        ];
    }

    save_json(TEAM_STORE, $equipos);
    $_SESSION['equipos_success'] = $id !== '' ? 'Equipo actualizado correctamente.' : 'Equipo registrado correctamente.';
    header('Location: equipos.php');
    exit;
}

if (($_GET['action'] ?? '') === 'eliminar' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $equipos = array_values(array_filter($equipos, fn($equipo) => ($equipo['id'] ?? '') !== $id));
    save_json(TEAM_STORE, $equipos);
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
                                <li class="breadcrumb-item active"><a href="javascript:void(0)">Equipos</a></li>
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
                                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($equipoEditando['id'] ?? ''); ?>">
                                            <div class="col-md-6">
                                                <label class="form-label">Nombre del equipo</label>
                                                <input type="text" name="nombre" class="form-control" placeholder="Nombre oficial" required value="<?php echo htmlspecialchars($equipoEditando['nombre'] ?? ''); ?>">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Club</label>
                                                <select name="club" class="form-control">
                                                    <option value="">Independiente</option>
                                                    <?php foreach ($clubes as $club): ?>
                                                        <?php $selectedClub = ($equipoEditando['club'] ?? '') === ($club['nombre'] ?? '') ? 'selected' : ''; ?>
                                                        <option value="<?php echo htmlspecialchars($club['nombre'] ?? ''); ?>" <?php echo $selectedClub; ?>><?php echo htmlspecialchars($club['nombre'] ?? ''); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Modalidad</label>
                                                <select name="modalidad" class="form-control" required>
                                                    <?php
                                                    $modalidades = [
                                                        'Liga por equipos',
                                                        'Dobles masculino',
                                                        'Dobles femenino',
                                                        'Dobles mixto',
                                                    ];
                                                    $modalidadActual = $equipoEditando['modalidad'] ?? '';
                                                    echo '<option value="">Selecciona</option>';
                                                    foreach ($modalidades as $modalidadOpcion) {
                                                        $selected = $modalidadActual === $modalidadOpcion ? 'selected' : '';
                                                        echo '<option ' . $selected . '>' . htmlspecialchars($modalidadOpcion) . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Categoría</label>
                                                <select name="categoria" class="form-control">
                                                    <?php
                                                    $categorias = ['General', 'Infantil', 'Juvenil', 'Absoluto', 'Senior'];
                                                    $categoriaActual = $equipoEditando['categoria'] ?? '';
                                                    foreach ($categorias as $cat) {
                                                        $selected = $categoriaActual === $cat ? 'selected' : '';
                                                        echo '<option ' . $selected . '>' . htmlspecialchars($cat) . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col-md-8">
                                                <label class="form-label">Integrantes</label>
                                                <?php $integrantesSeleccionados = $equipoEditando['integrantes'] ?? []; ?>
                                                <select name="integrantes[]" class="form-control" multiple size="6">
                                                    <?php foreach ($jugadores as $jugador): ?>
                                                        <?php $sel = in_array($jugador['id'] ?? '', $integrantesSeleccionados, true) ? 'selected' : ''; ?>
                                                        <option value="<?php echo htmlspecialchars($jugador['id'] ?? ''); ?>" <?php echo $sel; ?>><?php echo htmlspecialchars(($jugador['nombre'] ?? '') . ' ' . ($jugador['apellido'] ?? '')); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <small class="text-muted">Mantén Ctrl/Command para seleccionar varios jugadores.</small>
                                            </div>
                                            <div class="col-12 text-end">
                                                <button type="submit" class="btn btn-primary"><?php echo $equipoEditando ? 'Actualizar equipo' : 'Guardar equipo'; ?></button>
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
                                                        <th>Nombre</th>
                                                        <th>Club</th>
                                                        <th>Modalidad</th>
                                                        <th>Categoría</th>
                                                        <th>Integrantes</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if (empty($equipos)): ?>
                                                        <tr><td colspan="6" class="text-center text-muted">Sin registros</td></tr>
                                                    <?php else: ?>
                                                        <?php foreach ($equipos as $equipo): ?>
                                                            <tr>
                                                                <td><?php echo htmlspecialchars($equipo['nombre'] ?? ''); ?></td>
                                                                <td><?php echo htmlspecialchars($equipo['club'] ?? ''); ?></td>
                                                                <td><?php echo htmlspecialchars($equipo['modalidad'] ?? ''); ?></td>
                                                                <td><?php echo htmlspecialchars($equipo['categoria'] ?? ''); ?></td>
                                                                <td>
                                                                    <?php
                                                                    $nombres = [];
                                                                    foreach (($equipo['integrantes'] ?? []) as $id) {
                                                                        if (isset($jugadoresIndex[$id])) {
                                                                            $nombres[] = ($jugadoresIndex[$id]['nombre'] ?? '') . ' ' . ($jugadoresIndex[$id]['apellido'] ?? '');
                                                                        }
                                                                    }
                                                                    echo htmlspecialchars(implode(', ', $nombres));
                                                                    ?>
                                                                </td>
                                                                <td class="text-end">
                                                                    <a href="?action=editar&id=<?php echo urlencode($equipo['id']); ?>" class="btn btn-sm btn-outline-primary me-1">Editar</a>
                                                                    <a href="?action=eliminar&id=<?php echo urlencode($equipo['id']); ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar equipo?');">Eliminar</a>
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

                <!--**********************************
            Footer start
        ***********************************-->
                <?php include 'elements/footer.php'; ?>
                <!--**********************************
            Footer end
        ***********************************-->


            </div>
            <!--**********************************
        Main wrapper end
    ***********************************-->

        <!--**********************************
            Scripts
        ***********************************-->
        <?php include 'elements/page-js.php'; ?>

</body>
</html>
