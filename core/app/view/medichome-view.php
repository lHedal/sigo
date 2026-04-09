<?php
// Obtener información del médico logueado
$medic = MedicData::getById($_SESSION['medic_id']);
$today = date('Y-m-d');

// Obtener citas del médico
$medic_reservations_today = [];
$medic_reservations_upcoming = [];

try {
    $all_reservations = ReservationData::getByMedicId($_SESSION['medic_id']);
    
    foreach($all_reservations as $reservation) {
        if($reservation->date_at == $today) {
            $medic_reservations_today[] = $reservation;
        } elseif($reservation->date_at > $today) {
            $medic_reservations_upcoming[] = $reservation;
        }
    }
} catch(Exception $e) {
    // Manejar error si no hay citas
}

// Estadísticas del médico
$total_reservations_today = count($medic_reservations_today);
$total_upcoming = count($medic_reservations_upcoming);

// Obtener pacientes del médico
$medic_patients = [];
try {
    $all_reservations = ReservationData::getByMedicId($_SESSION['medic_id']);
    $patient_ids = [];
    foreach($all_reservations as $res) {
        if(!in_array($res->pacient_id, $patient_ids)) {
            $patient_ids[] = $res->pacient_id;
            $medic_patients[] = PacientData::getById($res->pacient_id);
        }
    }
} catch(Exception $e) {
    // Manejar error
}
?>

<div class="row">
    <div class="col-md-12">
        <div class="page-header">
            <h1>
                <i class="fa fa-user-md"></i> Dashboard Médico
                <small>Bienvenido, Dr. <?php echo $medic->name . " " . $medic->lastname; ?></small>
            </h1>
        </div>
    </div>
</div>

<!-- Estadísticas del médico -->
<div class="row">
    <div class="col-md-3">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-calendar fa-3x"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="huge"><?php echo $total_reservations_today; ?></div>
                        <div>Citas Hoy</div>
                    </div>
                </div>
            </div>
            <a href="#today-appointments">
                <div class="panel-footer">
                    <span class="pull-left">Ver Detalles</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="panel panel-green">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-clock-o fa-3x"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="huge"><?php echo $total_upcoming; ?></div>
                        <div>Próximas Citas</div>
                    </div>
                </div>
            </div>
            <a href="#upcoming-appointments">
                <div class="panel-footer">
                    <span class="pull-left">Ver Agenda</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="panel panel-yellow">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-users fa-3x"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="huge"><?php echo count($medic_patients); ?></div>
                        <div>Mis Pacientes</div>
                    </div>
                </div>
            </div>
            <a href="#my-patients">
                <div class="panel-footer">
                    <span class="pull-left">Ver Pacientes</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="panel panel-red">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-support fa-3x"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="huge">24/7</div>
                        <div>Soporte</div>
                    </div>
                </div>
            </div>
            <a href="#support">
                <div class="panel-footer">
                    <span class="pull-left">Contactar</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>
</div>

<!-- Citas de hoy -->
<div class="row" id="today-appointments">
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-calendar"></i> Citas de Hoy - <?php echo date('d/m/Y'); ?>
                </h3>
            </div>
            <div class="panel-body">
                <?php if(!empty($medic_reservations_today)): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Hora</th>
                                    <th>Paciente</th>
                                    <th>Tratamiento</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($medic_reservations_today as $reservation): ?>
                                    <?php $patient = $reservation->getPacient(); ?>
                                    <tr>
                                        <td><strong><?php echo date('H:i', strtotime($reservation->time_at)); ?></strong></td>
                                        <td><?php echo $patient ? $patient->name . " " . $patient->lastname : "Sin asignar"; ?></td>
                                        <td><?php echo $reservation->title; ?></td>
                                        <td>
                                            <?php
                                            $status_class = "default";
                                            $status_text = "Programada";
                                            switch($reservation->status_id) {
                                                case 1:
                                                    $status_class = "primary";
                                                    $status_text = "Programada";
                                                    break;
                                                case 2:
                                                    $status_class = "warning";
                                                    $status_text = "En Proceso";
                                                    break;
                                                case 3:
                                                    $status_class = "success";
                                                    $status_text = "Completada";
                                                    break;
                                                case 4:
                                                    $status_class = "danger";
                                                    $status_text = "Cancelada";
                                                    break;
                                            }
                                            ?>
                                            <span class="label label-<?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted">No tiene citas programadas para hoy.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Próximas citas -->
    <div class="col-md-6" id="upcoming-appointments">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-clock-o"></i> Próximas Citas
                </h3>
            </div>
            <div class="panel-body">
                <?php if(!empty($medic_reservations_upcoming)): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Hora</th>
                                    <th>Paciente</th>
                                    <th>Tratamiento</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $upcoming_limited = array_slice($medic_reservations_upcoming, 0, 5);
                                foreach($upcoming_limited as $reservation): 
                                    $patient = $reservation->getPacient(); 
                                ?>
                                    <tr>
                                        <td><?php echo date('d/m/Y', strtotime($reservation->date_at)); ?></td>
                                        <td><?php echo date('H:i', strtotime($reservation->time_at)); ?></td>
                                        <td><?php echo $patient ? $patient->name . " " . $patient->lastname : "Sin asignar"; ?></td>
                                        <td><?php echo $reservation->title; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php if(count($medic_reservations_upcoming) > 5): ?>
                        <p class="text-muted">
                            <a href="index.php?view=medicreservations">Ver todas las citas (<?php echo count($medic_reservations_upcoming); ?> próximas)</a>
                        </p>
                    <?php endif; ?>
                <?php else: ?>
                    <p class="text-muted">No tiene citas próximas programadas.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Mis pacientes -->
