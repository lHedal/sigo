<?php
$pacient = PacientData::getById($_GET["id"]);
?>
<div class="row">
    <div class="col-md-12">
        <div class="page-header">
            <h1>
                <i class="fa fa-history"></i> Historial de <?php echo $pacient->name . " " . $pacient->lastname; ?>
                <small>Registro completo de actividades médicas</small>
            </h1>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <!-- Información del paciente -->
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-user"></i> Información del Paciente
                </h3>
            </div>
            <div class="panel-body">
                <?php if($pacient->image): ?>
                    <div class="text-center">
                        <img src="storage/pacients/<?php echo $pacient->image; ?>" class="img-thumbnail" style="max-width: 150px;" alt="Foto del paciente">
                    </div>
                    <br>
                <?php endif; ?>
                <p><strong>Nombre:</strong> <?php echo $pacient->name . " " . $pacient->lastname; ?></p>
                <p><strong>Email:</strong> <?php echo $pacient->email; ?></p>
                <p><strong>Teléfono:</strong> <?php echo $pacient->phone; ?></p>
                <?php if($pacient->day_of_birth): ?>
                <p><strong>Fecha de Nacimiento:</strong> <?php echo date('d/m/Y', strtotime($pacient->day_of_birth)); ?></p>
                <p><strong>Edad:</strong> <?php echo date('Y') - date('Y', strtotime($pacient->day_of_birth)); ?> años</p>
                <?php endif; ?>
                <?php if($pacient->created_at): ?>
                <p><strong>Registro:</strong> <?php echo date('d/m/Y', strtotime($pacient->created_at)); ?></p>
                <?php endif; ?>
                
                <div class="btn-group btn-group-justified">
                    <a href="index.php?view=editpacient&id=<?php echo $pacient->id; ?>" class="btn btn-primary">
                        <i class="fa fa-edit"></i> Editar
                    </a>
                    <a href="index.php?view=pacients" class="btn btn-default">
                        <i class="fa fa-arrow-left"></i> Volver
                    </a>
                </div>
            </div>
        </div>

        <!-- Información médica resumen -->
        <div class="panel panel-info">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-stethoscope"></i> Resumen Médico
                </h3>
            </div>
            <div class="panel-body">
                <?php if($pacient->sick): ?>
                    <p><strong>Diagnóstico:</strong></p>
                    <div class="alert alert-info" style="padding: 8px; margin-bottom: 10px;">
                        <?php echo $pacient->sick; ?>
                    </div>
                <?php endif; ?>
                
                <?php if($pacient->alergy): ?>
                    <p><strong>Alergias:</strong></p>
                    <div class="alert alert-warning" style="padding: 8px; margin-bottom: 10px;">
                        <i class="fa fa-exclamation-triangle"></i> <?php echo $pacient->alergy; ?>
                    </div>
                <?php endif; ?>
                
                <?php if($pacient->medicaments): ?>
                    <p><strong>Medicamentos:</strong></p>
                    <div class="alert alert-success" style="padding: 8px; margin-bottom: 10px;">
                        <?php echo $pacient->medicaments; ?>
                    </div>
                <?php endif; ?>
                
                <?php
                try {
                    $all_reservations = ReservationData::getByPacientId($pacient->id);
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
                    <hr>
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
                    <i class="fa fa-calendar"></i> Historial de Citas Médicas
                </h3>
            </div>
            <div class="panel-body">
                <?php
                try {
                    $reservations = ReservationData::getByPacientId($pacient->id);
                    
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
                                    <th>Médico</th>
                                    <th>Tratamiento</th>
                                    <th>Estado</th>
                                    <th>Notas</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($reservations as $res): 
                                    $medic = $res->getMedic();
                                    $is_past = strtotime($res->date_at) < strtotime(date('Y-m-d'));
                                ?>
                                    <tr <?php echo $is_past ? 'class="text-muted"' : ''; ?>>
                                        <td><?php echo date('d/m/Y', strtotime($res->date_at)); ?></td>
                                        <td><?php echo date('H:i', strtotime($res->time_at)); ?></td>
                                        <td>
                                            <?php if($medic): ?>
                                                Dr. <?php echo $medic->name . " " . $medic->lastname; ?>
                                                <br><small class="text-muted">Médico Oncólogo</small>
                                            <?php else: ?>
                                                <span class="text-muted">Sin asignar</span>
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
                                            <?php if($is_past && $res->status_id == 1): ?>
                                                <br><small class="text-warning">Pasada</small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($res->note): ?>
                                                <span title="<?php echo htmlspecialchars($res->note); ?>" data-toggle="tooltip">
                                                    <?php echo substr($res->note, 0, 30) . (strlen($res->note) > 30 ? '...' : ''); ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">Sin notas</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-xs">
                                                <a href="index.php?view=editreservation&id=<?php echo $res->id; ?>" class="btn btn-primary" title="Editar cita">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <?php if($medic): ?>
                                                <a href="index.php?view=medichistory&id=<?php echo $medic->id; ?>" class="btn btn-info" title="Ver médico">
                                                    <i class="fa fa-user-md"></i>
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
                        echo "<p>Este paciente aún no tiene citas registradas en el sistema.</p>";
                        echo "<a href='index.php?view=newreservation&pacient_id={$pacient->id}' class='btn btn-success'>";
                        echo "<i class='fa fa-plus'></i> Agendar Primera Cita</a>";
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

        <!-- Lista de espera oncológica si existe -->
        <div class="panel panel-warning">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-clock-o"></i> Estado en Lista de Espera Oncológica
                </h3>
            </div>
            <div class="panel-body">
                <?php
                try {
                    $waitlist_entries = OncologyWaitlistData::getByPacientId($pacient->id);
                    
                    if($waitlist_entries && count($waitlist_entries) > 0) {
                        echo "<div class='table-responsive'>";
                        echo "<table class='table table-condensed'>";
                        echo "<tr><th>Fecha Ingreso</th><th>Prioridad</th><th>Estado</th><th>Observaciones</th></tr>";
                        
                        foreach($waitlist_entries as $entry) {
                            echo "<tr>";
                            echo "<td>" . date('d/m/Y', strtotime($entry->created_at)) . "</td>";
                            echo "<td><span class='label label-warning'>Normal</span></td>";
                            echo "<td><span class='label label-info'>En Espera</span></td>";
                            echo "<td>" . ($entry->observations ? $entry->observations : 'Sin observaciones') . "</td>";
                            echo "</tr>";
                        }
                        echo "</table>";
                        echo "</div>";
                    } else {
                        echo "<p class='text-muted'>Este paciente no está en la lista de espera oncológica.</p>";
                    }
                } catch(Exception $e) {
                    echo "<p class='text-muted'>Lista de espera no disponible.</p>";
                }
                ?>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
    // Habilitar tooltips
    $('[data-toggle="tooltip"]').tooltip();
});
</script>
