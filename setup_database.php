<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Configuracion Automatica de Base de Datos - Sistema de Oncologia</h1>";

$host = "localhost";
$user = "root";
$pass = "";
$database = "oncology_db";

function runSchemaFile(mysqli $con, string $filePath, string $label): void {
    echo "<h3>Importando esquema: {$label}</h3>";

    if (!file_exists($filePath)) {
        echo "<p>Archivo no encontrado: {$filePath}</p>";
        return;
    }

    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $statement = "";
    $executed = 0;

    foreach ($lines as $line) {
        $trimmed = trim($line);
        if ($trimmed === "" || strpos($trimmed, "--") === 0) {
            continue;
        }

        $statement .= $line . "\n";

        if (strpos($trimmed, ";") !== false) {
            if ($con->query($statement)) {
                $executed++;
            } else {
                $error = $con->error;
                if (
                    strpos($error, "already exists") === false &&
                    strpos($error, "Duplicate column") === false &&
                    strpos($error, "Duplicate entry") === false
                ) {
                    echo "<p>Consulta omitida en {$label}: " . htmlspecialchars($error) . "</p>";
                }
            }
            $statement = "";
        }
    }

    echo "<p>Esquema {$label} procesado. Consultas ejecutadas: {$executed}</p>";
}

try {
    echo "<h2>Paso 1: Conectando a MySQL</h2>";
    $con = new mysqli($host, $user, $pass);

    if ($con->connect_error) {
        throw new Exception("Error de conexion: " . $con->connect_error);
    }
    echo "<p>Conexion a MySQL exitosa</p>";

    echo "<h2>Paso 2: Verificando/Creando base de datos</h2>";
    $sql_create_db = "CREATE DATABASE IF NOT EXISTS `$database` CHARACTER SET utf8 COLLATE utf8_general_ci";

    if ($con->query($sql_create_db)) {
        echo "<p>Base de datos '$database' verificada/creada</p>";
    } else {
        throw new Exception("Error creando base de datos: " . $con->error);
    }

    $con->select_db($database);
    echo "<p>Base de datos '$database' seleccionada</p>";

    echo "<h2>Paso 3: Creando estructura base compatible</h2>";

    $basicTables = [
        "CREATE TABLE IF NOT EXISTS `user` (
            `id` int NOT NULL AUTO_INCREMENT,
            `username` varchar(50) DEFAULT NULL,
            `name` varchar(50) DEFAULT NULL,
            `lastname` varchar(50) DEFAULT NULL,
            `email` varchar(255) DEFAULT NULL,
            `password` varchar(60) DEFAULT NULL,
            `is_active` tinyint(1) NOT NULL DEFAULT 1,
            `is_admin` tinyint(1) NOT NULL DEFAULT 0,
            `kind` int NOT NULL DEFAULT 1,
            `view_reports` tinyint(1) NOT NULL DEFAULT 1,
            `view_users` tinyint(1) NOT NULL DEFAULT 1,
            `edit_users` tinyint(1) NOT NULL DEFAULT 1,
            `view_pacients` tinyint(1) NOT NULL DEFAULT 1,
            `edit_pacients` tinyint(1) NOT NULL DEFAULT 1,
            `view_medics` tinyint(1) NOT NULL DEFAULT 1,
            `edit_medics` tinyint(1) NOT NULL DEFAULT 1,
            `view_reservations` tinyint(1) NOT NULL DEFAULT 1,
            `edit_reservations` tinyint(1) NOT NULL DEFAULT 1,
            `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `uk_user_username` (`username`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;",

        "CREATE TABLE IF NOT EXISTS `category` (
            `id` int NOT NULL AUTO_INCREMENT,
            `name` varchar(200) NOT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;",

        "CREATE TABLE IF NOT EXISTS `medic` (
            `id` int NOT NULL AUTO_INCREMENT,
            `image` varchar(50) DEFAULT NULL,
            `no` varchar(50) DEFAULT NULL,
            `name` varchar(50) NOT NULL,
            `lastname` varchar(50) NOT NULL,
            `username` varchar(50) DEFAULT NULL,
            `gender` varchar(1) DEFAULT NULL,
            `day_of_birth` date DEFAULT NULL,
            `email` varchar(255) DEFAULT NULL,
            `address` varchar(255) DEFAULT NULL,
            `phone` varchar(255) DEFAULT NULL,
            `is_active` tinyint(1) NOT NULL DEFAULT 1,
            `password` varchar(60) DEFAULT NULL,
            `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
            `category_id` int DEFAULT NULL,
            `time1_data` text,
            `time2_data` text,
            `time3_data` text,
            `time4_data` text,
            `time5_data` text,
            `time6_data` text,
            `time7_data` text,
            `duration` int DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `idx_medic_category` (`category_id`),
            CONSTRAINT `fk_medic_category` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;",

        "CREATE TABLE IF NOT EXISTS `pacient` (
            `id` int NOT NULL AUTO_INCREMENT,
            `no` varchar(50) DEFAULT NULL,
            `image` varchar(50) DEFAULT NULL,
            `name` varchar(50) NOT NULL,
            `lastname` varchar(50) NOT NULL,
            `gender` varchar(1) DEFAULT NULL,
            `day_of_birth` date DEFAULT NULL,
            `email` varchar(255) DEFAULT NULL,
            `address` varchar(255) DEFAULT NULL,
            `phone` varchar(255) DEFAULT NULL,
            `cp` varchar(255) DEFAULT NULL,
            `pob` varchar(255) DEFAULT NULL,
            `sick` varchar(500) DEFAULT NULL,
            `medicaments` varchar(500) DEFAULT NULL,
            `password` varchar(60) DEFAULT NULL,
            `alergy` varchar(500) DEFAULT NULL,
            `is_favorite` tinyint(1) NOT NULL DEFAULT 0,
            `is_active` tinyint(1) NOT NULL DEFAULT 1,
            `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;",

        "CREATE TABLE IF NOT EXISTS `status` (
            `id` int NOT NULL AUTO_INCREMENT,
            `name` varchar(100) DEFAULT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;",

        "CREATE TABLE IF NOT EXISTS `payment` (
            `id` int NOT NULL AUTO_INCREMENT,
            `name` varchar(100) DEFAULT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;",

        "CREATE TABLE IF NOT EXISTS `reservation` (
            `id` int NOT NULL AUTO_INCREMENT,
            `no` varchar(100) DEFAULT NULL,
            `title` varchar(100) DEFAULT NULL,
            `note` text,
            `message` text,
            `date_at` date DEFAULT NULL,
            `time_at` time DEFAULT NULL,
            `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
            `pacient_id` int DEFAULT NULL,
            `symtoms` text,
            `sick` text,
            `medicaments` text,
            `user_id` int DEFAULT NULL,
            `medic_id` int DEFAULT NULL,
            `duration` int DEFAULT NULL,
            `price` double DEFAULT NULL,
            `is_web` tinyint(1) NOT NULL DEFAULT 0,
            `payment_id` int NOT NULL DEFAULT 1,
            `status_id` int NOT NULL DEFAULT 1,
            PRIMARY KEY (`id`),
            KEY `idx_reservation_payment` (`payment_id`),
            KEY `idx_reservation_status` (`status_id`),
            KEY `idx_reservation_user` (`user_id`),
            KEY `idx_reservation_pacient` (`pacient_id`),
            KEY `idx_reservation_medic` (`medic_id`),
            CONSTRAINT `fk_reservation_payment` FOREIGN KEY (`payment_id`) REFERENCES `payment` (`id`),
            CONSTRAINT `fk_reservation_status` FOREIGN KEY (`status_id`) REFERENCES `status` (`id`),
            CONSTRAINT `fk_reservation_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
            CONSTRAINT `fk_reservation_pacient` FOREIGN KEY (`pacient_id`) REFERENCES `pacient` (`id`),
            CONSTRAINT `fk_reservation_medic` FOREIGN KEY (`medic_id`) REFERENCES `medic` (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;",

        "CREATE TABLE IF NOT EXISTS `medic_category` (
            `medic_id` int NOT NULL,
            `category_id` int NOT NULL,
            KEY `idx_medic_category_medic` (`medic_id`),
            KEY `idx_medic_category_category` (`category_id`),
            CONSTRAINT `fk_medic_category_medic` FOREIGN KEY (`medic_id`) REFERENCES `medic` (`id`) ON DELETE CASCADE,
            CONSTRAINT `fk_medic_category_category` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;",

        "CREATE TABLE IF NOT EXISTS `initial_assessment` (
            `id` int NOT NULL AUTO_INCREMENT,
            `pacient_id` int NOT NULL,
            `evaluating_medic_id` int NOT NULL,
            `evaluation_date` datetime NOT NULL,
            `created_by` int NOT NULL,
            `primary_diagnosis` varchar(500) NOT NULL,
            `tumor_stage` varchar(50) DEFAULT NULL,
            `date_of_diagnosis` date NOT NULL,
            `previous_treatments` text,
            `family_history` text,
            `ecog_performance_status` tinyint NOT NULL,
            `weight_loss` decimal(5,2) DEFAULT NULL,
            `current_symptoms` text,
            `pain_scale` tinyint DEFAULT 0,
            `symptoms_other` varchar(500) DEFAULT NULL,
            `support_system` text,
            `psychological_state` varchar(100) NOT NULL,
            `coping_mechanisms` text,
            `proposed_treatment` varchar(200) NOT NULL,
            `treatment_goals` varchar(100) DEFAULT NULL,
            `estimated_duration` varchar(200) DEFAULT NULL,
            `treatment_priority` tinyint NOT NULL,
            `treatment_notes` text,
            `consents` text,
            `patient_concerns` text,
            `next_appointment` date DEFAULT NULL,
            `follow_up_type` varchar(100) DEFAULT NULL,
            `pending_studies` text,
            `referrals` varchar(500) DEFAULT NULL,
            `medical_summary` text NOT NULL,
            `recommendations` text,
            `status` enum('draft','completed','reviewed','archived') NOT NULL DEFAULT 'draft',
            `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `idx_assessment_pacient` (`pacient_id`),
            KEY `idx_assessment_medic` (`evaluating_medic_id`),
            KEY `idx_assessment_created_by` (`created_by`),
            CONSTRAINT `fk_assessment_pacient` FOREIGN KEY (`pacient_id`) REFERENCES `pacient` (`id`) ON DELETE CASCADE,
            CONSTRAINT `fk_assessment_medic` FOREIGN KEY (`evaluating_medic_id`) REFERENCES `medic` (`id`) ON DELETE RESTRICT,
            CONSTRAINT `fk_assessment_user` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE RESTRICT
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
    ];

    foreach ($basicTables as $tableSql) {
        if ($con->query($tableSql)) {
            preg_match('/CREATE TABLE IF NOT EXISTS `([^`]+)`/', $tableSql, $matches);
            $tableName = isset($matches[1]) ? $matches[1] : "tabla";
            echo "<p>Tabla '{$tableName}' creada/verificada</p>";
        } else {
            echo "<p>Error creando estructura: " . htmlspecialchars($con->error) . "</p>";
        }
    }

    $seedStatements = [
        "INSERT IGNORE INTO category (id, name) VALUES (1, 'Oncologia')",
        "INSERT IGNORE INTO status (id, name) VALUES (1, 'Pendiente'), (2, 'Aplicada'), (3, 'No asistio'), (4, 'Cancelada')",
        "INSERT IGNORE INTO payment (id, name) VALUES (1, 'Pendiente'), (2, 'Pagado'), (3, 'Anulado')"
    ];

    foreach ($seedStatements as $seedSql) {
        $con->query($seedSql);
    }
    echo "<p>Datos base insertados/verificados</p>";

    echo "<h2>Paso 4: Importando esquemas funcionales</h2>";
    runSchemaFile($con, __DIR__ . '/oncology_schema.sql', 'oncologia');
    runSchemaFile($con, __DIR__ . '/notification_schema.sql', 'notificaciones');

    echo "<h2>Paso 5: Verificando usuario administrador</h2>";
    $adminCheck = $con->query("SELECT id FROM user WHERE username = 'admin' LIMIT 1");
    if ($adminCheck && $adminCheck->num_rows == 0) {
        $adminPassword = sha1(md5('admin'));
        $adminSql = "INSERT INTO user (
            name, lastname, username, email, password,
            is_active, is_admin, kind,
            view_reports, view_users, edit_users,
            view_pacients, edit_pacients,
            view_medics, edit_medics,
            view_reservations, edit_reservations
        ) VALUES (
            'Administrador', 'Sistema', 'admin', 'admin@oncologia.local', '{$adminPassword}',
            1, 1, 1,
            1, 1, 1,
            1, 1,
            1, 1,
            1, 1
        )";

        if ($con->query($adminSql)) {
            echo "<p>Usuario administrador creado (usuario: admin, contrasena: admin)</p>";
        } else {
            echo "<p>No se pudo crear admin: " . htmlspecialchars($con->error) . "</p>";
        }
    } else {
        echo "<p>Usuario administrador ya existe</p>";
    }

    echo "<h2>Paso 6: Resumen de tablas</h2>";
    $tablesResult = $con->query("SHOW TABLES");
    if ($tablesResult) {
        echo "<ul>";
        while ($table = $tablesResult->fetch_assoc()) {
            $tableName = array_values($table)[0];
            $countResult = $con->query("SELECT COUNT(*) AS count FROM `$tableName`");
            $count = ($countResult) ? $countResult->fetch_assoc()['count'] : '?';
            echo "<li><strong>{$tableName}</strong> ({$count} registros)</li>";
        }
        echo "</ul>";
    }

    $con->close();

    echo "<h2>Configuracion completada</h2>";
    echo "<p>La base de datos fue configurada correctamente.</p>";
    echo "<p>Credenciales por defecto:</p>";
    echo "<ul>";
    echo "<li>Usuario: admin</li>";
    echo "<li>Contrasena: admin</li>";
    echo "</ul>";
    echo "<p><a href='index.php'>Ir al sistema</a></p>";
} catch (Exception $e) {
    echo "<h2>Error en la configuracion</h2>";
    echo "<p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<ul>";
    echo "<li>Verificar que MySQL/MariaDB este ejecutandose</li>";
    echo "<li>Verificar credenciales de conexion (usuario: root, contrasena vacia)</li>";
    echo "<li>Verificar puerto 3306 disponible</li>";
    echo "<li>Reiniciar servicios de XAMPP</li>";
    echo "</ul>";
}
?>
