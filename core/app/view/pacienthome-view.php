<?php
$pacient = PacientData::getById($_SESSION['pacient_id']);
?>

<div class="row">
    <div class="col-md-12">
        <div class="page-header">
            <h1>
                <i class="fa fa-user"></i> Mi Panel de Paciente
                <small>Bienvenido, <?php echo $pacient->name . " " . $pacient->lastname; ?></small>
            </h1>
        </div>
    </div>
</div>

<!-- Información del paciente -->
<div class="row">
    <div class="col-md-4">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-user"></i> Mi Información
                </h3>
            </div>
            <div class="panel-body">
                <p><strong>Nombre:</strong> <?php echo $pacient->name . " " . $pacient->lastname; ?></p>
                <p><strong>Email:</strong> <?php echo $pacient->email; ?></p>
                <p><strong>Teléfono:</strong> <?php echo $pacient->phone; ?></p>
                <p><strong>Fecha de Nacimiento:</strong> <?php echo date('d/m/Y', strtotime($pacient->day_of_birth)); ?></p>
                <?php if($pacient->sick): ?>
                <p><strong>Diagnóstico:</strong> <?php echo $pacient->sick; ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-calendar"></i> Mis Citas Próximas
                </h3>
            </div>
            <div class="panel-body">
                <?php
                try {
                    $reservations = ReservationData::getByPacientId($_SESSION['pacient_id']);
                    $upcoming_reservations = [];
                    $today = date('Y-m-d');
                    
                    if($reservations) {
                        foreach($reservations as $res) {
                            if($res->date_at >= $today) {
                                $upcoming_reservations[] = $res;
                            }
                        }
                    }
                    
                    if(count($upcoming_reservations) > 0) {
                        echo "<div class='table-responsive'>";
                        echo "<table class='table table-striped'>";
                        echo "<tr><th>Fecha</th><th>Hora</th><th>Médico</th><th>Tratamiento</th><th>Estado</th></tr>";
                        
                        foreach($upcoming_reservations as $res) {
                            $medic = $res->getMedic();
                            $status_text = "Programada";
                            switch($res->status_id) {
                                case 2: $status_text = "En Proceso"; break;
                                case 3: $status_text = "Completada"; break;
                                case 4: $status_text = "Cancelada"; break;
                            }
                            
                            echo "<tr>";
                            echo "<td>" . date('d/m/Y', strtotime($res->date_at)) . "</td>";
                            echo "<td>" . date('H:i', strtotime($res->time_at)) . "</td>";
                            echo "<td>Dr. " . ($medic ? $medic->name . " " . $medic->lastname : "Sin asignar") . "</td>";
                            echo "<td>" . $res->title . "</td>";
                            echo "<td><span class='label label-info'>" . $status_text . "</span></td>";
                            echo "</tr>";
                        }
                        echo "</table>";
                        echo "</div>";
                    } else {
                        echo "<p class='text-muted'>No tienes citas próximas programadas.</p>";
                    }
                } catch(Exception $e) {
                    echo "<p class='text-danger'>Error al cargar las citas: " . $e->getMessage() . "</p>";
                }
                ?>
            </div>
        </div>
    </div>
</div>

<!-- Acciones rápidas -->
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-cogs"></i> Acciones Rápidas
                </h3>
            </div>
            <div class="panel-body">
                <div class="btn-group btn-group-justified">
                    <a href="index.php?view=pacientreservations" class="btn btn-primary">
                        <i class="fa fa-calendar"></i> Ver Todas mis Citas
                    </a>
                    <a href="logout.php" class="btn btn-danger">
                        <i class="fa fa-sign-out"></i> Cerrar Sesión
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>