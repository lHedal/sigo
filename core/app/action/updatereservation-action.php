<?php

if(count($_POST) > 0){
	$reservation = ReservationData::getById($_POST["id"]);
	
	if($reservation) {
		$reservation->title = $_POST["title"];
		$reservation->pacient_id = $_POST["pacient_id"];
		$reservation->medic_id = $_POST["medic_id"];
		$reservation->date_at = $_POST["date_at"];
		$reservation->time_at = $_POST["time_at"];
		$reservation->note = isset($_POST["note"]) ? $_POST["note"] : "";
		$reservation->status_id = $_POST["status_id"];
		
		// Agregar sillón si está presente
		if(isset($_POST["chair_id"]) && !empty($_POST["chair_id"])) {
			$reservation->chair_id = $_POST["chair_id"];
		}

		$reservation->update();
		
		Core::alert("Cita actualizada exitosamente!");
	} else {
		Core::alert("Error: No se encontró la cita!");
	}

	// Redireccionar según el tipo de usuario
	if(isset($_SESSION["medic_id"])){
		print "<script>window.location='index.php?view=medicreservations';</script>";
	} else {
		print "<script>window.location='index.php?view=reservations';</script>";
	}
} else {
	Core::alert("Error: No se recibieron datos!");
	Core::redir("./index.php?view=reservations");
}

?>