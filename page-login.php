<?php
require_once __DIR__ . '/config/dz.php';
session_start();

if (!empty($_SESSION['user'])) {
    header('Location: panel-principal.php');
    exit;
}

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
                <div class="login-aside text-center d-none d-sm-flex flex-column flex-row-auto">
                        <div class="d-flex flex-column-auto flex-column pt-lg-40 pt-15">
                                <div class="text-center mb-4 pt-5">
                                        <a href="index.php"><img src="assets/images/logo-full.png" class="dark-login"  alt=""></a>
                                        <a href="index.php"><img src="assets/images/logo-full-white.png" class="light-login" alt=""></a>
                                </div>
                                <h3 class="mb-2">Gestiona tus campeonatos</h3>
                                <p>Administra inscripciones, árbitros y jugadores desde un solo lugar.</p>
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

                                                                <h4 class="text-center mb-4">Inicia sesión</h4>
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
                                                                        <input type="hidden" name="action" value="login">
                                                                        <div class="mb-3">
                                                                                <label class="mb-1 form-label">Usuario o correo</label>
                                                                                <input type="text" name="identifier" class="form-control" placeholder="eisla o correo@example.com" required>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                                <label class="mb-1 form-label">Contraseña</label>
                                                                                <div class="position-relative">
                                                                                        <input type="password" id="dz-password" name="password" class="form-control" placeholder="Ingresa tu contraseña" required>
                                                                                        <span class="show-pass eye">
                                                                                                <i class="fa fa-eye-slash"></i>
                                                                                                <i class="fa fa-eye"></i>
                                                                                        </span>
                                                                                </div>
                                                                        </div>
                                                                        <div class="form-row d-flex justify-content-between mt-4 mb-2">
                                                                                <div class="mb-3">
                                                                                   <div class="form-check custom-checkbox ms-1">
                                                                                                <input type="checkbox" class="form-check-input" id="basic_checkbox_1">
                                                                                                <label class="form-check-label" for="basic_checkbox_1">Recordar sesión</label>
                                                                                        </div>
                                                                                </div>
                                                                                <div class="mb-3">
                                                                                        <a href="page-forgot-password.php">¿Olvidaste tu contraseña?</a>
                                                                                </div>
                                                                        </div>
                                                                        <div class="text-center">
                                                                                <button type="submit" class="btn btn-primary btn-block">Entrar</button>
                                                                        </div>
                                                                </form>
                                                                <div class="new-account mt-3">
                                                                        <p>¿Aún no tienes cuenta? <a class="text-primary" href="./page-register.php">Crear usuario</a></p>
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
