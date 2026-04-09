<?php
$chair = OncologyChairData::getById($_GET["id"]);
?>
<div class="row">
	<div class="col-md-12">
		<h1>Editar Sillón de Oncología</h1>
		<br>
		<form class="form-horizontal" method="post" id="editchair" action="index.php?action=updateoncologychair" role="form">

		<div class="form-group">
		<label for="inputEmail1" class="col-lg-2 control-label">Nombre*</label>
		<div class="col-md-6">
		<input type="text" name="name" value="<?php echo $chair->name; ?>" required class="form-control" placeholder="Nombre del sillón">
		</div>
		</div>

		<div class="form-group">
		<label for="inputEmail1" class="col-lg-2 control-label">Descripción</label>
		<div class="col-md-6">
		<textarea name="description" class="form-control" rows="4" placeholder="Descripción del sillón"><?php echo $chair->description; ?></textarea>
		</div>
		</div>

		<div class="form-group">
		<label for="inputEmail1" class="col-lg-2 control-label">Estado</label>
		<div class="col-md-6">
		<select name="is_active" class="form-control">
			<option value="1" <?php if($chair->is_active==1){ echo "selected"; } ?>>Activo</option>
			<option value="0" <?php if($chair->is_active==0){ echo "selected"; } ?>>Inactivo</option>
		</select>
		</div>
		</div>

		<div class="form-group">
		<div class="col-lg-offset-2 col-lg-10">
		<input type="hidden" name="id" value="<?php echo $chair->id; ?>">
		<button type="submit" class="btn btn-primary">Actualizar Sillón</button>
		<a href="index.php?view=oncologychairs" class="btn btn-default">Cancelar</a>
		</div>
		</div>

		</form>
	</div>
</div>

<script>
$("#editchair").submit(function(e) {
	if($("#name").val()=="") {
		alert("Debe ingresar un nombre");
		e.preventDefault();
	}
});
</script>
