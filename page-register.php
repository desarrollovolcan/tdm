<?php
require_once __DIR__ . '/config/dz.php';
session_start();

$successMessage = $_SESSION['auth_success'] ?? null;
$errorMessage = $_SESSION['auth_error'] ?? null;
unset($_SESSION['auth_success'], $_SESSION['auth_error']);
?>
<!DOCTYPE html>
<html lang="es">

<head>
        <title><?php echo !empty($DexignZoneSettings['pagelevel'][$CurrentPage]['title']) ? $DexignZoneSettings['pagelevel'][$CurrentPage]['title'].' | ' : '' ; echo $DexignZoneSettings['site_level']['site_title'] ?></title>
        <?php include 'elements/meta.php';?>
        <!-- FAVICONS ICON -->
        <link rel="shortcut icon" type="image/png" href="<?php echo $DexignZoneSettings['site_level']['favicon']?>">
        <?php include 'elements/page-css.php'; ?>

</head>

<body>
    <div class="authincation d-flex flex-column flex-lg-row flex-column-fluid">
                <div class="login-aside text-center  d-flex flex-column flex-row-auto">
                        <div class="d-flex flex-column-auto flex-column pt-lg-40 pt-15">
                                <div class="text-center mb-4 pt-5">
                                        <a href="index.php"><img src="assets/images/logo-full.png" class="dark-login"  alt=""></a>
                                        <a href="index.php"><img src="assets/images/logo-full-white.png" class="light-login" alt=""></a>
                                </div>
                                <h3 class="mb-2">Crea tu cuenta</h3>
                                <p>Regístrate para administrar campeonatos, arbitrajes y jugadores.</p>
                        </div>
                        <div class="aside-image" style="background-image:url(assets/images/pic1.svg);"></div>
                </div>
                <div class="container flex-row-fluid d-flex flex-column justify-content-center position-relative overflow-hidden p-7 mx-auto">
                        <div class="d-flex justify-content-center h-100 align-items-center">
                                <div class="authincation-content style-2">
                                        <div class="row no-gutters">
                                                <div class="col-xl-12">
                                                        <div class="auth-form">
                                                                <div class="text-center d-block d-lg-none mb-4 pt-5">
                                                                        <a href="index.php"><img src="assets/images/logo-full.png" class="dark-login"  alt=""></a>
                                                                        <a href="index.php"><img src="assets/images/logo-full-white.png" class="light-login" alt=""></a>
                                                                </div>
                                                                <h4 class="text-center mb-4">Registrar nuevo usuario</h4>
                                                                <?php if ($successMessage): ?>
                                                                    <div class="alert alert-success" role="alert">
                                                                        <?php echo htmlspecialchars($successMessage); ?>
                                                                    </div>
                                                                <?php endif; ?>
                                                                <?php if ($errorMessage): ?>
                                                                    <div class="alert alert-danger" role="alert">
                                                                        <?php echo htmlspecialchars($errorMessage); ?>
                                                                    </div>
                                                                <?php endif; ?>
                                    <form action="auth.php" method="POST">
                                        <input type="hidden" name="action" value="register">
                                        <div class="mb-3">
                                            <label class="mb-1 form-label">Nombre completo</label>
                                            <input type="text" name="nombre" class="form-control" placeholder="Ej. Ana Pérez" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="mb-1 form-label">Usuario</label>
                                            <input type="text" name="username" class="form-control" placeholder="Nombre de usuario" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="mb-1 form-label">Correo electrónico</label>
                                            <input type="email" name="email" class="form-control" placeholder="correo@ejemplo.com" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="mb-1 form-label">Contraseña</label>
                                            <div class="position-relative">
                                                <input type="password" id="dz-password" name="password" class="form-control" placeholder="Crea una contraseña" required>
                                                <span class="show-pass eye">
                                                    <i class="fa fa-eye-slash"></i>
                                                    <i class="fa fa-eye"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="mb-1 form-label">Tipo de usuario</label>
                                            <select name="rol" class="form-control" required>
                                                <option value="">Selecciona una opción</option>
                                                <option value="Visitante">Visitante</option>
                                                <option value="Arbitro">Árbitro</option>
                                                <option value="Jugador">Jugador</option>
                                                <option value="Administrador">Administrador</option>
                                            </select>
                                        </div>
                                        <div class="text-center mt-4">
                                            <button type="submit" class="btn btn-primary btn-block">Crear cuenta</button>
                                        </div>
                                    </form>
                                    <div class="new-account mt-3">
                                        <p>¿Ya tienes cuenta? <a class="text-primary" href="page-login.php">Inicia sesión</a></p>
                                    </div>
                            </div>
                                                </div>
                                        </div>
                                </div>
                        </div>
                </div>
        </div>

<!--**********************************
        Scripts
***********************************-->
<!-- Required vendors -->
<?php include 'elements/page-js.php'; ?>


</body>
</html>
