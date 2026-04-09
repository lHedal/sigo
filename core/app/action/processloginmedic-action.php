<?php

// Procesamiento de login para médicos
if(!isset($_SESSION["medic_id"])) {
    $user = isset($_POST['email']) ? $_POST['email'] : $_POST['username'];
    $pass = $_POST['password'];

    $base = new Database();
    $con = $base->connect();
    
    // Verificar si el password está hasheado o no
    $sql = "select * from medic where email = '".$user."' and is_active = 1";
    $query = $con->query($sql);
    $found = false;
    $userid = null;
    $stored_password = null;
    
    while($r = $query->fetch_array()){
        $stored_password = $r['password'];
        // Verificar password (puede estar hasheado o en texto plano)
        if($stored_password == $pass || password_verify($pass, $stored_password)) {
            $found = true;
            $userid = $r['id'];
            break;
        }
    }

    if($found == true) {
        $_SESSION['medic_id'] = $userid;
        $_SESSION['medic_email'] = $user;
        
        // Redirect con mensaje de éxito
        echo "<div class='alert alert-success'>Login exitoso. Redirigiendo...</div>";
        echo "<script>
            setTimeout(function(){ 
                window.location='index.php?view=medichome'; 
            }, 1500);
        </script>";
    } else {
        // Redirect con error
        $_SESSION['login_error'] = "Email o contraseña incorrectos";
        echo "<script>window.location='index.php?view=mediclogin&error=1';</script>";
    }

} else {
    // Ya está logueado, redirigir al dashboard
    echo "<script>window.location='index.php?view=medichome';</script>";
}
?>