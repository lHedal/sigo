<?php
$reservation = ReservationData::getById($_GET["id"]);
$pacients = PacientData::getAll();
$medics = MedicData::getAll();
$chairs = OncologyChairData::getAll();
?>

<div class="row">
	<div class="col-md-12">
		<h1><i class="glyphicon glyphicon-pencil"></i> Editar Cita</h1>
		<p>Modifique los campos necesarios para actualizar la cita médica.</p>
		<br>
	</div>
</div>

<div class="row">
	<div class="col-md-8">
		
		<form class="form-horizontal" method="post" id="updatereservation" action="index.php?action=updatereservation" role="form">

			<div class="form-group">
				<label for="title" class="col-lg-2 control-label">Tratamiento*</label>
				<div class="col-md-10">
					<input type="text" name="title" class="form-control" id="title" value="<?php echo $reservation->title; ?>" placeholder="Ej: Quimioterapia - Ciclo 1" required>
				</div>
			</div>
			
			<div class="form-group">
				<label for="pacient_id" class="col-lg-2 control-label">Paciente*</label>
				<div class="col-md-10">
					<select name="pacient_id" class="form-control" id="pacient_id" required>
						<option value="">-- Seleccionar Paciente --</option>
						<?php foreach($pacients as $pacient):?>
						<option value="<?php echo $pacient->id; ?>" <?php if($pacient->id == $reservation->pacient_id) echo "selected"; ?>>
							<?php echo $pacient->name." ".$pacient->lastname; ?> (<?php echo $pacient->no; ?>)
						</option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>

			<div class="form-group">
				<label for="medic_id" class="col-lg-2 control-label">Médico*</label>
				<div class="col-md-10">
					<select name="medic_id" class="form-control" id="medic_id" required>
						<option value="">-- Seleccionar Médico --</option>
						<?php foreach($medics as $medic):?>
						<option value="<?php echo $medic->id; ?>" <?php if($medic->id == $reservation->medic_id) echo "selected"; ?>>
							<?php echo $medic->name." ".$medic->lastname; ?> (<?php echo $medic->no; ?>)
						</option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>

			<div class="form-group">
				<label for="chair_id" class="col-lg-2 control-label">Sillón</label>
				<div class="col-md-10">
					<div class="row">
						<div class="col-md-6">
							<select name="chair_id" class="form-control" id="chair_id">
								<option value="">-- Seleccionar Sillón (Opcional) --</option>
								<?php foreach($chairs as $chair):?>
								<option value="<?php echo $chair->id; ?>" <?php if($chair->id == $reservation->chair_id) echo "selected"; ?>>
									<?php echo $chair->name; ?>
								</option>
								<?php endforeach; ?>
							</select>
						</div>
						<div class="col-md-6">
							<button type="button" class="btn btn-info btn-block" onclick="abrirSelectorVisualEdit()">
								<i class="glyphicon glyphicon-map-marker"></i> Selector Visual de Sillones
							</button>
						</div>
					</div>
				</div>
			</div>

			<div class="form-group">
				<label for="date_at" class="col-lg-2 control-label">Fecha*</label>
				<div class="col-md-4">
					<input type="date" name="date_at" class="form-control" id="date_at" value="<?php echo $reservation->date_at; ?>" required>
				</div>
				<label for="time_at" class="col-lg-2 control-label">Hora*</label>
				<div class="col-md-4">
					<select name="time_at" class="form-control" id="time_at" required>
						<option value="">-- Seleccionar Hora --</option>
						<?php 
						for($h = 8; $h <= 17; $h++) {
							for($m = 0; $m < 60; $m += 15) {
								$time = sprintf("%02d:%02d:00", $h, $m);
								$display = sprintf("%02d:%02d", $h, $m);
								$selected = ($time == $reservation->time_at) ? "selected" : "";
								echo "<option value='$time' $selected>$display</option>";
							}
						}
						?>
					</select>
				</div>
			</div>

			<div class="form-group">
				<label for="status_id" class="col-lg-2 control-label">Estado*</label>
				<div class="col-md-10">
					<select name="status_id" class="form-control" id="status_id" required>
						<option value="1" <?php if($reservation->status_id == 1) echo "selected"; ?>>Programada</option>
						<option value="2" <?php if($reservation->status_id == 2) echo "selected"; ?>>En Proceso</option>
						<option value="3" <?php if($reservation->status_id == 3) echo "selected"; ?>>Completada</option>
						<option value="4" <?php if($reservation->status_id == 4) echo "selected"; ?>>Cancelada</option>
					</select>
				</div>
			</div>

			<div class="form-group">
				<label for="note" class="col-lg-2 control-label">Notas</label>
				<div class="col-md-10">
					<textarea name="note" class="form-control" id="note" placeholder="Observaciones adicionales, instrucciones especiales, etc." rows="3"><?php echo $reservation->note; ?></textarea>
				</div>
			</div>

			<div class="form-group">
				<div class="col-lg-offset-2 col-lg-10">
					<input type="hidden" name="id" value="<?php echo $reservation->id; ?>">
					<button type="submit" class="btn btn-primary">
						<i class="glyphicon glyphicon-ok"></i> Actualizar Cita
					</button>
					<a href="index.php?view=reservations" class="btn btn-default">
						<i class="glyphicon glyphicon-remove"></i> Cancelar
					</a>
					<a href="index.php?view=delreservation&id=<?php echo $reservation->id; ?>" class="btn btn-danger" onclick="return confirm('¿Está seguro que desea eliminar esta cita?')">
						<i class="glyphicon glyphicon-trash"></i> Eliminar
					</a>
				</div>
			</div>

		</form>

	</div>
	
	<div class="col-md-4">
		<div class="panel panel-info">
			<div class="panel-heading">
				<h3 class="panel-title">
					<i class="glyphicon glyphicon-info-sign"></i> Información de la Cita
				</h3>
			</div>
			<div class="panel-body">
				<p><strong>ID de Cita:</strong> #<?php echo $reservation->id; ?></p>
				<p><strong>Creada:</strong> <?php echo date("d/m/Y H:i", strtotime($reservation->created_at)); ?></p>
				
				<?php 
				$pacient = $reservation->getPacient();
				$medic = $reservation->getMedic();
				$chair = $reservation->getChair();
				?>
				
				<hr>
				
				<?php if($pacient): ?>
				<p><strong>Paciente Actual:</strong><br>
				<a href="index.php?view=editpacient&id=<?php echo $pacient->id; ?>">
					<?php echo $pacient->name." ".$pacient->lastname; ?>
				</a><br>
				<small><?php echo $pacient->phone; ?></small></p>
				<?php endif; ?>
				
				<?php if($medic): ?>
				<p><strong>Médico Actual:</strong><br>
				<a href="index.php?view=editmedic&id=<?php echo $medic->id; ?>">
					<?php echo $medic->name." ".$medic->lastname; ?>
				</a><br>
				<small><?php echo $medic->no; ?></small></p>
				<?php endif; ?>
				
				<?php if($chair): ?>
				<p><strong>Sillón Asignado:</strong><br>
				<?php echo $chair->name; ?><br>
				<small><?php echo $chair->description; ?></small></p>
				<?php endif; ?>
			</div>
		</div>
		
		<div class="panel panel-warning">
			<div class="panel-heading">
				<h3 class="panel-title">
					<i class="glyphicon glyphicon-exclamation-sign"></i> Importante
				</h3>
			</div>
			<div class="panel-body">
				<p><strong>Al cambiar fecha/hora:</strong> Verificar disponibilidad del médico y sillón.</p>
				<p><strong>Estados:</strong></p>
				<ul>
					<li><span class="label label-primary">Programada</span> - Cita confirmada</li>
					<li><span class="label label-warning">En Proceso</span> - Paciente en tratamiento</li>
					<li><span class="label label-success">Completada</span> - Tratamiento finalizado</li>
					<li><span class="label label-danger">Cancelada</span> - Cita cancelada</li>
				</ul>
			</div>
		</div>
	</div>
