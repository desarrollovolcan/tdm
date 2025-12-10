<?php
require_once __DIR__ . '/auth-guard.php';
require_once __DIR__ . '/config/dz.php';
require_once __DIR__ . '/helpers.php';

$campeonatos = load_json('campeonatos.json');
$jugadores = load_json('jugadores.json');
$clubes = load_json('clubes.json');
$equipos = load_json('equipos.json');
$partidos = load_json('partidos.json');
$inscripciones = load_json('inscripciones.json');
$programaciones = load_json('programacion.json');
$cuadros = load_json('cuadros.json');
$rankings = load_json('rankings.json');

function latest(array $items, int $limit = 5): array
{
    usort($items, fn($a, $b) => strcmp($b['created_at'] ?? '', $a['created_at'] ?? ''));
    return array_slice($items, 0, $limit);
}

$ultimosPartidos = latest($partidos);
$ultimosJugadores = latest($jugadores);
?>
<!DOCTYPE html>
<html lang="es">

<head>
        <title>Panel principal</title>
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
                        <li class="breadcrumb-item active"><a href="javascript:void(0)">Panel</a></li>
                    </ol>
                </div>

                <div class="row">
                    <div class="col-xl-3 col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div>
                                        <p class="mb-1">Campeonatos</p>
                                        <h3 class="mb-0"><?php echo count($campeonatos); ?></h3>
                                    </div>
                                    <div class="ms-auto bgl-primary rounded p-3"><i class="flaticon-trophy fs-36 text-primary"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div>
                                        <p class="mb-1">Jugadores</p>
                                        <h3 class="mb-0"><?php echo count($jugadores); ?></h3>
                                    </div>
                                    <div class="ms-auto bgl-success rounded p-3"><i class="flaticon-user fs-36 text-success"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div>
                                        <p class="mb-1">Clubes</p>
                                        <h3 class="mb-0"><?php echo count($clubes); ?></h3>
                                    </div>
                                    <div class="ms-auto bgl-warning rounded p-3"><i class="flaticon-suitcase fs-36 text-warning"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div>
                                        <p class="mb-1">Partidos programados</p>
                                        <h3 class="mb-0"><?php echo count($programaciones); ?></h3>
                                    </div>
                                    <div class="ms-auto bgl-info rounded p-3"><i class="flaticon-calendar fs-36 text-info"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-8">
                        <div class="card">
                            <div class="card-header d-sm-flex d-block border-0 pb-0">
                                <div>
                                    <h4 class="fs-20 mb-1">Próximos partidos</h4>
                                    <span class="fs-14 text-muted">Últimos registros capturados</span>
                                </div>
                                <a class="btn btn-primary btn-sm ms-auto" href="programacion-partidos.php">Ver programación</a>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped align-middle">
                                        <thead>
                                            <tr>
                                                <th>Campeonato</th>
                                                <th>Ronda</th>
                                                <th>Fecha</th>
                                                <th>Mesa</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($programaciones)): ?>
                                                <tr><td colspan="4" class="text-center text-muted">No hay programación registrada</td></tr>
                                            <?php else: ?>
                                                <?php foreach (latest($programaciones) as $item): ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($item['campeonato'] ?? ''); ?></td>
                                                        <td><?php echo htmlspecialchars($item['ronda'] ?? ''); ?></td>
                                                        <td><?php echo htmlspecialchars($item['fecha'] ?? ''); ?></td>
                                                        <td><?php echo htmlspecialchars($item['mesa'] ?? ''); ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4">
                        <div class="card">
                            <div class="card-header border-0 pb-0">
                                <div>
                                    <h4 class="fs-20 mb-1">Inscripciones</h4>
                                    <span class="fs-14 text-muted">Resumen por estado</span>
                                </div>
                            </div>
                            <div class="card-body">
                                <?php
                                $estados = ['Pendiente' => 0, 'Aprobada' => 0, 'Rechazada' => 0];
                                foreach ($inscripciones as $inscripcion) {
                                    $estado = $inscripcion['estado'] ?? 'Pendiente';
                                    if (!isset($estados[$estado])) {
                                        $estados[$estado] = 0;
                                    }
                                    $estados[$estado]++;
                                }
                                ?>
                                <ul class="list-group list-group-flush">
                                    <?php foreach ($estados as $estado => $total): ?>
                                        <li class="list-group-item d-flex justify-content-between">
                                            <span><?php echo htmlspecialchars($estado); ?></span>
                                            <span class="badge bg-primary rounded-pill"><?php echo $total; ?></span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header border-0 pb-0">
                                <div>
                                    <h4 class="fs-20 mb-1">Últimos jugadores</h4>
                                    <span class="fs-14 text-muted">Altas recientes</span>
                                </div>
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled">
                                    <?php if (empty($ultimosJugadores)): ?>
                                        <li class="text-muted">Aún no hay jugadores registrados.</li>
                                    <?php else: ?>
                                        <?php foreach ($ultimosJugadores as $jugador): ?>
                                            <li class="mb-3 d-flex align-items-center">
                                                <div class="me-3 bgl-primary rounded-circle" style="width:38px;height:38px;display:flex;align-items:center;justify-content:center;">
                                                    <i class="flaticon-user"></i>
                                                </div>
                                                <div>
                                                    <strong><?php echo htmlspecialchars(($jugador['nombre'] ?? '') . ' ' . ($jugador['apellido'] ?? '')); ?></strong><br>
                                                    <small class="text-muted"><?php echo htmlspecialchars($jugador['categoria'] ?? ''); ?></small>
                                                </div>
                                            </li>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header border-0 pb-0">
                                <div>
                                    <h4 class="fs-20 mb-1">Últimos resultados</h4>
                                    <span class="fs-14 text-muted">Partidos capturados</span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped">
                                        <thead>
                                            <tr>
                                                <th>Campeonato</th>
                                                <th>Jugador A</th>
                                                <th>Jugador B</th>
                                                <th>Marcador</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($ultimosPartidos)): ?>
                                                <tr><td colspan="4" class="text-center text-muted">Sin resultados cargados</td></tr>
                                            <?php else: ?>
                                                <?php foreach ($ultimosPartidos as $partido): ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($partido['campeonato'] ?? ''); ?></td>
                                                        <td><?php echo htmlspecialchars($partido['jugador_a'] ?? ''); ?></td>
                                                        <td><?php echo htmlspecialchars($partido['jugador_b'] ?? ''); ?></td>
                                                        <td><?php echo htmlspecialchars($partido['marcador'] ?? ''); ?></td>
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

                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-header border-0 pb-0">
                                <div>
                                    <h4 class="fs-20 mb-1">Resumen rápido</h4>
                                    <span class="fs-14 text-muted">Estado general del sistema</span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <div class="p-3 rounded bg-light">
                                            <small class="text-muted">Equipos</small>
                                            <h4 class="mb-0"><?php echo count($equipos); ?></h4>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="p-3 rounded bg-light">
                                            <small class="text-muted">Cuadros</small>
                                            <h4 class="mb-0"><?php echo count($cuadros); ?></h4>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="p-3 rounded bg-light">
                                            <small class="text-muted">Rankings</small>
                                            <h4 class="mb-0"><?php echo count($rankings); ?></h4>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="p-3 rounded bg-light">
                                            <small class="text-muted">Inscripciones</small>
                                            <h4 class="mb-0"><?php echo count($inscripciones); ?></h4>
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
