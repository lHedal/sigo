<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Metodo no permitido'
    ]);
    exit;
}

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
if ($id <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'ID invalido'
    ]);
    exit;
}

$con = Database::getCon();
$log_sql = "SELECT * FROM notification_log WHERE id=" . $id . " LIMIT 1";
$log_query = $con->query($log_sql);

if (!$log_query || $log_query->num_rows === 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Notificacion no encontrada'
    ]);
    exit;
}

$log = $log_query->fetch_assoc();

$notification_type_id = intval($log['notification_type_id']);
$recipient_email = $con->real_escape_string($log['recipient_email']);
$recipient_name = $con->real_escape_string($log['recipient_name']);
$recipient_type = $con->real_escape_string($log['recipient_type']);
$subject = $con->real_escape_string($log['subject']);
$body = $con->real_escape_string($log['body']);
$scheduled_at = date('Y-m-d H:i:s');
$reference_type = $con->real_escape_string($log['reference_type']);
$reference_id_value = is_numeric($log['reference_id']) ? intval($log['reference_id']) : 'NULL';

$insert_sql = "INSERT INTO notification_queue (notification_type_id, recipient_email, recipient_name, recipient_type, subject, body, scheduled_at, reference_id, reference_type, attempts, max_attempts, status, created_at) VALUES (" .
    $notification_type_id . ", '" . $recipient_email . "', '" . $recipient_name . "', '" . $recipient_type . "', '" . $subject . "', '" . $body . "', '" . $scheduled_at . "', " . $reference_id_value . ", '" . $reference_type . "', 0, 3, 'pending', NOW())";

$result = Executor::doit($insert_sql);
if ($result && isset($result[0]) && $result[0]) {
    Executor::doit("UPDATE notification_log SET status='pending', error_message=NULL WHERE id=" . $id);
    echo json_encode([
        'success' => true,
        'message' => 'Notificacion reprogramada para envio'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'No se pudo reprogramar la notificacion'
    ]);
}
?>