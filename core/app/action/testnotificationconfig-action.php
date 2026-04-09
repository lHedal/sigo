<?php
include "core/app/model/NotificationData.php";

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

// Log the request for debugging
error_log("Test notification config action called with data: " . print_r($_POST, true));

if(isset($_POST['test_email'])) {
    try {
        // Validate input data
        if(empty($_POST['smtp_host']) || empty($_POST['smtp_username']) || empty($_POST['smtp_password'])) {
            throw new Exception("Missing required SMTP configuration data");
        }
        
        // Crear configuración temporal con los datos del formulario
        $temp_config = new NotificationConfigData();
        $temp_config->smtp_host = $_POST['smtp_host'];
        $temp_config->smtp_port = intval($_POST['smtp_port']);
        $temp_config->smtp_security = $_POST['smtp_security'];
        $temp_config->smtp_username = $_POST['smtp_username'];
        $temp_config->smtp_password = $_POST['smtp_password'];
        $temp_config->from_email = $_POST['from_email'];
        $temp_config->from_name = $_POST['from_name'];
        
        // Probar envío con configuración temporal
        require_once("core/controller/class.phpmailer.php");
        
        if(!class_exists('PHPMailer')) {
            throw new Exception("PHPMailer class not found");
        }
        
        $phpmailer = new PHPMailer(true); // Enable exceptions
        
        // Configuración SMTP
        $phpmailer->isSMTP();
        $phpmailer->Host = $temp_config->smtp_host;
        $phpmailer->Port = $temp_config->smtp_port;
        $phpmailer->SMTPAuth = true;
        $phpmailer->Username = $temp_config->smtp_username;
        $phpmailer->Password = $temp_config->smtp_password;
        
        // Enable debugging for troubleshooting
        // $phpmailer->SMTPDebug = 2;
        
        // Configurar seguridad
        if ($temp_config->smtp_security == 'ssl') {
            $phpmailer->SMTPSecure = 'ssl';
        } else if ($temp_config->smtp_security == 'tls') {
            $phpmailer->SMTPSecure = 'tls';
        }
        
        // Configurar remitente
        $phpmailer->setFrom($temp_config->from_email, $temp_config->from_name);
        
        // Configurar destinatario
        $phpmailer->addAddress($_POST['test_email'], 'Administrador de Prueba');
        
        // Configurar mensaje
        $phpmailer->isHTML(true);
        $phpmailer->Subject = 'Prueba de Configuración SMTP - Sistema Oncológico';
        $phpmailer->Body = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Prueba SMTP</title>
        </head>
        <body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
            <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
                <h2 style="color: #28a745;">✅ Configuración SMTP Exitosa</h2>
                <p>Este es un email de prueba para verificar que la configuración SMTP está funcionando correctamente.</p>
                <div style="background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;">
                    <strong>Detalles de la configuración:</strong><br>
                    <strong>Servidor:</strong> ' . $temp_config->smtp_host . '<br>
                    <strong>Puerto:</strong> ' . $temp_config->smtp_port . '<br>
                    <strong>Seguridad:</strong> ' . strtoupper($temp_config->smtp_security) . '<br>
                    <strong>Usuario:</strong> ' . $temp_config->smtp_username . '<br>
                    <strong>Fecha:</strong> ' . date('d/m/Y H:i:s') . '
                </div>
                <p style="color: #28a745;"><strong>El sistema de notificaciones está listo para usar.</strong></p>
                <hr>
                <p style="font-size: 12px; color: #6c757d;">Sistema Oncológico - Notificaciones Automáticas</p>
            </div>
        </body>
        </html>';
        $phpmailer->CharSet = 'UTF-8';
        
        $result = $phpmailer->send();
        
        if($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Email de prueba enviado exitosamente'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error al enviar email: ' . $phpmailer->ErrorInfo
            ]);
        }
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error en la configuración: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Email de prueba no proporcionado'
    ]);
}
?>
