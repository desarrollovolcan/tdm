<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow-soft border-0">
            <div class="card-body p-4">
                <div class="page-heading mb-3">
                    <span class="accent"></span>
                    <div>
                        <p class="text-muted mb-0 small">Federación Tenis de Mesa</p>
                        <h4 class="mb-0">Acceso oficial</h4>
                    </div>
                </div>
                <?php if(isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                <form method="POST" action="/login">
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contraseña</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button class="btn btn-primary w-100">Ingresar</button>
                </form>
                <p class="small text-muted mt-3">Roles: administrador, árbitro y jugador</p>
            </div>
        </div>
    </div>
</div>
