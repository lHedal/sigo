<?php
include "core/app/model/NotificationData.php";
include "core/app/model/NotificationService.php";

header('Content-Type: application/json');

if(isset($_POST['id'])) {
    $notification_id = intval($_POST['id']);
    $notification = NotificationQueueData::getById($notification_id);
    
    if($notification && ($notification->status == 'pending' || $notification->status == 'failed')) {
        // Intentar enviar la notificación específica
        $result = NotificationService::sendEmail(
            $notification->recipient_email,
            $notification->recipient_name,
            $notification->subject,
            $notification->body
        );
        
        if($result) {
            $notification->status = 'sent';
            $notification->update();
            
            echo json_encode([
                'success' => true,
                'message' => 'Notificación enviada exitosamente'
            ]);
        } else {
            $notification->attempts++;
            if($notification->attempts >= $notification->max_attempts) {
                $notification->status = 'failed';
            }
            $notification->update();
            
            echo json_encode([
                'success' => false,
                'message' => 'Error al enviar la notificación'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Notificación no válida para procesamiento'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'ID no proporcionado'
    ]);
}
?>
