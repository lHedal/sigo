<?php
// Verificar que el usuario esté logueado
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["user_id"])) {
    Core::redir("./");
    exit;
}

// Obtener lista de pacientes para seleccionar
$pacients = PacientData::getAll();
$medics = MedicData::getAll();

// Si viene un paciente_id por GET, pre-seleccionarlo
$selected_patient_id = isset($_GET['pacient_id']) ? (int)$_GET['pacient_id'] : null;
?>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <i class="fa fa-stethoscope"></i> Evaluación Médica Inicial - Oncología
                    </h3>
                    <p class="box-subtitle">Formulario de evaluación pre-tratamiento oncológico</p>
                </div>
                
                <form class="form-horizontal" method="post" action="index.php?action=addinitialassessment" id="initialAssessmentForm">
                    <div class="box-body">
                        
                        <!-- SECCIÓN 1: INFORMACIÓN DEL PACIENTE -->
                        <div class="evaluation-section">
                            <h4 class="section-header">
                                <i class="fa fa-user"></i> 1. Información del Paciente
                            </h4>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="pacient_id" class="col-sm-3 control-label">Paciente *</label>
                                        <div class="col-sm-9">
                                            <select name="pacient_id" id="pacient_id" class="form-control select2" required>
                                                <option value="">-- Seleccione un paciente --</option>
                                                <?php foreach($pacients as $pacient): ?>
                                                <option value="<?php echo $pacient->id; ?>" 
                                                        <?php echo ($selected_patient_id == $pacient->id) ? 'selected' : ''; ?>>
                                                    <?php echo $pacient->name . " " . $pacient->lastname; ?>
                                                    <?php if($pacient->no): ?>
                                                        - RUT: <?php echo $pacient->no; ?>
                                                    <?php endif; ?>
                                                </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="evaluating_medic_id" class="col-sm-3 control-label">Médico Evaluador *</label>
                                        <div class="col-sm-9">
                                            <select name="evaluating_medic_id" id="evaluating_medic_id" class="form-control" required>
                                                <option value="">-- Seleccione médico --</option>
                                                <?php foreach($medics as $medic): ?>
                                                <option value="<?php echo $medic->id; ?>">
                                                    Dr. <?php echo $medic->name . " " . $medic->lastname; ?>
                                                </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="patient-summary" id="patient-summary" style="display: none;">
                                <div class="alert alert-info">
                                    <h5><i class="fa fa-info-circle"></i> Información del Paciente Seleccionado</h5>
                                    <div id="patient-details"></div>
                                </div>
                            </div>
                        </div>

                        <!-- SECCIÓN 2: HISTORIA ONCOLÓGICA -->
                        <div class="evaluation-section">
                            <h4 class="section-header">
                                <i class="fa fa-history"></i> 2. Historia Oncológica
                            </h4>
                            
                            <div class="form-group">
                                <label for="primary_diagnosis" class="col-sm-2 control-label">Diagnóstico Principal *</label>
                                <div class="col-sm-10">
                                    <input type="text" name="primary_diagnosis" class="form-control" id="primary_diagnosis" 
                                           placeholder="Diagnóstico oncológico principal" required>
                                    <p class="help-block">Ejemplo: Adenocarcinoma de mama, Linfoma de Hodgkin, etc.</p>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="tumor_stage" class="col-sm-2 control-label">Estadio del Tumor</label>
                                <div class="col-sm-4">
                                    <select name="tumor_stage" class="form-control" id="tumor_stage">
                                        <option value="">-- Seleccione estadio --</option>
                                        <option value="I">Estadio I</option>
                                        <option value="II">Estadio II</option>
                                        <option value="III">Estadio III</option>
                                        <option value="IV">Estadio IV</option>
                                        <option value="Pendiente">Pendiente de estadificación</option>
                                        <option value="No aplica">No aplica</option>
                                    </select>
                                </div>
                                <label for="date_of_diagnosis" class="col-sm-2 control-label">Fecha de Diagnóstico *</label>
                                <div class="col-sm-4">
                                    <input type="date" name="date_of_diagnosis" class="form-control" id="date_of_diagnosis" required>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="previous_treatments" class="col-sm-2 control-label">Tratamientos Previos</label>
                                <div class="col-sm-10">
                                    <textarea name="previous_treatments" class="form-control" id="previous_treatments" rows="4"
                                              placeholder="Describa cirugías, quimioterapias, radioterapias u otros tratamientos previos con fechas aproximadas"></textarea>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="family_history" class="col-sm-2 control-label">Antecedentes Familiares</label>
                                <div class="col-sm-10">
                                    <textarea name="family_history" class="form-control" id="family_history" rows="3"
                                              placeholder="Historia familiar de cáncer u otras enfermedades relevantes"></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- SECCIÓN 3: ESTADO FUNCIONAL Y SÍNTOMAS -->
                        <div class="evaluation-section">
                            <h4 class="section-header">
                                <i class="fa fa-heartbeat"></i> 3. Estado Funcional y Síntomas Actuales
                            </h4>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="ecog_performance_status" class="col-sm-4 control-label">ECOG Performance Status *</label>
                                        <div class="col-sm-8">
                                            <select name="ecog_performance_status" class="form-control" id="ecog_performance_status" required>
                                                <option value="">-- Seleccione ECOG --</option>
                                                <option value="0">0 - Totalmente activo</option>
                                                <option value="1">1 - Restricción actividad física intensa</option>
                                                <option value="2">2 - Ambulatorio >50% del tiempo</option>
                                                <option value="3">3 - Limitado al cuidado personal</option>
                                                <option value="4">4 - Completamente discapacitado</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="weight_loss" class="col-sm-4 control-label">Pérdida de Peso</label>
                                        <div class="col-sm-8">
                                            <div class="input-group">
                                                <input type="number" name="weight_loss" class="form-control" id="weight_loss" 
                                                       placeholder="0" min="0" max="100" step="0.1">
                                                <span class="input-group-addon">kg en los últimos 6 meses</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="current_symptoms" class="col-sm-2 control-label">Síntomas Actuales</label>
                                <div class="col-sm-10">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="checkbox-list">
                                                <label><input type="checkbox" name="symptoms[]" value="dolor"> Dolor</label>
                                                <label><input type="checkbox" name="symptoms[]" value="fatiga"> Fatiga</label>
                                                <label><input type="checkbox" name="symptoms[]" value="nauseas"> Náuseas/Vómitos</label>
                                                <label><input type="checkbox" name="symptoms[]" value="falta_apetito"> Falta de apetito</label>
                                                <label><input type="checkbox" name="symptoms[]" value="disnea"> Dificultad respiratoria</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="checkbox-list">
                                                <label><input type="checkbox" name="symptoms[]" value="fiebre"> Fiebre</label>
                                                <label><input type="checkbox" name="symptoms[]" value="insomnio"> Insomnio</label>
                                                <label><input type="checkbox" name="symptoms[]" value="depresion"> Estado depresivo</label>
                                                <label><input type="checkbox" name="symptoms[]" value="ansiedad"> Ansiedad</label>
                                                <label><input type="checkbox" name="symptoms[]" value="otros"> Otros</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="pain_scale" class="col-sm-2 control-label">Escala de Dolor (0-10)</label>
                                <div class="col-sm-4">
                                    <input type="range" name="pain_scale" class="form-control" id="pain_scale" 
                                           min="0" max="10" value="0" oninput="this.nextElementSibling.value = this.value">
                                    <output>0</output> <small>(0 = Sin dolor, 10 = Dolor máximo)</small>
                                </div>
                                <label for="symptoms_other" class="col-sm-2 control-label">Otros Síntomas</label>
                                <div class="col-sm-4">
                                    <input type="text" name="symptoms_other" class="form-control" id="symptoms_other" 
                                           placeholder="Especificar otros síntomas">
                                </div>
                            </div>
                        </div>

                        <!-- SECCIÓN 4: EVALUACIÓN PSICOSOCIAL -->
                        <div class="evaluation-section">
                            <h4 class="section-header">
                                <i class="fa fa-users"></i> 4. Evaluación Psicosocial
                            </h4>
                            
                            <div class="form-group">
                                <label for="support_system" class="col-sm-2 control-label">Sistema de Apoyo</label>
                                <div class="col-sm-10">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label><input type="checkbox" name="support[]" value="familia"> Familia</label>
                                        </div>
                                        <div class="col-md-4">
                                            <label><input type="checkbox" name="support[]" value="amigos"> Amigos</label>
                                        </div>
                                        <div class="col-md-4">
                                            <label><input type="checkbox" name="support[]" value="grupos_apoyo"> Grupos de apoyo</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="psychological_state" class="col-sm-2 control-label">Estado Psicológico *</label>
                                <div class="col-sm-10">
                                    <select name="psychological_state" class="form-control" id="psychological_state" required>
                                        <option value="">-- Evaluación psicológica --</option>
                                        <option value="estable">Estable</option>
                                        <option value="ansioso">Ansioso pero manejable</option>
                                        <option value="depresivo">Signos depresivos</option>
                                        <option value="crisis">En crisis, requiere intervención</option>
                                        <option value="negacion">En negación</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="coping_mechanisms" class="col-sm-2 control-label">Mecanismos de Afrontamiento</label>
                                <div class="col-sm-10">
                                    <textarea name="coping_mechanisms" class="form-control" id="coping_mechanisms" rows="3"
                                              placeholder="¿Cómo maneja el estrés? ¿Qué actividades le ayudan a sentirse mejor?"></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- SECCIÓN 5: PLAN DE TRATAMIENTO PROPUESTO -->
                        <div class="evaluation-section">
                            <h4 class="section-header">
                                <i class="fa fa-medkit"></i> 5. Plan de Tratamiento Propuesto
                            </h4>
                            
                            <div class="form-group">
                                <label for="proposed_treatment" class="col-sm-2 control-label">Tratamiento Recomendado *</label>
                                <div class="col-sm-10">
                                    <select name="proposed_treatment" class="form-control" id="proposed_treatment" required>
                                        <option value="">-- Seleccione tratamiento --</option>
                                        <option value="Quimioterapia">Quimioterapia</option>
                                        <option value="Radioterapia">Radioterapia</option>
                                        <option value="Inmunoterapia">Inmunoterapia</option>
                                        <option value="Terapia dirigida">Terapia dirigida</option>
                                        <option value="Cirugía">Cirugía</option>
                                        <option value="Cuidados paliativos">Cuidados paliativos</option>
                                        <option value="Combinado">Tratamiento combinado</option>
                                        <option value="Seguimiento">Seguimiento únicamente</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="treatment_goals" class="col-sm-2 control-label">Objetivos del Tratamiento *</label>
                                <div class="col-sm-10">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label><input type="radio" name="treatment_goals" value="curativo" required> Curativo</label>
                                        </div>
                                        <div class="col-md-4">
                                            <label><input type="radio" name="treatment_goals" value="paliativo" required> Paliativo</label>
                                        </div>
                                        <div class="col-md-4">
                                            <label><input type="radio" name="treatment_goals" value="preventivo" required> Preventivo</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="estimated_duration" class="col-sm-2 control-label">Duración Estimada</label>
                                <div class="col-sm-4">
                                    <input type="text" name="estimated_duration" class="form-control" id="estimated_duration" 
                                           placeholder="Ej: 6 meses, 12 sesiones">
                                </div>
                                <label for="treatment_priority" class="col-sm-2 control-label">Prioridad *</label>
                                <div class="col-sm-4">
                                    <select name="treatment_priority" class="form-control" id="treatment_priority" required>
                                        <option value="">-- Prioridad --</option>
                                        <option value="1">1 - Baja</option>
                                        <option value="2">2 - Media</option>
                                        <option value="3">3 - Alta</option>
                                        <option value="4">4 - Urgente</option>
                                        <option value="5">5 - Crítica</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="treatment_notes" class="col-sm-2 control-label">Observaciones del Tratamiento</label>
                                <div class="col-sm-10">
                                    <textarea name="treatment_notes" class="form-control" id="treatment_notes" rows="4"
                                              placeholder="Consideraciones especiales, contraindicaciones, ajustes necesarios, etc."></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- SECCIÓN 6: CONSENTIMIENTOS Y DECISIONES -->
                        <div class="evaluation-section">
                            <h4 class="section-header">
                                <i class="fa fa-file-text"></i> 6. Consentimientos y Decisiones
                            </h4>
                            
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Consentimientos</label>
                                <div class="col-sm-10">
                                    <div class="checkbox-list">
                                        <label>
                                            <input type="checkbox" name="consents[]" value="informed_consent">
                                            Consentimiento informado para el tratamiento
                                        </label>
                                        <label>
                                            <input type="checkbox" name="consents[]" value="research_participation">
                                            Acepta participar en estudios de investigación (opcional)
                                        </label>
                                        <label>
                                            <input type="checkbox" name="consents[]" value="data_sharing">
                                            Autoriza compartir datos con equipo multidisciplinario
                                        </label>
                                        <label>
                                            <input type="checkbox" name="consents[]" value="emergency_contact">
                                            Autoriza contactar en caso de emergencia
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="patient_concerns" class="col-sm-2 control-label">Preocupaciones del Paciente</label>
                                <div class="col-sm-10">
                                    <textarea name="patient_concerns" class="form-control" id="patient_concerns" rows="3"
                                              placeholder="Principales preocupaciones, miedos o preguntas expresadas por el paciente"></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- SECCIÓN 7: SEGUIMIENTO Y PRÓXIMOS PASOS -->
                        <div class="evaluation-section">
                            <h4 class="section-header">
                                <i class="fa fa-calendar"></i> 7. Seguimiento y Próximos Pasos
                            </h4>
                            
                            <div class="form-group">
                                <label for="next_appointment" class="col-sm-2 control-label">Próxima Cita</label>
                                <div class="col-sm-4">
                                    <input type="date" name="next_appointment" class="form-control" id="next_appointment">
                                </div>
                                <label for="follow_up_type" class="col-sm-2 control-label">Tipo de Seguimiento</label>
                                <div class="col-sm-4">
                                    <select name="follow_up_type" class="form-control" id="follow_up_type">
                                        <option value="">-- Tipo de seguimiento --</option>
                                        <option value="consulta">Consulta médica</option>
                                        <option value="laboratorio">Exámenes de laboratorio</option>
                                        <option value="imagenes">Estudios de imagen</option>
                                        <option value="tratamiento">Inicio de tratamiento</option>
                                        <option value="evaluacion">Evaluación de respuesta</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="pending_studies" class="col-sm-2 control-label">Estudios Pendientes</label>
                                <div class="col-sm-10">
                                    <textarea name="pending_studies" class="form-control" id="pending_studies" rows="3"
                                              placeholder="Exámenes, biopsias, estudios de imagen u otros estudios necesarios antes del próximo encuentro"></textarea>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="referrals" class="col-sm-2 control-label">Interconsultas</label>
                                <div class="col-sm-10">
                                    <input type="text" name="referrals" class="form-control" id="referrals" 
                                           placeholder="Especialidades a las que se deriva: nutrición, psicología, trabajo social, etc.">
                                </div>
                            </div>
                        </div>

                        <!-- SECCIÓN FINAL: RESUMEN MÉDICO -->
                        <div class="evaluation-section">
                            <h4 class="section-header">
                                <i class="fa fa-clipboard"></i> Resumen y Recomendaciones Finales
                            </h4>
                            
                            <div class="form-group">
                                <label for="medical_summary" class="col-sm-2 control-label">Resumen de la Evaluación *</label>
                                <div class="col-sm-10">
                                    <textarea name="medical_summary" class="form-control" id="medical_summary" rows="5" required
                                              placeholder="Resumen conciso de los hallazgos principales, estado actual del paciente y plan de manejo propuesto"></textarea>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="recommendations" class="col-sm-2 control-label">Recomendaciones</label>
                                <div class="col-sm-10">
                                    <textarea name="recommendations" class="form-control" id="recommendations" rows="4"
                                              placeholder="Recomendaciones específicas para el paciente: cuidados, precauciones, cambios en el estilo de vida, etc."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- BOTONES DE ACCIÓN -->
                    <div class="box-footer">
                        <div class="row">
                            <div class="col-sm-offset-2 col-sm-10">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fa fa-save"></i> Guardar Evaluación Inicial
                                </button>
                                <button type="button" class="btn btn-warning btn-lg" id="save-draft">
                                    <i class="fa fa-file"></i> Guardar como Borrador
                                </button>
                                <a href="javascript:history.back()" class="btn btn-default btn-lg">
                                    <i class="fa fa-arrow-left"></i> Cancelar
                                </a>
                                <button type="button" class="btn btn-info btn-lg pull-right" id="preview-assessment">
                                    <i class="fa fa-eye"></i> Vista Previa
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- CSS personalizado -->
<style>
.evaluation-section {
    background: #f9f9f9;
    border: 1px solid #e3e3e3;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 25px;
}

