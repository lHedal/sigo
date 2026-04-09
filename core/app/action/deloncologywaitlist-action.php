<?php
include "core/app/model/OncologyWaitlistData.php";

if(isset($_GET["id"])){
    OncologyWaitlistData::delById($_GET["id"]);
    print "<script>alert('Elemento eliminado de la lista de espera'); window.location='index.php?view=oncologywaitlist';</script>";
}
?>
