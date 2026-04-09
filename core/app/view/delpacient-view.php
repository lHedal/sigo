<?php
$pacient = PacientData::getById($_GET["id"]);
?>
<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <div class="panel panel-danger">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-warning"></i> Confirmar Eliminación de Paciente
                </h3>
            </div>
            <div class="panel-body">
                <div class="alert alert-danger">
                    <h4><i class="fa fa-exclamation-triangle"></i> ¡Atención!</h4>
                    <p>Estás a punto de eliminar permanentemente el siguiente paciente del sistema:</p>
                </div>
                
                <div class="well">
                    <div class="row">
                        <div class="col-md-3">
                            <?php if($pacient->image): ?>
                                <img src="storage/pacients/<?php echo $pacient->image; ?>" class="img-thumbnail" alt="Foto del paciente">
                            <?php else: ?>
                                <div class="text-center" style="padding: 40px; background: #f5f5f5; border-radius: 4px;">
                                    <i class="fa fa-user fa-3x text-muted"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-9">
                            <h4><?php echo $pacient->name . " " . $pacient->lastname; ?></h4>
                            <p><strong>Email:</strong> <?php echo $pacient->email; ?></p>
                            <p><strong>Teléfono:</strong> <?php echo $pacient->phone; ?></p>
                            <?php if($pacient->day_of_birth): ?>
                            <p><strong>Fecha de Nacimiento:</strong> <?php echo date('d/m/Y', strtotime($pacient->day_of_birth)); ?></p>
                            <?php endif; ?>
                            <?php if($pacient->address): ?>
                            <p><strong>Dirección:</strong> <?php echo $pacient->address; ?></p>
                            <?php endif; ?>
                            <?php if($pacient->sick): ?>
                            <p><strong>Diagnóstico:</strong> <?php echo $pacient->sick; ?></p>
                            <?php endif; ?>
                            <?php if($pacient->alergy): ?>
                            <p><strong>Alergias:</strong> <?php echo $pacient->alergy; ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Verificar dependencias -->
                <div class="alert alert-warning">
                    <h5><i class="fa fa-info-circle"></i> Verificación de Dependencias</h5>
                    <?php
                    try {
                        $reservations = ReservationData::getByPacientId($pacient->id);
                        $reservation_count = $reservations ? count($reservations) : 0;
                        
                        // Verificar también waitlist oncológica
                        $waitlist_count = 0;
                        try {
                            $waitlist = OncologyWaitlistData::getByPacientId($pacient->id);
                            $waitlist_count = $waitlist ? count($waitlist) : 0;
                        } catch(Exception $e) {
                            // Ignorar si no existe la tabla
                        }
                        
                        if ($reservation_count > 0) {
                            echo "<p><strong>⚠️ Este paciente tiene {$reservation_count} cita(s) médica(s) asociada(s).</strong></p>";
                        }
                        
                        if ($waitlist_count > 0) {
                            echo "<p><strong>⚠️ Este paciente tiene {$waitlist_count} entrada(s) en lista de espera oncológica.</strong></p>";
                        }
                        
                        if ($reservation_count > 0 || $waitlist_count > 0) {
                            echo "<p><strong>Al eliminar este paciente, se perderá todo su historial médico.</strong></p>";
                        } else {
                            echo "<p><strong>✅ Este paciente no tiene registros asociados.</strong></p>";
                            echo "<p>Es seguro eliminar este registro.</p>";
                        }
                    } catch(Exception $e) {
                        echo "<p><strong>⚠️ No se pudo verificar las dependencias.</strong></p>";
                        echo "<p>Procede con precaución: " . $e->getMessage() . "</p>";
                    }
                    ?>
                </div>

                <div class="alert alert-info">
                    <h5><i class="fa fa-lightbulb-o"></i> Alternativa Recomendada</h5>
                    <p>En lugar de eliminar permanentemente, considera <strong>desactivar</strong> el paciente para mantener el historial médico completo.</p>
                </div>

                <hr>

                <div class="text-center">
                    <h5>¿Estás seguro de que deseas eliminar este paciente?</h5>
                    <p class="text-muted">Esta acción eliminará permanentemente toda la información médica.</p>
                    
                    <form method="post" action="index.php?action=delpacient" style="display: inline;">
                        <input type="hidden" name="id" value="<?php echo $pacient->id; ?>">
                        <button type="submit" class="btn btn-danger btn-lg" onclick="return confirm('¿CONFIRMAS que deseas eliminar permanentemente a <?php echo $pacient->name . " " . $pacient->lastname; ?> y todo su historial médico?');">
                            <i class="fa fa-trash"></i> Sí, Eliminar Paciente
                        </button>
                    </form>
                    
                    <a href="index.php?view=pacients" class="btn btn-default btn-lg">
                        <i class="fa fa-arrow-left"></i> Cancelar
                    </a>
                    
                    <a href="index.php?view=editpacient&id=<?php echo $pacient->id; ?>" class="btn btn-warning btn-lg">
                        <i class="fa fa-edit"></i> Desactivar en su lugar
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.panel-danger .panel-heading {
    background-color: #d9534f;
    border-color: #d43f3a;
    color: white;
}
.well {
    background-color: #f9f9f9;
    border: 1px solid #e3e3e3;
}
</style>
