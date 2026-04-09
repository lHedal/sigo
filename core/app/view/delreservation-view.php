<?php
$reservation = ReservationData::getById($_GET["id"]);
$pacient = $reservation->getPacient();
$medic = $reservation->getMedic();
$chair = $reservation->getChair();
?>

<div class="row">
	<div class="col-md-12">
		<h1><i class="glyphicon glyphicon-trash"></i> Eliminar Cita</h1>
		<p>¿Está seguro que desea eliminar esta cita? Esta acción no se puede deshacer.</p>
		<br>
	</div>
</div>

<div class="row">
	<div class="col-md-8">
		
		<div class="panel panel-danger">
			<div class="panel-heading">
				<h3 class="panel-title">
					<i class="glyphicon glyphicon-warning-sign"></i> Confirmar Eliminación
				</h3>
			</div>
			<div class="panel-body">
				<div class="row">
					<div class="col-md-6">
						<h4>Detalles de la Cita</h4>
						<p><strong>ID:</strong> #<?php echo $reservation->id; ?></p>
						<p><strong>Tratamiento:</strong> <?php echo $reservation->title; ?></p>
						<p><strong>Fecha:</strong> <?php echo date("d/m/Y", strtotime($reservation->date_at)); ?></p>
						<p><strong>Hora:</strong> <?php echo date("H:i", strtotime($reservation->time_at)); ?></p>
						
						<?php if($reservation->note): ?>
						<p><strong>Notas:</strong> <?php echo $reservation->note; ?></p>
						<?php endif; ?>
					</div>
					
					<div class="col-md-6">
						<h4>Información del Tratamiento</h4>
						
						<?php if($pacient): ?>
						<p><strong>Paciente:</strong><br>
						<?php echo $pacient->name." ".$pacient->lastname; ?><br>
						<small><?php echo $pacient->no; ?></small></p>
						<?php endif; ?>
						
						<?php if($medic): ?>
						<p><strong>Médico:</strong><br>
						<?php echo $medic->name." ".$medic->lastname; ?><br>
						<small><?php echo $medic->no; ?></small></p>
						<?php endif; ?>
						
						<?php if($chair): ?>
						<p><strong>Sillón:</strong><br>
						<?php echo $chair->name; ?></p>
						<?php endif; ?>
					</div>
				</div>
				
				<hr>
				
				<div class="alert alert-danger">
					<strong><i class="glyphicon glyphicon-exclamation-sign"></i> Advertencia:</strong>
					Al eliminar esta cita se liberará el horario del médico y el sillón asignado. 
					Si el paciente necesita reprogramar, deberá crear una nueva cita.
				</div>
				
				<div class="text-center">
					<form method="post" action="index.php?action=delreservation" style="display: inline;">
						<input type="hidden" name="id" value="<?php echo $reservation->id; ?>">
						<button type="submit" class="btn btn-danger btn-lg">
							<i class="glyphicon glyphicon-trash"></i> Sí, Eliminar Cita
						</button>
					</form>
					
					<a href="index.php?view=reservations" class="btn btn-default btn-lg">
						<i class="glyphicon glyphicon-remove"></i> No, Cancelar
					</a>
				</div>
			</div>
		</div>

	</div>
	
	<div class="col-md-4">
		<div class="panel panel-info">
			<div class="panel-heading">
				<h3 class="panel-title">
					<i class="glyphicon glyphicon-info-sign"></i> Alternativas
				</h3>
			</div>
			<div class="panel-body">
				<p><strong>En lugar de eliminar, puede:</strong></p>
				<ul>
					<li><strong>Reprogramar:</strong> Cambiar fecha y hora</li>
					<li><strong>Marcar como cancelada:</strong> Mantener registro</li>
					<li><strong>Transferir:</strong> Cambiar médico o sillón</li>
				</ul>
				
				<hr>
				
				<p><strong>Acciones disponibles:</strong></p>
				<a href="index.php?view=editreservation&id=<?php echo $reservation->id; ?>" class="btn btn-warning btn-block">
					<i class="glyphicon glyphicon-pencil"></i> Editar en su lugar
				</a>
			</div>
		</div>
		
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">
					<i class="glyphicon glyphicon-time"></i> Historial
				</h3>
			</div>
			<div class="panel-body">
				<p><strong>Cita creada:</strong><br>
				<?php echo date("d/m/Y H:i", strtotime($reservation->created_at)); ?></p>
			</div>
		</div>
	</div>
</div>