</div>

<!-- Modal para Selector Visual de Sillones -->
<div class="modal fade" id="modal-selector-sillones-edit" tabindex="-1" role="dialog" aria-labelledby="modalSelectorEditLabel">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title" id="modalSelectorEditLabel">
					<i class="glyphicon glyphicon-map-marker"></i> Selector Visual de Sillones
				</h4>
			</div>
			<div class="modal-body">
				<!-- Panel de información de la selección -->
				<div class="alert alert-info">
					<strong>Fecha seleccionada:</strong> <span id="fecha-selector-edit">No seleccionada</span><br>
					<strong>Hora seleccionada:</strong> <span id="hora-selector-edit">No seleccionada</span>
				</div>
				
				<!-- Leyenda -->
				<div class="row">
					<div class="col-md-12">
						<div class="selector-legend">
							<div class="legend-item">
								<div class="sillon-mini-selector disponible"></div>
								<span>Disponible</span>
							</div>
							<div class="legend-item">
								<div class="sillon-mini-selector ocupado"></div>
								<span>Ocupado</span>
							</div>
							<div class="legend-item">
								<div class="sillon-mini-selector seleccionado"></div>
								<span>Seleccionado</span>
							</div>
							<div class="legend-item">
								<div class="sillon-mini-selector actual"></div>
								<span>Actual</span>
							</div>
						</div>
					</div>
				</div>
				
				<!-- Mapa de sillones -->
				<div id="selector-map-container-edit" class="selector-map-container">
					<!-- Estación central -->
					<div class="estacion-enfermeria-selector">
						<i class="glyphicon glyphicon-plus"></i>
						<div>Enfermería</div>
					</div>
					
					<!-- Grid de sillones -->
					<div id="sillones-selector-grid-edit" class="sillones-selector-grid">
						<?php 
						$contador = 0;
						foreach ($chairs as $chair): 
							$contador++;
							$fila = ceil($contador / 4);
							$columna = ($contador - 1) % 4 + 1;
							$esActual = ($chair->id == $reservation->chair_id);
						?>
						<div class="sillon-selector-item <?php echo $esActual ? 'actual' : 'disponible'; ?>" 
							 data-sillon-id="<?php echo $chair->id; ?>"
							 data-nombre="<?php echo htmlspecialchars($chair->name); ?>"
							 style="grid-row: <?php echo $fila; ?>; grid-column: <?php echo $columna; ?>;"
							 onclick="seleccionarSillonVisualEdit(<?php echo $chair->id; ?>, '<?php echo htmlspecialchars($chair->name); ?>')">
							
							<div class="sillon-visual-selector">
								<div class="sillon-back-selector"></div>
								<div class="sillon-seat-selector"></div>
							</div>
							
							<div class="sillon-info-selector">
								<div class="sillon-numero-selector"><?php echo $chair->id; ?></div>
								<div class="sillon-nombre-selector"><?php echo htmlspecialchars($chair->name); ?></div>
								<div class="sillon-estado-selector"><?php echo $esActual ? 'Actual' : 'Disponible'; ?></div>
							</div>
						</div>
						<?php endforeach; ?>
					</div>
				</div>
				
				<!-- Información del sillón seleccionado -->
				<div id="info-sillon-seleccionado-edit" class="alert alert-success" style="display: none;">
					<strong>Sillón seleccionado:</strong> <span id="nombre-sillon-seleccionado-edit"></span> (ID: <span id="id-sillon-seleccionado-edit"></span>)
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
				<button type="button" class="btn btn-primary" onclick="confirmarSeleccionSillonEdit()" id="btn-confirmar-sillon-edit" disabled>
					<i class="glyphicon glyphicon-ok"></i> Confirmar Selección
				</button>
			</div>
		</div>
	</div>
