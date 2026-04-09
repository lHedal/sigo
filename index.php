<?php
/**
* Sistema de Gestión de Oncología
* Sistema especializado para la gestión de pacientes oncológicos
**/

define("ROOT", dirname(__FILE__));

$debug= false;
if($debug){
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
}

include "core/autoload.php";

ob_start();
session_start();
Core::$root="";

// si quieres que se muestre las consultas SQL debes decomentar la siguiente linea
// Core::$debug_sql = true;

// Redireccionar a dashboard de oncología por defecto si no hay vista especificada
if(!isset($_GET['view']) && !isset($_SESSION["user_id"]) && !isset($_SESSION["medic_id"]) && !isset($_SESSION["pacient_id"])){
    $_GET['view'] = 'login';
} elseif(!isset($_GET['view']) && (isset($_SESSION["user_id"]) || isset($_SESSION["medic_id"]) || isset($_SESSION["pacient_id"]))){
    $_GET['view'] = 'oncologydashboard';
}

$lb = new Lb();
$lb->start();

?>