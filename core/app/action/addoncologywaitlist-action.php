<?php
include "core/app/model/OncologyWaitlistData.php";
include "core/app/model/NotificationService.php";

if(count($_POST) > 0){
    $waitlist = new OncologyWaitlistData();
    $waitlist->pacient_id = $_POST["pacient_id"];
    $waitlist->treatment_type = $_POST["treatment_type"];
    $waitlist->priority_level = $_POST["priority_level"];
    $waitlist->requested_date = $_POST["requested_date"];
    $waitlist->requested_time = $_POST["requested_time"] ? $_POST["requested_time"] : null;
    $waitlist->duration_minutes = $_POST["duration_minutes"];
    $waitlist->notes = $_POST["notes"];
    $waitlist->status = "pending";
      $waitlist_result = $waitlist->add();
    
    // Enviar notificación de agregado a lista de espera
    if($waitlist_result && $waitlist_result[1] > 0) {
        $waitlist_id = $waitlist_result[1];
        NotificationService::notifyWaitlistAdded($waitlist_id);
    }
    
    print "<script>alert('Paciente agregado a la lista de espera exitosamente'); window.location='index.php?view=oncologywaitlist';</script>";
}
?>
