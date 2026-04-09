<?php
include "core/app/model/NotificationData.php";

header('Content-Type: application/json');

try {
    // Obtener todas las notificaciones fallidas
    $failed_notifications = NotificationQueueData::getByStatus('failed');
    $retried = 0;
    
    foreach($failed_notifications as $notification) {
        // Resetear intentos y estado
        $notification->attempts = 0;
        $notification->status = 'pending';
        $notification->update();
        $retried++;
    }
    
    echo json_encode([
        'success' => true,
        'retried' => $retried,
        'message' => "Se reintentaron $retried notificaciones"
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al reintentar notificaciones: ' . $e->getMessage()
    ]);
}
?>
