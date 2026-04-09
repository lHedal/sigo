<?php
include "core/app/model/NotificationData.php";
include "core/app/model/NotificationService.php";

if(count($_POST) > 0){
    try {
        $config_data = [
            'smtp_host' => $_POST['smtp_host'],
            'smtp_port' => intval($_POST['smtp_port']),
            'smtp_security' => $_POST['smtp_security'],
            'smtp_username' => $_POST['smtp_username'],
            'smtp_password' => $_POST['smtp_password'],
            'from_email' => $_POST['from_email'],
            'from_name' => $_POST['from_name'],
            'notifications_enabled' => isset($_POST['notifications_enabled']) ? 1 : 0,
            'auto_send_enabled' => isset($_POST['auto_send_enabled']) ? 1 : 0
        ];
        
        $result = NotificationService::updateConfig($config_data);
        
        if($result !== false) {
            print "<script>alert('Configuración actualizada exitosamente'); window.location='index.php?view=notificationconfig';</script>";
        } else {
            print "<script>alert('Error al actualizar la configuración en la base de datos'); window.location='index.php?view=notificationconfig';</script>";
        }
    } catch(Exception $e) {
        error_log("Error updating notification config: " . $e->getMessage());
        print "<script>alert('Error: " . addslashes($e->getMessage()) . "'); window.location='index.php?view=notificationconfig';</script>";
    }
} else {
    print "<script>alert('Datos no válidos'); window.location='index.php?view=notificationconfig';</script>";
}
?>
