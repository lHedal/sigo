<section class="content">
<?php $user = PacientData::getById($_GET["id"]);?>
<div class="row">
	<div class="col-md-12">
	<h1>Editar Paciente</h1>
	<br>
		<form class="form-horizontal" method="post" id="addproduct" enctype="multipart/form-data" action="index.php?action=updatepacient" role="form">



  <div class="form-group">
    <label for="inputEmail1" class="col-lg-2 control-label">Imagen*</label>
    <div class="col-md-6">

        <?php if($user->image!=""):?>
  <img src="storage/<?php echo $user->image; ?>" class="img-responsive">
        <br><?php endif; ?>

      <input type="file" name="image">
    </div>
  </div>
  <div class="form-group">
    <label for="inputEmail1" class="col-lg-2 control-label">DNI*</label>
    <div class="col-md-6">
      <input type="text" name="no" value="<?php echo $user->no;?>" class="form-control" id="no" placeholder="DNI">
    </div>
  </div>
  <div class="form-group">
    <label for="inputEmail1" class="col-lg-2 control-label">Nombre*</label>
    <div class="col-md-6">
      <input type="text" name="name" value="<?php echo $user->name;?>" class="form-control" id="name" placeholder="Nombre">
    </div>
  </div>
  <div class="form-group">
    <label for="inputEmail1" class="col-lg-2 control-label">Apellido*</label>
    <div class="col-md-6">
      <input type="text" name="lastname" value="<?php echo $user->lastname;?>" required class="form-control" id="lastname" placeholder="Apellido">
    </div>
  </div>
  <div class="form-group">
    <label for="inputEmail1" class="col-lg-2 control-label">Genero*</label>
    <div class="col-md-6">
<label class="checkbox-inline">
  <input type="radio" id="inlineCheckbox1" name="gender" required <?php if($user->gender=="h"){ echo "checked"; }?> value="h"> Hombre
</label>
<label class="checkbox-inline">
  <input type="radio" id="inlineCheckbox2" name="gender" required <?php if($user->gender=="m"){ echo "checked"; }?> value="m"> Mujer
</label>

    </div>
  </div>

  <div class="form-group">
    <label for="inputEmail1" class="col-lg-2 control-label">Fecha de Nacimiento</label>
    <div class="col-md-6">
      <input type="date" name="day_of_birth" class="form-control" value="<?php echo $user->day_of_birth; ?>"  id="address1" placeholder="Fecha de Nacimiento">
    </div>
  </div>

  <div class="form-group">
    <label for="inputEmail1" class="col-lg-2 control-label">Direccion*</label>
    <div class="col-md-6">
      <input type="text" name="address" value="<?php echo $user->address;?>" class="form-control" id="username" placeholder="Direccion">
    </div>
  </div>
  <div class="form-group">
    <label for="inputEmail1" class="col-lg-2 control-label">Codigo Postal*</label>
    <div class="col-md-6">
      <input type="text" name="cp" value="<?php echo $user->cp;?>" class="form-control" id="username" placeholder="Codigo Postal">
    </div>
  </div>
  <div class="form-group">
    <label for="inputEmail1" class="col-lg-2 control-label">Poblacion*</label>
    <div class="col-md-6">
      <input type="text" name="pob" value="<?php echo $user->pob;?>" class="form-control" id="username" placeholder="Poblacion">
    </div>
  </div>

  <div class="form-group">
    <label for="inputEmail1" class="col-lg-2 control-label">Email*</label>
    <div class="col-md-6">
      <input type="text" name="email" value="<?php echo $user->email;?>" class="form-control" id="email" placeholder="Email">
     <p class="help-block">Si el email esta vacio se inhabilita el acceso al paciente.</p>
    </div>
  </div>
  <div class="form-group">
    <label for="inputEmail1" class="col-lg-2 control-label">Password*</label>
    <div class="col-md-6">
      <input type="password" name="password" class="form-control" id="email1" placeholder="Password">
      <p class="help-block">Si el password esta vacio se inhabilita el acceso al paciente.</p>
      <?php if($user->email!=""&&$user->password!=sha1(md5(""))):?>
      <p class="help-block">Este usuario tiene activo el acceso al paciente.</p>
      <?php endif;?>
      <p class="help-block">Si escribe una nueva contrase&ntilde;a se modificara la anterior.</p>
    </div>
  </div>
  <div class="form-group">
    <label for="inputEmail1" class="col-lg-2 control-label">Telefono</label>
    <div class="col-md-6">
      <input type="text" name="phone"  value="<?php echo $user->phone;?>"  class="form-control" id="inputEmail1" placeholder="Telefono">
    </div>
  </div>
  <div class="form-group">
    <label for="inputEmail1" class="col-lg-2 control-label">Enfermedad</label>
    <div class="col-md-6">
      <textarea name="sick" class="form-control" id="sick" placeholder="Enfermedad"><?php echo $user->sick;?></textarea>
    </div>
  </div>
  <div class="form-group">
    <label for="inputEmail1" class="col-lg-2 control-label">Medicamentos</label>
    <div class="col-md-6">
      <textarea name="medicaments" class="form-control" id="sick" placeholder="Medicamentos"><?php echo $user->medicaments;?></textarea>
    </div>
  </div>
  <div class="form-group">
    <label for="inputEmail1" class="col-lg-2 control-label">Alergia</label>
    <div class="col-md-6">
      <textarea name="alergy" class="form-control" id="sick" placeholder="Alergia"><?php echo $user->alergy;?></textarea>
    </div>
  </div>

  <p class="alert alert-info">* Campos obligatorios</p>

  <div class="form-group">
    <div class="col-lg-offset-2 col-lg-10">
    <input type="hidden" name="user_id" value="<?php echo $user->id;?>">
      <button type="submit" class="btn btn-success">Actualizar Paciente</button>
    </div>
  </div>
</form>
	</div>
</div>
</section>