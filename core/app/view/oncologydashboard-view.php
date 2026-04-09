<?php
// Las clases se cargan automáticamente mediante el autoloader
// No se necesitan inclusiones manuales

// Estadísticas del día
$today = date('Y-m-d');
$pending_waitlist = count(OncologyWaitlistData::getPending());
$today_appointments = count(ReservationData::getOncologyReservations($today));
$available_chairs = count(OncologyChairData::getAvailableChairs($today, '08:00:00', '18:00:00'));
$total_chairs = count(OncologyChairData::getAll());

// Estadísticas de notificaciones (simplificado)
$notification_stats = NotificationData::getNotificationStats();
$notifications_today = 0;
$notifications_pending = 0;

// Contar notificaciones del día actual si hay datos disponibles
foreach($notification_stats as $stat) {
    if($stat['date'] == $today && $stat['status'] == 'sent') {
        $notifications_today = $stat['total'];
        break;
    }
}

// Intentar obtener notificaciones pendientes si el modelo existe
try {
    if(class_exists('NotificationQueueData')) {
        $notifications_pending = count(NotificationQueueData::getByStatus('pending'));
    }
} catch (Exception $e) {
    $notifications_pending = 0;
}

// Próximas citas de oncología
$upcoming_appointments = ReservationData::getOncologyReservations($today);
?>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <h1>Dashboard - Oncología</h1>
            <br>
        </div>
    </div>

    <!-- Estadísticas principales -->
    <div class="row">
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-yellow">
                <div class="inner">
                    <h3><?php echo $pending_waitlist; ?></h3>
                    <p>En Lista de Espera</p>
                </div>
                <div class="icon">
                    <i class="fa fa-clock-o"></i>
                </div>
                <a href="index.php?view=oncologywaitlist" class="small-box-footer">
                    Ver lista <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-green">
                <div class="inner">
                    <h3><?php echo $today_appointments; ?></h3>
                    <p>Citas de Hoy</p>
                </div>
                <div class="icon">
                    <i class="fa fa-calendar"></i>
                </div>
                <a href="index.php?view=reservations&filter=oncology&date=<?php echo $today; ?>" class="small-box-footer">
                    Ver citas <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-aqua">
                <div class="inner">
                    <h3><?php echo $available_chairs; ?>/<?php echo $total_chairs; ?></h3>
                    <p>Sillones Disponibles</p>
                </div>
                <div class="icon">
                    <i class="fa fa-bed"></i>
                </div>
                <a href="index.php?view=oncologychairs" class="small-box-footer">
                    Ver sillones <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-red">
                <div class="inner">
                    <h3><?php echo ($total_chairs - $available_chairs); ?></h3>
                    <p>Sillones Ocupados</p>
                </div>
                <div class="icon">
                    <i class="fa fa-user-md"></i>
                </div>
                <a href="#" class="small-box-footer">
                    <i class="fa fa-heart"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Lista de Espera Urgente -->
        <div class="col-md-6">
            <div class="box box-warning">
                <div class="box-header with-border">
                    <h3 class="box-title">Lista de Espera - Casos Urgentes</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse">
                            <i class="fa fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="box-body" style="max-height: 300px; overflow-y: auto;">
                    <?php 
                    $urgent_waitlist = array_filter(OncologyWaitlistData::getPending(), function($item) {
                        return $item->priority_level >= 3;
                    });
                    
                    if(count($urgent_waitlist) > 0): ?>
                    <table class="table table-condensed">
                        <thead>
                            <tr>
                                <th>Paciente</th>
                                <th>Tratamiento</th>
                                <th>Prioridad</th>
                                <th>Fecha</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($urgent_waitlist as $item): ?>
                            <tr>
                                <td><?php echo $item->getPacient()->name . " " . $item->getPacient()->lastname; ?></td>
                                <td><?php echo $item->treatment_type; ?></td>                                <td>
                                    <span class="label <?php 
                                        echo $item->priority_level == 5 ? 'label-danger' : 
                                            ($item->priority_level == 4 ? 'label-danger' : 'label-warning'); 
                                    ?>">
                                        <?php 
                                        $priority_names = [
                                            3 => 'Alta',
                                            4 => 'Urgente', 
                                            5 => 'Crítica'
                                        ];
                                        echo isset($priority_names[$item->priority_level]) ? $priority_names[$item->priority_level] : 'Prioridad ' . $item->priority_level;
                                        ?>
                                    </span>
                                </td>
                                <td><?php echo date('d/m', strtotime($item->requested_date)); ?></td>
                                <td>
                                    <button class="btn btn-xs btn-success" onclick="autoAssign(<?php echo $item->id; ?>)">
                                        <i class="fa fa-magic"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <p class="text-center text-muted">No hay casos urgentes en lista de espera</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Citas de Hoy -->
        <div class="col-md-6">
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">Citas de Hoy - Oncología</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse">
                            <i class="fa fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="box-body" style="max-height: 300px; overflow-y: auto;">
                    <?php if(count($upcoming_appointments) > 0): ?>
                    <table class="table table-condensed">
                        <thead>
                            <tr>
                                <th>Hora</th>
                                <th>Paciente</th>
                                <th>Médico</th>
                                <th>Sillón</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($upcoming_appointments as $appointment): ?>
                            <tr>
                                <td><?php echo substr($appointment->time_at, 0, 5); ?></td>
                                <td><?php echo $appointment->getPacient()->name . " " . $appointment->getPacient()->lastname; ?></td>
                                <td><?php echo $appointment->getMedic()->name . " " . $appointment->getMedic()->lastname; ?></td>
                                <td>
                                    <?php 
                                    $chair = $appointment->getChair();
                                    echo $chair ? $chair->name : 'N/A';
                                    ?>
                                </td>
                                <td>
                                    <span class="label <?php 
                                        echo $appointment->status_id == 1 ? 'label-warning' : 
                                            ($appointment->status_id == 2 ? 'label-success' : 'label-default'); 
                                    ?>">
                                        <?php echo $appointment->getStatus()->name; ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <p class="text-center text-muted">No hay citas de oncología programadas para hoy</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas de Notificaciones -->
    <div class="row">
        <div class="col-lg-6 col-xs-6">
            <div class="small-box bg-purple">
                <div class="inner">
                    <h3><?php echo $notifications_today; ?></h3>
                    <p>Notificaciones Enviadas Hoy</p>
                </div>
                <div class="icon">
                    <i class="fa fa-envelope"></i>
                </div>
                <a href="index.php?view=notifications" class="small-box-footer">
                    Ver historial <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-6 col-xs-6">
            <div class="small-box bg-maroon">
                <div class="inner">
                    <h3><?php echo $notifications_pending; ?></h3>
                    <p>Notificaciones Pendientes</p>
                </div>
                <div class="icon">
                    <i class="fa fa-clock-o"></i>
                </div>
                <a href="index.php?view=notificationqueue" class="small-box-footer">
                    Ver cola <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Acciones Rápidas -->
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Acciones Rápidas</h3>
                </div>
                <div class="box-body">
                    <div class="btn-group" role="group">
                        <a href="index.php?view=newoncologywaitlist" class="btn btn-default">
                            <i class="fa fa-plus"></i> Agregar a Lista de Espera
                        </a>
                        <a href="index.php?view=newreservation" class="btn btn-primary">
                            <i class="fa fa-calendar-plus-o"></i> Nueva Cita
                        </a>
                        <button type="button" class="btn btn-success" onclick="processWaitlist()">
                            <i class="fa fa-magic"></i> Procesar Lista Automáticamente
                        </button>
                        <a href="index.php?view=oncologychairs" class="btn btn-info">
                            <i class="fa fa-bed"></i> Gestionar Sillones
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
function autoAssign(waitlistId) {
    if (confirm('¿Desea asignar automáticamente esta cita?')) {
        $.ajax({
            url: 'index.php?action=autoassignoncology',
            method: 'POST',
            data: { waitlist_id: waitlistId },
            success: function(response) {
                var result = JSON.parse(response);
                if (result.success) {
                    alert('Cita asignada exitosamente');
                    location.reload();
                } else {
                    alert('No se pudo asignar la cita: ' + result.message);
                }
            },
            error: function() {
                alert('Error al procesar la solicitud');
            }
        });
    }
}

function processWaitlist() {
    if (confirm('¿Desea procesar automáticamente toda la lista de espera?')) {
        $.ajax({
            url: 'index.php?action=processwaitlist',
            method: 'POST',
            success: function(response) {
                var result = JSON.parse(response);
                alert('Se asignaron ' + result.assigned_count + ' citas automáticamente');
                location.reload();
            },
            error: function() {
                alert('Error al procesar la lista de espera');
            }
        });
    }
}
</script>
