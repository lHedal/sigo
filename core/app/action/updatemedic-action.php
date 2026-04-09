<?php

if(count($_POST) > 0) {
    // Determinar si es actualización por admin o por el propio médico
    $is_self_update = isset($_SESSION["medic_id"]) && isset($_POST["medic_id"]) && $_SESSION["medic_id"] == $_POST["medic_id"];
    
    $medic_id = $is_self_update ? $_POST["medic_id"] : $_POST["id"];
    $medic = MedicData::getById($medic_id);
    
    if($medic) {
        // Actualizar campos básicos
        if(isset($_POST["no"])) $medic->no = $_POST["no"];
        $medic->name = $_POST["name"];
        $medic->lastname = $_POST["lastname"];
        if(isset($_POST["username"])) $medic->username = $_POST["username"];
        $medic->email = $_POST["email"];
        
        if(isset($_POST["phone"])) $medic->phone = $_POST["phone"];
        if(isset($_POST["address"])) $medic->address = $_POST["address"];
        if(isset($_POST["category_id"])) $medic->category_id = $_POST["category_id"];
        
        // Solo admin puede cambiar estado activo
        if(isset($_SESSION["user_id"]) && isset($_POST["is_active"])) {
            $medic->is_active = $_POST["is_active"];
        }
        
        // Manejo de contraseña
        if($is_self_update) {
            // Auto-actualización: verificar contraseña actual
            if(isset($_POST["new_password"]) && !empty($_POST["new_password"])) {
                $current_password = $_POST["current_password"];
                
                // Verificar contraseña actual
                if($medic->password == $current_password || password_verify($current_password, $medic->password)) {
                    // Verificar que las contraseñas nuevas coincidan
                    if($_POST["new_password"] == $_POST["confirm_password"]) {
                        if(strlen($_POST["new_password"]) >= 6) {
                            $medic->password = $_POST["new_password"]; // Guardar en texto plano por compatibilidad
                        } else {
                            Core::alert("La nueva contraseña debe tener al menos 6 caracteres!");
                            Core::redir("./?view=configuremedic");
                            exit;
                        }
                    } else {
                        Core::alert("Las contraseñas nuevas no coinciden!");
                        Core::redir("./?view=configuremedic");
                        exit;
                    }
                } else {
                    Core::alert("La contraseña actual es incorrecta!");
                    Core::redir("./?view=configuremedic");
                    exit;
                }
            }
        } else {
            // Actualización por admin
            if(isset($_POST["password"]) && $_POST["password"] != "") {
                $medic->password = $_POST["password"];
            }
        }
        
        $medic->update();
        
        Core::alert("Perfil actualizado exitosamente!");
        
        // Redireccionar según quien hizo la actualización
        if($is_self_update) {
            Core::redir("./?view=medichome");
        } else {
            Core::redir("./?view=medics");
        }
        
    } else {
        Core::alert("Error: No se encontró el médico!");
        Core::redir("./");
    }
    
} else {
    Core::alert("Error: No se recibieron datos!");
    Core::redir("./");
}

?>
