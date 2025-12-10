<div class="d-flex justify-content-between align-items-center mb-3">
    <div class="page-heading">
        <span class="accent"></span>
        <div>
            <p class="text-muted mb-0 small">Panel de control</p>
            <h4 class="mb-0">Campeonatos</h4>
        </div>
    </div>
    <a href="/championships/create" class="btn btn-primary">Nuevo campeonato</a>
</div>
<?php if(isset($error)): ?>
    <div class="alert alert-warning"><?php echo $error; ?></div>
<?php endif; ?>
<table class="table table-striped align-middle">
    <thead>
        <tr>
            <th>Nombre</th><th>Fecha</th><th>Ubicación</th><th>Modalidad</th><th>Acciones</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach($championships as $c): ?>
        <tr>
            <td class="fw-semibold"><?php echo htmlspecialchars($c['name']); ?></td>
            <td><?php echo htmlspecialchars($c['start_date']); ?></td>
            <td><?php echo htmlspecialchars($c['location']); ?></td>
            <td><span class="badge bg-secondary"><?php echo htmlspecialchars($c['modality']); ?></span></td>
            <td>
                <form class="d-flex gap-2" method="POST" action="/championships/generate-groups">
                    <input type="hidden" name="championship_id" value="<?php echo $c['id']; ?>">
                    <input type="number" name="group_size" value="4" class="form-control form-control-sm" style="width:100px" min="3" max="6">
                    <button class="btn btn-sm btn-outline-primary">Generar grupos</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