.section-header {
    color: #337ab7;
    border-bottom: 2px solid #337ab7;
    padding-bottom: 8px;
    margin-bottom: 20px;
    font-weight: bold;
}

.box-subtitle {
    color: #666;
    font-style: italic;
    margin-top: 5px;
}

.checkbox-list label {
    display: block;
    margin-bottom: 8px;
    font-weight: normal;
}

.checkbox-list input[type="checkbox"] {
    margin-right: 8px;
}

.patient-summary {
    background: #e8f4fd;
    border: 1px solid #b3d7ff;
    border-radius: 5px;
    padding: 15px;
    margin-top: 15px;
}

#pain_scale {
    width: 100%;
}

.form-group .help-block {
    font-size: 11px;
    color: #777;
    margin-top: 5px;
}

.box-footer {
    background: #f4f4f4;
    border-top: 1px solid #ddd;
    padding: 20px;
}

.alert-info h5 {
    margin-top: 0;
    color: #31708f;
}

input[type="range"] {
    -webkit-appearance: none;
    appearance: none;
    height: 8px;
    background: #ddd;
    border-radius: 5px;
    outline: none;
}

input[type="range"]::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 20px;
    height: 20px;
    background: #337ab7;
    border-radius: 50%;
    cursor: pointer;
}
</style>

