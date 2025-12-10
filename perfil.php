<?php
require_once __DIR__ . '/auth-guard.php';
require_once __DIR__ . '/config/dz.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/auth.php';

$currentSession = $_SESSION['user'] ?? null;
if (!$currentSession) {
    header('Location: page-login.php');
    exit;
}

$users = loadUsers();
$user = findUserById($users, (int) ($currentSession['id'] ?? 0));

if (!$user) {
    $_SESSION['auth_error'] = 'No se encontró el perfil solicitado. Inicia sesión de nuevo.';
    header('Location: page-login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $passwordConfirm = $_POST['password_confirm'] ?? '';

    if ($nombre === '' || $email === '') {
        flash('perfil_error', 'Nombre y correo son obligatorios.');
        header('Location: perfil.php');
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        flash('perfil_error', 'Correo electrónico inválido.');
        header('Location: perfil.php');
        exit;
    }

    foreach ($users as $existing) {
        if ((int) ($existing['id'] ?? 0) !== (int) $user['id'] && strcasecmp($existing['email'] ?? '', $email) === 0) {
            flash('perfil_error', 'Ya existe un usuario con ese correo.');
            header('Location: perfil.php');
            exit;
        }
    }

    foreach ($users as &$stored) {
        if ((int) ($stored['id'] ?? 0) === (int) $user['id']) {
            $stored['nombre'] = $nombre;
            $stored['email'] = $email;

            if ($password !== '' || $passwordConfirm !== '') {
                if ($password !== $passwordConfirm) {
                    flash('perfil_error', 'Las contraseñas no coinciden.');
                    header('Location: perfil.php');
                    exit;
                }

                $stored['password_hash'] = password_hash($password, PASSWORD_BCRYPT, HASH_OPTIONS);
            }

            break;
        }
    }
    unset($stored);

    saveUsers($users);
    $_SESSION['user']['nombre'] = $nombre;
    $_SESSION['user']['email'] = $email;

    flash('perfil_success', 'Perfil actualizado correctamente.');
    header('Location: perfil.php');
    exit;
}

$successMessage = flash('perfil_success');
$errorMessage = flash('perfil_error');
?>
<!DOCTYPE html>
<html lang="es">

<head>
        <title>Mi perfil | Sistema de Campeonatos</title>
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
                        <li class="breadcrumb-item active"><a href="javascript:void(0)">Mi perfil</a></li>
                    </ol>
                </div>

                <div class="row">
                    <div class="col-xl-8 col-lg-10">
                        <div class="card">
                            <div class="card-header border-0 pb-0">
                                <div>
                                    <h4 class="fs-20 mb-1">Perfil de usuario</h4>
                                    <span class="fs-14 text-muted">Actualiza tus datos de acceso y contacto.</span>
                                </div>
                            </div>
                            <div class="card-body">
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

                                <form method="post" class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Nombre completo</label>
                                        <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($user['nombre'] ?? ''); ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Usuario</label>
                                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>" disabled>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Correo electrónico</label>
        <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Rol</label>
                                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['rol'] ?? ''); ?>" disabled>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Nueva contraseña</label>
                                        <input type="password" name="password" class="form-control" placeholder="Dejar en blanco si no cambia">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Confirmar contraseña</label>
                                        <input type="password" name="password_confirm" class="form-control" placeholder="Repetir contraseña">
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary">Guardar cambios</button>
                                        <a href="panel-principal.php" class="btn btn-outline-secondary ms-2">Volver al panel</a>
                                    </div>
                                </form>
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
