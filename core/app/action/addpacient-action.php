<?php
include_once 'core/app/model/NotificationService.php';

$pacient = new PacientData();
$pacient->no = $_POST["no"];
$pacient->name = $_POST["name"];
$pacient->lastname = $_POST["lastname"];
$pacient->gender = $_POST["gender"];
$pacient->day_of_birth = $_POST["day_of_birth"];
$pacient->email = $_POST["email"];
$pacient->password = sha1(md5($_POST["password"]));
$pacient->address = $_POST["address"];
$pacient->cp = $_POST["cp"];
$pacient->pob = $_POST["pob"];
$pacient->phone = $_POST["phone"];
$pacient->sick = $_POST["sick"];
$pacient->medicaments = $_POST["medicaments"];
$pacient->alergy = $_POST["alergy"];
$pacient->is_active = 1;

$patient_id = $pacient->add();

// 🚀 NUEVA FUNCIONALIDAD: Enviar notificación de bienvenida
if($patient_id && $_POST["email"]) {
    try {
        NotificationService::notifyPatientRegistered($patient_id);
    } catch (Exception $e) {
        error_log("Error sending patient registration notification: " . $e->getMessage());
    }
}

Core::alert("Paciente agregado exitosamente!");
Core::redir("./?view=pacients");
?>
