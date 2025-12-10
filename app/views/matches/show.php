<?php if(!$match): ?>
    <div class="alert alert-warning">Partido no encontrado</div>
<?php else: ?>
    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title">Registro de resultado</h5>
            <?php if(isset($success)): ?><div class="alert alert-success">Resultado guardado</div><?php endif; ?>
            <?php if(isset($error)): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>
            <p class="lead">
                <strong><?php echo htmlspecialchars($match['player1_name']); ?></strong>
                vs
                <strong><?php echo htmlspecialchars($match['player2_name']); ?></strong>
                <span class="badge bg-secondary ms-2">#<?php echo $match['id']; ?></span>
            </p>
            <form method="POST" action="/matches/result">
                <input type="hidden" name="match_id" value="<?php echo $match['id']; ?>">
                <?php for($i=1; $i<=7; $i++): ?>
                <div class="row mb-2">
                    <div class="col-6">
                        <input type="number" name="set[<?php echo $i; ?>][p1]" class="form-control" placeholder="Puntos Jugador A">
                    </div>
                    <div class="col-6">
                        <input type="number" name="set[<?php echo $i; ?>][p2]" class="form-control" placeholder="Puntos Jugador B">
                    </div>
                </div>
                <?php endfor; ?>
                <button class="btn btn-primary">Guardar resultado</button>
            </form>
            <p class="small text-muted mt-3">Validación ITTF: sets a 11 con diferencia de 2 puntos (tie-break extendido permitido).</p>
        </div>
    </div>
<?php endif; ?>
