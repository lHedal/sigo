<?php
include_once 'core/app/model/NotificationData.php';
include_once 'core/controller/class.phpmailer.php';

class NotificationService {
    
    private static $config = null;
    
    /**
     * Obtiene la configuración de notificaciones
     */
    private static function getConfig() {
        if (self::$config === null) {
            self::$config = NotificationConfigData::getConfig();
        }
        return self::$config;
    }
    
    /**
     * Envía una notificación inmediata
     */
    public static function sendNotification($type_code, $recipient_data, $template_vars = [], $reference_id = null, $reference_type = null) {
        $config = self::getConfig();
        
        if (!$config || !$config->notifications_enabled) {
            return false;
        }
        
        $notification_type = NotificationTypeData::getByCode($type_code);
        if (!$notification_type) {
            error_log("Notification type not found: " . $type_code);
            return false;
        }
        
        // Verificar si debe enviar según el tipo de destinatario
        if ($recipient_data['type'] == 'patient' && !$notification_type->send_to_patient) {
            return true; // No es error, solo no se envía
        }
        if ($recipient_data['type'] == 'medic' && !$notification_type->send_to_medic) {
            return true;
        }
        
        // Procesar plantilla
        $subject = self::processTemplate($notification_type->template_subject, $template_vars);
        $body = self::processTemplate($notification_type->template_body, $template_vars);
        
        // Intentar enviar email
        $result = self::sendEmail(
            $recipient_data['email'],
            $recipient_data['name'],
            $subject,
            $body
        );
        
        // Registrar en log
        self::logNotification(
            $notification_type->id,
            $recipient_data,
            $subject,
            $body,
            $result ? 'sent' : 'failed',
            $reference_id,
            $reference_type,
            $result ? null : 'Error al enviar email'
        );
        
        return $result;
    }
    
    /**
     * Programa una notificación para envío futuro
     */
    public static function scheduleNotification($type_code, $recipient_data, $scheduled_at, $template_vars = [], $reference_id = null, $reference_type = null) {
        $notification_type = NotificationTypeData::getByCode($type_code);
        if (!$notification_type) {
            return false;
        }
        
        $subject = self::processTemplate($notification_type->template_subject, $template_vars);
        $body = self::processTemplate($notification_type->template_body, $template_vars);
        
        $queue_item = new NotificationQueueData();
        $queue_item->notification_type_id = $notification_type->id;
        $queue_item->recipient_email = $recipient_data['email'];
        $queue_item->recipient_name = $recipient_data['name'];
        $queue_item->recipient_type = $recipient_data['type'];
        $queue_item->subject = $subject;
        $queue_item->body = $body;
        $queue_item->scheduled_at = $scheduled_at;
        $queue_item->reference_id = $reference_id;
        $queue_item->reference_type = $reference_type;
        
        return $queue_item->add();
    }
    
    /**
     * Procesa notificaciones en cola
     */
    public static function processQueue() {
        $pending_notifications = NotificationQueueData::getPendingNotifications();
        $processed = 0;
        
        foreach ($pending_notifications as $notification) {
            $notification->status = 'processing';
            $notification->update();
            
            $result = self::sendEmail(
                $notification->recipient_email,
                $notification->recipient_name,
                $notification->subject,
                $notification->body
            );
            
            if ($result) {
                $notification->status = 'sent';
                
                // Log exitoso
                $notification_type = NotificationTypeData::getById($notification->notification_type_id);
                self::logNotification(
                    $notification->notification_type_id,
                    [
                        'email' => $notification->recipient_email,
                        'name' => $notification->recipient_name,
                        'type' => $notification->recipient_type
                    ],
                    $notification->subject,
                    $notification->body,
                    'sent',
                    $notification->reference_id,
                    $notification->reference_type
                );
                
                $processed++;
            } else {
                $notification->attempts++;
                if ($notification->attempts >= $notification->max_attempts) {
                    $notification->status = 'failed';
                } else {
                    $notification->status = 'pending';
                }
            }
            
            $notification->update();
        }
        
        return $processed;
    }
    
