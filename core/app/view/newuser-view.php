<section class="content">
<div class="row">
	<div class="col-md-12">
	<h1>Agregar Usuario</h1>
	<br>
		<form class="form-horizontal" method="post" id="addproduct" action="index.php?action=adduser" role="form">


  <div class="form-group">
    <label for="inputEmail1" class="col-lg-2 control-label">Nombre*</label>
    <div class="col-md-6">
      <input type="text" name="name" class="form-control" id="name" placeholder="Nombre">
    </div>
  </div>
  <div class="form-group">
    <label for="inputEmail1" class="col-lg-2 control-label">Apellido*</label>
    <div class="col-md-6">
      <input type="text" name="lastname" required class="form-control" id="lastname" placeholder="Apellido">
    </div>
  </div>
  <div class="form-group">
    <label for="inputEmail1" class="col-lg-2 control-label">Nombre de usuario*</label>
    <div class="col-md-6">
      <input type="text" name="username" class="form-control" required id="username" placeholder="Nombre de usuario">
    </div>
  </div>
  <div class="form-group">
    <label for="inputEmail1" class="col-lg-2 control-label">Email*</label>
    <div class="col-md-6">
      <input type="text" name="email" class="form-control" id="email" placeholder="Email">
    </div>
  </div>

  <div class="form-group">
    <label for="inputEmail1" class="col-lg-2 control-label">Contrase&ntilde;a</label>
    <div class="col-md-6">
      <input type="password" name="password" class="form-control" id="inputEmail1" placeholder="Contrase&ntilde;a">
    </div>
  </div>

  <div class="form-group">
    <label for="inputEmail1" class="col-lg-2 control-label">Es administrador</label>
    <div class="col-md-6">
<div class="checkbox">
    <label>
      <input type="checkbox" name="is_admin"> 
    </label>
  </div>
    </div>
  </div>


  <div class="form-group">
    <label for="inputEmail1" class="col-lg-2 control-label">Ver Reportes</label>
    <div class="col-md-6">
<div class="checkbox">
    <label>
      <input type="checkbox" name="view_reports"> 
    </label>
  </div>
    </div>
  </div>
  <div class="form-group">
    <label for="inputEmail1" class="col-lg-2 control-label">Ver Usuarios</label>
    <div class="col-md-1">
<div class="checkbox">
    <label>
      <input type="checkbox" name="view_users"> 
    </label>
  </div>
    </div>
    <label for="inputEmail1" class="col-lg-2 control-label">Editar y Eliminar Usuarios</label>
    <div class="col-md-1">
<div class="checkbox">
    <label>
      <input type="checkbox" name="edit_users"> 
    </label>
  </div>
    </div>

  </div>
  <div class="form-group">
    <label for="inputEmail1" class="col-lg-2 control-label">Ver Pacientes</label>
    <div class="col-md-1">
<div class="checkbox">
    <label>
      <input type="checkbox" name="view_pacients"> 
    </label>
  </div>
    </div>
    <label for="inputEmail1" class="col-lg-2 control-label">Editar y Eliminar Pacientes</label>
    <div class="col-md-1">
<div class="checkbox">
    <label>
      <input type="checkbox" name="edit_pacients"> 
    </label>
  </div>
    </div>

  </div>
  <div class="form-group">
    <label for="inputEmail1" class="col-lg-2 control-label">Ver Medicos</label>
    <div class="col-md-1">
<div class="checkbox">
    <label>
      <input type="checkbox" name="view_medics"> 
    </label>
  </div>
    </div>
    <label for="inputEmail1" class="col-lg-2 control-label">Editar y Eliminar Medicos</label>
    <div class="col-md-1">
<div class="checkbox">
    <label>
      <input type="checkbox" name="edit_medics"> 
    </label>
  </div>
    </div>

  </div>

    <div class="form-group">
    <label for="inputEmail1" class="col-lg-2 control-label">Ver Citas</label>
    <div class="col-md-1">
<div class="checkbox">
    <label>
      <input type="checkbox" name="view_reservations"> 
    </label>
  </div>
    </div>
    <label for="inputEmail1" class="col-lg-2 control-label">Editar y Eliminar Citas</label>
    <div class="col-md-1">
<div class="checkbox">
    <label>
      <input type="checkbox" name="edit_reservations"> 
    </label>
  </div>
    </div>

  </div>
  <p class="alert alert-info">* Campos obligatorios</p>

  <div class="form-group">
    <div class="col-lg-offset-2 col-lg-10">
      <button type="submit" class="btn btn-primary">Agregar Usuario</button>
    </div>
  </div>
</form>
	</div>
</div>
</section>