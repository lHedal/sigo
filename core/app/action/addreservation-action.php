<?php
include_once 'core/app/model/NotificationService.php';

// Verificar que los campos requeridos estén presentes
if(!isset($_POST["title"]) || !isset($_POST["pacient_id"]) || !isset($_POST["medic_id"]) || !isset($_POST["date_at"]) || !isset($_POST["time_at"])) {
    Core::alert("Error: Faltan campos requeridos!");
    Core::redir("./index.php?view=newreservation");
    exit;
}

// Verificar si ya existe una cita similar (mismo paciente, médico, fecha y hora)
$existing = ReservationData::getByPacientId($_POST["pacient_id"]);
$is_repeated = false;
foreach($existing as $ex) {
    if($ex->medic_id == $_POST["medic_id"] && $ex->date_at == $_POST["date_at"] && $ex->time_at == $_POST["time_at"]) {
        $is_repeated = true;
        break;
    }
}

if(!$is_repeated){
    $r = new ReservationData();
    $r->title = $_POST["title"];
    $r->note = isset($_POST["note"]) ? $_POST["note"] : "";
    $r->pacient_id = $_POST["pacient_id"];
    $r->medic_id = $_POST["medic_id"];
    $r->date_at = $_POST["date_at"];
    $r->time_at = $_POST["time_at"];
    $r->status_id = 1; // Programada
    
    // Agregar sillón si está presente
    if(isset($_POST["chair_id"]) && !empty($_POST["chair_id"])) {
        $r->chair_id = $_POST["chair_id"];
    }

    $reservation_id = $r->add();

    if($reservation_id) {
        // Enviar notificaciones si están configuradas
        try {
            if(class_exists('NotificationService')) {
                NotificationService::notifyAppointmentScheduled($reservation_id);
            }
        } catch (Exception $e) {
            error_log("Error sending appointment notification: " . $e->getMessage());
        }
        
        Core::alert("Cita programada exitosamente!");
    } else {
        Core::alert("Error al programar la cita!");
    }
} else {
    Core::alert("Error: Ya existe una cita con los mismos datos!");
}

Core::redir("./index.php?view=reservations");
?>