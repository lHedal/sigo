<?php
header('Content-Type: application/json');

$start = isset($_GET['start']) ? $_GET['start'] : date('Y-m-01');
$end = isset($_GET['end']) ? $_GET['end'] : date('Y-m-t');

$start_date = substr($start, 0, 10);
$end_date = substr($end, 0, 10);

$events = [];
$reservations = ReservationData::getByDateRange($start_date, $end_date);

foreach ($reservations as $reservation) {
    $patient = $reservation->getPacient();
    $patient_name = $patient ? ($patient->name . ' ' . $patient->lastname) : 'Paciente';

    $title = $reservation->title ? $reservation->title : 'Cita';
    $color = '#3c8dbc';

    if ((int)$reservation->status_id === 2) {
        $color = '#00a65a';
    } elseif ((int)$reservation->status_id === 3) {
        $color = '#f39c12';
    } elseif ((int)$reservation->status_id === 4) {
        $color = '#dd4b39';
    }

    $events[] = [
        'id' => (int)$reservation->id,
        'title' => $title . ' - ' . $patient_name,
        'start' => $reservation->date_at . 'T' . $reservation->time_at,
        'color' => $color,
        'url' => 'index.php?view=editreservation&id=' . $reservation->id
    ];
}

echo json_encode($events);
?>