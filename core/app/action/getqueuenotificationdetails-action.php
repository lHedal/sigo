<?php
include "core/app/model/NotificationData.php";

header('Content-Type: application/json');

if(isset($_GET['id'])) {
    $notification_id = intval($_GET['id']);
    $notification = NotificationQueueData::getById($notification_id);
    
    if($notification) {
        echo json_encode([
            'success' => true,
            'notification' => [
                'id' => $notification->id,
                'recipient_name' => $notification->recipient_name,
                'recipient_email' => $notification->recipient_email,
                'subject' => $notification->subject,
                'body' => $notification->body,
                'status' => $notification->status,
                'scheduled_at' => date('d/m/Y H:i:s', strtotime($notification->scheduled_at)),
                'attempts' => $notification->attempts,
                'max_attempts' => $notification->max_attempts,
                'created_at' => date('d/m/Y H:i:s', strtotime($notification->created_at))
            ]
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
