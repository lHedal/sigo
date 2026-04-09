<?php
include "core/app/model/NotificationData.php";

$queue_notifications = NotificationQueueData::getAll();
?>

<div class="row">
    <div class="col-md-12">
        <h1><i class="fa fa-clock-o"></i> Cola de Notificaciones</h1>
        <br>
        
        <!-- Estadísticas de la cola -->
        <div class="row">
            <div class="col-md-3">
                <div class="panel panel-warning">
                    <div class="panel-body">
                        <h3 class="text-center">
                            <?php
                            $pending_count = 0;
                            foreach($queue_notifications as $notification) {
                                if($notification->status == 'pending') $pending_count++;
                            }
                            echo $pending_count;
                            ?>
                        </h3>
                        <p class="text-center">Pendientes</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="panel panel-info">
                    <div class="panel-body">
                        <h3 class="text-center">
                            <?php
                            $processing_count = 0;
                            foreach($queue_notifications as $notification) {
                                if($notification->status == 'processing') $processing_count++;
                            }
                            echo $processing_count;
                            ?>
                        </h3>
                        <p class="text-center">Procesando</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="panel panel-success">
                    <div class="panel-body">
                        <h3 class="text-center">
                            <?php
                            $sent_count = 0;
                            foreach($queue_notifications as $notification) {
                                if($notification->status == 'sent') $sent_count++;
                            }
                            echo $sent_count;
                            ?>
                        </h3>
                        <p class="text-center">Enviadas</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="panel panel-danger">
                    <div class="panel-body">
                        <h3 class="text-center">
                            <?php
                            $failed_count = 0;
                            foreach($queue_notifications as $notification) {
                                if($notification->status == 'failed') $failed_count++;
                            }
                            echo $failed_count;
                            ?>
                        </h3>
                        <p class="text-center">Fallidas</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Acciones -->
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Acciones de Cola</h3>
            </div>
            <div class="panel-body">
                <button class="btn btn-success" onclick="processQueue()">
                    <i class="fa fa-play"></i> Procesar Cola Ahora
                </button>
                <button class="btn btn-warning" onclick="retryFailedNotifications()">
                    <i class="fa fa-repeat"></i> Reintentar Fallidas
                </button>
                <button class="btn btn-danger" onclick="clearProcessedNotifications()" 
                        <?php echo $sent_count == 0 ? 'disabled' : ''; ?>>
                    <i class="fa fa-trash"></i> Limpiar Enviadas
                </button>
                <a href="index.php?view=notifications" class="btn btn-default">
                    <i class="fa fa-arrow-left"></i> Volver al Historial
                </a>
            </div>
        </div>

        <!-- Tabla de notificaciones en cola -->
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    Notificaciones en Cola
                    <span class="pull-right">
                        <button class="btn btn-xs btn-default" onclick="location.reload()">
                            <i class="fa fa-refresh"></i> Actualizar
                        </button>
                    </span>
                </h3>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="queueTable">
                        <thead>
                            <tr>
                                <th>Programada para</th>
                                <th>Destinatario</th>
                                <th>Asunto</th>
                                <th>Estado</th>
                                <th>Intentos</th>
                                <th>Creada</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($queue_notifications as $notification): ?>
                            <tr class="<?php 
                                switch($notification->status) {
                                    case 'pending': echo 'warning'; break;
                                    case 'processing': echo 'info'; break;
                                    case 'sent': echo 'success'; break;
                                    case 'failed': echo 'danger'; break;
                                }
                            ?>">
                                <td>
                                    <?php 
                                    $scheduled_time = strtotime($notification->scheduled_at);
                                    $now = time();
                                    echo date('d/m/Y H:i', $scheduled_time);
                                    
                                    if($notification->status == 'pending') {
                                        if($scheduled_time <= $now) {
                                            echo ' <span class="label label-warning">Vencida</span>';
                                        } else {
                                            $diff = $scheduled_time - $now;
                                            if($diff < 3600) {
                                                echo ' <span class="label label-info">Próxima</span>';
                                            }
                                        }
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php echo $notification->recipient_name; ?><br>
                                    <small class="text-muted"><?php echo $notification->recipient_email; ?></small>
                                </td>
                                <td><?php echo $notification->subject; ?></td>
                                <td>
                                    <?php
                                    $status_class = '';
                                    $status_text = '';
                                    switch($notification->status) {
                                        case 'pending': 
                                            $status_class = 'warning'; 
                                            $status_text = 'Pendiente';
                                            break;
                                        case 'processing': 
                                            $status_class = 'info'; 
                                            $status_text = 'Procesando';
                                            break;
                                        case 'sent': 
                                            $status_class = 'success'; 
                                            $status_text = 'Enviada';
                                            break;
                                        case 'failed': 
                                            $status_class = 'danger'; 
                                            $status_text = 'Fallida';
                                            break;
                                        default: 
                                            $status_class = 'default';
                                            $status_text = ucfirst($notification->status);
                                    }
                                    ?>
                                    <span class="label label-<?php echo $status_class; ?>">
                                        <?php echo $status_text; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php echo $notification->attempts; ?>/<?php echo $notification->max_attempts; ?>
                                    <?php if($notification->attempts >= $notification->max_attempts && $notification->status != 'sent'): ?>
                                        <br><small class="text-danger">Máx. alcanzado</small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php echo date('d/m/Y H:i', strtotime($notification->created_at)); ?>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-xs">
                                        <button class="btn btn-info" onclick="viewQueueNotification(<?php echo $notification->id; ?>)">
                                            <i class="fa fa-eye"></i>
                                        </button>
                                        <?php if($notification->status == 'pending' || $notification->status == 'failed'): ?>
                                        <button class="btn btn-success" onclick="processSpecific(<?php echo $notification->id; ?>)">
                                            <i class="fa fa-play"></i>
                                        </button>
                                        <?php endif; ?>
                                        <?php if($notification->status != 'processing'): ?>
                                        <button class="btn btn-danger" onclick="cancelNotification(<?php echo $notification->id; ?>)">
                                            <i class="fa fa-times"></i>
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <?php if(empty($queue_notifications)): ?>
                <div class="text-center">
                    <p class="text-muted">
                        <i class="fa fa-inbox fa-3x"></i><br>
                        No hay notificaciones en cola
                    </p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal para ver notificación de cola -->
<div class="modal fade" id="queueNotificationModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Detalles de Notificación en Cola</h4>
            </div>
            <div class="modal-body" id="queueNotificationDetails">
                <!-- Contenido dinámico -->
            </div>
        </div>
    </div>
</div>

<script>
// Inicializar DataTable
$(document).ready(function() {
    $('#queueTable').DataTable({
        "order": [[ 0, "asc" ]],
        "pageLength": 25,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
        }
    });
});

