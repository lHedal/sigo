<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>Sistema de Oncología | Panel de Administración</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- Bootstrap 3.3.4 -->
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- Font Awesome Icons -->
    <link href="font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <!-- Theme style -->
    <link href="dist/css/AdminLTE.min.css" rel="stylesheet" type="text/css" />
    <link href="dist/css/skins/skin-blue-light.min.css" rel="stylesheet" type="text/css" />
    <script src="plugins/jquery/jquery-2.1.4.min.js"></script>
<script src="plugins/morris/raphael-min.js"></script>
<script src="plugins/morris/morris.js"></script>
  <link rel="stylesheet" href="plugins/morris/morris.css">
  <link rel="stylesheet" href="plugins/morris/example.css">

    <link rel="stylesheet" href="plugins/datatables/dataTables.bootstrap.css">
<link href='plugins/fullcalendar/fullcalendar.min.css' rel='stylesheet' />
<link href='plugins/fullcalendar/scheduler.min.css' rel='stylesheet' />
<link href='plugins/fullcalendar/fullcalendar.print.css' rel='stylesheet' media='print' />
<script src='plugins/fullcalendar/moment.min.js'></script>
<script src='plugins/fullcalendar/fullcalendar.min.js'></script>
<script src='plugins/fullcalendar/scheduler.min.js'></script>
<!--  pickadate -->
<link rel="stylesheet" type="text/css" href="plugins/pickadate/themes/classic.css">
<link rel="stylesheet" type="text/css" href="plugins/pickadate/themes/classic.date.css">
<link rel="stylesheet" type="text/css" href="plugins/pickadate/themes/classic.time.css">
<script src='plugins/pickadate/picker.js'></script>
<script src='plugins/pickadate/picker.date.js'></script>
<script src='plugins/pickadate/picker.time.js'></script>
<script src="plugins/jspdf/jspdf.min.js"></script>
<script src="plugins/jspdf/jspdf.plugin.autotable.js"></script>

<link rel="stylesheet" type="text/css" href="plugins/select2/select2.min.css"/>
<script src='plugins/select2/select2.min.js'></script>
<script type="text/javascript">
$(document).ready(function(){
  // Aplicar select2 solo a selects que no tengan clases específicas
  $("select:not(.treatment-select):not(.priority-select):not(.time-select):not(.duration-select)").select2();
});
</script>
  </head>

  <body class="<?php if(isset($_SESSION["user_id"]) || isset($_SESSION["pacient_id"]) || isset($_SESSION["medic_id"]) ):?>  skin-blue-light sidebar-mini <?php else:?>login-page<?php endif; ?>" >
    <div class="wrapper">
      <!-- Main Header -->
      <?php if(isset($_SESSION["user_id"])|| isset($_SESSION["pacient_id"]) || isset($_SESSION["medic_id"])):?>
      <header class="main-header">        <!-- Logo -->
        <a href="./" class="logo">
          <!-- mini logo for sidebar mini 50x50 pixels -->
          <span class="logo-mini"><b>O</b>S</span>
          <!-- logo for regular state and mobile devices -->
          <span class="logo-lg"><b>ONCOLOGÍA</b>SISTEMA</span>
        </a>

        <!-- Header Navbar -->
        <nav class="navbar navbar-static-top" role="navigation">
          <!-- Sidebar toggle button-->
          <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
          </a>
          <!-- Navbar Right Menu -->
          <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">


              <!-- User Account Menu -->
              <li class="dropdown user user-menu">
                <!-- Menu Toggle Button -->
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                  <!-- The user image in the navbar-->
                  <!-- hidden-xs hides the username on small devices so only the image appears. -->
                  <span class=""><?php 
                  if(isset($_SESSION["user_id"])){ echo UserData::getById($_SESSION["user_id"])->name; }
                  else if(isset($_SESSION["pacient_id"])){ echo PacientData::getById($_SESSION["pacient_id"])->name." (Paciente)"; }
                  else if(isset($_SESSION["medic_id"])){ echo MedicData::getById($_SESSION["medic_id"])->name." (Medico)"; }

                  ?></span>
                  <span class="caret"></span>
                </a>
                <ul class="dropdown-menu">
                  <!-- The user image in the menu -->
                  
                  <!-- Menu Footer-->
                  <li class="user-footer">
                    <div class="pull-right">
                      <?php if(isset($_SESSION["medic_id"])):?>
                      <a href="./?view=configuremedic" class="btn btn-default btn-flat"><i class='fa fa-cog'></i> Configurar</a>
                    <?php endif; ?>
                      <a href="./logout.php" class="btn btn-default btn-flat">Salir</a>
                    </div>
                  </li>
                </ul>
              </li>
              <!-- Control Sidebar Toggle Button -->
            </ul>
          </div>
        </nav>
      </header>
      <!-- Left side column. contains the logo and sidebar -->
      <aside class="main-sidebar">

        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">
