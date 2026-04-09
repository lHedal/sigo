<?php
/**
 * Action para guardar borradores de evaluación inicial
 * Permite guardar evaluaciones parciales sin validación completa
 */

// Verificar que el usuario esté logueado
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
    exit;
}

// Solo procesar requests POST
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

// Configurar header para respuesta JSON
header('Content-Type: application/json');

try {
    // Crear objeto de evaluación inicial
    $assessment = new InitialAssessmentData();
    
    // Si viene un ID, es una actualización de borrador existente
    if (!empty($_POST['assessment_id'])) {
        $assessment = InitialAssessmentData::getById($_POST['assessment_id']);
        if (!$assessment || $assessment->status != 'draft') {
            throw new Exception('Borrador no encontrado o ya está completado');
        }
    }
    
    // Datos básicos (solo si están presentes)
    if (!empty($_POST['pacient_id'])) {
        $assessment->pacient_id = (int)$_POST['pacient_id'];
    }
    if (!empty($_POST['evaluating_medic_id'])) {
        $assessment->evaluating_medic_id = (int)$_POST['evaluating_medic_id'];
    }
    
    // Si es nuevo borrador, establecer fecha y usuario
    if (empty($assessment->id)) {
        $assessment->evaluation_date = date('Y-m-d H:i:s');
        $assessment->created_by = $_SESSION["user_id"];
    }
    
    // Historia oncológica
    if (isset($_POST['primary_diagnosis'])) {
        $assessment->primary_diagnosis = $_POST['primary_diagnosis'];
    }
    if (isset($_POST['tumor_stage'])) {
        $assessment->tumor_stage = $_POST['tumor_stage'];
    }
    if (isset($_POST['date_of_diagnosis'])) {
        $assessment->date_of_diagnosis = $_POST['date_of_diagnosis'];
    }
    if (isset($_POST['previous_treatments'])) {
        $assessment->previous_treatments = $_POST['previous_treatments'];
    }
    if (isset($_POST['family_history'])) {
        $assessment->family_history = $_POST['family_history'];
    }
    
    // Estado funcional y síntomas
    if (isset($_POST['ecog_performance_status'])) {
        $assessment->ecog_performance_status = (int)$_POST['ecog_performance_status'];
    }
    if (isset($_POST['weight_loss'])) {
        $assessment->weight_loss = !empty($_POST['weight_loss']) ? (float)$_POST['weight_loss'] : null;
    }
    
    // Procesar síntomas
    $symptoms = [];
    if (isset($_POST['symptoms']) && is_array($_POST['symptoms'])) {
        $symptoms = $_POST['symptoms'];
    }
    $assessment->current_symptoms = json_encode($symptoms);
    
    if (isset($_POST['pain_scale'])) {
        $assessment->pain_scale = (int)$_POST['pain_scale'];
    }
    if (isset($_POST['symptoms_other'])) {
        $assessment->symptoms_other = $_POST['symptoms_other'];
    }
    
    // Evaluación psicosocial
    $support_system = [];
    if (isset($_POST['support']) && is_array($_POST['support'])) {
        $support_system = $_POST['support'];
    }
    $assessment->support_system = json_encode($support_system);
    
    if (isset($_POST['psychological_state'])) {
        $assessment->psychological_state = $_POST['psychological_state'];
    }
    if (isset($_POST['coping_mechanisms'])) {
        $assessment->coping_mechanisms = $_POST['coping_mechanisms'];
    }
    
    // Plan de tratamiento
    if (isset($_POST['proposed_treatment'])) {
        $assessment->proposed_treatment = $_POST['proposed_treatment'];
    }
    if (isset($_POST['treatment_goals'])) {
        $assessment->treatment_goals = $_POST['treatment_goals'];
    }
    if (isset($_POST['estimated_duration'])) {
        $assessment->estimated_duration = $_POST['estimated_duration'];
    }
    if (isset($_POST['treatment_priority'])) {
        $assessment->treatment_priority = (int)$_POST['treatment_priority'];
    }
    if (isset($_POST['treatment_notes'])) {
        $assessment->treatment_notes = $_POST['treatment_notes'];
    }
    
    // Consentimientos
    $consents = [];
    if (isset($_POST['consents']) && is_array($_POST['consents'])) {
        $consents = $_POST['consents'];
    }
    $assessment->consents = json_encode($consents);
    
    if (isset($_POST['patient_concerns'])) {
        $assessment->patient_concerns = $_POST['patient_concerns'];
    }
    
    // Seguimiento
    if (isset($_POST['next_appointment'])) {
        $assessment->next_appointment = !empty($_POST['next_appointment']) ? $_POST['next_appointment'] : null;
    }
    if (isset($_POST['follow_up_type'])) {
        $assessment->follow_up_type = $_POST['follow_up_type'];
    }
    if (isset($_POST['pending_studies'])) {
        $assessment->pending_studies = $_POST['pending_studies'];
    }
    if (isset($_POST['referrals'])) {
        $assessment->referrals = $_POST['referrals'];
    }
    
    // Resumen médico
    if (isset($_POST['medical_summary'])) {
        $assessment->medical_summary = $_POST['medical_summary'];
    }
    if (isset($_POST['recommendations'])) {
        $assessment->recommendations = $_POST['recommendations'];
    }
    
    // Siempre es borrador en esta acción
    $assessment->status = 'draft';
    
    // Guardar (nuevo o actualización)
    if (empty($assessment->id)) {
        $result = $assessment->add();
        $assessment->id = $result; // Para respuesta
    } else {
        $result = $assessment->update();
    }
    
    if ($result) {
        // Calcular progreso del formulario
        $total_sections = 7;
        $completed_sections = 0;
        
        // Verificar secciones completadas
        if (!empty($assessment->pacient_id) && !empty($assessment->evaluating_medic_id)) {
            $completed_sections++;
        }
        if (!empty($assessment->primary_diagnosis) && !empty($assessment->date_of_diagnosis)) {
            $completed_sections++;
        }
        if (!empty($assessment->ecog_performance_status)) {
            $completed_sections++;
        }
        if (!empty($assessment->psychological_state)) {
            $completed_sections++;
        }
        if (!empty($assessment->proposed_treatment) && !empty($assessment->treatment_priority)) {
            $completed_sections++;
        }
        if (!empty($assessment->consents)) {
            $completed_sections++;
        }
        if (!empty($assessment->medical_summary)) {
            $completed_sections++;
        }
        
        $progress_percentage = round(($completed_sections / $total_sections) * 100);
        
        // Respuesta exitosa
        $response = [
            'success' => true,
            'message' => 'Borrador guardado exitosamente',
            'assessment_id' => $assessment->id,
            'progress' => [
                'completed_sections' => $completed_sections,
                'total_sections' => $total_sections,
                'percentage' => $progress_percentage
            ],
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        // Crear notificación si es un borrador significativo (>50% completo)
        if ($progress_percentage > 50 && class_exists('NotificationService')) {
            $notification_service = new NotificationService();
            $patient = PacientData::getById($assessment->pacient_id);
            
            $notification_data = [
                'type' => 'assessment_draft_saved',
                'title' => 'Borrador de Evaluación Guardado',
                'message' => "Borrador de evaluación guardado para {$patient->name} {$patient->lastname} ({$progress_percentage}% completo)",
                'reference_type' => 'initial_assessment_draft',
                'reference_id' => $assessment->id,
                'priority' => 'low',
                'created_by' => $_SESSION["user_id"]
            ];
            
            $notification_service->createNotification($notification_data);
        }
        
        echo json_encode($response);
        
    } else {
        throw new Exception('Error al guardar el borrador en la base de datos');
    }
    
} catch (Exception $e) {
    error_log("Error en guardado de borrador: " . $e->getMessage());
    
    $response = [
        'success' => false,
        'message' => 'Error al guardar borrador: ' . $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    http_response_code(500);
    echo json_encode($response);
}
?>
