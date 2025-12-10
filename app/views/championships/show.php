<div class="d-flex justify-content-between align-items-center mb-3">
    <div class="page-heading">
        <span class="accent"></span>
        <div>
            <p class="text-muted mb-0 small">Detalle</p>
            <h4 class="mb-0"><?php echo htmlspecialchars($championship['name']); ?></h4>
        </div>
    </div>
    <div class="d-flex gap-2">
        <a class="btn btn-outline-primary" href="/championships/<?php echo $championship['id']; ?>/book/excel">Descargar Excel</a>
        <a class="btn btn-primary" href="/championships/<?php echo $championship['id']; ?>/book/pdf">Descargar PDF</a>
    </div>
</div>
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card shadow-soft">
            <div class="card-body">
                <h6 class="text-uppercase text-muted">Campeonato</h6>
                <p class="mb-1 fw-semibold"><?php echo htmlspecialchars($championship['location']); ?></p>
                <p class="mb-1">Fecha: <?php echo htmlspecialchars($championship['start_date']); ?></p>
                <div class="d-flex gap-2">
                    <span class="pill">Categoría: <?php echo htmlspecialchars($championship['category']); ?></span>
                    <span class="pill">Modalidad: <?php echo htmlspecialchars($championship['modality']); ?></span>
                    <span class="pill">Sets: Mejor de <?php echo htmlspecialchars($championship['best_of_sets']); ?></span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card shadow-soft">
            <div class="card-body">
                <h6 class="text-uppercase text-muted">Institución</h6>
                <p class="fw-semibold mb-1"><?php echo htmlspecialchars($institution['name']); ?></p>
                <p class="mb-1">Correo: <?php echo htmlspecialchars($institution['contact_email'] ?? ''); ?></p>
                <p class="mb-0">Sitio: <?php echo htmlspecialchars($institution['website'] ?? ''); ?></p>
            </div>
        </div>
    </div>
</div>
<div class="card shadow-soft mb-4">
    <div class="card-body">
        <h5 class="card-title">Jugadores registrados (<?php echo count($players); ?>)</h5>
        <table class="table table-sm align-middle">
            <thead><tr><th>Nombre</th><th>Club</th><th>Ranking</th><th>Contacto</th></tr></thead>
            <tbody>
                <?php foreach($players as $p): ?>
                    <tr>
                        <td class="fw-semibold"><?php echo htmlspecialchars($p['name']); ?></td>
                        <td><?php echo htmlspecialchars($p['club']); ?></td>
                        <td><span class="pill"><?php echo htmlspecialchars($p['ranking'] ?? 'Provisorio'); ?></span></td>
                        <td class="text-muted small"><?php echo htmlspecialchars($p['email']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<div class="row g-3">
    <div class="col-lg-6">
        <div class="card shadow-soft h-100">
            <div class="card-body">
                <h5 class="card-title">Grupos</h5>
                <?php if(empty($groups)): ?>
                    <p class="text-muted">Aún no se generan grupos.</p>
                <?php endif; ?>
                <div class="row g-2">
                <?php foreach($groups as $group): ?>
                    <div class="col-md-6">
                        <div class="border rounded-3 p-3 h-100">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0">Grupo <?php echo htmlspecialchars($group['name']); ?></h6>
                                <span class="badge bg-secondary">ITTF</span>
                            </div>
                            <ul class="mb-0 list-unstyled">
                                <?php foreach($group['players'] as $gp): ?>
                                <li class="d-flex justify-content-between">
                                    <span><?php echo htmlspecialchars($gp['name']); ?></span>
                                    <span class="text-muted small">Rank: <?php echo htmlspecialchars($gp['ranking'] ?? 'Prov.'); ?></span>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card shadow-soft h-100">
            <div class="card-body">
                <h5 class="card-title">Partidos</h5>
                <table class="table table-sm">
                    <thead><tr><th>Encuentro</th><th>Etapa</th><th>Marcador</th></tr></thead>
                    <tbody>
                        <?php foreach($matches as $m): ?>
                            <tr>
                                <td class="fw-semibold"><?php echo htmlspecialchars($m['player1_name'] . ' vs ' . $m['player2_name']); ?></td>
                                <td><span class="badge badge-stage"><?php echo htmlspecialchars($m['stage']); ?></span></td>
                                <td class="text-muted small">
                                    <?php if(!empty($m['sets_json'])): ?>
                                        <?php $sets = json_decode($m['sets_json'], true); $scores = array_map(fn($s) => $s[0].'-'.$s[1], $sets); echo implode(', ', $scores); ?>
                                    <?php else: ?>
                                        Pendiente
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
