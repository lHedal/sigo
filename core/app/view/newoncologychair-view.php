<section class="content">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Nuevo Sillón de Oncología</h3>
                </div>
                <form class="form-horizontal" method="post" action="index.php?action=addoncologychair">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="name" class="col-lg-2 control-label">Nombre*</label>
                            <div class="col-md-10">
                                <input type="text" name="name" id="name" class="form-control" 
                                       placeholder="Ej: Sillón 1, Sala A, etc." required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description" class="col-lg-2 control-label">Descripción</label>
                            <div class="col-md-10">
                                <textarea name="description" id="description" class="form-control" rows="3" 
                                          placeholder="Descripción del sillón, ubicación, características especiales, etc."></textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-lg-offset-2 col-lg-10">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="is_active" checked> Activo
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="box-footer">
                        <div class="form-group">
                            <div class="col-lg-offset-2 col-lg-10">
                                <button type="submit" class="btn btn-primary">Crear Sillón</button>
                                <a href="index.php?view=oncologychairs" class="btn btn-default">Cancelar</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
