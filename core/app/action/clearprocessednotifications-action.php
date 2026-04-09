<?php
include "core/app/model/NotificationData.php";

header('Content-Type: application/json');

try {
    // Eliminar notificaciones enviadas
    $sql = "DELETE FROM notification_queue WHERE status = 'sent'";
    $result = Executor::doit($sql);
    
    echo json_encode([
        'success' => true,
        'cleared' => $result[1], // Número de filas afectadas
        'message' => "Se eliminaron las notificaciones enviadas"
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al limpiar notificaciones: ' . $e->getMessage()
    ]);
}
?>
