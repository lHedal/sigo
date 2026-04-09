<?php
/**
 * Index View - Dynamic View Loader
 * Carga una vista existente validando formato del parámetro.
 */

if (!isset($_SESSION["user_id"]) && !isset($_SESSION["medic_id"]) && !isset($_SESSION["pacient_id"])) {
    if (file_exists("core/app/view/login-view.php")) {
        include "core/app/view/login-view.php";
    } else {
        echo "<h1>Error: Login view not found</h1>";
    }
    return;
}

$view = isset($_GET['view']) ? $_GET['view'] : 'oncologydashboard';

// Solo permitir nombres simples de vista para evitar path traversal.
if (!preg_match('/^[A-Za-z0-9_-]+$/', $view)) {
    $view = 'oncologydashboard';
}

$view_file = "core/app/view/" . $view . "-view.php";

if (file_exists($view_file)) {
    include $view_file;
} else {
    include "core/app/view/oncologydashboard-view.php";
}
?>