<!-- JavaScript para funcionalidades avanzadas -->
<script>
jQuery(document).ready(function() {
    // Información del paciente al seleccionar
    jQuery('#pacient_id').on('change', function() {
        const pacientId = this.value;
        if (pacientId) {
            // Aquí harías una llamada AJAX para obtener información del paciente
            // Por ahora, mostramos el contenedor
            jQuery('#patient-summary').show();
            jQuery('#patient-details').html('Cargando información del paciente...');
            
            // Simulación de carga de datos del paciente
            setTimeout(function() {
                const selectedOption = jQuery('#pacient_id option:selected').text();
                jQuery('#patient-details').html(`
                    <strong>Paciente:</strong> ${selectedOption}<br>
                    <strong>Información:</strong> Se cargarán automáticamente los datos médicos básicos registrados previamente.
                `);
            }, 500);
        } else {
            jQuery('#patient-summary').hide();
        }
    });

    // Auto-guardar como borrador cada 5 minutos
    let autosaveInterval;
    function startAutosave() {
        autosaveInterval = setInterval(function() {
            saveDraft(true); // true = silencioso
        }, 300000); // 5 minutos
    }

    function saveDraft(silent = false) {
        const formData = jQuery('#initialAssessmentForm').serialize();
        
        // Aquí harías la llamada AJAX para guardar borrador
        jQuery.ajax({
            url: 'index.php?action=savedraftassessment',
            method: 'POST',
            data: formData + '&draft=1',
            success: function(response) {
                if (!silent) {
                    mostrarNotificacion('Borrador guardado exitosamente', 'success');
                }
            },
            error: function() {
                if (!silent) {
                    mostrarNotificacion('Error al guardar borrador', 'error');
                }
            }
        });
    }

    // Botón guardar borrador
    jQuery('#save-draft').on('click', function() {
        saveDraft(false);
    });

    // Vista previa
    jQuery('#preview-assessment').on('click', function() {
        // Generar vista previa en modal o nueva ventana
        const formData = new FormData(document.getElementById('initialAssessmentForm'));
        
        // Aquí abrirías un modal o nueva ventana con la vista previa
        mostrarNotificacion('Función de vista previa en desarrollo', 'info');
    });

    // Validaciones específicas
    jQuery('#initialAssessmentForm').on('submit', function(e) {
        let isValid = true;
        
        // Validar que se seleccionó paciente
        if (!jQuery('#pacient_id').val()) {
            mostrarNotificacion('Debe seleccionar un paciente', 'warning');
            isValid = false;
        }
        
        // Validar que se seleccionó médico evaluador
        if (!jQuery('#evaluating_medic_id').val()) {
            mostrarNotificacion('Debe seleccionar el médico evaluador', 'warning');
            isValid = false;
        }
        
        // Validar campos requeridos específicos
        const requiredFields = [
            '#primary_diagnosis',
            '#date_of_diagnosis',
            '#ecog_performance_status',
            '#psychological_state',
            '#proposed_treatment',
            '#treatment_priority',
            '#medical_summary'
        ];
        
        requiredFields.forEach(function(field) {
            if (!jQuery(field).val()) {
                const label = jQuery(`label[for="${field.substring(1)}"]`).text();
                mostrarNotificacion(`El campo "${label}" es requerido`, 'warning');
                isValid = false;
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            return false;
        }
        
        // Deshabilitar botón para evitar doble envío
        jQuery(this).find('button[type="submit"]').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Guardando...');
    });

    // Iniciar auto-guardado
    startAutosave();

    // Limpiar auto-guardado al salir
    jQuery(window).on('beforeunload', function() {
        if (autosaveInterval) {
            clearInterval(autosaveInterval);
        }
    });

    // Select2 para mejor experiencia en selección de pacientes
    if (jQuery.fn.select2) {
        jQuery('#pacient_id').select2({
            placeholder: "Buscar paciente por nombre o RUT...",
            allowClear: true
        });
    }
});

// Función para mostrar notificaciones
function mostrarNotificacion(mensaje, tipo) {
    if (typeof window.mostrarNotificacion === 'function') {
        window.mostrarNotificacion(mensaje, tipo);
    } else {
        alert(mensaje);
    }
}
</script>
