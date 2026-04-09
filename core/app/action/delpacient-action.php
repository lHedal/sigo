<?php
if(count($_GET)==1){
	$pacient = PacientData::getById($_GET["id"]);
	if($pacient != null) {
		$pacient->del();
		Core::alert("Paciente eliminado exitosamente!");
		Core::redir("./?view=pacients");
	} else {
		Core::alert("No se encontró el paciente especificado!");
		Core::redir("./?view=pacients");
	}
}
?>
