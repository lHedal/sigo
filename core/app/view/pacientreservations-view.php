<?php
$pacient = PacientData::getById($_SESSION['pacient_id']);
$pacient_reservations = ReservationData::getByPacientId($_SESSION['pacient_id']);
?>

<div class="row">
    <div class="col-md-12">
        <div class="page-header">
            <h1>
                <i class="fa fa-calendar"></i> Mis Citas Médicas
                <small><?php echo $pacient->name . " " . $pacient->lastname; ?></small>
            </h1>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-list"></i> Historial de Citas
                </h3>
                <div class="pull-right">
                    <a href="index.php?view=pacienthome" class="btn btn-sm btn-primary">
                        <i class="fa fa-arrow-left"></i> Volver al Panel
                    </a>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="panel-body">
                <?php if($pacient_reservations != null && count($pacient_reservations) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Hora</th>
                                    <th>Médico</th>
                                    <th>Tratamiento</th>
                                    <th>Estado</th>
                                    <th>Nota</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                foreach($pacient_reservations as $reservation):
                                    $medic = $reservation->getMedic();
                                ?>
                                    <tr>
                                        <td><?php echo date('d/m/Y', strtotime($reservation->date_at)); ?></td>
                                        <td><?php echo date('H:i', strtotime($reservation->time_at)); ?></td>
                                        <td>
                                            <?php if($medic): ?>
                                                Dr. <?php echo $medic->name . " " . $medic->lastname; ?>
                                            <?php else: ?>
                                                <span class="text-muted">Sin asignar</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $reservation->title; ?></td>
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
                                                <?php echo substr($reservation->note, 0, 50) . (strlen($reservation->note) > 50 ? '...' : ''); ?>
                                            <?php else: ?>
                                                <span class="text-muted">Sin notas</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <h4><i class="fa fa-info-circle"></i> Sin Citas</h4>
                        No tienes citas programadas en el sistema.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>