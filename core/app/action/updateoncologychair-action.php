<?php
if(count($_POST)>0){
	$chair = OncologyChairData::getById($_POST["id"]);
	$chair->name = $_POST["name"];
	$chair->description = $_POST["description"];
	$chair->is_active = $_POST["is_active"];
	$chair->update();

	Core::alert("Sillón actualizado exitosamente!");
	Core::redir("./?view=oncologychairs");
}
?>
