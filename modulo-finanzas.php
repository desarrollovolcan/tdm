<?php
         require_once __DIR__ . '/config/dz.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
        <title>Planes y cobranzas | <?php echo $DexignZoneSettings['site_level']['site_title']; ?></title>
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
                                        <li class="breadcrumb-item"><a href="panel-principal.php">Inicio</a></li>
                                        <li class="breadcrumb-item active">Planes y cobranzas</li>
                                </ol>
                        </div>
                        <div class="row">
                                <div class="col-xl-12">
                                        <div class="card">
                                                <div class="card-header">
                                                        <h4 class="card-title mb-0">Planes, membresías y facturación</h4>
                                                </div>
                                                <div class="card-body">
                                                        <p class="mb-4">Administra planes de entrenamiento, cuotas del club, productos adicionales y el ciclo completo de facturación y pagos.</p>
                                                        <div class="row g-4">
                                                                <div class="col-lg-6">
                                                                        <div class="h-100 p-4 bg-light rounded">
                                                                                <h5>Planes y productos</h5>
                                                                                <ul class="mb-0">
                                                                                        <li>Planes grupales, individuales y mixtos con sesiones incluidas o períodos definidos.</li>
                                                                                        <li>Membresías mensuales, planes especiales y servicios adicionales (videoanálisis, clínicas, equipamiento).</li>
                                                                                        <li>Restricciones por categoría, horarios o sede.</li>
                                                                                </ul>
                                                                        </div>
                                                                </div>
                                                                <div class="col-lg-6">
                                                                        <div class="h-100 p-4 bg-light rounded">
                                                                                <h5>Ciclo de cobranzas</h5>
                                                                                <ul class="mb-0">
                                                                                        <li>Órdenes de cobro por cuotas, planes o productos.</li>
                                                                                        <li>Estados: pendiente, pagado, vencido, en negociación.</li>
                                                                                        <li>Registro de pagos con método, fecha, descuentos o recargos.</li>
                                                                                </ul>
                                                                        </div>
                                                                </div>
                                                                <div class="col-lg-6">
                                                                        <div class="h-100 p-4 bg-light rounded">
                                                                                <h5>Notificaciones y recordatorios</h5>
                                                                                <ul class="mb-0">
                                                                                        <li>Alertas por vencimiento vía email, WhatsApp o notificación in-app.</li>
                                                                                        <li>Bloqueo opcional de reservas o participación por morosidad.</li>
                                                                                        <li>Comprobantes descargables en PDF y notificación a apoderados.</li>
                                                                                </ul>
                                                                        </div>
                                                                </div>
                                                                <div class="col-lg-6">
                                                                        <div class="h-100 p-4 bg-light rounded">
                                                                                <h5>Reportes financieros</h5>
                                                                                <ul class="mb-0">
                                                                                        <li>Ingresos por período, plan y entrenador.</li>
                                                                                        <li>Deudores, aging y seguimiento de promesas de pago.</li>
                                                                                        <li>Integración preparada para pasarelas de pago.</li>
                                                                                </ul>
                                                                        </div>
                                                                </div>
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
</html>
