
<section class="content">
<div class="row">
	<div class="col-md-12">	<h1>Nuevo Paciente</h1>
	<br>
		<form class="form-horizontal" method="post" id="addpacient" enctype="multipart/form-data" action="index.php?action=addpacient" role="form">

  <div class="form-group">
    <label for="inputEmail1" class="col-lg-2 control-label">Imagen*</label>
    <div class="col-md-6">
      <input type="file" name="image">
    </div>
  </div>  <div class="form-group">
    <label for="no" class="col-lg-2 control-label">RUT*</label>
    <div class="col-md-6">
      <input type="text" name="no" required class="form-control" id="no" placeholder="12.345.678-9" maxlength="12">
      <p class="help-block">Ingrese el RUT con puntos y guión</p>
    </div>
  </div>  <div class="form-group">
    <label for="name" class="col-lg-2 control-label">Nombre*</label>
    <div class="col-md-6">
      <input type="text" name="name" required class="form-control" id="name" placeholder="Solo letras" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+" minlength="2">
    </div>
  </div>
  <div class="form-group">
    <label for="lastname" class="col-lg-2 control-label">Apellido*</label>
    <div class="col-md-6">
      <input type="text" name="lastname" required class="form-control" id="lastname" placeholder="Solo letras" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+" minlength="2">
    </div>
  </div>
  <div class="form-group">
    <label for="inputEmail1" class="col-lg-2 control-label">Genero*</label>
    <div class="col-md-6">
<label class="checkbox-inline">
  <input type="radio" id="inlineCheckbox1" name="gender" required value="h"> Hombre
</label>
<label class="checkbox-inline">
  <input type="radio" id="inlineCheckbox2" name="gender" required value="m"> Mujer
</label>

    </div>
  </div>
  <div class="form-group">
    <label for="day_of_birth" class="col-lg-2 control-label">Fecha de Nacimiento*</label>
    <div class="col-md-6">
      <input type="date" name="day_of_birth" required class="form-control" id="day_of_birth" max="<?php echo date('Y-m-d'); ?>">
    </div>
  </div>


  <div class="form-group">
    <label for="inputEmail1" class="col-lg-2 control-label">Direccion*</label>
    <div class="col-md-6">
      <input type="text" name="address" class="form-control"  id="address1" placeholder="Direccion">
    </div>
  </div>
  <div class="form-group">
    <label for="cp" class="col-lg-2 control-label">Código Postal*</label>
    <div class="col-md-6">
      <input type="text" name="cp" required class="form-control" id="cp" placeholder="Solo números" pattern="[0-9]+" maxlength="7">
    </div>
  </div>

  <div class="form-group">
    <label for="pob" class="col-lg-2 control-label">Comuna*</label>
    <div class="col-md-6">
      <input type="text" name="pob" required class="form-control" id="pob" placeholder="Nombre de la comuna">
    </div>
  </div>
  <div class="form-group">
    <label for="email" class="col-lg-2 control-label">Email*</label>
    <div class="col-md-6">
      <input type="email" name="email" required class="form-control" id="email" placeholder="ejemplo@correo.com">
      <p class="help-block">Si el email está vacío se inhabilita el acceso al paciente.</p>
    </div>
  </div>
  <div class="form-group">
    <label for="password" class="col-lg-2 control-label">Password*</label>
    <div class="col-md-6">
      <input type="password" name="password" required class="form-control" id="password" placeholder="Contraseña" minlength="6">
      <p class="help-block">Mínimo 6 caracteres. Si está vacío se inhabilita el acceso al paciente.</p>
    </div>
  </div>

  <div class="form-group">
    <label for="phone" class="col-lg-2 control-label">Teléfono*</label>
    <div class="col-md-6">
      <input type="tel" name="phone" required class="form-control" id="phone" placeholder="+56 9 1234 5678">
      <p class="help-block">Ingrese teléfono celular o fijo chileno</p>
    </div>
  </div>
  <div class="form-group">
    <label for="inputEmail1" class="col-lg-2 control-label">Enfermedad</label>
    <div class="col-md-6">
      <textarea name="sick" class="form-control" id="sick" placeholder="Enfermedad"></textarea>
    </div>
  </div>
  <div class="form-group">
    <label for="inputEmail1" class="col-lg-2 control-label">Medicamentos</label>
    <div class="col-md-6">
      <textarea name="medicaments" class="form-control" id="sick" placeholder="Medicamentos"></textarea>
    </div>
  </div>
  <div class="form-group">
    <label for="inputEmail1" class="col-lg-2 control-label">Alergia</label>
    <div class="col-md-6">
      <textarea name="alergy" class="form-control" id="sick" placeholder="Alergia"></textarea>
    </div>
  </div>
  <p class="alert alert-info">* Campos obligatorios</p>

  <div class="form-group">
    <div class="col-lg-offset-2 col-lg-10">
      <button type="submit" class="btn btn-primary">Agregar Paciente</button>
    </div>
  </div>
</form>
	</div>
</div>
</section>