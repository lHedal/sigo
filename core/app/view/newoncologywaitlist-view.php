<?php
// Las clases se cargan automáticamente mediante el autoloader

$pacients = PacientData::getAll();
?>

<section class="content">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Agregar Paciente a Lista de Espera - Oncología</h3>
                </div>
                <form class="form-horizontal" method="post" action="index.php?action=addoncologywaitlist">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="pacient_id" class="col-lg-2 control-label">Paciente*</label>
                            <div class="col-md-10">
                                <select name="pacient_id" id="pacient_id" class="form-control select2" required>
                                    <option value="">-- Seleccione un paciente --</option>
                                    <?php foreach($pacients as $pacient): ?>
                                    <option value="<?php echo $pacient->id; ?>">
                                        <?php echo $pacient->name . " " . $pacient->lastname . " - " . $pacient->no; ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>                        <div class="form-group">
                            <label for="treatment_type" class="col-lg-2 control-label">Tipo de Tratamiento*</label>
                            <div class="col-md-10">
                                <select name="treatment_type" id="treatment_type" class="form-control treatment-select" required>
                                    <option value="">-- Seleccione tipo de tratamiento --</option>
                                    <option value="Quimioterapia">Quimioterapia</option>
                                    <option value="Radioterapia">Radioterapia</option>
                                    <option value="Inmunoterapia">Inmunoterapia</option>
                                    <option value="Terapia dirigida">Terapia dirigida</option>
                                    <option value="Consulta oncológica">Consulta oncológica</option>
                                    <option value="Seguimiento">Seguimiento</option>
                                    <option value="Otro">Otro</option>
                                </select>
                            </div>
                        </div>                        <div class="form-group">
                            <label for="priority_level" class="col-lg-2 control-label">Prioridad*</label>
                            <div class="col-md-10">
                                <select name="priority_level" id="priority_level" class="form-control priority-select" required>
                                    <option value="">-- Seleccione prioridad --</option>
                                    <option value="1">1 - Baja (No urgente)</option>
                                    <option value="2" selected>2 - Media (Programación normal)</option>
                                    <option value="3">3 - Alta (Preferente)</option>
                                    <option value="4">4 - Urgente (Prioritario)</option>
                                    <option value="5">5 - Crítica (Inmediata)</option>
                                </select>
                                <p class="help-block">
                                    <small>La prioridad determina el orden de asignación en la lista de espera.</small>
                                </p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="requested_date" class="col-lg-2 control-label">Fecha Preferida</label>
                            <div class="col-md-10">
                                <input type="date" name="requested_date" id="requested_date" class="form-control" 
                                       min="<?php echo date('Y-m-d'); ?>" value="<?php echo date('Y-m-d'); ?>">
                            </div>
                        </div>                        <div class="form-group">
                            <label for="requested_time" class="col-lg-2 control-label">Hora Preferida</label>
                            <div class="col-md-10">
                                <select name="requested_time" id="requested_time" class="form-control time-select">
                                    <option value="">-- Sin preferencia de hora --</option>
                                    <?php 
                                    for($h = 8; $h < 18; $h++) {
                                        for($m = 0; $m < 60; $m += 30) {
                                            $time = sprintf("%02d:%02d", $h, $m);
                                            echo "<option value='$time:00'>$time</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>                        <div class="form-group">
                            <label for="duration_minutes" class="col-lg-2 control-label">Duración (minutos)*</label>
                            <div class="col-md-10">
                                <select name="duration_minutes" id="duration_minutes" class="form-control duration-select" required>
                                    <option value="">-- Seleccione duración --</option>
                                    <option value="30">30 minutos</option>
                                    <option value="60" selected>60 minutos</option>
                                    <option value="90">90 minutos</option>
                                    <option value="120">120 minutos (2 horas)</option>
                                    <option value="180">180 minutos (3 horas)</option>
                                    <option value="240">240 minutos (4 horas)</option>
                                    <option value="300">300 minutos (5 horas)</option>
                                </select>
                                <p class="help-block">
                                    <small>
                                        <strong>Guía de duración:</strong><br>
                                        • Consulta: 30-60 min<br>
                                        • Quimioterapia: 60-240 min<br>
                                        • Inmunoterapia: 90-180 min<br>
                                        • Seguimiento: 30 min
                                    </small>
                                </p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="notes" class="col-lg-2 control-label">Notas</label>
                            <div class="col-md-10">
                                <textarea name="notes" id="notes" class="form-control" rows="3" 
                                          placeholder="Información adicional sobre el tratamiento o requerimientos especiales"></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="box-footer">
                        <div class="form-group">
                            <div class="col-lg-offset-2 col-lg-10">
                                <button type="submit" class="btn btn-primary">Agregar a Lista de Espera</button>
                                <a href="index.php?view=oncologywaitlist" class="btn btn-default">Cancelar</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<script>
$(document).ready(function() {
    // Configurar select2 específicamente para el campo de pacientes
    $('#pacient_id').select2({
        placeholder: "Buscar paciente...",
        allowClear: true,
        width: '100%'
    });
    
    // Auto-asignar duración según tipo de tratamiento
    $('#treatment_type').change(function() {
        var treatmentType = $(this).val();
        var durationSelect = $('#duration_minutes');
        var prioritySelect = $('#priority_level');
        
        // Auto-suggest duration based on treatment type
        switch(treatmentType) {
            case 'Quimioterapia':
                durationSelect.val('120');
                showTreatmentInfo('⚕️ Quimioterapia: Tratamiento que puede durar 2-4 horas. Se recomienda programar en horario matutino.');
                break;
            case 'Radioterapia':
                durationSelect.val('30');
                showTreatmentInfo('☢️ Radioterapia: Sesión rápida de 15-30 minutos. Puede programarse cualquier horario.');
                break;
            case 'Inmunoterapia':
                durationSelect.val('90');
                showTreatmentInfo('🧬 Inmunoterapia: Tratamiento de 90-180 minutos. Requiere observación post-infusión.');
                break;
            case 'Terapia dirigida':
                durationSelect.val('60');
                showTreatmentInfo('🎯 Terapia dirigida: Duración variable según medicamento, típicamente 60-120 minutos.');
                break;
            case 'Consulta oncológica':
                durationSelect.val('30');
                showTreatmentInfo('👨‍⚕️ Consulta: Evaluación médica de 30-60 minutos para seguimiento o valoración inicial.');
                break;
            case 'Seguimiento':
                durationSelect.val('30');
                showTreatmentInfo('📋 Seguimiento: Revisión rápida del estado del paciente, 30 minutos.');
                break;
            default:
                hideTreatmentInfo();
        }
    });
    
    // Priority level information
    $('#priority_level').change(function() {
        var priority = $(this).val();
        var infoText = '';
        var alertClass = '';
        
        switch(priority) {
            case '1':
                infoText = '📅 Prioridad Baja: Programación flexible, no urgente.';
                alertClass = 'alert-info';
                break;
            case '2':
                infoText = '📆 Prioridad Media: Programación normal, dentro de 1-2 semanas.';
                alertClass = 'alert-info';
                break;
            case '3':
                infoText = '⚡ Prioridad Alta: Programación preferente, dentro de 3-5 días.';
                alertClass = 'alert-warning';
                break;
            case '4':
                infoText = '🚨 Prioridad Urgente: Programación prioritaria, dentro de 24-48 horas.';
                alertClass = 'alert-warning';
                break;
            case '5':
                infoText = '🆘 Prioridad Crítica: Programación inmediata, el mismo día si es posible.';
                alertClass = 'alert-danger';
                break;
        }
        
        showPriorityInfo(infoText, alertClass);
    });
    
    // Form validation before submit
    $('form').submit(function(e) {
        var pacientId = $('#pacient_id').val();
        var treatmentType = $('#treatment_type').val();
        var priority = $('#priority_level').val();
        var duration = $('#duration_minutes').val();
        
        if (!pacientId || !treatmentType || !priority || !duration) {
            e.preventDefault();
            alert('⚠️ Por favor complete todos los campos obligatorios marcados con *');
            return false;
        }
        
        // Show confirmation for high priority cases
        if (priority >= 4) {
            var confirmed = confirm('🚨 Ha seleccionado una prioridad alta (' + priority + '). ¿Está seguro de que este caso requiere atención prioritaria?');
            if (!confirmed) {
                e.preventDefault();
                return false;
            }
        }
        
        return true;
    });
    
    function showTreatmentInfo(message) {
        $('#treatment-info').remove();
        $('#treatment_type').parent().append(
            '<div id="treatment-info" class="alert alert-info" style="margin-top: 10px; padding: 10px;">' +
            '<small>' + message + '</small>' +
            '</div>'
        );
    }
    
    function hideTreatmentInfo() {
        $('#treatment-info').remove();
    }
    
    function showPriorityInfo(message, alertClass) {
        $('#priority-info').remove();
        if (message) {
            $('#priority_level').parent().append(
                '<div id="priority-info" class="alert ' + alertClass + '" style="margin-top: 10px; padding: 10px;">' +
                '<small>' + message + '</small>' +
                '</div>'
            );
        }
    }
});
</script>
