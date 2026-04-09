-- Sistema de Notificaciones para Oncología
-- Tabla para configuración de notificaciones

CREATE TABLE notification_config (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    smtp_host VARCHAR(255) DEFAULT 'smtp.gmail.com',
    smtp_port INT DEFAULT 587,
    smtp_security ENUM('tls', 'ssl', 'none') DEFAULT 'tls',
    smtp_username VARCHAR(255),
    smtp_password VARCHAR(255),
    from_email VARCHAR(255),
    from_name VARCHAR(255) DEFAULT 'Sistema Oncológico',
    notifications_enabled BOOLEAN DEFAULT 1,
    auto_send_enabled BOOLEAN DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT NOW()
);

-- Tabla para tipos de notificaciones
CREATE TABLE notification_types (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    template_subject VARCHAR(255),
    template_body TEXT,
    is_active BOOLEAN DEFAULT 1,
    send_to_patient BOOLEAN DEFAULT 1,
    send_to_medic BOOLEAN DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT NOW()
);

-- Tabla para log de notificaciones enviadas
CREATE TABLE notification_log (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    notification_type_id INT NOT NULL,
    recipient_email VARCHAR(255) NOT NULL,
    recipient_name VARCHAR(255),
    recipient_type ENUM('patient', 'medic', 'admin') NOT NULL,
    subject VARCHAR(255),
    body TEXT,
    status ENUM('pending', 'sent', 'failed', 'cancelled') DEFAULT 'pending',
    error_message TEXT,
    reference_id INT, -- ID de la reserva, waitlist, etc.
    reference_type ENUM('reservation', 'waitlist', 'patient', 'medic'),
    sent_at DATETIME,
    created_at DATETIME NOT NULL DEFAULT NOW(),
    FOREIGN KEY (notification_type_id) REFERENCES notification_types(id)
);

-- Tabla para notificaciones programadas
CREATE TABLE notification_queue (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    notification_type_id INT NOT NULL,
    recipient_email VARCHAR(255) NOT NULL,
    recipient_name VARCHAR(255),
    recipient_type ENUM('patient', 'medic', 'admin') NOT NULL,
    subject VARCHAR(255),
    body TEXT,
    scheduled_at DATETIME NOT NULL,
    reference_id INT,
    reference_type ENUM('reservation', 'waitlist', 'patient', 'medic'),
    attempts INT DEFAULT 0,
    max_attempts INT DEFAULT 3,
    status ENUM('pending', 'processing', 'sent', 'failed', 'cancelled') DEFAULT 'pending',
    created_at DATETIME NOT NULL DEFAULT NOW(),
    FOREIGN KEY (notification_type_id) REFERENCES notification_types(id)
);

-- Insertar configuración inicial
INSERT INTO notification_config (
    smtp_host, smtp_port, smtp_security, 
    from_email, from_name, notifications_enabled
) VALUES (
    'smtp.gmail.com', 587, 'tls',
    'sistema@oncologia.cl', 'Sistema Oncológico', 1
);

-- Insertar tipos de notificaciones predefinidos
INSERT INTO notification_types (code, name, description, template_subject, template_body, send_to_patient, send_to_medic) VALUES
('appointment_scheduled', 'Cita Agendada', 'Notificación cuando se agenda una nueva cita', 
 'Cita Agendada - Sistema Oncológico', 
 '<h2>✅ Cita Agendada Exitosamente</h2><p>Su cita ha sido agendada para el {{date}} a las {{time}}.</p><p><strong>Médico:</strong> {{medic_name}}</p><p><strong>Tratamiento:</strong> {{treatment_type}}</p><p>Por favor llegue 15 minutos antes de su cita.</p>',
 1, 1),

('appointment_confirmed', 'Cita Confirmada', 'Notificación de confirmación de cita',
 'Confirmación de Cita - Sistema Oncológico',
 '<h2>✅ Cita Confirmada</h2><p>Su cita del {{date}} a las {{time}} ha sido confirmada.</p><p><strong>Médico:</strong> {{medic_name}}</p><p><strong>Sillón:</strong> {{chair_name}}</p>',
 1, 1),

('appointment_reminder', 'Recordatorio de Cita', 'Recordatorio 24 horas antes de la cita',
 'Recordatorio: Cita Mañana - Sistema Oncológico',
 '<h2>⏰ Recordatorio de Cita</h2><p>Le recordamos que tiene una cita mañana {{date}} a las {{time}}.</p><p><strong>Médico:</strong> {{medic_name}}</p><p><strong>Sillón:</strong> {{chair_name}}</p><p>Por favor llegue 15 minutos antes.</p>',
 1, 0),

('waitlist_added', 'Agregado a Lista de Espera', 'Notificación cuando se agrega a lista de espera',
 'Agregado a Lista de Espera - Sistema Oncológico',
 '<h2>📋 Agregado a Lista de Espera</h2><p>Ha sido agregado a la lista de espera para tratamiento oncológico.</p><p><strong>Tipo de Tratamiento:</strong> {{treatment_type}}</p><p><strong>Prioridad:</strong> {{priority_level}}</p><p>Le notificaremos cuando se asigne una cita.</p>',
 1, 1),

('waitlist_assigned', 'Cita Asignada desde Lista de Espera', 'Notificación cuando se asigna cita desde lista de espera',
 '🎉 Cita Asignada - Sistema Oncológico',
 '<h2>🎉 ¡Su Cita Ha Sido Asignada!</h2><p>Nos complace informarle que se ha asignado una cita desde la lista de espera.</p><p><strong>Fecha:</strong> {{date}}</p><p><strong>Hora:</strong> {{time}}</p><p><strong>Médico:</strong> {{medic_name}}</p><p><strong>Sillón:</strong> {{chair_name}}</p>',
 1, 1),

('appointment_cancelled', 'Cita Cancelada', 'Notificación de cancelación de cita',
 'Cita Cancelada - Sistema Oncológico',
 '<h2>❌ Cita Cancelada</h2><p>Su cita del {{date}} a las {{time}} ha sido cancelada.</p><p><strong>Motivo:</strong> {{reason}}</p><p>Contacte al centro para reagendar.</p>',
 1, 1),

('treatment_completed', 'Tratamiento Completado', 'Notificación cuando se completa un tratamiento',
 'Tratamiento Completado - Sistema Oncológico',
 '<h2>✅ Tratamiento Completado</h2><p>Su tratamiento ha sido completado exitosamente.</p><p><strong>Fecha:</strong> {{date}}</p><p><strong>Médico:</strong> {{medic_name}}</p><p>Siga las indicaciones médicas proporcionadas.</p>',
 1, 1),

('patient_registered', 'Paciente Registrado', 'Notificación de bienvenida para nuevos pacientes',
 'Bienvenido al Sistema Oncológico',
 '<h2>👋 ¡Bienvenido al Sistema Oncológico!</h2><p>Su registro ha sido completado exitosamente.</p><p><strong>Usuario:</strong> {{email}}</p><p>Ya puede acceder al sistema para ver sus citas y tratamientos.</p>',
 1, 0);

-- Índices para optimización
CREATE INDEX idx_notification_log_status ON notification_log(status);
CREATE INDEX idx_notification_log_type ON notification_log(notification_type_id);
CREATE INDEX idx_notification_queue_scheduled ON notification_queue(scheduled_at);
CREATE INDEX idx_notification_queue_status ON notification_queue(status);