</div>

<!-- CSS para el selector visual -->
<style>
.selector-legend {
	display: flex;
	justify-content: center;
	gap: 20px;
	margin-bottom: 15px;
}

.legend-item {
	display: flex;
	align-items: center;
	gap: 5px;
}

.sillon-mini-selector {
	width: 20px;
	height: 20px;
	border-radius: 3px;
	border: 2px solid #333;
}

.sillon-mini-selector.disponible {
	background: #5cb85c;
}

.sillon-mini-selector.ocupado {
	background: #d9534f;
}

.sillon-mini-selector.seleccionado {
	background: #f0ad4e;
	box-shadow: 0 0 8px rgba(240, 173, 78, 0.6);
}

.sillon-mini-selector.actual {
	background: #337ab7;
}

.selector-map-container {
	min-height: 400px;
	background: linear-gradient(135deg, #f5f5f5 0%, #e8e8e8 100%);
	border: 2px solid #ddd;
	border-radius: 8px;
	padding: 20px;
	position: relative;
	margin: 15px 0;
}

.estacion-enfermeria-selector {
	position: absolute;
	top: 50%;
	left: 50%;
	transform: translate(-50%, -50%);
	background: #337ab7;
	color: white;
	padding: 15px;
	border-radius: 50%;
	text-align: center;
	min-width: 80px;
	min-height: 80px;
	display: flex;
	flex-direction: column;
	justify-content: center;
	align-items: center;
	box-shadow: 0 4px 8px rgba(0,0,0,0.2);
	z-index: 10;
}

.sillones-selector-grid {
	display: grid;
	grid-template-columns: repeat(4, 1fr);
	gap: 15px;
	max-width: 600px;
	margin: 0 auto;
	position: relative;
	z-index: 5;
}

.sillon-selector-item {
	position: relative;
	cursor: pointer;
	transition: all 0.3s ease;
	padding: 8px;
	border-radius: 6px;
	text-align: center;
	border: 2px solid transparent;
}

.sillon-selector-item:hover {
	transform: scale(1.05);
	box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.sillon-selector-item.seleccionado {
	border-color: #f0ad4e !important;
	box-shadow: 0 0 15px rgba(240, 173, 78, 0.5);
}

.sillon-visual-selector {
	position: relative;
	width: 50px;
	height: 60px;
	margin: 0 auto 8px;
}

.sillon-back-selector {
	position: absolute;
	top: 0;
	left: 8px;
	width: 34px;
	height: 38px;
	border-radius: 6px 6px 0 0;
	border: 2px solid #333;
}

.sillon-seat-selector {
	position: absolute;
	bottom: 12px;
	left: 4px;
	width: 42px;
	height: 25px;
	border-radius: 4px;
	border: 2px solid #333;
}

.sillon-selector-item.disponible .sillon-back-selector,
.sillon-selector-item.disponible .sillon-seat-selector {
	background: #5cb85c;
}

.sillon-selector-item.ocupado .sillon-back-selector,
.sillon-selector-item.ocupado .sillon-seat-selector {
	background: #d9534f;
}

.sillon-selector-item.seleccionado .sillon-back-selector,
.sillon-selector-item.seleccionado .sillon-seat-selector {
	background: #f0ad4e;
}

.sillon-selector-item.actual .sillon-back-selector,
.sillon-selector-item.actual .sillon-seat-selector {
	background: #337ab7;
}

.sillon-info-selector {
	font-size: 10px;
	line-height: 1.2;
}

.sillon-numero-selector {
	font-weight: bold;
	font-size: 12px;
	color: #333;
}

.sillon-nombre-selector {
	color: #666;
	margin: 2px 0;
}

.sillon-estado-selector {
	font-weight: bold;
	margin: 2px 0;
}
</style>

<script src="js/sillon-manager.js"></script>
<script>
$(document).ready(function(){
	// Aplicar Select2 para mejor búsqueda
	$('#pacient_id, #medic_id, #chair_id').select2({
		width: '100%'
	});
});

// Variables globales para el selector visual en edición
let sillonSeleccionadoVisualEdit = null;

function abrirSelectorVisualEdit() {
	// Verificar que se hayan seleccionado fecha y hora
	const fecha = $('#date_at').val();
	const hora = $('#time_at').val();
	
	if (!fecha || !hora) {
		alert('Por favor selecciona primero la fecha y hora de la cita.');
		return;
	}
	
	// Actualizar información en el modal
	$('#fecha-selector-edit').text(fecha);
	$('#hora-selector-edit').text(hora);
	
	// Verificar disponibilidad de sillones para la fecha/hora seleccionada
	verificarDisponibilidadSillonesEdit(fecha, hora);
	
	// Mostrar modal
	$('#modal-selector-sillones-edit').modal('show');
}

function verificarDisponibilidadSillonesEdit(fecha, hora) {
	// En producción, esto sería una llamada AJAX para verificar disponibilidad
	// Por ahora, simular algunos sillones ocupados aleatoriamente
	$('#sillones-selector-grid-edit .sillon-selector-item').each(function() {
		// No cambiar el estado del sillón actual
		if ($(this).hasClass('actual')) {
			return;
		}
		
		const ocupado = Math.random() < 0.3; // 30% probabilidad de estar ocupado
		
		if (ocupado) {
			$(this).removeClass('disponible').addClass('ocupado');
			$(this).find('.sillon-estado-selector').text('Ocupado');
			$(this).css('pointer-events', 'none');
		} else {
			$(this).removeClass('ocupado').addClass('disponible');
			$(this).find('.sillon-estado-selector').text('Disponible');
			$(this).css('pointer-events', 'auto');
		}
	});
}

function seleccionarSillonVisualEdit(sillonId, nombre) {
	// Remover selección anterior
	$('#sillones-selector-grid-edit .sillon-selector-item').removeClass('seleccionado');
	
	// Seleccionar nuevo sillón
	const sillonElement = $(`#sillones-selector-grid-edit .sillon-selector-item[data-sillon-id="${sillonId}"]`);
	sillonElement.addClass('seleccionado');
	
	// Guardar selección
	sillonSeleccionadoVisualEdit = {
		id: sillonId,
		nombre: nombre
	};
	
	// Mostrar información
	$('#nombre-sillon-seleccionado-edit').text(nombre);
	$('#id-sillon-seleccionado-edit').text(sillonId);
	$('#info-sillon-seleccionado-edit').show();
	
	// Habilitar botón de confirmación
	$('#btn-confirmar-sillon-edit').prop('disabled', false);
}

function confirmarSeleccionSillonEdit() {
	if (sillonSeleccionadoVisualEdit) {
		// Actualizar select tradicional
		$('#chair_id').val(sillonSeleccionadoVisualEdit.id).trigger('change');
		
		// Cerrar modal
		$('#modal-selector-sillones-edit').modal('hide');
		
		// Mostrar confirmación
		const mensaje = `Sillón "${sillonSeleccionadoVisualEdit.nombre}" seleccionado correctamente.`;
		
		// Crear notificación temporal
		const notification = $(`
			<div class="alert alert-success alert-dismissible" style="position: fixed; top: 20px; right: 20px; z-index: 9999; width: 300px;">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				<strong>¡Éxito!</strong> ${mensaje}
			</div>
		`);
		
		$('body').append(notification);
		
		// Auto-remover después de 3 segundos
		setTimeout(function() {
			notification.fadeOut(function() {
				$(this).remove();
			});
		}, 3000);
		
		// Limpiar selección
		sillonSeleccionadoVisualEdit = null;
	}
}

// Limpiar selección al cerrar modal
$('#modal-selector-sillones-edit').on('hidden.bs.modal', function () {
	$('#sillones-selector-grid-edit .sillon-selector-item').removeClass('seleccionado');
	$('#info-sillon-seleccionado-edit').hide();
	$('#btn-confirmar-sillon-edit').prop('disabled', true);
	sillonSeleccionadoVisualEdit = null;
});
</script>
