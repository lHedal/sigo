<?php
include "core/app/model/OncologyWaitlistData.php";

if(count($_POST)>0){
	$item = OncologyWaitlistData::getById($_POST["id"]);
	$item->pacient_id = $_POST["pacient_id"];
	$item->treatment_type = $_POST["treatment_type"];
	$item->priority_level = $_POST["priority_level"];
	$item->requested_date = $_POST["requested_date"];
	$item->requested_time = $_POST["requested_time"];
	$item->duration_minutes = $_POST["duration_minutes"];
	$item->notes = $_POST["notes"];
	$item->status = $_POST["status"];
	$item->update();

	Core::alert("Item de lista de espera actualizado exitosamente!");
	Core::redir("./?view=oncologywaitlist");
}
?>
