<?php
// Las clases se cargan automáticamente mediante el autoloader

$waitlist_item = OncologyWaitlistData::getById($_GET["id"]);
$pacients = PacientData::getAll();
?>
<div class="row">
	<div class="col-md-12">
		<h1>Editar Item de Lista de Espera</h1>
		<br>
		<form class="form-horizontal" method="post" id="editwaitlist" action="index.php?action=updateoncologywaitlist" role="form">

		<div class="form-group">
		<label for="inputEmail1" class="col-lg-2 control-label">Paciente*</label>
		<div class="col-md-6">
		<select name="pacient_id" class="form-control" required>
			<option value="">-- SELECCIONE --</option>
			<?php foreach($pacients as $p):?>
			<option value="<?php echo $p->id; ?>" <?php if($p->id==$waitlist_item->pacient_id){ echo "selected"; } ?>><?php echo $p->name." ".$p->lastname; ?></option>
			<?php endforeach; ?>
		</select>
		</div>
		</div>
		<div class="form-group">
		<label for="inputEmail1" class="col-lg-2 control-label">Tipo de Tratamiento*</label>
		<div class="col-md-6">
		<select name="treatment_type" class="form-control" required>
			<option value="">-- Seleccione --</option>
			<option value="Quimioterapia" <?php if($waitlist_item->treatment_type=='Quimioterapia'){ echo "selected"; } ?>>Quimioterapia</option>
			<option value="Radioterapia" <?php if($waitlist_item->treatment_type=='Radioterapia'){ echo "selected"; } ?>>Radioterapia</option>
			<option value="Inmunoterapia" <?php if($waitlist_item->treatment_type=='Inmunoterapia'){ echo "selected"; } ?>>Inmunoterapia</option>
			<option value="Terapia dirigida" <?php if($waitlist_item->treatment_type=='Terapia dirigida'){ echo "selected"; } ?>>Terapia dirigida</option>
			<option value="Consulta oncológica" <?php if($waitlist_item->treatment_type=='Consulta oncológica'){ echo "selected"; } ?>>Consulta oncológica</option>
			<option value="Seguimiento" <?php if($waitlist_item->treatment_type=='Seguimiento'){ echo "selected"; } ?>>Seguimiento</option>
			<option value="Otro" <?php if($waitlist_item->treatment_type=='Otro'){ echo "selected"; } ?>>Otro</option>
		</select>
		</div>
		</div>
		<div class="form-group">
		<label for="inputEmail1" class="col-lg-2 control-label">Nivel de Prioridad*</label>
		<div class="col-md-6">
		<select name="priority_level" class="form-control" required>
			<option value="">-- Seleccione prioridad --</option>
			<option value="1" <?php if($waitlist_item->priority_level==1){ echo "selected"; } ?>>1 - Baja (No urgente)</option>
			<option value="2" <?php if($waitlist_item->priority_level==2){ echo "selected"; } ?>>2 - Media (Programación normal)</option>
			<option value="3" <?php if($waitlist_item->priority_level==3){ echo "selected"; } ?>>3 - Alta (Preferente)</option>
			<option value="4" <?php if($waitlist_item->priority_level==4){ echo "selected"; } ?>>4 - Urgente (Prioritario)</option>
			<option value="5" <?php if($waitlist_item->priority_level==5){ echo "selected"; } ?>>5 - Crítica (Inmediata)</option>
		</select>
		</div>
		</div>

		<div class="form-group">
		<label for="inputEmail1" class="col-lg-2 control-label">Fecha Solicitada</label>
		<div class="col-md-6">
		<input type="date" name="requested_date" value="<?php echo $waitlist_item->requested_date; ?>" class="form-control">
		</div>
		</div>
		<div class="form-group">
		<label for="inputEmail1" class="col-lg-2 control-label">Hora Solicitada</label>
		<div class="col-md-6">
		<select name="requested_time" class="form-control">
			<option value="">-- Sin preferencia --</option>
			<?php 
			for($h = 8; $h < 18; $h++) {
				for($m = 0; $m < 60; $m += 30) {
					$time = sprintf("%02d:%02d:00", $h, $m);
					$display_time = sprintf("%02d:%02d", $h, $m);
					$selected = ($waitlist_item->requested_time == $time) ? "selected" : "";
					echo "<option value='$time' $selected>$display_time</option>";
				}
			}
			?>
		</select>
		</div>
		</div>		<div class="form-group">
		<label for="inputEmail1" class="col-lg-2 control-label">Duración (minutos)*</label>
		<div class="col-md-6">
		<select name="duration_minutes" class="form-control" required>
			<option value="30" <?php if($waitlist_item->duration_minutes==30){ echo "selected"; } ?>>30 minutos</option>
			<option value="60" <?php if($waitlist_item->duration_minutes==60){ echo "selected"; } ?>>60 minutos</option>
			<option value="90" <?php if($waitlist_item->duration_minutes==90){ echo "selected"; } ?>>90 minutos</option>
			<option value="120" <?php if($waitlist_item->duration_minutes==120){ echo "selected"; } ?>>120 minutos (2 horas)</option>
			<option value="180" <?php if($waitlist_item->duration_minutes==180){ echo "selected"; } ?>>180 minutos (3 horas)</option>
			<option value="240" <?php if($waitlist_item->duration_minutes==240){ echo "selected"; } ?>>240 minutos (4 horas)</option>
			<option value="300" <?php if($waitlist_item->duration_minutes==300){ echo "selected"; } ?>>300 minutos (5 horas)</option>
		</select>
		</div>
		</div>

		<div class="form-group">
		<label for="inputEmail1" class="col-lg-2 control-label">Estado</label>
		<div class="col-md-6">
		<select name="status" class="form-control">
			<option value="pending" <?php if($waitlist_item->status=='pending'){ echo "selected"; } ?>>Pendiente</option>
			<option value="assigned" <?php if($waitlist_item->status=='assigned'){ echo "selected"; } ?>>Asignado</option>
			<option value="completed" <?php if($waitlist_item->status=='completed'){ echo "selected"; } ?>>Completado</option>
			<option value="cancelled" <?php if($waitlist_item->status=='cancelled'){ echo "selected"; } ?>>Cancelado</option>
		</select>
		</div>
		</div>

		<div class="form-group">
		<label for="inputEmail1" class="col-lg-2 control-label">Notas</label>
		<div class="col-md-6">
		<textarea name="notes" class="form-control" rows="4"><?php echo $waitlist_item->notes; ?></textarea>
		</div>
		</div>

		<div class="form-group">
		<div class="col-lg-offset-2 col-lg-10">
		<input type="hidden" name="id" value="<?php echo $waitlist_item->id; ?>">
		<button type="submit" class="btn btn-primary">Actualizar Item</button>
		<a href="index.php?view=oncologywaitlist" class="btn btn-default">Cancelar</a>
		</div>
		</div>

		</form>
	</div>
