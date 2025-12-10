<div class="d-flex justify-content-between align-items-center mb-3">
    <div class="page-heading">
        <span class="accent"></span>
        <div>
            <p class="text-muted mb-0 small">Estado general</p>
            <h4 class="mb-0">Dashboard</h4>
        </div>
    </div>
</div>
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card text-bg-primary h-100">
            <div class="card-body">
                <h6 class="card-title">Campeonatos</h6>
                <p class="display-6"><?php echo $metrics['championships']; ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-bg-dark h-100">
            <div class="card-body">
                <h6 class="card-title">Jugadores inscritos</h6>
                <p class="display-6"><?php echo $metrics['players']; ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-bg-success h-100">
            <div class="card-body">
                <h6 class="card-title">Partidos jugados</h6>
                <p class="display-6"><?php echo $metrics['matches_played']; ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-bg-warning h-100">
            <div class="card-body">
                <h6 class="card-title">Partidos pendientes</h6>
                <p class="display-6"><?php echo $metrics['matches_pending']; ?></p>
            </div>
        </div>
    </div>
</div>
<div class="card shadow-soft border-0">
    <div class="card-body">
        <h5 class="card-title">Resumen de campeonatos</h5>
        <table class="table table-sm">
            <thead><tr><th>Nombre</th><th>Fecha</th><th>Ubicación</th><th>Formato</th></tr></thead>
            <tbody>
                <?php foreach($championships as $c): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($c['name']); ?></td>
                        <td><?php echo htmlspecialchars($c['start_date']); ?></td>
                        <td><?php echo htmlspecialchars($c['location']); ?></td>
                        <td><?php echo htmlspecialchars($c['format']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
