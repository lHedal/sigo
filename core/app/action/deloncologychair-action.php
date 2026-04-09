<?php
// Mostrar errores para depuración
error_reporting(E_ALL);
ini_set('display_errors', 1);

if(isset($_GET["id"]) && is_numeric($_GET["id"])){
	$item = OncologyChairData::getById($_GET["id"]);
	if($item != null) {
		$item->del();
		Core::alert("Sillón de oncología eliminado exitosamente!");
		Core::redir("./?view=oncologychairs");
	} else {
		Core::alert("No se encontró el sillón especificado!");
		Core::redir("./?view=oncologychairs");
	}
} else {
	Core::alert("ID de sillón inválido!");
	Core::redir("./?view=oncologychairs");
}
?>
