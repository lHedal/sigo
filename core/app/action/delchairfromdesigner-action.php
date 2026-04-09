<?php
// Acción para eliminar sillón desde el diseñador visual
if(isset($_GET['id']) && is_numeric($_GET['id'])){
    include "core/app/model/OncologyChairData.php";
    
    $response = array('success' => false, 'message' => '');
    
    try {
        $chair_id = intval($_GET['id']);
        
        // Verificar que el sillón existe
        $chair = OncologyChairData::getById($chair_id);
        if(!$chair) {
            throw new Exception("El sillón no existe");
        }
        
        // Verificar si el sillón tiene reservas activas
        // (Opcional: puedes descomentar esto si quieres evitar eliminar sillones con reservas)
        /*
        $sql = "SELECT COUNT(*) as count FROM reservation WHERE chair_id = $chair_id AND status_id = 1";
        $result = Executor::doit($sql);
        $reservations = $result[0][0]['count'];
        
        if($reservations > 0) {
            throw new Exception("No se puede eliminar: el sillón tiene $reservations reservas activas");
        }
        */
        
        // Eliminar sillón
        OncologyChairData::delById($chair_id);
        
        $response['success'] = true;
        $response['message'] = "Sillón eliminado exitosamente";
        
    } catch (Exception $e) {
        $response['success'] = false;
        $response['message'] = $e->getMessage();
    }
    
    // Enviar respuesta JSON
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Si no hay ID, redireccionar
Core::redir("index.php?view=sillonlayout");
?>
