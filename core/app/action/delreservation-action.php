<?php
if(isset($_SESSION["user_id"]) || isset($_SESSION["medic_id"])){
$user = ReservationData::getById($_GET["id"]);
$user->del();
if(isset($_SESSION["user_id"])){
print "<script>window.location='index.php?view=reservations';</script>";
}
else if(isset($_SESSION["medic_id"])){
print "<script>window.location='index.php?view=medicreservations';</script>";

}
}
?>