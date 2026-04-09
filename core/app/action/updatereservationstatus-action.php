<?php
if (!isset($_SESSION['medic_id']) && !isset($_SESSION['user_id'])) {
    Core::redir('./?view=login');
    exit;
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$status = isset($_GET['status']) ? intval($_GET['status']) : 0;
$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'reservations';

if (!preg_match('/^[A-Za-z0-9_-]+$/', $redirect)) {
    $redirect = 'reservations';
}

if ($id <= 0 || $status <= 0) {
    Core::alert('Datos invalidos para actualizar estado de cita');
    Core::redir('./?view=' . $redirect);
    exit;
}

$reservation = ReservationData::getById($id);
if (!$reservation) {
    Core::alert('No se encontro la cita solicitada');
    Core::redir('./?view=' . $redirect);
    exit;
}

$reservation->status_id = $status;
$reservation->update();

Core::alert('Estado de la cita actualizado');
Core::redir('./?view=' . $redirect);
?>