<?php
// Vista de login para pacientes
?>
<div class="row">
    <div class="col-md-4 col-md-offset-4">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4><i class="fa fa-user"></i> Login de Pacientes</h4>
                </div>
            </div>
            <div class="panel-body">
                <form accept-charset="UTF-8" role="form" method="post" action="index.php?action=processloginpacient">
                    <fieldset>
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                                <input class="form-control" placeholder="Email" name="email" type="email" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                                <input class="form-control" placeholder="Contraseña" name="password" type="password" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <input class="btn btn-lg btn-success btn-block" type="submit" value="Iniciar Sesión">
                        </div>
                        
                        <div class="text-center">
                            <p>¿No tienes cuenta? 
                                <a href="index.php?view=pacientregister">
                                    <strong>Registrarse</strong>
                                </a>
                            </p>
                            <p>
                                <a href="index.php">
                                    <i class="fa fa-arrow-left"></i> Volver al inicio
                                </a>
                            </p>
                        </div>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.panel {
    border: 1px solid #ddd;
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}
.panel-heading {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 5px 5px 0 0;
}
.input-group-addon {
    background-color: #f8f9fa;
    border: 1px solid #ced4da;
}
.btn-success {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
}
.btn-success:hover {
    background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
}
</style>
