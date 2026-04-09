<?php
/**
 * Action para manejar la evaluación inicial oncológica
 * Procesa el formulario de evaluación médica pre-tratamiento
 */

// Verificar que el usuario esté logueado
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["user_id"])) {
    Core::redir("./");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verificar campos requeridos
    $required_fields = [
        'pacient_id', 'evaluating_medic_id', 'primary_diagnosis', 
        'date_of_diagnosis', 'ecog_performance_status', 'psychological_state',
        'proposed_treatment', 'treatment_priority', 'medical_summary'
    ];
    
    $missing_fields = [];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $missing_fields[] = $field;
        }
    }
    
    if (!empty($missing_fields)) {
        $_SESSION['flash_message'] = [
            'type' => 'error',
            'message' => 'Faltan campos requeridos: ' . implode(', ', $missing_fields)
        ];
        Core::redir("./?view=initialassessment");
        exit;
    }
    
    try {
        // Crear objeto de evaluación inicial
        $assessment = new InitialAssessmentData();
        
        // Datos básicos
        $assessment->pacient_id = (int)$_POST['pacient_id'];
        $assessment->evaluating_medic_id = (int)$_POST['evaluating_medic_id'];
        $assessment->evaluation_date = date('Y-m-d H:i:s');
        $assessment->created_by = $_SESSION["user_id"];
        
        // Historia oncológica
        $assessment->primary_diagnosis = $_POST['primary_diagnosis'];
        $assessment->tumor_stage = $_POST['tumor_stage'] ?? null;
        $assessment->date_of_diagnosis = $_POST['date_of_diagnosis'];
        $assessment->previous_treatments = $_POST['previous_treatments'] ?? null;
        $assessment->family_history = $_POST['family_history'] ?? null;
        
        // Estado funcional y síntomas
        $assessment->ecog_performance_status = (int)$_POST['ecog_performance_status'];
        $assessment->weight_loss = !empty($_POST['weight_loss']) ? (float)$_POST['weight_loss'] : null;
        
        // Procesar síntomas (checkbox array)
        $symptoms = [];
        if (isset($_POST['symptoms']) && is_array($_POST['symptoms'])) {
            $symptoms = $_POST['symptoms'];
        }
        $assessment->current_symptoms = json_encode($symptoms);
        
        $assessment->pain_scale = (int)($_POST['pain_scale'] ?? 0);
        $assessment->symptoms_other = $_POST['symptoms_other'] ?? null;
        
        // Evaluación psicosocial
        $support_system = [];
        if (isset($_POST['support']) && is_array($_POST['support'])) {
            $support_system = $_POST['support'];
        }
        $assessment->support_system = json_encode($support_system);
        
        $assessment->psychological_state = $_POST['psychological_state'];
        $assessment->coping_mechanisms = $_POST['coping_mechanisms'] ?? null;
        
        // Plan de tratamiento
        $assessment->proposed_treatment = $_POST['proposed_treatment'];
        $assessment->treatment_goals = $_POST['treatment_goals'] ?? null;
        $assessment->estimated_duration = $_POST['estimated_duration'] ?? null;
        $assessment->treatment_priority = (int)$_POST['treatment_priority'];
        $assessment->treatment_notes = $_POST['treatment_notes'] ?? null;
        
        // Consentimientos
        $consents = [];
        if (isset($_POST['consents']) && is_array($_POST['consents'])) {
            $consents = $_POST['consents'];
        }
        $assessment->consents = json_encode($consents);
        
        $assessment->patient_concerns = $_POST['patient_concerns'] ?? null;
        
        // Seguimiento
        $assessment->next_appointment = !empty($_POST['next_appointment']) ? $_POST['next_appointment'] : null;
        $assessment->follow_up_type = $_POST['follow_up_type'] ?? null;
        $assessment->pending_studies = $_POST['pending_studies'] ?? null;
        $assessment->referrals = $_POST['referrals'] ?? null;
        
        // Resumen médico
        $assessment->medical_summary = $_POST['medical_summary'];
        $assessment->recommendations = $_POST['recommendations'] ?? null;
        
        // Estado de la evaluación
        $assessment->status = isset($_POST['draft']) && $_POST['draft'] == '1' ? 'draft' : 'completed';
        
        // Guardar la evaluación
        $result = $assessment->add();
        
        if ($result) {
            // Crear notificación para el equipo médico
            if (class_exists('NotificationService')) {
                try {
                    $patient = PacientData::getById($assessment->pacient_id);
                    $medic = MedicData::getById($assessment->evaluating_medic_id);
                    
                    // Preparar datos del destinatario
                    $recipient_data = [
                        'email' => $medic->email,
                        'name' => $medic->name . ' ' . $medic->lastname,
                        'type' => 'medic'
                    ];
                    
                    // Variables de la plantilla
                    $template_vars = [
                        'patient_name' => $patient->name . ' ' . $patient->lastname,
                        'medic_name' => $medic->name . ' ' . $medic->lastname,
                        'assessment_date' => date('d/m/Y'),
                        'diagnosis' => $assessment->primary_diagnosis
                    ];
                    
                    // Enviar notificación
                    NotificationService::sendNotification(
                        'assessment_completed',
                        $recipient_data,
                        $template_vars,
                        $result,
                        'initial_assessment'
                    );
                } catch (Exception $e) {
                    // Log error pero no detener el proceso
                    error_log("Error enviando notificación: " . $e->getMessage());
                }
            }
            
            $_SESSION['flash_message'] = [
                'type' => 'success',
                'message' => 'Evaluación inicial guardada exitosamente'
            ];
            
            // Redirigir a la vista del paciente o lista de evaluaciones
            Core::redir("./?view=pacients&opt=one&id=" . $assessment->pacient_id);
            
        } else {
            throw new Exception('Error al guardar la evaluación en la base de datos');
        }
        
    } catch (Exception $e) {
        error_log("Error en evaluación inicial: " . $e->getMessage());
        
        $_SESSION['flash_message'] = [
            'type' => 'error',
            'message' => 'Error al guardar la evaluación: ' . $e->getMessage()
        ];
        
        Core::redir("./?view=initialassessment");
    }
    
} else {
    // Si no es POST, redirigir a la vista
    Core::redir("./?view=initialassessment");
}
?>