    /**
     * Envía email usando PHPMailer
     */
    private static function sendEmail($to_email, $to_name, $subject, $body) {
        $config = self::getConfig();
        
        if (!$config || !$config->smtp_username || !$config->smtp_password) {
            error_log("SMTP configuration incomplete");
            return false;
        }
        
        try {
            $phpmailer = new PHPMailer();
            
            // Configuración SMTP
            $phpmailer->isSMTP();
            $phpmailer->Host = $config->smtp_host;
            $phpmailer->Port = $config->smtp_port;
            $phpmailer->SMTPAuth = true;
            $phpmailer->Username = $config->smtp_username;
            $phpmailer->Password = $config->smtp_password;
            
            // Configurar seguridad
            if ($config->smtp_security == 'ssl') {
                $phpmailer->SMTPSecure = 'ssl';
            } else if ($config->smtp_security == 'tls') {
                $phpmailer->SMTPSecure = 'tls';
            }
            
            // Configurar remitente
            $phpmailer->setFrom($config->from_email, $config->from_name);
            
            // Configurar destinatario
            $phpmailer->addAddress($to_email, $to_name);
            
            // Configurar mensaje
            $phpmailer->isHTML(true);
            $phpmailer->Subject = $subject;
            $phpmailer->Body = self::wrapEmailTemplate($body);
            $phpmailer->CharSet = 'UTF-8';
            
            return $phpmailer->send();
            
        } catch (Exception $e) {
            error_log("PHPMailer Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Procesa variables en plantillas
     */
    private static function processTemplate($template, $vars) {
        $processed = $template;
        
        foreach ($vars as $key => $value) {
            $processed = str_replace('{{' . $key . '}}', $value, $processed);
        }
        
        return $processed;
    }
    
    /**
     * Registra notificación en log
     */
    private static function logNotification($type_id, $recipient_data, $subject, $body, $status, $reference_id = null, $reference_type = null, $error_message = null) {
        $log = new NotificationData();
        $log->notification_type_id = $type_id;
        $log->recipient_email = $recipient_data['email'];
        $log->recipient_name = $recipient_data['name'];
        $log->recipient_type = $recipient_data['type'];
        $log->subject = $subject;
        $log->body = $body;
        $log->status = $status;
        $log->reference_id = $reference_id;
        $log->reference_type = $reference_type;
        $log->error_message = $error_message;
        
        if ($status == 'sent') {
            $log->sent_at = date('Y-m-d H:i:s');
        }
        
        return $log->add();
    }
    
    /**
     * Envuelve el contenido del email en una plantilla HTML
     */
    private static function wrapEmailTemplate($content) {
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Sistema Oncológico</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    line-height: 1.6;
                    color: #333;
                    background-color: #f4f4f4;
                    margin: 0;
                    padding: 20px;
                }
                .container {
                    max-width: 600px;
                    margin: 0 auto;
                    background-color: #ffffff;
                    border-radius: 8px;
                    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                    overflow: hidden;
                }
                .header {
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    color: white;
                    padding: 30px;
                    text-align: center;
                }
                .header h1 {
                    margin: 0;
                    font-size: 24px;
                }
                .content {
                    padding: 30px;
                }
                .footer {
                    background-color: #f8f9fa;
                    padding: 20px;
                    text-align: center;
                    color: #6c757d;
                    font-size: 14px;
                }
                h2 {
                    color: #495057;
                    margin-top: 0;
                }
                .btn {
                    display: inline-block;
                    background-color: #007bff;
                    color: white;
                    padding: 12px 24px;
                    text-decoration: none;
                    border-radius: 4px;
                    margin: 10px 0;
                }
                .alert {
                    padding: 15px;
                    margin: 20px 0;
                    border-radius: 4px;
                    border: 1px solid;
                }
                .alert-info {
                    background-color: #d1ecf1;
                    border-color: #bee5eb;
                    color: #0c5460;
                }
                .alert-success {
                    background-color: #d4edda;
                    border-color: #c3e6cb;
                    color: #155724;
                }
                .alert-warning {
                    background-color: #fff3cd;
                    border-color: #ffeaa7;
                    color: #856404;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>🏥 Sistema Oncológico</h1>
                    <p>Centro de Tratamiento Especializado</p>
                </div>
                <div class="content">
                    ' . $content . '
                </div>
                <div class="footer">
                    <p>Este es un mensaje automático del Sistema Oncológico.</p>
                    <p>Para consultas, contacte a nuestro centro médico.</p>
                    <hr style="border: none; border-top: 1px solid #dee2e6; margin: 20px 0;">
                    <p>© ' . date('Y') . ' Sistema Oncológico. Todos los derechos reservados.</p>
                </div>
            </div>
        </body>
        </html>';
    }
    
    /**
     * Métodos específicos para notificaciones oncológicas
     */
    
    // Notificación de cita agendada
    public static function notifyAppointmentScheduled($reservation_id) {
        $reservation = ReservationData::getById($reservation_id);
        $patient = $reservation->getPacient();
        $medic = $reservation->getMedic();
        
        $template_vars = [
            'patient_name' => $patient->name . ' ' . $patient->lastname,
            'medic_name' => $medic->name . ' ' . $medic->lastname,
            'date' => date('d/m/Y', strtotime($reservation->date_at)),
            'time' => date('H:i', strtotime($reservation->time_at)),
            'treatment_type' => $reservation->title,
            'chair_name' => $reservation->getChair() ? $reservation->getChair()->name : 'Por asignar'
        ];
        
        // Notificar al paciente
        if ($patient->email) {
            self::sendNotification(
                'appointment_scheduled',
                ['email' => $patient->email, 'name' => $patient->name . ' ' . $patient->lastname, 'type' => 'patient'],
                $template_vars,
                $reservation_id,
                'reservation'
            );
        }
        
        // Notificar al médico
        if ($medic->email) {
            self::sendNotification(
                'appointment_scheduled',
                ['email' => $medic->email, 'name' => $medic->name . ' ' . $medic->lastname, 'type' => 'medic'],
                $template_vars,
                $reservation_id,
                'reservation'
            );
        }
        
        // Programar recordatorio 24 horas antes
        $reminder_time = date('Y-m-d H:i:s', strtotime($reservation->date_at . ' ' . $reservation->time_at . ' -1 day'));
        if (strtotime($reminder_time) > time()) {
            self::scheduleNotification(
                'appointment_reminder',
                ['email' => $patient->email, 'name' => $patient->name . ' ' . $patient->lastname, 'type' => 'patient'],
                $reminder_time,
                $template_vars,
                $reservation_id,
                'reservation'
            );
        }
    }
    
    // Notificación de asignación desde lista de espera
    public static function notifyWaitlistAssignment($waitlist_id, $reservation_id) {
        $waitlist = OncologyWaitlistData::getById($waitlist_id);
        $reservation = ReservationData::getById($reservation_id);
        $patient = $waitlist->getPacient();
        $medic = $reservation->getMedic();
        
        $template_vars = [
            'patient_name' => $patient->name . ' ' . $patient->lastname,
            'medic_name' => $medic->name . ' ' . $medic->lastname,
            'date' => date('d/m/Y', strtotime($reservation->date_at)),
            'time' => date('H:i', strtotime($reservation->time_at)),
            'treatment_type' => $waitlist->treatment_type,
            'chair_name' => $reservation->getChair() ? $reservation->getChair()->name : 'Por asignar',
            'priority_level' => self::getPriorityLabel($waitlist->priority_level)
        ];
        
        // Notificar al paciente
        if ($patient->email) {
            self::sendNotification(
                'waitlist_assigned',
                ['email' => $patient->email, 'name' => $patient->name . ' ' . $patient->lastname, 'type' => 'patient'],
                $template_vars,
                $reservation_id,
                'reservation'
            );
        }
        
        // Notificar al médico
        if ($medic->email) {
            self::sendNotification(
                'waitlist_assigned',
                ['email' => $medic->email, 'name' => $medic->name . ' ' . $medic->lastname, 'type' => 'medic'],
                $template_vars,
                $reservation_id,
                'reservation'
            );
        }
    }
    
    // Notificación de nuevo paciente
    public static function notifyPatientRegistered($patient_id) {
        $patient = PacientData::getById($patient_id);
        
        if (!$patient->email) {
            return;
        }
        
        $template_vars = [
            'patient_name' => $patient->name . ' ' . $patient->lastname,
            'email' => $patient->email,
            'phone' => $patient->phone
        ];
        
        self::sendNotification(
            'patient_registered',
            ['email' => $patient->email, 'name' => $patient->name . ' ' . $patient->lastname, 'type' => 'patient'],
            $template_vars,
            $patient_id,
            'patient'
        );
    }
    
    // Notificación de agregado a lista de espera
    public static function notifyWaitlistAdded($waitlist_id) {
        $waitlist = OncologyWaitlistData::getById($waitlist_id);
        $patient = $waitlist->getPacient();
        
        if (!$patient->email) {
            return;
        }
        
        $template_vars = [
            'patient_name' => $patient->name . ' ' . $patient->lastname,
            'treatment_type' => $waitlist->treatment_type,
            'priority_level' => self::getPriorityLabel($waitlist->priority_level),
            'requested_date' => $waitlist->requested_date ? date('d/m/Y', strtotime($waitlist->requested_date)) : 'Fecha flexible'
        ];
        
        self::sendNotification(
            'waitlist_added',
            ['email' => $patient->email, 'name' => $patient->name . ' ' . $patient->lastname, 'type' => 'patient'],
            $template_vars,
            $waitlist_id,
            'waitlist'
        );
    }
    
    // Helper para obtener etiqueta de prioridad
    private static function getPriorityLabel($level) {
        $labels = [
            1 => 'Baja',
            2 => 'Media',
            3 => 'Alta',
            4 => 'Urgente',
            5 => 'Crítica'
        ];
        return isset($labels[$level]) ? $labels[$level] : 'Normal';
    }
    
    /**
     * Configurar notificaciones
     */
    public static function updateConfig($config_data) {
        $config = NotificationConfigData::getConfig();
        
        if (!$config) {
            $config = new NotificationConfigData();
        }
        
        foreach ($config_data as $key => $value) {
            if (property_exists($config, $key)) {
                $config->$key = $value;
            }
        }
        
        if (isset($config->id) && $config->id > 0) {
            return $config->update();
        } else {
            return $config->add();
        }
    }
    
    /**
     * Probar configuración de email
     */
    public static function testEmailConfig($test_email) {
        return self::sendEmail(
            $test_email,
            'Administrador',
            'Prueba de Configuración - Sistema Oncológico',
            '<h2>✅ Configuración Exitosa</h2><p>Este es un email de prueba para verificar que la configuración SMTP está funcionando correctamente.</p><p>El sistema de notificaciones está listo para usar.</p>'
        );
    }
}
?>
