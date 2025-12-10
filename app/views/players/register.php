<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Inscripción oficial</h5>
                <?php if(isset($success)): ?>
                    <div class="alert alert-success">Inscripción recibida. Será validada por la organización.</div>
                <?php endif; ?>
                <form method="POST" action="/register">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nombre completo</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">RUT / Documento</label>
                            <input type="text" name="document" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Fecha de nacimiento</label>
                            <input type="date" name="birthdate" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Club / Asociación</label>
                            <input type="text" name="club" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Categoría</label>
                            <select name="category" class="form-select">
                                <option value="adulto">Adulto</option>
                                <option value="juvenil">Juvenil</option>
                                <option value="infantil">Infantil</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Sexo</label>
                            <select name="gender" class="form-select">
                                <option value="masculino">Masculino</option>
                                <option value="femenino">Femenino</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Correo electrónico</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="phone" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Ranking (opcional)</label>
                            <input type="number" name="ranking" class="form-control" placeholder="Se asignará provisorio si se deja vacío">
                        </div>
                    </div>
                    <button class="btn btn-primary mt-3">Enviar inscripción</button>
                </form>
            </div>
        </div>
    </div>
</div>