<!--
<div class="user-panel">
            <div class="pull-left image">
              <img src="1.jpg" class="img-circle" alt="User Image" />
            </div>
            <div class="pull-left info">
              <p>Alexander Pierce</p>

              <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
          </div>
          -->          <!-- Sidebar Menu -->
          <ul class="sidebar-menu">
            <?php if(isset($_SESSION["user_id"])):?>
              <?php $u = UserData::getById($_SESSION["user_id"]); ?>
            
            <!-- Dashboard Principal -->
            <li><a href="./index.php?view=oncologydashboard"><i class='fa fa-dashboard'></i> <span>Dashboard Oncología</span></a></li>
            
            <!-- Gestión de Pacientes -->
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-users"></i> <span>Gestión de Pacientes</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>                <ul class="treeview-menu">
                    <li><a href="./?view=pacients"><i class="fa fa-list"></i> Ver Pacientes</a></li>
                    <li><a href="./?view=newpacient"><i class="fa fa-plus"></i> Nuevo Paciente</a></li>
                    <li><a href="./?view=unifiedpatientregister"><i class="fa fa-user-plus"></i> Registro Unificado</a></li>
                </ul>
            </li>
            
            <!-- Evaluaciones Médicas -->
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-stethoscope"></i> <span>Evaluaciones Médicas</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="./?view=assessments"><i class="fa fa-list"></i> Ver Evaluaciones</a></li>
                    <li><a href="./?view=initialassessment"><i class="fa fa-plus"></i> Nueva Evaluación</a></li>
                    <li><a href="./?view=assessments&status=draft"><i class="fa fa-edit"></i> Borradores</a></li>
                    <li><a href="./?view=assessments&status=completed"><i class="fa fa-check"></i> Completadas</a></li>
                </ul>
            </li>
            
            <!-- Gestión de Médicos -->
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-user-md"></i> <span>Médicos Oncólogos</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>                <ul class="treeview-menu">
                    <li><a href="./?view=medics"><i class="fa fa-list"></i> Ver Médicos</a></li>
                    <li><a href="./?view=newmedic"><i class="fa fa-plus"></i> Nuevo Médico</a></li>
                </ul>
            </li>
              <!-- Sistema de Oncología -->
            <li class="treeview active">
                <a href="#">
                    <i class="fa fa-heart"></i> <span>Sistema Oncológico</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="./?view=oncologysystem"><i class="fa fa-info-circle"></i> Estado General</a></li>
                    <li><a href="./?view=oncologywaitlist"><i class="fa fa-clock-o"></i> Lista de Espera</a></li>
                    <li><a href="./?view=oncologychairs"><i class="fa fa-bed"></i> Gestión de Sillones</a></li>
                    <li><a href="./?view=sillonmap"><i class="fa fa-map-o"></i> Mapa Visual de Sillones</a></li>
                    <li><a href="./?view=sillonlayout"><i class="fa fa-cogs"></i> Configurar Layout</a></li>
                    <li><a href="./?view=chairschedule"><i class="fa fa-calendar-check-o"></i> Horarios de Sillones</a></li>
                    <li><a href="./?view=oncologycalendar"><i class="fa fa-calendar"></i> Calendario</a></li>
                </ul>
            </li>
            
            <!-- Sistema de Notificaciones -->
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-bell"></i> <span>Notificaciones</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="./?view=notifications"><i class="fa fa-list"></i> Historial</a></li>
                    <li><a href="./?view=notificationconfig"><i class="fa fa-cog"></i> Configuración SMTP</a></li>
                    <li><a href="./?view=notificationqueue"><i class="fa fa-clock-o"></i> Cola de Envíos</a></li>
                </ul>
            </li>
            
            <!-- Administración -->
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-cogs"></i> <span>Administración</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="./?view=users"><i class="fa fa-user"></i> Usuarios del Sistema</a></li>
                </ul>
            </li>

            <?php elseif(isset($_SESSION["medic_id"])):?>
            <!-- Menú para Médicos -->
            <?php $medic = MedicData::getById($_SESSION["medic_id"]); ?>
            
            <!-- Dashboard Médico -->
            <li><a href="./?view=medichome"><i class='fa fa-dashboard'></i> <span>Dashboard Médico</span></a></li>
            
            <!-- Mis Citas -->
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-calendar"></i> <span>Mis Citas</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="./?view=medicreservations"><i class="fa fa-list"></i> Todas mis Citas</a></li>
                    <li><a href="./?view=oncologycalendar"><i class="fa fa-calendar-o"></i> Calendario</a></li>
                </ul>
            </li>
            
            <!-- Pacientes -->
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-users"></i> <span>Mis Pacientes</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="./?view=pacients"><i class="fa fa-list"></i> Lista de Pacientes</a></li>
                    <li><a href="./?view=newpacient"><i class="fa fa-plus"></i> Nuevo Paciente</a></li>
                </ul>
            </li>
            
            <!-- Evaluaciones Médicas -->
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-stethoscope"></i> <span>Evaluaciones Médicas</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="./?view=assessments"><i class="fa fa-list"></i> Mis Evaluaciones</a></li>
                    <li><a href="./?view=initialassessment"><i class="fa fa-plus"></i> Nueva Evaluación</a></li>
                    <li><a href="./?view=assessments&status=draft"><i class="fa fa-edit"></i> Borradores</a></li>
                </ul>
            </li>
            
            <!-- Sistema Oncológico -->
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-heart"></i> <span>Sistema Oncológico</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="./?view=oncologywaitlist"><i class="fa fa-clock-o"></i> Lista de Espera</a></li>
                    <li><a href="./?view=oncologychairs"><i class="fa fa-bed"></i> Sillones Disponibles</a></li>
                </ul>
            </li>
            
            <?php endif; ?>

          </ul><!-- /.sidebar-menu -->
        </section>
        <!-- /.sidebar -->
      </aside>
    <?php endif;?>

      <!-- Content Wrapper. Contains page content -->
      <?php if(isset($_SESSION["user_id"])  || isset($_SESSION["pacient_id"]) || isset($_SESSION["medic_id"]) ):?>
      <div class="content-wrapper">
        <?php View::load("index");?>
      </div><!-- /.content-wrapper -->        <footer class="main-footer">
        <div class="pull-right hidden-xs">
          <b>Version</b> v2.0 - Oncología
        </div>
        <strong>Copyright &copy; 2025 Sistema de Gestión Oncológica</strong>
      </footer>
      <?php else:?>
        <?php if(isset($_GET["view"]) && $_GET["view"]=="pacientlogin"):?>
