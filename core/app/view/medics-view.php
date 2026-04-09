<?php if(!Core::$user->view_medics){ Core::redir("./?view=home"); } ?>
<section class="content">
<div class="row">
	<div class="col-md-12">
		<h1>Medicos</h1>
	<a href="index.php?view=newmedic" class="btn btn-default"><i class='fa fa-support'></i> Nuevo Medico</a>

<br>
<br>
		<?php

		$users = MedicData::getAll();
		if(count($users)>0){
			// si hay usuarios
			?>
			<div class="box box-primary">
			<div class="box-body">
			<table class="table table-bordered table-hover medics-table">
			<thead>
			<th>Ced</th>
			<th><i class="fa fa-picture-o"></i></th>
			<th>Nombre completo</th>
			<th>Direccion</th>
			<th>Email</th>
			<th>Telefono</th>
			<th>Area</th>
			<th></th>
			</thead>
			<?php
			foreach($users as $user){
				?>
				<tr>
				<td><?php echo $user->no;?></td>
<td style="width:80px;">
				<?php if($user->image!=""):?>
	<img src="storage/<?php echo $user->image; ?>" class="img-responsive">
				<?php endif; ?>

				</td>
				<td><?php echo $user->name." ".$user->lastname; ?></td>
				<td><?php echo $user->address; ?></td>
				<td><?php echo $user->email; ?></td>
				<td><?php echo $user->phone; ?></td>
				<td><?php if($user->category_id!=null){ echo $user->getCategory()->name; } ?></td>
				<td style="width:250px;">
				<a href="index.php?view=medichistory&id=<?php echo $user->id;?>" class="btn btn-default btn-xs">Historial</a>
					<?php if(Core::$user->edit_medics): ?>
				<a href="index.php?view=editmedic&id=<?php echo $user->id;?>" class="btn btn-warning btn-xs">Editar</a>
				<a href="index.php?view=delmedic&id=<?php echo $user->id;?>" class="btn btn-danger btn-xs">Eliminar</a>
<?php endif; ?>
				</td>
				</tr>
				<?php

			}
			?>			</table>
			</div>
			</div>

<script>
$(document).ready(function() {
    $('.medics-table').DataTable({
        "language": {
            "lengthMenu": "Mostrar _MENU_ registros por página",
            "zeroRecords": "No se encontraron registros",
            "info": "Mostrando página _PAGE_ de _PAGES_",
            "infoEmpty": "No hay registros disponibles",
            "infoFiltered": "(filtrado de _MAX_ registros totales)",
            "search": "Buscar:",
            "paginate": {
                "first": "Primero",
                "last": "Último",
                "next": "Siguiente",
                "previous": "Anterior"
            }
        }
    });
});
</script>

			<?php
		}else{
			echo "<p class='alert alert-danger'>No hay medicos</p>";
		}


		?>


	</div>
</div>
</section>