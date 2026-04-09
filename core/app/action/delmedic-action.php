<?php
if(count($_GET)==1){
	$medic = MedicData::getById($_GET["id"]);
	if($medic != null) {
		$medic->del();
		Core::alert("Médico eliminado exitosamente!");
		Core::redir("./?view=medics");
	} else {
		Core::alert("No se encontró el médico especificado!");
		Core::redir("./?view=medics");
	}
}
?>
