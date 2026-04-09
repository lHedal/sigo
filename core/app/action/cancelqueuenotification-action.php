<?php
include "core/app/model/NotificationData.php";

header('Content-Type: application/json');

if(isset($_POST['id'])) {
    $notification_id = intval($_POST['id']);
    $notification = NotificationQueueData::getById($notification_id);
    
    if($notification) {
        $notification->status = 'cancelled';
        $notification->update();
        
        echo json_encode([
            'success' => true,
            'message' => 'Notificación cancelada exitosamente'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Notificación no encontrada'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'ID no proporcionado'
    ]);
}
?>
