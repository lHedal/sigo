<?php
include "core/app/model/NotificationData.php";

$notifications = NotificationData::getRecentNotifications(100);
$stats = NotificationData::getNotificationStats();
?>

<div class="row">
    <div class="col-md-12">
        <h1><i class="fa fa-bell"></i> Sistema de Notificaciones</h1>
        <br>
        
        <!-- Estadísticas -->
        <div class="row">
            <div class="col-md-3">
                <div class="panel panel-primary">
                    <div class="panel-body">
                        <h3 class="text-center">
                            <?php
                            $sent_today = 0;
                            $today = date('Y-m-d');
                            foreach($stats as $stat) {
                                if($stat['date'] == $today && $stat['status'] == 'sent') {
                                    $sent_today = $stat['total'];
                                    break;
                                }
                            }
                            echo $sent_today;
                            ?>
                        </h3>
                        <p class="text-center">Enviadas Hoy</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="panel panel-success">
                    <div class="panel-body">
                        <h3 class="text-center">
                            <?php
                            $total_sent = 0;
                            foreach($stats as $stat) {
                                if($stat['status'] == 'sent') {
                                    $total_sent += $stat['total'];
                                }
                            }
                            echo $total_sent;
                            ?>
                        </h3>
                        <p class="text-center">Total Enviadas</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="panel panel-warning">
                    <div class="panel-body">
                        <h3 class="text-center">
                            <?php
                            $total_pending = 0;
                            foreach($stats as $stat) {
                                if($stat['status'] == 'pending') {
                                    $total_pending += $stat['total'];
                                }
                            }
                            echo $total_pending;
                            ?>
                        </h3>
                        <p class="text-center">Pendientes</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="panel panel-danger">
                    <div class="panel-body">
                        <h3 class="text-center">
                            <?php
                            $total_failed = 0;
                            foreach($stats as $stat) {
                                if($stat['status'] == 'failed') {
                                    $total_failed += $stat['total'];
                                }
                            }
                            echo $total_failed;
                            ?>
                        </h3>
                        <p class="text-center">Fallidas</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Acciones rápidas -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Acciones Rápidas</h3>
                    </div>
                    <div class="panel-body">
                        <a href="index.php?view=notificationconfig" class="btn btn-primary">
                            <i class="fa fa-cog"></i> Configurar SMTP
                        </a>
                        <a href="index.php?view=notificationtypes" class="btn btn-info">
                            <i class="fa fa-list"></i> Tipos de Notificaciones
                        </a>
                        <a href="index.php?view=notificationqueue" class="btn btn-warning">
                            <i class="fa fa-clock-o"></i> Cola de Notificaciones
                        </a>
                        <button class="btn btn-success" onclick="processQueue()">
                            <i class="fa fa-play"></i> Procesar Cola
                        </button>
                        <button class="btn btn-secondary" onclick="testEmail()">
                            <i class="fa fa-envelope"></i> Probar Email
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Historial de notificaciones -->
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Historial de Notificaciones</h3>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Tipo</th>
                                <th>Destinatario</th>
                                <th>Asunto</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($notifications as $notification): ?>
                            <tr>
                                <td><?php echo date('d/m/Y H:i', strtotime($notification->created_at)); ?></td>
                                <td>
                                    <span class="label label-info">
                                        <?php echo isset($notification->type_name) ? $notification->type_name : 'N/A'; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php echo $notification->recipient_name; ?><br>
                                    <small class="text-muted"><?php echo $notification->recipient_email; ?></small>
                                </td>
                                <td><?php echo $notification->subject; ?></td>
                                <td>
                                    <?php
                                    $status_class = '';
                                    switch($notification->status) {
                                        case 'sent': $status_class = 'success'; break;
                                        case 'failed': $status_class = 'danger'; break;
                                        case 'pending': $status_class = 'warning'; break;
                                        default: $status_class = 'default';
                                    }
                                    ?>
                                    <span class="label label-<?php echo $status_class; ?>">
                                        <?php echo ucfirst($notification->status); ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-xs btn-info" onclick="viewNotification(<?php echo $notification->id; ?>)">
                                        <i class="fa fa-eye"></i> Ver
                                    </button>
                                    <?php if($notification->status == 'failed'): ?>
                                    <button class="btn btn-xs btn-warning" onclick="retryNotification(<?php echo $notification->id; ?>)">
                                        <i class="fa fa-repeat"></i> Reintentar
                                    </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para ver notificación -->
<div class="modal fade" id="notificationModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Detalles de Notificación</h4>
            </div>
            <div class="modal-body" id="notificationDetails">
                <!-- Contenido dinámico -->
            </div>
        </div>
    </div>
</div>

<script>
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

function testEmail() {
    var email = prompt('Ingrese su email para la prueba:');
    if(email && email.includes('@')) {
        $.post('index.php?action=testnotificationemail', {email: email}, function(response) {
            if(response.success) {
                alert('Email de prueba enviado exitosamente');
            } else {
                alert('Error al enviar email: ' + response.message);
            }
        }, 'json');
    }
}

function viewNotification(id) {
    $.get('index.php?action=getnotificationdetails&id=' + id, function(response) {
        if(response.success) {
            var notification = response.notification;
            var html = `
                <div class="row">
                    <div class="col-md-6">
                        <strong>Destinatario:</strong> ${notification.recipient_name}<br>
                        <strong>Email:</strong> ${notification.recipient_email}<br>
                        <strong>Estado:</strong> ${notification.status}<br>
                        <strong>Fecha:</strong> ${notification.created_at}
                    </div>
                    <div class="col-md-6">
                        <strong>Asunto:</strong> ${notification.subject}<br>
                        ${notification.error_message ? '<strong>Error:</strong> ' + notification.error_message : ''}
                    </div>
                </div>
                <hr>
                <strong>Contenido:</strong>
                <div style="border:1px solid #ddd; padding:10px; margin-top:10px;">
                    ${notification.body}
                </div>
            `;
            $('#notificationDetails').html(html);
            $('#notificationModal').modal('show');
        }
    }, 'json');
}

function retryNotification(id) {
    if(confirm('¿Desea reintentar enviar esta notificación?')) {
        $.post('index.php?action=retrynotification', {id: id}, function(response) {
            if(response.success) {
                alert('Notificación reenviada exitosamente');
                location.reload();
            } else {
                alert('Error: ' + response.message);
            }
        }, 'json');
    }
}
</script>
