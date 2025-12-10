<?php
require_once __DIR__ . '/auth-guard.php';
require_once __DIR__ . '/config/dz.php';
require_once __DIR__ . '/helpers.php';

const PLAYER_STORE = 'jugadores.json';
const CLUB_STORE = 'clubes.json';

$jugadores = load_json(PLAYER_STORE);
$clubes = load_json(CLUB_STORE);
$jugadorEditando = null;

if (($_GET['action'] ?? '') === 'editar' && isset($_GET['id'])) {
    $jugadorEditando = find_by_id($jugadores, $_GET['id']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = trim($_POST['id'] ?? '');
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

    if ($id !== '') {
        $jugadores = array_map(function ($jugador) use ($id, $nombre, $apellido, $correo, $telefono, $nacimiento, $club, $categoria, $ranking, $mano) {
            if (($jugador['id'] ?? '') !== $id) {
                return $jugador;
            }

            return array_merge($jugador, [
                'nombre' => $nombre,
                'apellido' => $apellido,
                'correo' => $correo,
                'telefono' => $telefono,
                'nacimiento' => $nacimiento,
                'club' => $club,
                'categoria' => $categoria,
                'ranking' => $ranking,
                'mano' => $mano,
                'updated_at' => date(DATE_ATOM),
            ]);
        }, $jugadores);
    } else {
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
    }

    if (!save_json(PLAYER_STORE, $jugadores)) {
        $_SESSION['jugadores_error'] = 'No se pudo guardar el jugador. Verifica permisos de escritura.';
        header('Location: jugadores.php');
        exit;
    }

    $_SESSION['jugadores_success'] = $id !== '' ? 'Jugador actualizado correctamente.' : 'Jugador registrado correctamente.';
    header('Location: jugadores.php');
    exit;
}

if (($_GET['action'] ?? '') === 'eliminar' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $jugadores = array_values(array_filter($jugadores, fn($jugador) => ($jugador['id'] ?? '') !== $id));
    if (!save_json(PLAYER_STORE, $jugadores)) {
        $_SESSION['jugadores_error'] = 'No se pudo eliminar el jugador en disco.';
        header('Location: jugadores.php');
        exit;
    }
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
                                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($jugadorEditando['id'] ?? ''); ?>">
                                    <div class="col-md-6">
                                        <label class="form-label">Nombre</label>
                                        <input type="text" name="nombre" class="form-control" placeholder="Nombre" required value="<?php echo htmlspecialchars($jugadorEditando['nombre'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Apellido</label>
                                        <input type="text" name="apellido" class="form-control" placeholder="Apellido" required value="<?php echo htmlspecialchars($jugadorEditando['apellido'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Correo electrónico</label>
                                        <input type="email" name="correo" class="form-control" placeholder="correo@dominio.com" required value="<?php echo htmlspecialchars($jugadorEditando['correo'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Teléfono</label>
                                        <input type="text" name="telefono" class="form-control" placeholder="Opcional" value="<?php echo htmlspecialchars($jugadorEditando['telefono'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Fecha de nacimiento</label>
                                        <input type="date" name="nacimiento" class="form-control" value="<?php echo htmlspecialchars($jugadorEditando['nacimiento'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Club</label>
                                        <select name="club" class="form-control">
                                            <option value="">Sin club</option>
                                            <?php foreach ($clubes as $club): ?>
                                                <option value="<?php echo htmlspecialchars($club['nombre'] ?? ''); ?>" <?php echo (($jugadorEditando['club'] ?? '') === ($club['nombre'] ?? '')) ? 'selected' : ''; ?>><?php echo htmlspecialchars($club['nombre'] ?? ''); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Categoría</label>
                                        <select name="categoria" class="form-control">
                                            <?php $catActual = $jugadorEditando['categoria'] ?? ''; ?>
                                            <option value="" <?php echo $catActual === '' ? 'selected' : ''; ?>>Selecciona</option>
                                            <option <?php echo $catActual === 'Infantil' ? 'selected' : ''; ?>>Infantil</option>
                                            <option <?php echo $catActual === 'Juvenil' ? 'selected' : ''; ?>>Juvenil</option>
                                            <option <?php echo $catActual === 'Adulto' ? 'selected' : ''; ?>>Adulto</option>
                                            <option <?php echo $catActual === 'Senior' ? 'selected' : ''; ?>>Senior</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Ranking / NIV</label>
                                        <input type="text" name="ranking" class="form-control" placeholder="Ej. 1240" value="<?php echo htmlspecialchars($jugadorEditando['ranking'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Mano hábil</label>
                                        <select name="mano" class="form-control">
                                            <?php $manoActual = $jugadorEditando['mano'] ?? ''; ?>
                                            <option value="" <?php echo $manoActual === '' ? 'selected' : ''; ?>>Selecciona</option>
                                            <option <?php echo $manoActual === 'Derecha' ? 'selected' : ''; ?>>Derecha</option>
                                            <option <?php echo $manoActual === 'Izquierda' ? 'selected' : ''; ?>>Izquierda</option>
                                        </select>
                                    </div>
                                    <div class="col-12 text-end">
                                        <button type="submit" class="btn btn-primary"><?php echo $jugadorEditando ? 'Actualizar jugador' : 'Guardar jugador'; ?></button>
                                        <?php if ($jugadorEditando): ?>
                                            <a href="jugadores.php" class="btn btn-outline-secondary">Limpiar edición</a>
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
                                                        <div class="btn-group">
                                                            <a href="jugadores.php?action=editar&id=<?php echo urlencode($jugador['id']); ?>" class="btn btn-outline-primary btn-xs">Editar</a>
                                                            <a href="jugadores.php?action=eliminar&id=<?php echo urlencode($jugador['id']); ?>" class="btn btn-outline-danger btn-xs" onclick="return confirm('¿Eliminar jugador?');">Eliminar</a>
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
