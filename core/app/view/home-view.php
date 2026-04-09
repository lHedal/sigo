<?php
/**
 * Home View - Redirect to Dashboard
 * Este archivo redirige cualquier intento de acceso a 'home' hacia el dashboard oncológico
 */

// Debug: Log that home view was accessed
error_log("HOME-VIEW: Accessed, redirecting to oncologydashboard");

// Verificar si hay sesión activa
session_start();
if(isset($_SESSION["user_id"]) || isset($_SESSION["medic_id"]) || isset($_SESSION["pacient_id"])) {
    // Usuario logueado, redirigir al dashboard
    echo "<script>window.location.href = './?view=oncologydashboard';</script>";
    echo "<p>Redirecting to dashboard... <a href='./?view=oncologydashboard'>Click here if not redirected automatically</a></p>";
} else {
    // Usuario no logueado, redirigir al login
    echo "<script>window.location.href = './?view=login';</script>";
    echo "<p>Redirecting to login... <a href='./?view=login'>Click here if not redirected automatically</a></p>";
}
exit;
?>
