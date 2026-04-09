<?php
include "core/app/model/OncologyWaitlistData.php";
include "core/app/model/OncologySchedulingService.php";

$response = ['success' => false, 'processed' => 0, 'message' => ''];

try {
    $pending_items = OncologyWaitlistData::getPending();
    $scheduler = new OncologySchedulingService();
    $processed = 0;
    $errors = [];
    
    foreach($pending_items as $item) {
        try {
            $result = $scheduler->autoAssignFromWaitlist($item->id);
            if($result['success']) {
                $processed++;
            } else {
                $errors[] = "Paciente ID {$item->pacient_id}: " . $result['message'];
            }
        } catch(Exception $e) {
            $errors[] = "Paciente ID {$item->pacient_id}: " . $e->getMessage();
        }
    }
    
    $response['success'] = true;
    $response['processed'] = $processed;
    
    if(count($errors) > 0) {
        $response['message'] = "Se procesaron $processed elementos. Errores: " . implode('; ', array_slice($errors, 0, 3));
    } else {
        $response['message'] = "Todos los elementos fueron procesados exitosamente.";
    }
    
} catch(Exception $e) {
    $response['message'] = $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($response);
?>
