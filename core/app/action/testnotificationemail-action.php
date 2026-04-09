<?php
include "core/app/model/NotificationData.php";
include "core/app/model/NotificationService.php";

header('Content-Type: application/json');

if(isset($_POST['email'])) {
    $test_email = $_POST['email'];
    
    $result = NotificationService::testEmailConfig($test_email);
    
    if($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Email de prueba enviado exitosamente'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Error al enviar email de prueba. Verifique la configuración SMTP.'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Email no proporcionado'
    ]);
}
?>
