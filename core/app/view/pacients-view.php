<?php if(!Core::$user->view_pacients){ Core::redir("./?view=home"); } ?>
<section class="content">
<div class="row">
	<div class="col-md-12">
		<h1>Pacientes</h1>
	<a href="index.php?view=newpacient" class="btn btn-default"><i class='fa fa-male'></i> Nuevo Paciente</a>
<br>
<br>
		<?php

		$users = PacientData::getAll();
		if(count($users)>0){
			// si hay usuarios
			?>
			<div class="box box-primary">
			<div class="box-body">			<table class="table table-bordered table-hover pacients-table">
			<thead>
			<th></th>
			<th>DNI.</th>
			<th><i class="fa fa-picture"></i></th>
			<th>Nombre completo</th>
			<th>Direccion</th>
			<th>Email</th>
			<th>Telefono</th>
			<th></th>
			</thead>
			<?php
			foreach($users as $user){
				?>
				<tr>
				<td style="width:30px;"><a href="./?view=pacient&id=<?php echo $user->id; ?>" class="btn btn-default btn-xs"><i class="fa fa-folder-open"></i></a></td>
				<td><?php echo $user->no;?></td>
				<td style="width:80px;">
				<?php if($user->image!=""):?>
	<img src="storage/<?php echo $user->image; ?>" class="img-responsive">
				<?php endif; ?>

				</td>
				<td><p><?php echo $user->name." ".$user->lastname; ?></p>

				</td>
				<td><?php echo $user->address; ?></td>
				<td><?php echo $user->email; ?></td>
				<td><?php echo $user->phone; ?></td>
				<td style="width:270px;">
				<a href="index.php?view=pacienthistory&id=<?php echo $user->id;?>" class="btn btn-default btn-xs">Historial</a>
					<?php if(Core::$user->edit_pacients): ?>

				<a href="index.php?view=editpacient&id=<?php echo $user->id;?>" class="btn btn-warning btn-xs">Editar</a>
				<a href="index.php?view=delpacient&id=<?php echo $user->id;?>" class="btn btn-danger btn-xs">Eliminar</a>
			<?php endif; ?>
				</td>
				</tr>
				<?php

			}
			?>
			</table>
			</div>
			</div>
			<?php



		}else{
			echo "<p class='alert alert-danger'>No hay pacientes</p>";
		}


		?>


	</div>
</div>
</section>

<script>
$(document).ready(function(){
    // Initialize DataTable with unique class to avoid conflicts
    $('.pacients-table').DataTable({
        "pageLength": 25,
        "language": {
            "lengthMenu": "Mostrar _MENU_ registros por página",
            "zeroRecords": "No se encontraron registros",
            "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
            "infoFiltered": "(filtrado de un total de _MAX_ registros)",
            "search": "Buscar:",
            "loadingRecords": "Cargando...",
            "processing": "Procesando...",
            "emptyTable": "Ningún dato disponible en esta tabla",
            "paginate": {
                "first": "Primero",
                "last": "Último",
                "next": "Siguiente",
                "previous": "Anterior"
            },
            "aria": {
                "sortAscending": ": Activar para ordenar la columna de manera ascendente",
                "sortDescending": ": Activar para ordenar la columna de manera descendente"
            }
        }
    });
});
</script>