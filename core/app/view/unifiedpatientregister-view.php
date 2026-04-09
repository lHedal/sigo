<?php
// Verificar si es auto-registro (paciente) o registro administrativo
$is_self_registration = !isset($_SESSION["user_id"]);
$page_title = $is_self_registration ? "Registro de Nuevo Paciente" : "Agregar Paciente";
$form_action = $is_self_registration ? "index.php?action=addpacient" : "index.php?action=addpacient";

// Campos requeridos según el tipo de registro
$required_fields = $is_self_registration ? 
    ['name', 'lastname', 'email', 'password', 'confirm_password'] : 
    ['name', 'lastname', 'no', 'email'];
?>

<div class="row">
    <div class="col-md-10 col-md-offset-1">
        <div class="<?php echo $is_self_registration ? 'panel panel-primary' : 'box box-primary'; ?>">
            <div class="<?php echo $is_self_registration ? 'panel-heading' : 'box-header with-border'; ?>">
                <h3 class="<?php echo $is_self_registration ? 'panel-title' : 'box-title'; ?>">
                    <i class="fa fa-user-plus"></i> <?php echo $page_title; ?>
                </h3>
                <?php if(!$is_self_registration): ?>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse">
                        <i class="fa fa-minus"></i>
                    </button>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="<?php echo $is_self_registration ? 'panel-body' : 'box-body'; ?>">
                <form class="form-horizontal" method="post" id="unifiedPatientForm" 
                      enctype="multipart/form-data" action="<?php echo $form_action; ?>" role="form">
                    
                    <!-- SECCIÓN 1: DATOS PERSONALES -->
                    <div class="form-section">
                        <h4 class="section-title">
                            <i class="fa fa-user"></i> Datos Personales
                            <small>Información básica del paciente</small>
                        </h4>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Nombres *</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-user"></i></span>
                                        <input type="text" name="name" class="form-control" id="name" 
                                               placeholder="Nombres completos" required
                                               pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+" minlength="2">
                                    </div>
                                    <p class="help-block">Solo letras y espacios</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="lastname">Apellidos *</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-user"></i></span>
                                        <input type="text" name="lastname" class="form-control" id="lastname" 
                                               placeholder="Apellidos completos" required
                                               pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+" minlength="2">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="no">RUT <?php echo $is_self_registration ? '' : '*'; ?></label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-id-card"></i></span>
                                        <input type="text" name="no" class="form-control" id="no" 
                                               placeholder="12.345.678-9" maxlength="12"
                                               <?php echo $is_self_registration ? '' : 'required'; ?>>
                                    </div>
                                    <p class="help-block">Formato: 12.345.678-9 (con puntos y guión)</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="gender">Género</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-venus-mars"></i></span>
                                        <select name="gender" class="form-control" id="gender">
                                            <option value="">Seleccionar...</option>
                                            <option value="M">Masculino</option>
                                            <option value="F">Femenino</option>
                                            <option value="O">Otro</option>
                                        </select>
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
                                        <input type="date" name="day_of_birth" class="form-control" id="day_of_birth" 
                                               max="<?php echo date('Y-m-d'); ?>">
                                    </div>
                                    <p class="help-block" id="age-display"></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <?php if(!$is_self_registration): ?>
                                <div class="form-group">
                                    <label for="image">Foto del Paciente</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-camera"></i></span>
                                        <input type="file" name="image" class="form-control" accept="image/*">
                                    </div>
                                    <p class="help-block">Formatos: JPG, PNG (máx. 2MB)</p>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN 2: CONTACTO -->
                    <div class="form-section">
                        <h4 class="section-title">
                            <i class="fa fa-phone"></i> Información de Contacto
                        </h4>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email *</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                                        <input type="email" name="email" class="form-control" id="email" 
                                               placeholder="correo@ejemplo.com" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone">Teléfono</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-phone"></i></span>
                                        <input type="text" name="phone" class="form-control" id="phone" 
                                               placeholder="+56 9 1234 5678">
                                    </div>
                                    <p class="help-block">Para notificaciones importantes</p>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="address">Dirección</label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-home"></i></span>
                                <input type="text" name="address" class="form-control" id="address" 
                                       placeholder="Calle, número, comuna, ciudad">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="cp">Código Postal</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-map-pin"></i></span>
                                        <input type="text" name="cp" class="form-control" id="cp" 
                                               placeholder="7500000">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="pob">Ciudad</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-map-marker"></i></span>
                                        <input type="text" name="pob" class="form-control" id="pob" 
                                               placeholder="Santiago, Valparaíso, etc.">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if($is_self_registration): ?>
                    <!-- SECCIÓN 3: SEGURIDAD (Solo auto-registro) -->
                    <div class="form-section">
                        <h4 class="section-title">
                            <i class="fa fa-lock"></i> Seguridad de la Cuenta
                        </h4>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">Contraseña *</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                                        <input type="password" name="password" class="form-control" id="password" 
                                               placeholder="Mínimo 6 caracteres" required minlength="6">
                                    </div>
                                    <div class="password-strength" id="password-strength"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="confirm_password">Confirmar Contraseña *</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                                        <input type="password" name="confirm_password" class="form-control" 
                                               id="confirm_password" placeholder="Repetir contraseña" required>
                                    </div>
                                    <div class="password-match" id="password-match"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- SECCIÓN 4: INFORMACIÓN MÉDICA BÁSICA -->
                    <div class="form-section">
                        <h4 class="section-title">
                            <i class="fa fa-stethoscope"></i> Información Médica Básica
                            <small>Información inicial importante</small>
                        </h4>
                        
                        <div class="form-group">
                            <label for="sick">Diagnósticos Conocidos</label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-heartbeat"></i></span>
                                <textarea name="sick" class="form-control" id="sick" rows="3"
                                          placeholder="Enfermedades o condiciones médicas conocidas (diabetes, hipertensión, etc.)"></textarea>
                            </div>
                            <p class="help-block">Esta información ayuda a brindar mejor atención médica</p>
                        </div>

                        <div class="form-group">
                            <label for="medicaments">Medicamentos Actuales</label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-pills"></i></span>
                                <textarea name="medicaments" class="form-control" id="medicaments" rows="3"
                                          placeholder="Medicamentos que toma regularmente, dosis y frecuencia"></textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="alergy">Alergias</label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-exclamation-triangle text-danger"></i></span>
                                <textarea name="alergy" class="form-control" id="alergy" rows="2"
                                          placeholder="Alergias a medicamentos, alimentos o sustancias"></textarea>
                            </div>
                            <p class="help-block text-danger">
                                <strong>Importante:</strong> Esta información es crucial para su seguridad
                            </p>
                        </div>
                    </div>

                    <?php if($is_self_registration): ?>
                    <!-- SECCIÓN 5: TÉRMINOS Y CONDICIONES -->
                    <div class="form-section">
                        <h4 class="section-title">
                            <i class="fa fa-file-text"></i> Términos y Condiciones
                        </h4>
                        
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" id="terms" name="terms" required>
                                Acepto los <a href="#" data-toggle="modal" data-target="#termsModal">términos y condiciones</a> *
                            </label>
                        </div>
                        
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" id="privacy" name="privacy" required>
                                Acepto el <a href="#" data-toggle="modal" data-target="#privacyModal">tratamiento de datos personales</a> *
                            </label>
                        </div>
                        
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" id="notifications" name="notifications" checked>
                                Acepto recibir notificaciones por email sobre mis citas médicas
                            </label>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- BOTONES DE ACCIÓN -->
                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <button type="submit" class="btn btn-success btn-lg" id="submit-btn">
                                    <i class="fa fa-save"></i> 
                                    <?php echo $is_self_registration ? 'Registrarse' : 'Agregar Paciente'; ?>
                                </button>
                                <?php if(!$is_self_registration): ?>
                                <a href="index.php?view=pacients" class="btn btn-default btn-lg">
                                    <i class="fa fa-arrow-left"></i> Cancelar
                                </a>
                                <?php else: ?>
                                <a href="index.php?view=pacientlogin" class="btn btn-link">
                                    ¿Ya tienes cuenta? Inicia sesión aquí
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Estilos CSS -->
<style>
.form-section {
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 25px;
    background: #fafafa;
}

