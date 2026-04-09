<?php
// Vista de registro para pacientes
?>
<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4><i class="fa fa-user-plus"></i> Registro de Nuevo Paciente</h4>
                </div>
            </div>
            <div class="panel-body">
                <form accept-charset="UTF-8" role="form" method="post" action="index.php?action=addpacient">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Nombre *</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-user"></i></span>
                                    <input class="form-control" placeholder="Tu nombre" name="name" type="text" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="lastname">Apellidos *</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-user"></i></span>
                                    <input class="form-control" placeholder="Tus apellidos" name="lastname" type="text" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">Email *</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                                    <input class="form-control" placeholder="tu@email.com" name="email" type="email" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="phone">Teléfono</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-phone"></i></span>
                                    <input class="form-control" placeholder="Tu teléfono" name="phone" type="text">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password">Contraseña *</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                                    <input class="form-control" placeholder="Tu contraseña" name="password" type="password" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="confirm_password">Confirmar Contraseña *</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                                    <input class="form-control" placeholder="Confirma tu contraseña" name="confirm_password" type="password" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="day_of_birth">Fecha de Nacimiento</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    <input class="form-control" name="day_of_birth" type="date">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="gender">Género</label>
                                <select name="gender" class="form-control">
                                    <option value="">Seleccionar...</option>
                                    <option value="M">Masculino</option>
                                    <option value="F">Femenino</option>
                                    <option value="O">Otro</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="address">Dirección</label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-home"></i></span>
                            <input class="form-control" placeholder="Tu dirección" name="address" type="text">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="sick">Diagnóstico/Enfermedad (opcional)</label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-stethoscope"></i></span>
                            <input class="form-control" placeholder="Diagnóstico médico si aplica" name="sick" type="text">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="allergies">Alergias (opcional)</label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-exclamation-triangle"></i></span>
                            <input class="form-control" placeholder="Alergias conocidas" name="allergies" type="text">
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" required>
                                Acepto los <a href="#" data-toggle="modal" data-target="#termsModal">términos y condiciones</a> *
                            </label>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="notifications_enabled" checked>
                                Deseo recibir notificaciones sobre mis citas por email
                            </label>
                        </div>
                    </div>
                    
                    <div class="form-group text-center">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fa fa-user-plus"></i> Registrarme
                        </button>
                        <a href="index.php?view=pacientlogin" class="btn btn-default btn-lg">
                            <i class="fa fa-arrow-left"></i> Volver al Login
                        </a>
                    </div>
                </form>
                
                <div class="text-center">
                    <p>¿Ya tienes cuenta? 
                        <a href="index.php?view=pacientlogin">
                            <strong>Iniciar Sesión</strong>
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para términos y condiciones -->
<div class="modal fade" id="termsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Términos y Condiciones</h4>
            </div>
            <div class="modal-body">
                <h5>Política de Privacidad y Términos de Uso</h5>
                <p>Al registrarse en nuestro sistema oncológico, usted acepta:</p>
                <ul>
                    <li>Proporcionar información veraz y actualizada</li>
                    <li>Usar el sistema de manera responsable</li>
                    <li>Mantener la confidencialidad de su contraseña</li>
                    <li>Permitir el uso de sus datos para fines médicos y administrativos</li>
                    <li>Recibir notificaciones importantes sobre su tratamiento</li>
                </ul>
                <p><strong>Confidencialidad:</strong> Su información médica será tratada con estricta confidencialidad según las normativas vigentes.</p>
                <p><strong>Derechos:</strong> Usted tiene derecho a acceder, modificar o eliminar sus datos personales en cualquier momento.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<style>
.panel {
    border: 1px solid #ddd;
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    margin-bottom: 30px;
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
.form-group label {
    font-weight: bold;
    color: #555;
}
.checkbox label {
    font-weight: normal;
}
hr {
    border-top: 2px solid #eee;
    margin: 20px 0;
}
</style>

<script>
// Validación de contraseñas
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const password = document.querySelector('input[name="password"]');
    const confirmPassword = document.querySelector('input[name="confirm_password"]');
    
    form.addEventListener('submit', function(e) {
        if (password.value !== confirmPassword.value) {
            e.preventDefault();
            alert('Las contraseñas no coinciden');
            confirmPassword.focus();
        }
        
        if (password.value.length < 6) {
            e.preventDefault();
            alert('La contraseña debe tener al menos 6 caracteres');
            password.focus();
        }
    });
    
    confirmPassword.addEventListener('input', function() {
        if (password.value !== confirmPassword.value) {
            confirmPassword.setCustomValidity('Las contraseñas no coinciden');
        } else {
            confirmPassword.setCustomValidity('');
        }
    });
});
</script>
