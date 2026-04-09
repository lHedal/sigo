<?php if(!Core::$user->view_users){ Core::redir("./?view=home"); } ?>
<?php $u = UserData::getById($_SESSION["user_id"]); ?>
<?php if($u->is_admin):?>
<section class="content">
<div class="row">
	<div class="col-md-12">
	<a href="index.php?view=newuser" class="btn btn-default pull-right"><i class='glyphicon glyphicon-user'></i> Nuevo Usuario</a>
		<h1>Lista de Usuarios</h1>
<br>
		<?php
		/*
		$u = new UserData();
		print_r($u);
		$u->name = "Guillermo";
		$u->lastname = "Espinosa";
		$u->email = "hedaallows@gmail.com";
		$u->password = sha1(md5("l00lapal00za"));
		$u->add();


		$f = $u->createForm();
		print_r($f);
		echo $f->label("name")." ".$f->render("name");
		*/
		?>
		<?php

		$users = UserData::getAll();
		if(count($users)>0){
			// si hay usuarios
			?>
			<div class="box box-primary">
			<div class="box-body">			<table class="table table-bordered table-hover users-table">
			<thead>
			<th>Nombre completo</th>
			<th>Nick</th>
			<th>Email</th>
			<th>Activo</th>
			<th>Admin</th>
			<th></th>
			</thead>
			<?php
			foreach($users as $user){
				?>
				<tr>
				<td><?php echo $user->name." ".$user->lastname; ?></td>
				<td><?php echo $user->email; ?></td>
				<td><?php echo $user->username; ?></td>
				<td>
					<?php if($user->is_active):?>
						<i class="glyphicon glyphicon-ok"></i>
					<?php endif; ?>
				</td>
				<td>
					<?php if($user->is_admin):?>
						<i class="glyphicon glyphicon-ok"></i>
					<?php endif; ?>
				</td>
				<td style="width:125px;">
					<?php if(Core::$user->edit_users): ?>
				<a href="index.php?view=edituser&id=<?php echo $user->id;?>" class="btn btn-warning btn-xs">Editar</a>
				<a href="index.php?action=deluser&id=<?php echo $user->id;?>" class="btn btn-danger btn-xs">Eliminar</a>
			<?php endif; ?>
				</td>
				</tr>
				<?php

			}
			echo "</table></div></div>";



		}else{
			// no hay usuarios
		}


		?>


	</div>
</div>
</section>
<?php endif;?>

<script>
$(document).ready(function(){
    // Initialize DataTable with unique class to avoid conflicts
    $('.users-table').DataTable({
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