</div>

<script>
$(document).ready(function() {
    // Enhanced form validation
    $("#editwaitlist").submit(function(e) {
        var pacientId = $("select[name='pacient_id']").val();
        var treatmentType = $("select[name='treatment_type']").val();
        var priority = $("select[name='priority_level']").val();
        var duration = $("select[name='duration_minutes']").val();
        
        if(!pacientId) {
            alert("⚠️ Debe seleccionar un paciente");
            e.preventDefault();
            return false;
        }
        
        if(!treatmentType) {
            alert("⚠️ Debe seleccionar un tipo de tratamiento");
            e.preventDefault();
            return false;
        }
        
        if(!priority) {
            alert("⚠️ Debe seleccionar un nivel de prioridad");
            e.preventDefault();
            return false;
        }
        
        if(!duration) {
            alert("⚠️ Debe seleccionar una duración");
            e.preventDefault();
            return false;
        }
        
        // Confirmation for high priority changes
        if(priority >= 4) {
            var confirmed = confirm('🚨 Ha seleccionado una prioridad alta (' + priority + '). ¿Confirma este cambio?');
            if(!confirmed) {
                e.preventDefault();
                return false;
            }
        }
        
        return true;
    });
    
    // Treatment type change handler
    $("select[name='treatment_type']").change(function() {
        var treatmentType = $(this).val();
        var durationSelect = $("select[name='duration_minutes']");
        
        // Auto-suggest duration based on treatment type
        switch(treatmentType) {
            case 'Quimioterapia':
                durationSelect.val('120');
                break;
            case 'Radioterapia':
                durationSelect.val('30');
                break;
            case 'Inmunoterapia':
                durationSelect.val('90');
                break;
            case 'Terapia dirigida':
                durationSelect.val('60');
                break;
            case 'Consulta oncológica':
                durationSelect.val('30');
                break;
            case 'Seguimiento':
                durationSelect.val('30');
                break;
        }
    });
    
    // Initialize form styling
    $("select").addClass("form-control");
});
</script>
