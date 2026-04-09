<?php
include "core/app/model/NotificationData.php";
include "core/app/model/NotificationService.php";

header('Content-Type: application/json');

try {
    $processed = NotificationService::processQueue();
    
    echo json_encode([
        'success' => true,
        'processed' => $processed,
        'message' => "Se procesaron $processed notificaciones"
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al procesar la cola: ' . $e->getMessage()
    ]);
}
?>
