<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Core::redir("./?view=notificationtypes");
    exit;
}

$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$description = isset($_POST['description']) ? trim($_POST['description']) : '';
$template_body = isset($_POST['template_body']) ? trim($_POST['template_body']) : '';
$template_legacy = isset($_POST['template']) ? trim($_POST['template']) : '';
$template_subject = isset($_POST['template_subject']) ? trim($_POST['template_subject']) : '';

if ($name === '') {
    Core::alert("Debe ingresar un nombre para el tipo de notificacion");
    Core::redir("./?view=notificationtypes");
    exit;
}

$base_code = isset($_POST['code']) ? trim($_POST['code']) : $name;
$code = strtolower($base_code);
$code = preg_replace('/[^a-z0-9]+/', '_', $code);
$code = trim($code, '_');
if ($code === '') {
    $code = 'notification_' . time();
}

$is_active = isset($_POST['is_active']) ? 1 : 0;
$send_to_patient = (isset($_POST['send_to_patient']) || isset($_POST['send_email'])) ? 1 : 0;
$send_to_medic = (isset($_POST['send_to_medic']) || isset($_POST['send_sms'])) ? 1 : 0;

$subject_value = $template_subject !== '' ? $template_subject : $name;
$body_value = $template_body !== '' ? $template_body : ($template_legacy !== '' ? $template_legacy : $description);

$con = Database::getCon();
$code_db = $con->real_escape_string($code);
$name_db = $con->real_escape_string($name);
$description_db = $con->real_escape_string($description);
$subject_db = $con->real_escape_string($subject_value);
$body_db = $con->real_escape_string($body_value);

$sql = "INSERT INTO notification_types (code, name, description, template_subject, template_body, is_active, send_to_patient, send_to_medic, created_at) VALUES (" .
    "'" . $code_db . "', '" . $name_db . "', '" . $description_db . "', '" . $subject_db . "', '" . $body_db . "', " . $is_active . ", " . $send_to_patient . ", " . $send_to_medic . ", NOW())";

$result = Executor::doit($sql);
if ($result && isset($result[0]) && $result[0]) {
    Core::alert("Tipo de notificacion creado exitosamente");
} else {
    Core::alert("No se pudo crear el tipo de notificacion. Verifique que el codigo no este repetido");
}

Core::redir("./?view=notificationtypes");
?>