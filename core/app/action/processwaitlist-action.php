<?php
include "core/app/model/OncologySchedulingService.php";

header('Content-Type: application/json');

$assigned_count = OncologySchedulingService::processWaitlist();

echo json_encode([
    'success' => true,
    'assigned_count' => $assigned_count
]);
?>
