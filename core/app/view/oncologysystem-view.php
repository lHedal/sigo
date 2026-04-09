<?php
$today = date('Y-m-d');
$total_chairs = count(OncologyChairData::getAll());
$active_chairs = count(OncologyChairData::getActiveChairs());
$pending_waitlist = count(OncologyWaitlistData::getPending());
$today_appointments = count(ReservationData::getOncologyReservations($today));
$total_waitlist = count(OncologyWaitlistData::getAll());
$total_pacients = count(PacientData::getAll());
$oncology_medics = count(MedicData::getOncologyMedics());
?>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <h1>Sistema de Oncología - Estado General</h1>
            <p class="lead">Panel completo de gestión del departamento de oncología</p>
            <br>
        </div>
    </div>

    <!-- Estadísticas principales -->
    <div class="row">
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-aqua">
                <div class="inner">
                    <h3><?php echo $active_chairs; ?>/<?php echo $total_chairs; ?></h3>
                    <p>Sillones Activos</p>
                </div>
                <div class="icon">
                    <i class="fa fa-bed"></i>
                </div>
                <a href="index.php?view=oncologychairs" class="small-box-footer">
                    Gestionar <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-yellow">
                <div class="inner">
                    <h3><?php echo $pending_waitlist; ?></h3>
                    <p>Pacientes en Espera</p>
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
                <a href="index.php?view=oncologycalendar" class="small-box-footer">
                    Ver calendario <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-red">
                <div class="inner">
                    <h3><?php echo $oncology_medics; ?></h3>
                    <p>Médicos Oncólogos</p>
                </div>
                <div class="icon">
                    <i class="fa fa-user-md"></i>
                </div>
                <a href="index.php?view=medics" class="small-box-footer">
                    Ver médicos <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Acciones rápidas -->
    <div class="row">
        <div class="col-md-6">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Acciones Rápidas</h3>
                </div>
                <div class="box-body">
                    <div class="btn-group-vertical btn-block">
                        <a href="index.php?view=newoncologywaitlist" class="btn btn-warning btn-lg">
                            <i class="fa fa-plus"></i> Agregar a Lista de Espera
                        </a>
                        <a href="index.php?view=newreservation" class="btn btn-success btn-lg">
                            <i class="fa fa-calendar-plus-o"></i> Nueva Cita Oncológica
                        </a>
                        <a href="index.php?view=newoncologychair" class="btn btn-info btn-lg">
                            <i class="fa fa-bed"></i> Nuevo Sillón
                        </a>
                        <button class="btn btn-primary btn-lg" onclick="processAllWaitlist()">
                            <i class="fa fa-magic"></i> Procesar Lista Completa
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">Estado del Sistema</h3>
                </div>
                <div class="box-body">
                    <ul class="list-group">
                        <li class="list-group-item">
                            <span class="badge"><?php echo $total_chairs; ?></span>
                            Total de Sillones
                        </li>
                        <li class="list-group-item">
                            <span class="badge"><?php echo $total_waitlist; ?></span>
                            Total en Lista de Espera
                        </li>
                        <li class="list-group-item">
                            <span class="badge"><?php echo $total_pacients; ?></span>
                            Total de Pacientes
                        </li>
                        <li class="list-group-item">
                            <span class="badge"><?php echo $oncology_medics; ?></span>
                            Médicos Oncólogos
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Enlaces del sistema -->
    <div class="row">
        <div class="col-md-12">
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title">Acceso Completo al Sistema</h3>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-3">
                            <h5><i class="fa fa-dashboard"></i> Dashboards</h5>
                            <ul class="list-unstyled">
                                <li><a href="index.php?view=oncologydashboard">Dashboard Oncología</a></li>
                                <li><a href="index.php?view=home">Dashboard Principal</a></li>
                            </ul>
                        </div>
                        <div class="col-md-3">
                            <h5><i class="fa fa-users"></i> Gestión</h5>
                            <ul class="list-unstyled">
                                <li><a href="index.php?view=oncologywaitlist">Lista de Espera</a></li>
                                <li><a href="index.php?view=oncologychairs">Sillones</a></li>
                                <li><a href="index.php?view=pacients">Pacientes</a></li>
                                <li><a href="index.php?view=medics">Médicos</a></li>
                            </ul>
                        </div>
                        <div class="col-md-3">
                            <h5><i class="fa fa-calendar"></i> Calendario</h5>
                            <ul class="list-unstyled">
                                <li><a href="index.php?view=oncologycalendar">Calendario Oncología</a></li>
                                <li><a href="index.php?view=calendar">Calendario General</a></li>
                                <li><a href="index.php?view=reservations">Todas las Citas</a></li>
                            </ul>
                        </div>
                        <div class="col-md-3">
                            <h5><i class="fa fa-cog"></i> Configuración</h5>
                            <ul class="list-unstyled">
                                <li><a href="index.php?view=notificationconfig">Configurar Notificaciones</a></li>
                                <li><a href="index.php?view=notificationtypes">Tipos de Notificación</a></li>
                                <li><a href="index.php?view=notificationqueue">Cola de Envíos</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
function processAllWaitlist() {
    if(confirm('¿Desea procesar automáticamente toda la lista de espera? Esto intentará asignar citas a todos los pacientes pendientes.')) {
        $.ajax({
            url: 'index.php?action=processallwaitlist',
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                if(response.success) {
                    alert('Se procesaron ' + response.processed + ' elementos de la lista de espera.');
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('Error al procesar la lista de espera');
            }
        });
    }
}
</script>
