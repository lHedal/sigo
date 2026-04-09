<?php

// define('LBROOT',getcwd()); // LegoBox Root ... the server root
// include("core/controller/Database.php");
if(!isset($_SESSION["pacient_id"])) {
$user = $_POST['email'];  // Cambiado de username a email
$pass = sha1(md5($_POST['password']));
$empty = sha1(md5(""));

$base = new Database();
$con = $base->connect();
 $sql = "select * from pacient where (email= \"".$user."\") and password= \"".$pass."\" and (email!=\"\" and password!=\"$empty\")";
//print $sql;
$query = $con->query($sql);
$found = false;
$userid = null;
while($r = $query->fetch_array()){
	$found = true ;
	$userid = $r['id'];
}

if($found==true) {
//	session_start();
//	print $userid;
	$_SESSION['pacient_id']=$userid ;
//	setcookie('userid',$userid);
//	print $_SESSION['userid'];
	print "Cargando ... $user";
	print "<script>window.location='index.php?view=pacienthome';</script>";
}else {
	print "<script>window.location='index.php?view=pacientlogin';</script>";
}

}else{
	print "<script>window.location='index.php?view=home';</script>";
	
}
?>