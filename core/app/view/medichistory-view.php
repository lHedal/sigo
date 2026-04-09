<?php
$medic = MedicData::getById($_GET["id"]);
?>
<div class="row">
    <div class="col-md-12">
        <div class="page-header">
            <h1>
                <i class="fa fa-history"></i> Historial del Dr. <?php echo $medic->name . " " . $medic->lastname; ?>
                <small>Registro completo de actividades médicas</small>
            </h1>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <!-- Información del médico -->
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-user-md"></i> Información del Médico
                </h3>
            </div>
            <div class="panel-body">
                <?php if($medic->image): ?>
                    <div class="text-center">
                        <img src="storage/medics/<?php echo $medic->image; ?>" class="img-thumbnail" style="max-width: 150px;" alt="Foto del médico">
                    </div>
                    <br>
                <?php endif; ?>
                <p><strong>Nombre:</strong> Dr. <?php echo $medic->name . " " . $medic->lastname; ?></p>
                <p><strong>Email:</strong> <?php echo $medic->email; ?></p>
                <p><strong>Teléfono:</strong> <?php echo $medic->phone; ?></p>
                <p><strong>Especialidad:</strong> Médico Oncólogo</p>
                <?php if($medic->created_at): ?>
                <p><strong>Registro:</strong> <?php echo date('d/m/Y', strtotime($medic->created_at)); ?></p>
                <?php endif; ?>
                
                <div class="btn-group btn-group-justified">
                    <a href="index.php?view=editmedic&id=<?php echo $medic->id; ?>" class="btn btn-primary">
                        <i class="fa fa-edit"></i> Editar
                    </a>
                    <a href="index.php?view=medics" class="btn btn-default">
                        <i class="fa fa-arrow-left"></i> Volver
                    </a>
                </div>
            </div>
        </div>

        <!-- Estadísticas rápidas -->
        <div class="panel panel-info">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-chart-bar"></i> Estadísticas
                </h3>
            </div>
            <div class="panel-body">
                <?php
                try {
                    $all_reservations = ReservationData::getByMedicId($medic->id);
                    $total_appointments = $all_reservations ? count($all_reservations) : 0;
                    
                    $completed_appointments = 0;
                    $pending_appointments = 0;
                    $cancelled_appointments = 0;
                    
                    if($all_reservations) {
                        foreach($all_reservations as $res) {
                            switch($res->status_id) {
                                case 3: $completed_appointments++; break;
                                case 4: $cancelled_appointments++; break;
                                default: $pending_appointments++; break;
                            }
                        }
                    }
                ?>
                    <div class="row">
                        <div class="col-xs-6">
                            <div class="text-center">
                                <h4 class="text-primary"><?php echo $total_appointments; ?></h4>
                                <small>Total Citas</small>
                            </div>
                        </div>
                        <div class="col-xs-6">
                            <div class="text-center">
                                <h4 class="text-success"><?php echo $completed_appointments; ?></h4>
                                <small>Completadas</small>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 10px;">
                        <div class="col-xs-6">
                            <div class="text-center">
                                <h4 class="text-warning"><?php echo $pending_appointments; ?></h4>
                                <small>Pendientes</small>
                            </div>
                        </div>
                        <div class="col-xs-6">
                            <div class="text-center">
                                <h4 class="text-danger"><?php echo $cancelled_appointments; ?></h4>
                                <small>Canceladas</small>
                            </div>
                        </div>
                    </div>
                <?php
                } catch(Exception $e) {
                    echo "<p class='text-danger'>Error cargando estadísticas: " . $e->getMessage() . "</p>";
                }
                ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <!-- Historial de citas -->
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-calendar"></i> Historial de Citas
                </h3>
            </div>
            <div class="panel-body">
                <?php
                try {
                    $reservations = ReservationData::getByMedicId($medic->id);
                    
                    if($reservations && count($reservations) > 0) {
                        // Ordenar por fecha más reciente
                        usort($reservations, function($a, $b) {
                            return strtotime($b->date_at . ' ' . $b->time_at) - strtotime($a->date_at . ' ' . $a->time_at);
                        });
                ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Hora</th>
                                    <th>Paciente</th>
                                    <th>Tratamiento</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($reservations as $res): 
                                    $pacient = $res->getPacient();
                                ?>
                                    <tr>
                                        <td><?php echo date('d/m/Y', strtotime($res->date_at)); ?></td>
                                        <td><?php echo date('H:i', strtotime($res->time_at)); ?></td>
                                        <td>
                                            <?php if($pacient): ?>
                                                <?php echo $pacient->name . " " . $pacient->lastname; ?>
                                            <?php else: ?>
                                                <span class="text-muted">Paciente no encontrado</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $res->title; ?></td>
                                        <td>
                                            <?php
                                            $status_class = "default";
                                            $status_text = "Sin definir";
                                            switch($res->status_id) {
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
                                        <td>
                                            <div class="btn-group btn-group-xs">
                                                <a href="index.php?view=editreservation&id=<?php echo $res->id; ?>" class="btn btn-primary">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <?php if($pacient): ?>
                                                <a href="index.php?view=pacienthistory&id=<?php echo $pacient->id; ?>" class="btn btn-info">
                                                    <i class="fa fa-user"></i>
                                                </a>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php
                    } else {
                        echo "<div class='alert alert-info'>";
                        echo "<h4><i class='fa fa-info-circle'></i> Sin Historial</h4>";
                        echo "<p>Este médico aún no tiene citas registradas en el sistema.</p>";
                        echo "</div>";
                    }
                } catch(Exception $e) {
                    echo "<div class='alert alert-danger'>";
                    echo "<h4><i class='fa fa-exclamation-triangle'></i> Error</h4>";
                    echo "<p>No se pudo cargar el historial: " . $e->getMessage() . "</p>";
                    echo "</div>";
                }
                ?>
            </div>
        </div>
    </div>
</div>
