<?php
// Determinar si se está buscando un paciente específico
$pacient_id = isset($_GET["id"]) ? $_GET["id"] : null;
$pacient = $pacient_id ? PacientData::getById($pacient_id) : null;
?>
<div class="row">
    <div class="col-md-12">
        <div class="page-header">
            <h1>
                <i class="fa fa-user"></i> 
                <?php if($pacient): ?>
                    Información del Paciente: <?php echo $pacient->name . " " . $pacient->lastname; ?>
                <?php else: ?>
                    Información de Paciente
                <?php endif; ?>
                <small>Vista detallada del paciente</small>
            </h1>
        </div>
    </div>
</div>

<?php if(!$pacient && !$pacient_id): ?>
    <!-- Selector de paciente si no se especifica uno -->
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <i class="fa fa-search"></i> Seleccionar Paciente
                    </h3>
                </div>
                <div class="panel-body">
                    <form method="get" action="index.php">
                        <input type="hidden" name="view" value="pacient">
                        <div class="form-group">
                            <label>Seleccionar Paciente:</label>
                            <select name="id" class="form-control" required>
                                <option value="">-- Seleccione un paciente --</option>
                                <?php
                                try {
                                    $all_pacients = PacientData::getAll();
                                    foreach($all_pacients as $p) {
                                        echo "<option value='{$p->id}'>{$p->name} {$p->lastname} - {$p->email}</option>";
                                    }
                                } catch(Exception $e) {
                                    echo "<option disabled>Error cargando pacientes</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-eye"></i> Ver Información
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php elseif($pacient): ?>
    <!-- Información detallada del paciente -->
    <div class="row">
        <div class="col-md-4">
            <!-- Información personal -->
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <i class="fa fa-user"></i> Información Personal
                    </h3>
                </div>
                <div class="panel-body">
                    <?php if($pacient->image): ?>
                        <div class="text-center">
                            <img src="storage/pacients/<?php echo $pacient->image; ?>" class="img-thumbnail" style="max-width: 150px;" alt="Foto del paciente">
                        </div>
                        <br>
                    <?php else: ?>
                        <div class="text-center" style="padding: 30px; background: #f5f5f5; border-radius: 4px; margin-bottom: 15px;">
                            <i class="fa fa-user fa-3x text-muted"></i>
                        </div>
                    <?php endif; ?>
                    
                    <p><strong>Nombre:</strong> <?php echo $pacient->name . " " . $pacient->lastname; ?></p>
                    <p><strong>Email:</strong> <?php echo $pacient->email; ?></p>
                    <p><strong>Teléfono:</strong> <?php echo $pacient->phone; ?></p>
                    <?php if($pacient->day_of_birth): ?>
                        <p><strong>Fecha de Nacimiento:</strong> <?php echo date('d/m/Y', strtotime($pacient->day_of_birth)); ?></p>
                        <p><strong>Edad:</strong> <?php echo date('Y') - date('Y', strtotime($pacient->day_of_birth)); ?> años</p>
                    <?php endif; ?>
                    <?php if($pacient->gender): ?>
                        <p><strong>Género:</strong> 
                            <?php 
                            switch($pacient->gender) {
                                case 'M': echo 'Masculino'; break;
                                case 'F': echo 'Femenino'; break;
                                default: echo $pacient->gender; break;
                            }
                            ?>
                        </p>
                    <?php endif; ?>
                    <?php if($pacient->address): ?>
                        <p><strong>Dirección:</strong> <?php echo $pacient->address; ?></p>
                    <?php endif; ?>
                    
                    <div class="btn-group btn-group-justified">
                        <a href="index.php?view=editpacient&id=<?php echo $pacient->id; ?>" class="btn btn-primary">
                            <i class="fa fa-edit"></i> Editar
                        </a>
                        <a href="index.php?view=pacienthistory&id=<?php echo $pacient->id; ?>" class="btn btn-info">
                            <i class="fa fa-history"></i> Historial
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <!-- Información médica -->
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <i class="fa fa-stethoscope"></i> Información Médica
                    </h3>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Diagnóstico:</h5>
                            <?php if($pacient->sick): ?>
                                <div class="alert alert-info">
                                    <strong><?php echo $pacient->sick; ?></strong>
                                </div>
                            <?php else: ?>
                                <p class="text-muted">No especificado</p>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <h5>Alergias:</h5>
                            <?php if($pacient->alergy): ?>
                                <div class="alert alert-warning">
                                    <strong><i class="fa fa-exclamation-triangle"></i> <?php echo $pacient->alergy; ?></strong>
                                </div>
                            <?php else: ?>
                                <p class="text-muted">Ninguna conocida</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <?php if($pacient->medicaments): ?>
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Medicamentos:</h5>
                            <div class="alert alert-success">
                                <strong><?php echo $pacient->medicaments; ?></strong>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Próximas citas -->
            <div class="panel panel-success">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <i class="fa fa-calendar"></i> Próximas Citas
                    </h3>
                </div>
                <div class="panel-body">
                    <?php
                    try {
                        $reservations = ReservationData::getByPacientId($pacient->id);
                        $upcoming_reservations = [];
                        $today = date('Y-m-d');
                        
                        if($reservations) {
                            foreach($reservations as $res) {
                                if($res->date_at >= $today && $res->status_id != 4) {
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
                                $status_class = "primary";
                                switch($res->status_id) {
                                    case 2: 
                                        $status_text = "En Proceso"; 
                                        $status_class = "warning"; 
                                        break;
                                    case 3: 
                                        $status_text = "Completada"; 
                                        $status_class = "success"; 
                                        break;
                                }
                                
                                echo "<tr>";
                                echo "<td>" . date('d/m/Y', strtotime($res->date_at)) . "</td>";
                                echo "<td>" . date('H:i', strtotime($res->time_at)) . "</td>";
                                echo "<td>Dr. " . ($medic ? $medic->name . " " . $medic->lastname : "Sin asignar") . "</td>";
                                echo "<td>" . $res->title . "</td>";
                                echo "<td><span class='label label-{$status_class}'>" . $status_text . "</span></td>";
                                echo "</tr>";
                            }
                            echo "</table>";
                            echo "</div>";
                        } else {
                            echo "<div class='alert alert-info'>";
                            echo "<h5><i class='fa fa-info-circle'></i> Sin Citas Próximas</h5>";
                            echo "<p>Este paciente no tiene citas programadas.</p>";
                            echo "<a href='index.php?view=newreservation&pacient_id={$pacient->id}' class='btn btn-success'>";
                            echo "<i class='fa fa-plus'></i> Agendar Nueva Cita</a>";
                            echo "</div>";
                        }
                    } catch(Exception $e) {
                        echo "<div class='alert alert-danger'>";
                        echo "<p>Error cargando las citas: " . $e->getMessage() . "</p>";
                        echo "</div>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Navegación -->
    <div class="row">
        <div class="col-md-12">
            <div class="text-center">
                <a href="index.php?view=pacients" class="btn btn-default">
                    <i class="fa fa-arrow-left"></i> Volver a Lista de Pacientes
                </a>
                <a href="index.php?view=newreservation&pacient_id=<?php echo $pacient->id; ?>" class="btn btn-success">
                    <i class="fa fa-plus"></i> Nueva Cita
                </a>
                <a href="index.php?view=pacienthistory&id=<?php echo $pacient->id; ?>" class="btn btn-info">
                    <i class="fa fa-history"></i> Ver Historial Completo
                </a>
            </div>
        </div>
    </div>
<?php else: ?>
    <!-- Paciente no encontrado -->
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-danger">
                <h4><i class="fa fa-exclamation-triangle"></i> Paciente No Encontrado</h4>
                <p>No se pudo encontrar el paciente con ID: <?php echo htmlspecialchars($pacient_id); ?></p>
                <a href="index.php?view=pacients" class="btn btn-primary">
                    <i class="fa fa-arrow-left"></i> Volver a Lista de Pacientes
                </a>
            </div>
        </div>
    </div>
<?php endif; ?>