<div class="login-box">      <div class="login-logo">
        <h4>ACCESO AL PACIENTE</h4>
        <a href="./?view=pacientlogin"><b>SISTEMA</b> ONCOLÓGICO</a>
      </div><!-- /.login-logo -->
      <div class="login-box-body">
        <form action="./?action=processloginpacient" method="post">
          <div class="form-group has-feedback">
            <input type="text" name="username" required class="form-control" placeholder="Usuario"/>
            <span class="glyphicon glyphicon-user form-control-feedback"></span>
          </div>
          <div class="form-group has-feedback">
            <input type="password" name="password" required class="form-control" placeholder="Password"/>
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
          </div>
          <div class="row">

            <div class="col-xs-12">
              <button type="submit" class="btn btn-primary btn-block btn-flat">Acceder</button>
              <a href="./" class="btn btn-default btn-block">Regresar</a>
            </div><!-- /.col -->
          </div>
        </form>
      </div><!-- /.login-box-body -->
    </div><!-- /.login-box -->  
        <?php elseif(isset($_GET["view"]) && $_GET["view"]=="pacientregister"):?>
<div class="login-box">      <div class="login-logo">
        <h4>REGISTRO DE PACIENTES</h4>
        <a href="./?view=pacientlogin"><b>SISTEMA</b> ONCOLÓGICO</a>
      </div><!-- /.login-logo -->
      <div class="login-box-body">
        <form action="./?action=processregisterpacient" method="post">
          <div class="form-group has-feedback">
            <input type="text" name="no" required class="form-control" placeholder="Cedula/DNI"/>
            <span class="glyphicon glyphicon-th-list form-control-feedback"></span>
          </div>
          <div class="form-group has-feedback">
            <input type="text" name="name" required class="form-control" placeholder="Nombre"/>
            <span class="glyphicon glyphicon-user form-control-feedback"></span>
          </div>
          <div class="form-group has-feedback">
            <input type="text" name="lastname" required class="form-control" placeholder="Apellidos"/>
            <span class="glyphicon glyphicon-user form-control-feedback"></span>
          </div>
          <div class="form-group has-feedback">
            <input type="date" name="day_of_birth" required class="form-control" placeholder="Fecha de Nacimiento"/>
            <span class="glyphicon glyphicon-calendar form-control-feedback"></span>
          </div>
          <div class="form-group has-feedback">
            <input type="text" name="phone" required class="form-control" placeholder="Telefono"/>
            <span class="glyphicon glyphicon-phone form-control-feedback"></span>
          </div>
          <div class="form-group has-feedback">
            <input type="text" name="email" required class="form-control" placeholder="Email"/>
            <span class="glyphicon glyphicon-user form-control-feedback"></span>
          </div>
          <div class="form-group has-feedback">
            <input type="password" name="password" required class="form-control" placeholder="Password"/>
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
          </div>
          <div class="row">

            <div class="col-xs-12">
              <button type="submit" class="btn btn-primary btn-block btn-flat">Acceder</button>
              <a href="./" class="btn btn-default btn-block">Regresar</a>
            </div><!-- /.col -->
          </div>
        </form>
      </div><!-- /.login-box-body -->
    </div><!-- /.login-box -->  
            <?php elseif(isset($_GET["view"]) && $_GET["view"]=="mediclogin"):?>
