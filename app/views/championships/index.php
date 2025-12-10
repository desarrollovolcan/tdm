<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Campeonatos</h3>
    <a href="/championships/create" class="btn btn-primary">Nuevo campeonato</a>
</div>
<table class="table table-striped">
    <thead>
        <tr>
            <th>Nombre</th><th>Fecha</th><th>Ubicación</th><th>Modalidad</th><th>Acciones</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach($championships as $c): ?>
        <tr>
            <td><?php echo htmlspecialchars($c['name']); ?></td>
            <td><?php echo htmlspecialchars($c['start_date']); ?></td>
            <td><?php echo htmlspecialchars($c['location']); ?></td>
            <td><?php echo htmlspecialchars($c['modality']); ?></td>
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
