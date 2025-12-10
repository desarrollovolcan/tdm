<div class="d-flex justify-content-between align-items-center mb-3">
    <div class="page-heading">
        <span class="accent"></span>
        <div>
            <p class="text-muted mb-0 small">Panel técnico</p>
            <h4 class="mb-0">Jugadores</h4>
        </div>
    </div>
</div>
<form method="POST" action="/players/ranking">
<table class="table table-hover align-middle">
    <thead>
        <tr><th>Nombre</th><th>Club</th><th>Categoría</th><th>Sexo</th><th>Ranking</th></tr>
    </thead>
    <tbody>
        <?php foreach($players as $p): ?>
            <tr>
                <td class="fw-semibold"><?php echo htmlspecialchars($p['name']); ?></td>
                <td><?php echo htmlspecialchars($p['club']); ?></td>
                <td><?php echo htmlspecialchars(ucfirst($p['category'])); ?></td>
                <td><?php echo htmlspecialchars(ucfirst($p['gender'])); ?></td>
                <td style="max-width:120px">
                    <input type="number" name="ranking[<?php echo $p['id']; ?>]" class="form-control form-control-sm" value="<?php echo $p['ranking']; ?>">
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<button class="btn btn-primary">Actualizar rankings</button>
</form>
