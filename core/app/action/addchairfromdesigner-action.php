<?php
// Acción para añadir sillón desde el diseñador visual
if(count($_POST) > 0){
    include "core/app/model/OncologyChairData.php";
    
    $response = array('success' => false, 'message' => '', 'chair_id' => null);
    
    try {
        // Validar datos requeridos
        if(empty($_POST["name"])) {
            throw new Exception("El nombre del sillón es requerido");
        }
        
        if(strlen($_POST["name"]) < 3) {
            throw new Exception("El nombre debe tener al menos 3 caracteres");
        }
        
        // Crear nuevo sillón
        $chair = new OncologyChairData();
        $name = trim($_POST["name"]);
        $description = trim($_POST["description"]) ?: "Sillón creado desde diseñador visual";
        
        $chair->name = $name;
        $chair->description = $description;
        $chair->is_active = 1;
        
        // Añadir a la base de datos
        $result = $chair->add();
        
        if($result) {
            // Obtener el ID del sillón recién creado
            $new_chair_id = $result[1]; // El ID se obtiene del resultado de la inserción
            
            $response['success'] = true;
            $response['message'] = "Sillón '{$name}' creado exitosamente";
            $response['chair_id'] = $new_chair_id;
            $response['chair'] = array(
                'id' => $new_chair_id,
                'name' => $name,
                'description' => $description,
                'is_active' => 1
            );
        } else {
            throw new Exception("Error al crear el sillón en la base de datos");
        }
        
    } catch (Exception $e) {
        $response['success'] = false;
        $response['message'] = $e->getMessage();
    }
    
    // Enviar respuesta JSON
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Si no es POST, redireccionar
Core::redir("index.php?view=sillonlayout");
?>
