<?php
$medic = new MedicData();
$medic->no = $_POST["no"];
$medic->name = $_POST["name"];
$medic->lastname = $_POST["lastname"];
$medic->username = $_POST["username"];
$medic->email = $_POST["email"];
$medic->password = sha1(md5($_POST["password"]));
$medic->category_id = $_POST["category_id"];
$medic->is_active = 1;

$medic->add();

Core::alert("Médico agregado exitosamente!");
Core::redir("./?view=medics");
?>
