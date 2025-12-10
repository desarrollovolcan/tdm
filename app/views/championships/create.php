<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title mb-3">Nuevo campeonato</h5>
                <form method="POST" action="/championships">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nombre oficial</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Fecha</label>
                            <input type="date" name="start_date" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Ubicación</label>
                            <input type="text" name="location" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Categoría</label>
                            <select name="category" class="form-select" required>
                                <option value="adulto">Adulto</option>
                                <option value="juvenil">Juvenil</option>
                                <option value="infantil">Infantil</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Modalidad</label>
                            <select name="modality" class="form-select">
                                <option value="individual">Individual</option>
                                <option value="dobles">Dobles</option>
                                <option value="mixto">Mixto</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Participantes esperados</label>
                            <input type="number" name="participants" class="form-control" min="2" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Sets (mejor de)</label>
                            <select name="best_of_sets" class="form-select">
                                <option value="5">5</option>
                                <option value="7">7</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Puntos por set</label>
                            <input type="number" name="set_points" value="11" class="form-control" readonly>
                        </div>
                    </div>
                    <button class="btn btn-primary mt-3">Guardar campeonato</button>
                </form>
            </div>
        </div>
    </div>
</div>
