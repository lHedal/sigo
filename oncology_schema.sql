-- Extensiones para el sistema de oncología
-- Tabla para gestionar los sillones/recursos de oncología

CREATE TABLE oncology_chair (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    is_active BOOLEAN NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT NOW()
);

-- Insertar sillones de ejemplo
INSERT INTO oncology_chair (name, description) VALUES 
('Sillón 1', 'Sillón de tratamiento oncológico 1'),
('Sillón 2', 'Sillón de tratamiento oncológico 2'),
('Sillón 3', 'Sillón de tratamiento oncológico 3'),
('Sillón 4', 'Sillón de tratamiento oncológico 4');

-- Tabla para la lista de espera de oncología
CREATE TABLE oncology_waitlist (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    pacient_id INT NOT NULL,
    treatment_type VARCHAR(255),
    priority_level INT DEFAULT 1, -- 1=baja, 2=media, 3=alta, 4=urgente
    requested_date DATE,
    requested_time TIME,
    duration_minutes INT DEFAULT 60,
    notes TEXT,
    status ENUM('pending', 'assigned', 'completed', 'cancelled') DEFAULT 'pending',
    created_at DATETIME NOT NULL DEFAULT NOW(),
    assigned_at DATETIME NULL,
    reservation_id INT NULL, -- FK cuando se asigne una cita
    FOREIGN KEY (pacient_id) REFERENCES pacient(id),
    FOREIGN KEY (reservation_id) REFERENCES reservation(id)
);

-- Modificar tabla de reservas para incluir sillón asignado
ALTER TABLE reservation ADD COLUMN chair_id INT NULL;
ALTER TABLE reservation ADD FOREIGN KEY (chair_id) REFERENCES oncology_chair(id);

-- Tabla para horarios de disponibilidad de sillones
CREATE TABLE chair_availability (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    chair_id INT NOT NULL,
    date_at DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    is_blocked BOOLEAN DEFAULT 0, -- para mantenimiento
    created_at DATETIME NOT NULL DEFAULT NOW(),
    FOREIGN KEY (chair_id) REFERENCES oncology_chair(id)
);

-- Insertar categoría de oncología si no existe
INSERT INTO category (name) VALUES ('Oncología');

-- Tabla para configuración de oncología
CREATE TABLE oncology_config (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    default_appointment_duration INT DEFAULT 60, -- minutos
    max_daily_appointments INT DEFAULT 8,
    auto_assign_enabled BOOLEAN DEFAULT 1,
    waitlist_enabled BOOLEAN DEFAULT 1
);

INSERT INTO oncology_config (default_appointment_duration, max_daily_appointments) VALUES (60, 8);
