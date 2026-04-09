<?php
include "core/app/model/NotificationData.php";
$notification_types = NotificationTypeData::getAll();
?>
<div class="row">
    <div class="col-md-12">
        <div class="page-header">
            <h1>
                <i class="fa fa-bell"></i> Tipos de Notificaciones
                <small>Configuración de categorías de notificaciones del sistema</small>
            </h1>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <!-- Formulario para nuevo tipo -->
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-plus"></i> Nuevo Tipo de Notificación
                </h3>
            </div>
            <div class="panel-body">
                <form action="index.php?action=addnotificationtype" method="post">
                    <div class="form-group">
                        <label for="name">Nombre del Tipo *</label>
                        <input type="text" class="form-control" name="name" id="name" placeholder="Ej: Recordatorio de Cita" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Descripción</label>
                        <textarea class="form-control" name="description" id="description" rows="3" placeholder="Descripción del tipo de notificación"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="template">Plantilla de Mensaje</label>
                        <textarea class="form-control" name="template" id="template" rows="4" placeholder="Usa {paciente}, {fecha}, {hora} para variables dinámicas"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="priority">Prioridad</label>
                        <select class="form-control" name="priority" id="priority">
                            <option value="low">Baja</option>
                            <option value="normal" selected>Normal</option>
                            <option value="high">Alta</option>
                            <option value="urgent">Urgente</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="color">Color de Identificación</label>
                        <select class="form-control" name="color" id="color">
                            <option value="primary">Azul (Primario)</option>
                            <option value="success">Verde (Éxito)</option>
                            <option value="info">Celeste (Información)</option>
                            <option value="warning">Naranja (Advertencia)</option>
                            <option value="danger">Rojo (Peligro)</option>
                        </select>
                    </div>
                    
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="is_active" value="1" checked> Activo
                        </label>
                    </div>
                    
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="send_email" value="1" checked> Enviar por Email
                        </label>
                    </div>
                    
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="send_sms" value="1"> Enviar por SMS
                        </label>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fa fa-save"></i> Crear Tipo
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <!-- Lista de tipos existentes -->
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-list"></i> Tipos de Notificaciones Existentes
                </h3>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Codigo</th>
                                <th>Nombre</th>
                                <th>Canales</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(count($notification_types) > 0): ?>
                                <?php foreach($notification_types as $type): ?>
                                    <tr>
                                        <td><code><?php echo htmlspecialchars($type->code); ?></code></td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($type->name); ?></strong><br>
                                            <small class="text-muted"><?php echo htmlspecialchars($type->description); ?></small>
                                        </td>
                                        <td>
                                            <?php if((int)$type->send_to_patient === 1): ?>
                                                <i class="fa fa-user text-primary" title="Paciente"></i>
                                            <?php endif; ?>
                                            <?php if((int)$type->send_to_medic === 1): ?>
                                                <i class="fa fa-user-md text-success" title="Medico"></i>
                                            <?php endif; ?>
                                            <?php if((int)$type->send_to_patient !== 1 && (int)$type->send_to_medic !== 1): ?>
                                                <span class="label label-default">Sin canales</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if((int)$type->is_active === 1): ?>
                                                <span class="label label-success">Activo</span>
                                            <?php else: ?>
                                                <span class="label label-default">Inactivo</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No hay tipos de notificacion configurados.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Información adicional -->
        <div class="panel panel-info">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-info-circle"></i> Variables Disponibles para Plantillas
                </h3>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Variables de Paciente:</h5>
                        <ul>
                            <li><code>{paciente}</code> - Nombre completo</li>
                            <li><code>{email}</code> - Email del paciente</li>
                            <li><code>{telefono}</code> - Teléfono</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h5>Variables de Cita:</h5>
                        <ul>
                            <li><code>{fecha}</code> - Fecha de la cita</li>
                            <li><code>{hora}</code> - Hora de la cita</li>
                            <li><code>{medico}</code> - Médico asignado</li>
                            <li><code>{tratamiento}</code> - Tipo de tratamiento</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