function processQueue() {
    if(confirm('¿Desea procesar todas las notificaciones pendientes en cola?')) {
        $.post('index.php?action=processnotificationqueue', {}, function(response) {
            if(response.success) {
                alert('Se procesaron ' + response.processed + ' notificaciones');
                location.reload();
            } else {
                alert('Error: ' + response.message);
            }
        }, 'json');
    }
}

function processSpecific(id) {
    if(confirm('¿Desea procesar esta notificación específica?')) {
        $.post('index.php?action=processspecificnotification', {id: id}, function(response) {
            if(response.success) {
                alert('Notificación procesada exitosamente');
                location.reload();
            } else {
                alert('Error: ' + response.message);
            }
        }, 'json');
    }
}

function retryFailedNotifications() {
    if(confirm('¿Desea reintentar todas las notificaciones fallidas?')) {
        $.post('index.php?action=retryfailednotifications', {}, function(response) {
            if(response.success) {
                alert('Se reintentaron ' + response.retried + ' notificaciones');
                location.reload();
            } else {
                alert('Error: ' + response.message);
            }
        }, 'json');
    }
}

function clearProcessedNotifications() {
    if(confirm('¿Desea eliminar todas las notificaciones ya enviadas de la cola?')) {
        $.post('index.php?action=clearprocessednotifications', {}, function(response) {
            if(response.success) {
                alert('Se eliminaron ' + response.cleared + ' notificaciones enviadas');
                location.reload();
            } else {
                alert('Error: ' + response.message);
            }
        }, 'json');
    }
}

function cancelNotification(id) {
    if(confirm('¿Desea cancelar esta notificación?')) {
        $.post('index.php?action=cancelqueuenotification', {id: id}, function(response) {
            if(response.success) {
                alert('Notificación cancelada');
                location.reload();
            } else {
                alert('Error: ' + response.message);
            }
        }, 'json');
    }
}

function viewQueueNotification(id) {
    $.get('index.php?action=getqueuenotificationdetails&id=' + id, function(response) {
        if(response.success) {
            var notification = response.notification;
            var html = `
                <div class="row">
                    <div class="col-md-6">
                        <strong>Destinatario:</strong> ${notification.recipient_name}<br>
                        <strong>Email:</strong> ${notification.recipient_email}<br>
                        <strong>Estado:</strong> ${notification.status}<br>
                        <strong>Programada para:</strong> ${notification.scheduled_at}
                    </div>
                    <div class="col-md-6">
                        <strong>Intentos:</strong> ${notification.attempts}/${notification.max_attempts}<br>
                        <strong>Creada:</strong> ${notification.created_at}<br>
                        <strong>Asunto:</strong> ${notification.subject}
                    </div>
                </div>
                <hr>
                <strong>Contenido:</strong>
                <div style="border:1px solid #ddd; padding:10px; margin-top:10px;">
                    ${notification.body}
                </div>
            `;
            $('#queueNotificationDetails').html(html);
            $('#queueNotificationModal').modal('show');
        }
    }, 'json');
}
</script>
