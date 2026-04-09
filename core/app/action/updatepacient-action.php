<?php
$pacient = PacientData::getById($_POST["id"]);
$pacient->name = $_POST["name"];
$pacient->lastname = $_POST["lastname"];
$pacient->gender = $_POST["gender"];
$pacient->born = $_POST["born"];
$pacient->email = $_POST["email"];
$pacient->address = $_POST["address"];
$pacient->phone = $_POST["phone"];
$pacient->sick = $_POST["sick"];
$pacient->medicaments = $_POST["medicaments"];
$pacient->alergy = $_POST["alergy"];
$pacient->is_active = $_POST["is_active"];

$pacient->update();

Core::alert("Paciente actualizado exitosamente!");
Core::redir("./?view=pacients");
?>