<div class="login-box">
      <div class="login-logo">
        <h4>ACCESO MÉDICO</h4>
        <a href="./?view=mediclogin"><b>SISTEMA</b> ONCOLÓGICO</a>
      </div><!-- /.login-logo -->
      <div class="login-box-body">
        <form action="./?action=processloginmedic" method="post">
          <div class="form-group has-feedback">
            <input type="email" name="email" required class="form-control" placeholder="Email"/>
            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
          </div>
          <div class="form-group has-feedback">
            <input type="password" name="password" required class="form-control" placeholder="Contraseña"/>
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
          </div>
          <div class="row">
            <div class="col-xs-12">
              <button type="submit" class="btn btn-primary btn-block btn-flat">Acceder como Médico</button>
              <a href="./" class="btn btn-default btn-block">Regresar</a>
            </div><!-- /.col -->
          </div>
        </form>
        
        <hr>
        <div class="text-center">
          <h5><strong>Médicos de Prueba:</strong></h5>
          <p style="font-size: 12px;">
            <strong>Email:</strong> dr.garcia@oncologia.com<br>
            <strong>Password:</strong> password123
          </p>
        </div>
      </div><!-- /.login-box-body -->
    </div><!-- /.login-box -->  
        <?php else:?>
<div class="login-box">
      <div class="login-logo">
        <a href="./"><b>BOOKCLINICA</b>PRO</a>
      </div><!-- /.login-logo -->
      <div class="login-box-body">
        <form action="./?action=processlogin" method="post">
          <div class="form-group has-feedback">
            <input type="text" name="username" required class="form-control" placeholder="Usuario"/>
            <span class="glyphicon glyphicon-user form-control-feedback"></span>
          </div>
          <div class="form-group has-feedback">
            <input type="password" name="password" required class="form-control" placeholder="Password"/>
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
          </div>
          <div class="row">

            <div class="col-xs-12">
              <button type="submit" class="btn btn-primary btn-block btn-flat">Acceder</button>
              <a href="./?view=pacientlogin" class="btn btn-default btn-block">Login Paciente</a>
              <a href="./?view=mediclogin" class="btn btn-default btn-block">Login Medico</a>
              <a href="./?view=pacientregister" class="btn btn-default btn-block">Registro de Pacientes</a>
            </div><!-- /.col -->
          </div>
        </form>
      </div><!-- /.login-box-body -->
    </div><!-- /.login-box -->  
    <?php endif;?>

     <?php endif;?>


    </div><!-- ./wrapper -->    <!-- REQUIRED JS SCRIPTS -->

    <!-- jQuery 2.1.4 -->
    <script src="plugins/jquery/jquery-2.1.4.min.js" type="text/javascript"></script>
    <!-- Bootstrap 3.3.2 JS -->
    <script src="bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    <!-- AdminLTE App -->
    <script src="dist/js/app.min.js" type="text/javascript"></script>
    <script type="text/javascript">
      $(document).ready(function(){
        $(".pickadate").pickadate({format: 'yyyy-mm-dd',min: '<?php echo date('Y-m-d',time()-(24*60*60)); ?>'});
        $(".pickadate2").pickadate({format: 'yyyy-mm-dd'});
        $(".pickatime").pickatime({format: 'HH:i',interval: 10 });
      })
    </script>    <script src="plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="plugins/datatables/dataTables.bootstrap.min.js"></script>
    <script src="js/form-validations.js"></script>
    <!-- DataTables global initialization removed to prevent conflicts -->
    <!-- Each view now handles its own DataTable initialization with unique classes -->
    <!-- Optionally, you can add Slimscroll and FastClick plugins.
          Both of these plugins are recommended to enhance the
          user experience. Slimscroll is required when using the
          fixed layout. -->
  </body>
</html>