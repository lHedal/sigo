<?php
$reservations = ReservationData::getAll();
$today = date('Y-m-d');
?>

<div class="row">
	<div class="col-md-12">
		<div class="btn-group pull-right">
			<a href="index.php?view=newreservation" class="btn btn-success">
				<i class="glyphicon glyphicon-plus"></i> Nueva Cita
			</a>
			<a href="index.php?view=oncologycalendar" class="btn btn-default">
				<i class="glyphicon glyphicon-calendar"></i> Vista Calendario
			</a>
		</div>
		<h1><i class="glyphicon glyphicon-calendar"></i> Gestión de Citas</h1>
		<div class="clearfix"></div>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		
		<?php if(count($reservations) > 0):?>

		<div class="panel panel-default">
			<div class="panel-heading">
				<div class="btn-group pull-right">
					<button class="btn btn-default btn-xs">
						<i class="glyphicon glyphicon-time"></i> Total: <?php echo count($reservations); ?> citas
					</button>
				</div>
				<h3 class="panel-title"><i class="glyphicon glyphicon-th"></i> Todas las Citas Programadas</h3>
			</div>

			<div class="table-responsive">
			<table class="table table-striped table-hover">
			<thead>
				<tr>
					<th>ID</th>
					<th>Fecha</th>
					<th>Hora</th>
					<th>Tratamiento</th>
					<th>Paciente</th>
					<th>Médico</th>
					<th>Sillón</th>
					<th>Estado</th>
					<th>Acciones</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($reservations as $reservation):?>
				
				<?php
				$pacient = $reservation->getPacient();
				$medic = $reservation->getMedic(); 
				$chair = $reservation->getChair();
				
				// Determinar estado
				$status_class = "default";
				$status_text = "Programada";
				switch($reservation->status_id) {
					case 1:
						$status_class = "primary";
						$status_text = "Programada";
						break;
					case 2:
						$status_class = "warning";
						$status_text = "En Proceso";
						break;
					case 3:
						$status_class = "success";
						$status_text = "Completada";
						break;
					case 4:
						$status_class = "danger";
						$status_text = "Cancelada";
						break;
				}

				// Verificar si es hoy
				$is_today = ($reservation->date_at == $today);
				$row_class = $is_today ? "warning" : "";
				?>
				
				<tr class="<?php echo $row_class; ?>">
					<td><?php echo $reservation->id; ?></td>
					<td>
						<?php 
						echo date("d/m/Y", strtotime($reservation->date_at)); 
						if($is_today) echo " <span class='label label-warning'>HOY</span>";
						?>
					</td>
					<td><?php echo date("H:i", strtotime($reservation->time_at)); ?></td>
					<td>
						<strong><?php echo $reservation->title; ?></strong>
						<?php if($reservation->note): ?>
						<br><small class="text-muted"><?php echo $reservation->note; ?></small>
						<?php endif; ?>
					</td>
					<td>
						<?php if($pacient): ?>
						<a href="index.php?view=editpacient&id=<?php echo $pacient->id; ?>">
							<?php echo $pacient->name." ".$pacient->lastname; ?>
						</a>
						<br><small class="text-muted"><?php echo $pacient->no; ?></small>
						<?php else: ?>
						<span class="text-danger">Sin asignar</span>
						<?php endif; ?>
					</td>
					<td>
						<?php if($medic): ?>
						<a href="index.php?view=editmedic&id=<?php echo $medic->id; ?>">
							<?php echo $medic->name." ".$medic->lastname; ?>
						</a>
						<br><small class="text-muted"><?php echo $medic->no; ?></small>
						<?php else: ?>
						<span class="text-danger">Sin asignar</span>
						<?php endif; ?>
					</td>
					<td>
						<?php if($chair): ?>
						<span class="label label-info"><?php echo $chair->name; ?></span>
						<?php else: ?>
						<span class="text-muted">No asignado</span>
						<?php endif; ?>
					</td>
					<td>
						<span class="label label-<?php echo $status_class; ?>"><?php echo $status_text; ?></span>
					</td>
					<td style="width:130px;">
						<div class="btn-group btn-group-xs">
							<a href="index.php?view=editreservation&id=<?php echo $reservation->id; ?>" class="btn btn-warning btn-xs" title="Editar">
								<i class="glyphicon glyphicon-pencil"></i>
							</a>
							<a href="index.php?view=delreservation&id=<?php echo $reservation->id; ?>" class="btn btn-danger btn-xs" title="Eliminar" onclick="return confirm('¿Está seguro que desea eliminar esta cita?')">
								<i class="glyphicon glyphicon-trash"></i>
							</a>
						</div>
					</td>
				</tr>
				
				<?php endforeach; ?>
			</tbody>
			</table>
			</div>
		</div>
		
		<?php else: ?>
			<div class="jumbotron">
				<h2>No hay citas programadas</h2>
				<p>No se han programado citas aún. ¡Programa la primera cita!</p>
				<a href="index.php?view=newreservation" class="btn btn-primary btn-lg">
					<i class="glyphicon glyphicon-plus"></i> Programar Primera Cita
				</a>
			</div>
		<?php endif; ?>

	</div>
</div>

<script>
$(document).ready(function(){
	$("table").DataTable({
		"language": {
			"sProcessing": "Procesando...",
			"sLengthMenu": "Mostrar _MENU_ registros",
			"sZeroRecords": "No se encontraron resultados",
			"sEmptyTable": "Ningún dato disponible en esta tabla",
			"sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
			"sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
			"sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
			"sSearch": "Buscar:",
			"oPaginate": {
				"sFirst": "Primero",
				"sLast": "Último",
				"sNext": "Siguiente",
				"sPrevious": "Anterior"
			}
		},
		"order": [[ 1, "asc" ], [ 2, "asc" ]],
		"pageLength": 25
	});
});
</script>
