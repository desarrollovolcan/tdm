<div class="d-flex justify-content-between align-items-center mb-3">
    <div class="page-heading">
        <span class="accent"></span>
        <div>
            <p class="text-muted mb-0 small">Identidad organizadora</p>
            <h4 class="mb-0">Institución</h4>
        </div>
    </div>
</div>
<?php if(!empty($success)): ?>
    <div class="alert alert-success">Datos de institución actualizados.</div>
<?php endif; ?>
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card shadow-soft">
            <div class="card-body">
                <form method="POST" action="/institution" enctype="multipart/form-data" class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Nombre oficial *</label>
                        <input type="text" name="name" class="form-control" required value="<?php echo htmlspecialchars($institution['name']); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Correo de contacto</label>
                        <input type="email" name="contact_email" class="form-control" value="<?php echo htmlspecialchars($institution['contact_email'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Teléfono</label>
                        <input type="text" name="contact_phone" class="form-control" value="<?php echo htmlspecialchars($institution['contact_phone'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Sitio web</label>
                        <input type="text" name="website" class="form-control" value="<?php echo htmlspecialchars($institution['website'] ?? ''); ?>" placeholder="https://">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Dirección</label>
                        <input type="text" name="address" class="form-control" value="<?php echo htmlspecialchars($institution['address'] ?? ''); ?>">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Logo (PNG o JPG)</label>
                        <input type="file" name="logo" class="form-control" accept="image/png, image/jpeg">
                    </div>
                    <div class="col-12 d-flex justify-content-between align-items-center">
                        <div>
                            <?php if(!empty($institution['logo_path'])): ?>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="logo-chip">
                                        <img src="<?php echo htmlspecialchars($institution['logo_path']); ?>" alt="Logo" style="max-width:32px; max-height:32px;">
                                    </div>
                                    <span class="text-muted small">Logo actual</span>
                                </div>
                            <?php else: ?>
                                <span class="text-muted small">No hay logo cargado.</span>
                            <?php endif; ?>
                        </div>
                        <button class="btn btn-primary">Guardar cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card h-100 border-0 shadow-soft hero-card">
            <div class="card-body">
                <p class="text-uppercase small fw-bold mb-1">Impacto visual</p>
                <h5 class="fw-bold">El logo y los datos de tu institución se mostrarán en:</h5>
                <ul class="mb-0">
                    <li>Navbar del panel</li>
                    <li>Libro oficial (PDF/Excel)</li>
                    <li>Portada de campeonatos</li>
                </ul>
            </div>
        </div>
    </div>
</div>
