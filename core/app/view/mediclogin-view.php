<div class="row">
    <div class="col-md-4 col-md-offset-4">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="panel-title text-center">
                    <i class="fa fa-user-md fa-3x"></i>
                    <h3>Acceso Médicos</h3>
                    <p class="text-muted">Sistema de Oncología</p>
                </div>
            </div>
            
            <div class="panel-body">
                <form accept-charset="UTF-8" role="form" method="post" action="index.php?action=processloginmedic">
                    <fieldset>
                        <div class="form-group">
                            <label for="username">Email Profesional</label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="glyphicon glyphicon-envelope"></i>
                                </span>
                                <input class="form-control" placeholder="doctor@email.com" name="username" type="email" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="password">Contraseña</label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="glyphicon glyphicon-lock"></i>
                                </span>
                                <input class="form-control" placeholder="Contraseña" name="password" type="password" value="" required>
                            </div>
                        </div>
                        
                        <div class="checkbox">
                            <label>
                                <input name="remember" type="checkbox" value="Remember Me"> Recordar sesión
                            </label>
                        </div>
                        
                        <input class="btn btn-lg btn-success btn-block" type="submit" value="Iniciar Sesión">
                    </fieldset>
                </form>
                
                <div class="text-center" style="margin-top: 20px;">
                    <p class="text-muted">
                        <small>
                            <a href="index.php?view=login">¿Eres personal administrativo?</a>
                        </small>
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Información de credenciales para demo -->
        <div class="panel panel-info">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <i class="glyphicon glyphicon-info-sign"></i> Información de Acceso
                </h4>
            </div>
            <div class="panel-body">
                <p><strong>Para médicos registrados:</strong></p>
                <ul class="list-unstyled">
                    <li><strong>Usuario:</strong> Su email profesional</li>
                    <li><strong>Contraseña:</strong> <code>medico123</code></li>
                </ul>
                
                <p class="text-muted">
                    <small>
                        <strong>Médicos disponibles:</strong><br>
                        • roberto.fernandez@oncologia.com<br>
                        • carmen.vasquez@oncologia.com<br>
                        • miguel.herrera@oncologia.com<br>
                        • isabel.morales@oncologia.com<br>
                        • francisco.castillo@oncologia.com<br>
                        • patricia.jimenez@oncologia.com
                    </small>
                </p>
            </div>
        </div>
    </div>
</div>

<style>
.panel {
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.panel-heading {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 10px 10px 0 0;
    border: none;
}

.panel-title h3 {
    color: white;
    margin: 10px 0;
}

.input-group-addon {
    background-color: #f8f9fa;
    border-color: #e9ecef;
}

.btn-success {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    border-radius: 25px;
    padding: 12px;
    font-weight: bold;
}

.btn-success:hover {
    background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

.form-control {
    border-radius: 20px;
    padding: 12px 15px;
}

.input-group {
    margin-bottom: 15px;
}

body {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    min-height: 100vh;
}

.container-fluid {
    padding-top: 50px;
}
</style>
