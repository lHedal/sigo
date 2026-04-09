<?php
include "core/app/model/OncologyWaitlistData.php";
include "core/app/model/OncologySchedulingService.php";
include "core/app/model/NotificationService.php";

header('Content-Type: application/json');

if(isset($_POST["waitlist_id"])){
    $waitlist_id = $_POST["waitlist_id"];
    $reservation_id = OncologySchedulingService::autoAssignAppointment($waitlist_id);
    
    if($reservation_id){
        // Enviar notificación de asignación desde lista de espera
        NotificationService::notifyWaitlistAssignment($waitlist_id, $reservation_id);
        
        echo json_encode([
            'success' => true, 
            'reservation_id' => $reservation_id,
            'message' => 'Cita asignada exitosamente'
        ]);
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'No se encontraron horarios disponibles'
        ]);
    }
} else {
    echo json_encode([
        'success' => false, 
        'message' => 'ID de lista de espera no proporcionado'
    ]);
}
?>
