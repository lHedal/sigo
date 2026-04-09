<section class="content">
<?php
$categories = CategoryData::getAll();
?>
<div class="row">
	<div class="col-md-12">	<h1>Nuevo Medico</h1>
	<br>
		<form class="form-horizontal" method="post" id="addmedic" enctype="multipart/form-data" action="index.php?action=addmedic" role="form">

  <div class="form-group">
    <label for="inputEmail1" class="col-lg-2 control-label">Imagen*</label>
    <div class="col-md-6">
      <input type="file" name="image">
    </div>
  </div>

  <div class="form-group">
    <label for="inputEmail1" class="col-lg-2 control-label">Area*</label>
    <div class="col-md-6">
    <select name="category_id" class="form-control">
    <option value="">-- SELECCIONE --</option>      
    <?php foreach($categories as $cat):?>
    <option value="<?php echo $cat->id; ?>"><?php echo $cat->name; ?></option>      
    <?php endforeach;?>
    </select>
    </div>
  </div>
  <div class="form-group">
    <label for="no" class="col-lg-2 control-label">RUT*</label>
    <div class="col-md-6">
      <input type="text" name="no" required class="form-control" id="no" placeholder="12.345.678-9" maxlength="12">
      <p class="help-block">Ingrese el RUT con puntos y guión</p>
    </div>
  </div>
    <div class="form-group">
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
    <label for="inputEmail1" class="col-lg-2 control-label">Direccion*</label>
    <div class="col-md-6">
      <input type="text" name="address" class="form-control"  id="address" placeholder="Direccion">
    </div>
  </div>
  <div class="form-group">
    <label for="inputEmail1" class="col-lg-2 control-label">Email*</label>
    <div class="col-md-6">
      <input type="text" name="email"  class="form-control" id="email" placeholder="Email">
    </div>
  </div>

  <div class="form-group">
    <label for="inputEmail1" class="col-lg-2 control-label">Telefono*</label>
    <div class="col-md-6">
      <input type="text" name="phone" class="form-control" id="phone" placeholder="Telefono">
    </div>
  </div>

  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-6">
      <div class="checkbox">
        <label>
          <input type="checkbox" name="is_active"> Activar acceso
        </label>
      </div>
    </div>
  </div>
  <div class="form-group">
    <label for="inputEmail1" class="col-lg-2 control-label">Password</label>
    <div class="col-md-6">
      <input type="password" name="password" class="form-control" id="phone" placeholder="Password">
    </div>
  </div>
<p class="text-info">La hora inicio y fin son las horas iniciales, las hora inicio 2 y fin 2 son horas considerando un descanso.</p>
  <div class="row">
    <div class="col-md-2"></div>
    <?php
$data = array(1=>"Lunes",2=>"Martes",3=>"Miercoles",4=>"Jueves",5=>"Viernes",6=>"Sabado",7=>"Domingo");

    ?>
<div class="col-md-7">
  <?php foreach($data as $k=>$v):?>
    <h3><?php echo $v; ?></h3>
  <div class="form-group">
    <div class="col-md-2">
    <label for="inputEmail1" class="control-label">Activar</label><br><br>
      <input type="checkbox" checked name="time_active_<?php echo $k; ?>" >
    </div>
        <div class="col-md-2">
    <label for="inputEmail1" class="control-label">Hora inicio (formato 24h)</label>
      <input type="time" name="time1_start_<?php echo $k; ?>"  class="form-control" id="time1_start" value="08:00" placeholder="Hora inicio">
    </div>
    <div class="col-md-2">
    <label for="inputEmail1" class="control-label">Hora fin (formato 24h)</label>
      <input type="time" name="time1_finish_<?php echo $k; ?>"  class="form-control" id="time1_finish" value="14:00" placeholder="Hora fin">
    </div>
    <div class="col-md-2">
    <label for="inputEmail1" class="control-label">Hora inicio 2 (formato 24h)</label>
      <input type="time" name="time2_start_<?php echo $k; ?>"  class="form-control" id="time2_start" value="14:30" placeholder="Hora inicio">
    </div>
    <div class="col-md-2">
    <label for="inputEmail1" class="control-label">Hora fin 2 (formato 24h)</label>
      <input type="time" name="time2_finish_<?php echo $k; ?>"  class="form-control" id="time2_finish" value="17:00" placeholder="Hora fin">
    </div>
    <div class="col-md-2">
    <label for="inputEmail1" class="control-label">Duracion de la cita (Minutos)</label>
      <input type="text" name="duration_<?php echo $k; ?>" required class="form-control" id="duration" value="15" placeholder="Duracion de la cita">
    </div>
  </div>
<?php endforeach; ?>
</div>
</div>

  <p class="alert alert-info">* Campos obligatorios</p>

  <div class="form-group">
    <div class="col-lg-offset-2 col-lg-10">
      <button type="submit" class="btn btn-primary">Agregar Medico</button>
    </div>
  </div>
</form>
	</div>
</div>
</section>