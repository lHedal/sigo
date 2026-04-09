<?php
include "core/app/model/NotificationData.php";

$config = NotificationConfigData::getConfig();
if(!$config) {
    $config = new NotificationConfigData();
}
?>

<div class="row">
    <div class="col-md-12">
        <h1><i class="fa fa-cog"></i> Configuración de Notificaciones</h1>
        <br>
        
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Configuración SMTP</h3>
            </div>
            <div class="panel-body">
                <form class="form-horizontal" method="post" action="index.php?action=updatenotificationconfig">
                    
                    <!-- Estado del sistema -->
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Estado del Sistema</label>
                        <div class="col-sm-9">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="notifications_enabled" value="1" 
                                           <?php echo $config->notifications_enabled ? 'checked' : ''; ?>>
                                    Notificaciones habilitadas
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="auto_send_enabled" value="1" 
                                           <?php echo $config->auto_send_enabled ? 'checked' : ''; ?>>
                                    Envío automático habilitado
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <!-- Configuración SMTP -->
                    <div class="form-group">
                        <label for="smtp_host" class="col-sm-3 control-label">Servidor SMTP</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="smtp_host" name="smtp_host" 
                                   value="<?php echo $config->smtp_host; ?>" placeholder="smtp.gmail.com">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="smtp_port" class="col-sm-3 control-label">Puerto</label>
                        <div class="col-sm-9">
                            <input type="number" class="form-control" id="smtp_port" name="smtp_port" 
                                   value="<?php echo $config->smtp_port; ?>" placeholder="587">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="smtp_security" class="col-sm-3 control-label">Seguridad</label>
                        <div class="col-sm-9">
                            <select class="form-control" id="smtp_security" name="smtp_security">
                                <option value="tls" <?php echo $config->smtp_security == 'tls' ? 'selected' : ''; ?>>TLS</option>
                                <option value="ssl" <?php echo $config->smtp_security == 'ssl' ? 'selected' : ''; ?>>SSL</option>
                                <option value="none" <?php echo $config->smtp_security == 'none' ? 'selected' : ''; ?>>Ninguna</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="smtp_username" class="col-sm-3 control-label">Usuario SMTP</label>
                        <div class="col-sm-9">
                            <input type="email" class="form-control" id="smtp_username" name="smtp_username" 
                                   value="<?php echo $config->smtp_username; ?>" placeholder="usuario@gmail.com">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="smtp_password" class="col-sm-3 control-label">Contraseña SMTP</label>
                        <div class="col-sm-9">
                            <input type="password" class="form-control" id="smtp_password" name="smtp_password" 
                                   value="<?php echo $config->smtp_password; ?>" placeholder="Contraseña o App Password">
                            <small class="help-block">Para Gmail, use una contraseña de aplicación específica</small>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <!-- Configuración del remitente -->
                    <div class="form-group">
                        <label for="from_email" class="col-sm-3 control-label">Email Remitente</label>
                        <div class="col-sm-9">
                            <input type="email" class="form-control" id="from_email" name="from_email" 
                                   value="<?php echo $config->from_email; ?>" placeholder="sistema@oncologia.cl">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="from_name" class="col-sm-3 control-label">Nombre Remitente</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="from_name" name="from_name" 
                                   value="<?php echo $config->from_name; ?>" placeholder="Sistema Oncológico">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="col-sm-offset-3 col-sm-9">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Guardar Configuración
                            </button>
                            <button type="button" class="btn btn-info" onclick="testConfiguration()">
                                <i class="fa fa-envelope"></i> Probar Configuración
                            </button>
                            <a href="index.php?view=notifications" class="btn btn-default">
                                <i class="fa fa-arrow-left"></i> Volver
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Panel de ayuda -->
        <div class="panel panel-info">
            <div class="panel-heading">
                <h3 class="panel-title">Configuración para Gmail</h3>
            </div>
            <div class="panel-body">
                <p><strong>Para usar Gmail como servidor SMTP:</strong></p>
                <ol>
                    <li>Servidor SMTP: <code>smtp.gmail.com</code></li>
                    <li>Puerto: <code>587</code></li>
                    <li>Seguridad: <code>TLS</code></li>
                    <li>Usuario: Su dirección de Gmail completa</li>
                    <li>Contraseña: Use una <strong>Contraseña de Aplicación</strong> (no su contraseña normal)</li>
                </ol>
                <p><strong>Para generar una contraseña de aplicación:</strong></p>
                <ol>
                    <li>Vaya a <a href="https://myaccount.google.com/security" target="_blank">Configuración de seguridad de Google</a></li>
                    <li>Active la verificación en 2 pasos</li>
                    <li>Genere una contraseña de aplicación específica para este sistema</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<script>
function testConfiguration() {
    var email = prompt('Ingrese un email para probar la configuración:');
    if(email && email.includes('@')) {
        // Mostrar loading
        var btn = event.target;
        var originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Probando...';
        
        // Obtener datos del formulario
        var formData = new FormData();
        formData.append('test_email', email);
        formData.append('smtp_host', document.getElementById('smtp_host').value);
        formData.append('smtp_port', document.getElementById('smtp_port').value);
        formData.append('smtp_security', document.getElementById('smtp_security').value);
        formData.append('smtp_username', document.getElementById('smtp_username').value);
        formData.append('smtp_password', document.getElementById('smtp_password').value);
        formData.append('from_email', document.getElementById('from_email').value);
        formData.append('from_name', document.getElementById('from_name').value);
        
        console.log('Testing SMTP configuration with data:', {
            email: email,
            smtp_host: document.getElementById('smtp_host').value,
            smtp_port: document.getElementById('smtp_port').value,
            smtp_security: document.getElementById('smtp_security').value,
            smtp_username: document.getElementById('smtp_username').value,
            from_email: document.getElementById('from_email').value,
            from_name: document.getElementById('from_name').value
        });
        
        // Hacer la petición
        fetch('index.php?action=testnotificationconfig', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            return response.text();
        })
        .then(text => {
            console.log('Raw response:', text);
            
            // Try to parse as JSON
            try {
                var data = JSON.parse(text);
                console.log('Parsed JSON:', data);
                
                if(data.success) {
                    alert('¡Configuración exitosa! Email de prueba enviado a: ' + email);
                } else {
                    alert('Error en la configuración: ' + data.message);
                }
            } catch (e) {
                console.error('Failed to parse JSON:', e);
                console.error('Response was:', text);
                
                // Show a more helpful error message
                if (text.includes('<!DOCTYPE') || text.includes('<html>')) {
                    alert('Error: Se recibió una página HTML en lugar de JSON. Revise la consola del navegador para más detalles.');
                } else if (text.trim() === '') {
                    alert('Error: Respuesta vacía del servidor. Verifique que el action existe.');
                } else {
                    alert('Error: Respuesta no válida del servidor. Revise la consola para más detalles.');
                }
            }
        })
        .catch(error => {
            console.error('Network error:', error);
            alert('Error de red al probar la configuración: ' + error.message);
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
    } else {
        alert('Por favor ingrese un email válido');
    }
}

</script>
