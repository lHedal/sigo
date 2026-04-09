<?php
include "core/app/model/OncologyChairData.php";

if(count($_POST) > 0){
    $chair = new OncologyChairData();
    $chair->name = $_POST["name"];
    $chair->description = $_POST["description"];
    $chair->is_active = isset($_POST["is_active"]) ? 1 : 0;
    
    $chair->add();
    
    print "<script>alert('Sillón agregado exitosamente'); window.location='index.php?view=oncologychairs';</script>";
}
?>
