<?php
/**
 * Procesador automático de notificaciones
 * Este archivo debe ser ejecutado periódicamente (cada 5-15 minutos) mediante cron job
 * 
 * Ejemplo de cron job (cada 10 minutos):
 * Cron: cada 10 minutos ejecutar /usr/bin/php /ruta/al/proyecto/notification_processor.php
 */

// Configurar para ejecución desde línea de comandos
if (php_sapi_name() !== 'cli') {
    die('Este script solo puede ejecutarse desde línea de comandos.');
}

// Incluir dependencias
require_once(__DIR__ . '/core/autoload.php');
require_once(__DIR__ . '/core/app/model/NotificationData.php');
require_once(__DIR__ . '/core/app/model/NotificationService.php');

// Log de inicio
$log_file = __DIR__ . '/logs/notification_processor.log';
$log_dir = dirname($log_file);
if (!is_dir($log_dir)) {
    mkdir($log_dir, 0755, true);
}

function writeLog($message) {
    global $log_file;
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($log_file, "[$timestamp] $message\n", FILE_APPEND | LOCK_EX);
}

writeLog("=== Iniciando procesador de notificaciones ===");

try {
    // Verificar configuración
    $config = NotificationConfigData::getConfig();
    if (!$config || !$config->notifications_enabled) {
        writeLog("Las notificaciones están deshabilitadas. Saliendo.");
        exit(0);
    }
    
    if (!$config->auto_send_enabled) {
        writeLog("El envío automático está deshabilitado. Saliendo.");
        exit(0);
    }
    
    // Procesar cola
    writeLog("Procesando cola de notificaciones...");
    $processed = NotificationService::processQueue();
    writeLog("Se procesaron $processed notificaciones");
    
    // Limpiar logs antiguos (opcional, mantener solo últimos 30 días)
    $cleanup_date = date('Y-m-d', strtotime('-30 days'));
    $cleanup_sql = "DELETE FROM notification_log WHERE created_at < '$cleanup_date 00:00:00' AND status IN ('sent', 'failed')";
    Executor::doit($cleanup_sql);
    writeLog("Logs antiguos limpiados");
    
    writeLog("=== Procesador finalizado exitosamente ===");
    
} catch (Exception $e) {
    writeLog("ERROR: " . $e->getMessage());
    writeLog("Stack trace: " . $e->getTraceAsString());
    exit(1);
}
?>
