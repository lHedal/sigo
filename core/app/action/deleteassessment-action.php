<?php
/**
 * Action para eliminar evaluaciones en estado borrador
 * Solo permite eliminar evaluaciones que están en estado 'draft'
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
    // Verificar que se envió el ID de la evaluación
    if (empty($_POST['assessment_id'])) {
        throw new Exception('ID de evaluación no proporcionado');
    }
    
    $assessment_id = (int)$_POST['assessment_id'];
    
    // Obtener la evaluación
    $assessment = InitialAssessmentData::getById($assessment_id);
    
    if (!$assessment) {
        throw new Exception('Evaluación no encontrada');
    }
    
    // Verificar que sea un borrador
    if ($assessment->status != 'draft') {
        throw new Exception('Solo se pueden eliminar evaluaciones en estado borrador');
    }
    
    // Verificar que el usuario tenga permisos para eliminar
    // Solo el creador o un administrador pueden eliminar
    if ($assessment->created_by != $_SESSION["user_id"]) {
        // Verificar si es administrador
        $current_user = UserData::getById($_SESSION["user_id"]);
        if (!$current_user || $current_user->kind != 1) { // Asumiendo que kind=1 es admin
            throw new Exception('No tiene permisos para eliminar esta evaluación');
        }
    }
    
    // Eliminar la evaluación
    $assessment->id = $assessment_id;
    $result = $assessment->del();
    
    if ($result) {
        $response = [
            'success' => true,
            'message' => 'Borrador eliminado exitosamente',
            'assessment_id' => $assessment_id,
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        echo json_encode($response);
        
    } else {
        throw new Exception('Error al eliminar la evaluación de la base de datos');
    }
    
} catch (Exception $e) {
    error_log("Error al eliminar evaluación: " . $e->getMessage());
    
    $response = [
        'success' => false,
        'message' => $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    http_response_code(400);
    echo json_encode($response);
}
?>