<div class="row" id="my-patients">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-users"></i> Mis Pacientes Recientes
                </h3>
            </div>
            <div class="panel-body">
                <?php if(!empty($medic_patients)): ?>
                    <div class="row">
                        <?php 
                        $patients_limited = array_slice($medic_patients, 0, 6);
                        foreach($patients_limited as $patient): 
                        ?>
                            <div class="col-md-4 col-sm-6">
                                <div class="panel panel-info">
                                    <div class="panel-body text-center">
                                        <div class="patient-avatar">
                                            <i class="fa fa-user fa-2x"></i>
                                        </div>
                                        <h4><?php echo $patient->name . " " . $patient->lastname; ?></h4>
                                        <p class="text-muted">
                                            <?php echo $patient->sick ? substr($patient->sick, 0, 30) . "..." : "Sin diagnóstico"; ?>
                                        </p>
                                        <a href="index.php?view=editpacient&id=<?php echo $patient->id; ?>" class="btn btn-sm btn-primary">
                                            Ver Historial
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted">Aún no tiene pacientes asignados.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Enlaces rápidos -->
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-link"></i> Acceso Rápido
                </h3>
            </div>
            <div class="panel-body">
                <div class="btn-group btn-group-justified" role="group">
                    <a href="index.php?view=medicreservations" class="btn btn-primary">
                        <i class="fa fa-calendar"></i> Mis Citas
                    </a>
                    <a href="index.php?view=pacients" class="btn btn-success">
                        <i class="fa fa-users"></i> Todos los Pacientes
                    </a>
                    <a href="index.php?view=oncologywaitlist" class="btn btn-warning">
                        <i class="fa fa-clock-o"></i> Lista de Espera
                    </a>
                    <a href="index.php?view=oncologychairs" class="btn btn-info">
                        <i class="fa fa-bed"></i> Sillones
                    </a>
                    <a href="logout.php" class="btn btn-danger">
                        <i class="fa fa-sign-out"></i> Cerrar Sesión
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.huge {
    font-size: 40px;
}

.panel-green {
    border-color: #5cb85c;
}

.panel-green > .panel-heading {
    border-color: #5cb85c;
    color: white;
    background-color: #5cb85c;
}

.panel-green > a {
    color: #5cb85c;
}

.panel-green > a:hover {
    color: #3d8b3d;
}

.panel-yellow {
    border-color: #f0ad4e;
}

.panel-yellow > .panel-heading {
    border-color: #f0ad4e;
    color: white;
    background-color: #f0ad4e;
}

.panel-yellow > a {
    color: #f0ad4e;
}

.panel-yellow > a:hover {
    color: #df8a13;
}

.panel-red {
    border-color: #d9534f;
}

.panel-red > .panel-heading {
    border-color: #d9534f;
    color: white;
    background-color: #d9534f;
}

.panel-red > a {
    color: #d9534f;
}

.panel-red > a:hover {
    color: #c12e2a;
}

.patient-avatar {
    margin-bottom: 15px;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 50%;
    display: inline-block;
}
</style>
