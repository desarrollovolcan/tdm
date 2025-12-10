<?php
require_once __DIR__ . '/auth-guard.php';
require_once __DIR__ . '/config/dz.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/auth.php';

$roles = availableRoles();
$users = loadUsers();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $rol = $_POST['rol'] ?? '';

    if ($nombre === '' || $username === '' || $email === '' || $rol === '') {
        $_SESSION['usuarios_error'] = 'Todos los campos son obligatorios.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['usuarios_error'] = 'El correo no es válido.';
    } elseif (!in_array($rol, $roles, true)) {
        $_SESSION['usuarios_error'] = 'Rol inválido.';
    } else {
        foreach ($users as $user) {
            if (strcasecmp($user['username'] ?? '', $username) === 0 || strcasecmp($user['email'] ?? '', $email) === 0) {
                $_SESSION['usuarios_error'] = 'El usuario o correo ya existe.';
                header('Location: usuarios.php');
                exit;
            }
        }

        $users[] = [
            'id' => count($users) + 1,
            'nombre' => $nombre,
            'username' => $username,
            'email' => $email,
            'password_hash' => password_hash($password ?: 'temporal123', PASSWORD_BCRYPT, HASH_OPTIONS),
            'rol' => $rol,
            'created_at' => date(DATE_ATOM),
        ];

        if (saveUsers($users)) {
            $_SESSION['usuarios_success'] = 'Usuario creado correctamente.';
        } else {
            $_SESSION['usuarios_error'] = 'No fue posible guardar el usuario. Verifica la conexión a la base de datos.';
        }
    }

    header('Location: usuarios.php');
    exit;
}

if (($_GET['action'] ?? '') === 'eliminar' && isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    $users = array_values(array_filter($users, fn($user) => ($user['id'] ?? 0) !== $id));
    if (saveUsers($users)) {
        $_SESSION['usuarios_success'] = 'Usuario eliminado correctamente.';
    } else {
        $_SESSION['usuarios_error'] = 'No se pudo eliminar el usuario en el almacenamiento.';
    }
    header('Location: usuarios.php');
    exit;
}

$successMessage = flash('usuarios_success');
$errorMessage = flash('usuarios_error');
?>
<!DOCTYPE html>
<html lang="es">

<head>
        <title>Usuarios</title>
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
                                                                        <h4 class="fs-20 mb-1">Crear usuario</h4>
                                                                        <span class="fs-14 text-muted">Control de acceso</span>
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
                                                                <div class="col-12">
                                                                    <label class="form-label">Nombre</label>
                                                                    <input type="text" name="nombre" class="form-control" required>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label class="form-label">Usuario</label>
                                                                    <input type="text" name="username" class="form-control" required>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label class="form-label">Correo</label>
                                                                    <input type="email" name="email" class="form-control" required>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label class="form-label">Contraseña</label>
                                                                    <input type="text" name="password" class="form-control" placeholder="Si la dejas vacía se asignará temporal123">
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label class="form-label">Rol</label>
                                                                    <select name="rol" class="form-control" required>
                                                                        <option value="">Selecciona</option>
                                                                        <?php foreach ($roles as $rol): ?>
                                                                            <option value="<?php echo htmlspecialchars($rol); ?>"><?php echo htmlspecialchars($rol); ?></option>
                                                                        <?php endforeach; ?>
                                                                    </select>
                                                                </div>
                                                                <div class="col-12 text-end">
                                                                    <button type="submit" class="btn btn-primary">Guardar usuario</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                </div>
                                        </div>
                                        <div class="col-xl-8">
                                                <div class="card">
                                                        <div class="card-header border-0 pb-0">
                                                            <div>
                                                                <h4 class="fs-20 mb-1">Usuarios registrados</h4>
                                                                <span class="fs-14 text-muted">Roles y acceso</span>
                                                            </div>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="table-responsive">
                                                                <table class="table table-striped align-middle">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Nombre</th>
                                                                            <th>Usuario</th>
                                                                            <th>Correo</th>
                                                                            <th>Rol</th>
                                                                            <th>Creado</th>
                                                                            <th></th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <?php if (empty($users)): ?>
                                                                            <tr><td colspan="6" class="text-center text-muted">Sin registros</td></tr>
                                                                        <?php else: ?>
                                                                            <?php foreach ($users as $user): ?>
                                                                                <tr>
                                                                                    <td><?php echo htmlspecialchars($user['nombre'] ?? ''); ?></td>
                                                                                    <td><?php echo htmlspecialchars($user['username'] ?? ''); ?></td>
                                                                                    <td><?php echo htmlspecialchars($user['email'] ?? ''); ?></td>
                                                                                    <td><?php echo htmlspecialchars($user['rol'] ?? ''); ?></td>
                                                                                    <td><?php echo htmlspecialchars(substr($user['created_at'] ?? '', 0, 10)); ?></td>
                                                                                    <td class="text-end">
                                                                                        <?php if (($user['username'] ?? '') !== 'eisla'): ?>
                                                                                            <a href="?action=eliminar&id=<?php echo urlencode((string) $user['id']); ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar usuario?');">Eliminar</a>
                                                                                        <?php else: ?>
                                                                                            <span class="badge bg-success">Root</span>
                                                                                        <?php endif; ?>
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
