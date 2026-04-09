<?php
$medic = MedicData::getById($_GET["id"]);
?>
<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <div class="panel panel-danger">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-warning"></i> Confirmar Eliminación de Médico
                </h3>
            </div>
            <div class="panel-body">
                <div class="alert alert-danger">
                    <h4><i class="fa fa-exclamation-triangle"></i> ¡Atención!</h4>
                    <p>Estás a punto de eliminar permanentemente el siguiente médico del sistema:</p>
                </div>
                
                <div class="well">
                    <div class="row">
                        <div class="col-md-3">
                            <?php if($medic->image): ?>
                                <img src="storage/medics/<?php echo $medic->image; ?>" class="img-thumbnail" alt="Foto del médico">
                            <?php else: ?>
                                <div class="text-center" style="padding: 40px; background: #f5f5f5; border-radius: 4px;">
                                    <i class="fa fa-user-md fa-3x text-muted"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-9">
                            <h4>Dr. <?php echo $medic->name . " " . $medic->lastname; ?></h4>
                            <p><strong>Email:</strong> <?php echo $medic->email; ?></p>
                            <p><strong>Teléfono:</strong> <?php echo $medic->phone; ?></p>
                            <p><strong>Especialidad:</strong> Médico Oncólogo</p>
                            <?php if($medic->address): ?>
                            <p><strong>Dirección:</strong> <?php echo $medic->address; ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Verificar dependencias -->
                <div class="alert alert-warning">
                    <h5><i class="fa fa-info-circle"></i> Verificación de Dependencias</h5>
                    <?php
                    try {
                        $reservations = ReservationData::getByMedicId($medic->id);
                        $reservation_count = $reservations ? count($reservations) : 0;
                        
                        if ($reservation_count > 0) {
                            echo "<p><strong>⚠️ Este médico tiene {$reservation_count} cita(s) asociada(s).</strong></p>";
                            echo "<p>Al eliminar este médico, todas sus citas quedarán sin asignar.</p>";
                        } else {
                            echo "<p><strong>✅ Este médico no tiene citas asociadas.</strong></p>";
                            echo "<p>Es seguro eliminar este registro.</p>";
                        }
                    } catch(Exception $e) {
                        echo "<p><strong>⚠️ No se pudo verificar las dependencias.</strong></p>";
                        echo "<p>Procede con precaución.</p>";
                    }
                    ?>
                </div>

                <div class="alert alert-info">
                    <h5><i class="fa fa-lightbulb-o"></i> Alternativa Recomendada</h5>
                    <p>En lugar de eliminar permanentemente, considera <strong>desactivar</strong> el médico para mantener el historial de citas.</p>
                </div>

                <hr>

                <div class="text-center">
                    <h5>¿Estás seguro de que deseas eliminar este médico?</h5>
                    <p class="text-muted">Esta acción no se puede deshacer.</p>
                    
                    <form method="post" action="index.php?action=delmedic" style="display: inline;">
                        <input type="hidden" name="id" value="<?php echo $medic->id; ?>">
                        <button type="submit" class="btn btn-danger btn-lg" onclick="return confirm('¿CONFIRMAS que deseas eliminar permanentemente a Dr. <?php echo $medic->name . " " . $medic->lastname; ?>?');">
                            <i class="fa fa-trash"></i> Sí, Eliminar Médico
                        </button>
                    </form>
                    
                    <a href="index.php?view=medics" class="btn btn-default btn-lg">
                        <i class="fa fa-arrow-left"></i> Cancelar
                    </a>
                    
                    <a href="index.php?view=editmedic&id=<?php echo $medic->id; ?>" class="btn btn-warning btn-lg">
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
