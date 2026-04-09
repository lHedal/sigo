<?php

if(count($_POST) > 0){
    $user = UserData::getById($_POST["user_id"]);
    
    if($user) {
        // Actualizar campos básicos
        $user->name = $_POST["name"];
        $user->lastname = $_POST["lastname"];
        $user->username = $_POST["username"];
        $user->email = $_POST["email"];
        
        // Solo actualizar la contraseña si se proporcionó una nueva
        if(isset($_POST["password"]) && !empty($_POST["password"])) {
            $user->password = $_POST["password"];
        }
        
        // Campos de estado y permisos
        $user->is_active = isset($_POST["is_active"]) ? 1 : 0;
        $user->is_admin = isset($_POST["is_admin"]) ? 1 : 0;
        
        // Permisos específicos
        $user->view_reports = isset($_POST["view_reports"]) ? 1 : 0;
        $user->view_users = isset($_POST["view_users"]) ? 1 : 0;
        $user->edit_users = isset($_POST["edit_users"]) ? 1 : 0;
        $user->view_pacients = isset($_POST["view_pacients"]) ? 1 : 0;
        $user->edit_pacients = isset($_POST["edit_pacients"]) ? 1 : 0;
        $user->view_medics = isset($_POST["view_medics"]) ? 1 : 0;
        $user->edit_medics = isset($_POST["edit_medics"]) ? 1 : 0;
        $user->view_reservations = isset($_POST["view_reservations"]) ? 1 : 0;
        $user->edit_reservations = isset($_POST["edit_reservations"]) ? 1 : 0;
        
        // Actualizar el usuario
        $user->update();
        
        Core::alert("Usuario actualizado exitosamente!");
        print "<script>window.location='index.php?view=users';</script>";
        
    } else {
        Core::alert("Error: No se encontró el usuario!");
        print "<script>window.location='index.php?view=users';</script>";
    }
    
} else {
    Core::alert("Error: No se recibieron datos!");
    print "<script>window.location='index.php?view=users';</script>";
}

?>