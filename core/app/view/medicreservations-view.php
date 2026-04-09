<?php
$medic = MedicData::getById($_SESSION['medic_id']);
$medic_reservations = ReservationData::getByMedicId($_SESSION['medic_id']);
?>

<div class="row">
    <div class="col-md-12">
        <div class="page-header">
            <h1>
                <i class="fa fa-calendar"></i> Mis Citas y Reservaciones
                <small>Dr. <?php echo $medic->name . " " . $medic->lastname; ?></small>
            </h1>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <!-- Filtros -->
        <div class="panel panel-default">
            <div class="panel-body">
                <form class="form-inline">
                    <div class="form-group">
                        <label for="filter_date">Fecha:</label>
                        <input type="date" class="form-control" id="filter_date" name="filter_date">
                    </div>
                    <div class="form-group">
                        <label for="filter_status">Estado:</label>
                        <select class="form-control" id="filter_status">
                            <option value="">Todos</option>
                            <option value="1">Programada</option>
                            <option value="2">En Proceso</option>
                            <option value="3">Completada</option>
                            <option value="4">Cancelada</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <button type="button" class="btn btn-primary" onclick="filterReservations()">
                            <i class="fa fa-search"></i> Filtrar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabla de reservaciones -->
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-list"></i> Lista de Citas
                </h3>
                <div class="pull-right">
                    <a href="index.php?view=medichome" class="btn btn-sm btn-primary">
                        <i class="fa fa-arrow-left"></i> Volver al Dashboard
                    </a>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="panel-body">
                <?php if($medic_reservations != null && count($medic_reservations) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="reservations-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Fecha</th>
                                    <th>Hora</th>
                                    <th>Paciente</th>
                                    <th>Tratamiento</th>
                                    <th>Sillón</th>
                                    <th>Estado</th>
                                    <th>Nota</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                // Ordenar por fecha más reciente primero
                                usort($medic_reservations, function($a, $b) {
                                    $date_a = strtotime($a->date_at . ' ' . $a->time_at);
                                    $date_b = strtotime($b->date_at . ' ' . $b->time_at);
                                    return $date_b - $date_a; // Más reciente primero
                                });
                                
                                foreach($medic_reservations as $reservation):
                                    $pacient = $reservation->getPacient();
                                    $oncology_chair = null;
                                    if($reservation->oncology_chair_id) {
                                        $oncology_chair = OncologyChairData::getById($reservation->oncology_chair_id);
                                    }
                                ?>
                                    <tr data-date="<?php echo $reservation->date_at; ?>" data-status="<?php echo $reservation->status_id; ?>">
                                        <td><?php echo $reservation->id; ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($reservation->date_at)); ?></td>
                                        <td><?php echo date('H:i', strtotime($reservation->time_at)); ?></td>
                                        <td>
                                            <?php if($pacient): ?>
                                                <strong><?php echo $pacient->name . " " . $pacient->lastname; ?></strong><br>
                                                <small class="text-muted"><?php echo $pacient->email; ?></small>
                                            <?php else: ?>
                                                <span class="text-muted">Sin asignar</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $reservation->title; ?></td>
                                        <td>
                                            <?php if($oncology_chair): ?>
                                                <?php echo $oncology_chair->name; ?>
                                            <?php else: ?>
                                                <span class="text-muted">Sin asignar</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php
                                            $status_class = "default";
                                            $status_text = "Sin definir";
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
                                        <td>
                                            <?php if($reservation->note): ?>
                                                <span title="<?php echo htmlentities($reservation->note); ?>">
                                                    <?php echo substr($reservation->note, 0, 30) . (strlen($reservation->note) > 30 ? '...' : ''); ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">Sin notas</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-xs">
                                                <?php if($pacient): ?>
                                                    <a href="index.php?view=editpacient&id=<?php echo $pacient->id; ?>" 
                                                       class="btn btn-info" title="Ver Paciente">
                                                        <i class="fa fa-user"></i>
                                                    </a>
                                                <?php endif; ?>
                                                
                                                <a href="index.php?view=editreservation&id=<?php echo $reservation->id; ?>" 
                                                   class="btn btn-warning" title="Editar Cita">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                
                                                <?php if($reservation->status_id != 3): ?>
                                                    <button class="btn btn-success" 
                                                            onclick="completeReservation(<?php echo $reservation->id; ?>)" 
                                                            title="Marcar como Completada">
                                                        <i class="fa fa-check"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <h4><i class="fa fa-info-circle"></i> Sin Citas</h4>
                        No tiene citas programadas en el sistema. Las nuevas citas aparecerán aquí automáticamente.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Estadísticas rápidas -->
<div class="row">
    <div class="col-md-3">
        <div class="panel panel-primary">
            <div class="panel-heading text-center">
                <h4>Hoy</h4>
            </div>
            <div class="panel-body text-center">
                <?php
                $today = date('Y-m-d');
                $today_count = 0;
                if($medic_reservations) {
                    foreach($medic_reservations as $res) {
                        if($res->date_at == $today) $today_count++;
                    }
                }
                ?>
                <h2><?php echo $today_count; ?></h2>
                <p>Citas</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="panel panel-success">
            <div class="panel-heading text-center">
                <h4>Completadas</h4>
            </div>
            <div class="panel-body text-center">
                <?php
                $completed_count = 0;
                if($medic_reservations) {
                    foreach($medic_reservations as $res) {
                        if($res->status_id == 3) $completed_count++;
                    }
                }
                ?>
                <h2><?php echo $completed_count; ?></h2>
                <p>Total</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="panel panel-warning">
            <div class="panel-heading text-center">
                <h4>Pendientes</h4>
            </div>
            <div class="panel-body text-center">
                <?php
                $pending_count = 0;
                if($medic_reservations) {
                    foreach($medic_reservations as $res) {
                        if($res->status_id == 1 || $res->status_id == 2) $pending_count++;
                    }
                }
                ?>
                <h2><?php echo $pending_count; ?></h2>
                <p>Citas</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="panel panel-info">
            <div class="panel-heading text-center">
                <h4>Total</h4>
            </div>
            <div class="panel-body text-center">
                <h2><?php echo $medic_reservations ? count($medic_reservations) : 0; ?></h2>
                <p>Todas</p>
            </div>
        </div>
    </div>
</div>

<script>
function filterReservations() {
    var filterDate = document.getElementById('filter_date').value;
    var filterStatus = document.getElementById('filter_status').value;
    var table = document.getElementById('reservations-table');
    var rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
    
    for (var i = 0; i < rows.length; i++) {
        var row = rows[i];
        var rowDate = row.getAttribute('data-date');
        var rowStatus = row.getAttribute('data-status');
        var showRow = true;
        
        if (filterDate && rowDate !== filterDate) {
            showRow = false;
        }
        
        if (filterStatus && rowStatus !== filterStatus) {
            showRow = false;
        }
        
        if (showRow) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    }
}

function completeReservation(reservationId) {
    if (confirm('¿Marcar esta cita como completada?')) {
        // Aquí podrías hacer una petición AJAX para actualizar el estado
        window.location.href = 'index.php?action=updatereservationstatus&id=' + reservationId + '&status=3&redirect=medicreservations';
    }
}

// Aplicar DataTables para mejor funcionalidad
$(document).ready(function() {
    $('#reservations-table').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json"
        },
        "order": [[ 1, "desc" ]], // Ordenar por fecha descendente
        "pageLength": 25
    });
});
</script>