.section-title {
    color: #337ab7;
    border-bottom: 2px solid #337ab7;
    padding-bottom: 10px;
    margin-bottom: 20px;
}

.section-title small {
    color: #666;
    font-weight: normal;
}

.form-group .help-block {
    font-size: 12px;
    color: #666;
    margin-top: 5px;
}

.password-strength {
    height: 4px;
    background: #ddd;
    margin-top: 5px;
    border-radius: 2px;
}

.password-strength.weak { background: #dc3545; }
.password-strength.medium { background: #ffc107; }
.password-strength.strong { background: #28a745; }

.password-match.match { color: #28a745; }
.password-match.no-match { color: #dc3545; }

.form-actions {
    background: #f9f9f9;
    padding: 20px;
    border-radius: 8px;
    margin-top: 30px;
}

.input-group-addon {
    min-width: 45px;
}

#age-display {
    font-style: italic;
    color: #337ab7;
}
</style>

<!-- JavaScript para validaciones y UX -->
<script>
jQuery(document).ready(function() {
    // Cálculo automático de edad
    jQuery('#day_of_birth').on('change', function() {
        const birthDate = new Date(this.value);
        const today = new Date();
        let age = today.getFullYear() - birthDate.getFullYear();
        const monthDiff = today.getMonth() - birthDate.getMonth();
        
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }
        
        if (age >= 0 && age <= 120) {
            jQuery('#age-display').text(age + ' años');
        } else {
            jQuery('#age-display').text('');
        }
    });

    <?php if($is_self_registration): ?>
    // Validación de contraseñas
    function checkPasswordStrength(password) {
        let strength = 0;
        if (password.length >= 6) strength++;
        if (password.match(/[a-z]/)) strength++;
        if (password.match(/[A-Z]/)) strength++;
        if (password.match(/[0-9]/)) strength++;
        if (password.match(/[^a-zA-Z0-9]/)) strength++;
        
        return strength;
    }
    
    jQuery('#password').on('input', function() {
        const password = this.value;
        const strength = checkPasswordStrength(password);
        const strengthDiv = jQuery('#password-strength');
        
        if (password.length === 0) {
            strengthDiv.removeClass('weak medium strong');
            return;
        }
        
        if (strength <= 2) {
            strengthDiv.removeClass('medium strong').addClass('weak');
        } else if (strength <= 3) {
            strengthDiv.removeClass('weak strong').addClass('medium');
        } else {
            strengthDiv.removeClass('weak medium').addClass('strong');
        }
    });
    
    // Validación de confirmación de contraseña
    function checkPasswordMatch() {
        const password = jQuery('#password').val();
        const confirmPassword = jQuery('#confirm_password').val();
        const matchDiv = jQuery('#password-match');
        
        if (confirmPassword.length === 0) {
            matchDiv.text('').removeClass('match no-match');
            return;
        }
        
        if (password === confirmPassword) {
            matchDiv.text('✓ Las contraseñas coinciden').removeClass('no-match').addClass('match');
        } else {
            matchDiv.text('✗ Las contraseñas no coinciden').removeClass('match').addClass('no-match');
        }
    }
    
    jQuery('#password, #confirm_password').on('input', checkPasswordMatch);
    <?php endif; ?>

    // Validación del formulario
    jQuery('#unifiedPatientForm').on('submit', function(e) {
        let isValid = true;
        
        <?php if($is_self_registration): ?>
        // Validar contraseñas
        const password = jQuery('#password').val();
        const confirmPassword = jQuery('#confirm_password').val();
        
        if (password.length < 6) {
            mostrarNotificacion('La contraseña debe tener al menos 6 caracteres', 'warning');
            isValid = false;
        }
        
        if (password !== confirmPassword) {
            mostrarNotificacion('Las contraseñas no coinciden', 'warning');
            isValid = false;
        }
        
        // Validar términos
        if (!jQuery('#terms').is(':checked')) {
            mostrarNotificacion('Debe aceptar los términos y condiciones', 'warning');
            isValid = false;
        }
        
        if (!jQuery('#privacy').is(':checked')) {
            mostrarNotificacion('Debe aceptar el tratamiento de datos personales', 'warning');
            isValid = false;
        }
        <?php endif; ?>
        
        if (!isValid) {
            e.preventDefault();
            return false;
        }
        
        // Deshabilitar botón para evitar doble envío
        jQuery('#submit-btn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Procesando...');
    });

    // Formateo automático del RUT
    jQuery('#no').on('input', function() {
        let rut = this.value.replace(/[^0-9kK]/g, '');
        if (rut.length > 1) {
            rut = rut.slice(0, -1).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.') + '-' + rut.slice(-1);
        }
        this.value = rut;
    });

    // Formateo automático del teléfono
    jQuery('#phone').on('input', function() {
        let phone = this.value.replace(/\D/g, '');
        if (phone.startsWith('56')) {
            phone = '+' + phone.replace(/(\d{2})(\d{1})(\d{4})(\d{4})/, '$1 $2 $3 $4');
        } else if (phone.startsWith('9') && phone.length === 9) {
            phone = '+56 ' + phone.replace(/(\d{1})(\d{4})(\d{4})/, '$1 $2 $3');
        }
        this.value = phone;
    });
});

// Función para mostrar notificaciones (debe existir en el sistema)
function mostrarNotificacion(mensaje, tipo) {
    if (typeof window.mostrarNotificacion === 'function') {
        window.mostrarNotificacion(mensaje, tipo);
    } else {
        alert(mensaje);
    }
}
</script>
