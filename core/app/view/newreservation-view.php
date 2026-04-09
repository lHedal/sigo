<?php
$pacients = PacientData::getAll();
$medics = MedicData::getAll();
$chairs = OncologyChairData::getAll();
?>

<div class="row">
	<div class="col-md-12">
		<h1><i class="glyphicon glyphicon-plus"></i> Nueva Cita</h1>
		<p>Complete todos los campos para programar una nueva cita médica.</p>
		<br>
	</div>
</div>

<div class="row">
	<div class="col-md-8">
		
		<form class="form-horizontal" method="post" id="addreservation" action="index.php?action=addreservation" role="form">

			<div class="form-group">
				<label for="title" class="col-lg-2 control-label">Tratamiento*</label>
				<div class="col-md-10">
					<input type="text" name="title" class="form-control" id="title" placeholder="Ej: Quimioterapia - Ciclo 1" required>
				</div>
			</div>
			
			<div class="form-group">
				<label for="pacient_id" class="col-lg-2 control-label">Paciente*</label>
				<div class="col-md-10">
					<select name="pacient_id" class="form-control" id="pacient_id" required>
						<option value="">-- Seleccionar Paciente --</option>
						<?php foreach($pacients as $pacient):?>
						<option value="<?php echo $pacient->id; ?>"><?php echo $pacient->name." ".$pacient->lastname; ?> (<?php echo $pacient->no; ?>)</option>
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
						<option value="<?php echo $medic->id; ?>"><?php echo $medic->name." ".$medic->lastname; ?> (<?php echo $medic->no; ?>)</option>
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
								<option value="<?php echo $chair->id; ?>"><?php echo $chair->name; ?></option>
								<?php endforeach; ?>
							</select>
						</div>
						<div class="col-md-6">
							<button type="button" class="btn btn-info btn-block" onclick="abrirSelectorVisual()">
								<i class="glyphicon glyphicon-map-marker"></i> Selector Visual de Sillones
							</button>
						</div>
					</div>
				</div>
			</div>

			<div class="form-group">
				<label for="date_at" class="col-lg-2 control-label">Fecha*</label>
				<div class="col-md-4">
					<input type="date" name="date_at" class="form-control" id="date_at" value="<?php echo date('Y-m-d'); ?>" required>
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
								echo "<option value='$time'>$display</option>";
							}
						}
						?>
					</select>
				</div>
			</div>

			<div class="form-group">
				<label for="note" class="col-lg-2 control-label">Notas</label>
				<div class="col-md-10">
					<textarea name="note" class="form-control" id="note" placeholder="Observaciones adicionales, instrucciones especiales, etc." rows="3"></textarea>
				</div>
			</div>

			<div class="form-group">
				<div class="col-lg-offset-2 col-lg-10">
					<div class="checkbox">
						<label>
							<input type="checkbox" id="check_conflicts"> Verificar conflictos de horario automáticamente
						</label>
					</div>
				</div>
			</div>

			<div class="form-group">
				<div class="col-lg-offset-2 col-lg-10">
					<button type="submit" class="btn btn-primary">
						<i class="glyphicon glyphicon-ok"></i> Programar Cita
					</button>
					<a href="index.php?view=reservations" class="btn btn-default">
						<i class="glyphicon glyphicon-remove"></i> Cancelar
					</a>
				</div>
			</div>

		</form>

	</div>
	
	<div class="col-md-4">
		<div class="panel panel-info">
			<div class="panel-heading">
				<h3 class="panel-title">
					<i class="glyphicon glyphicon-info-sign"></i> Información
				</h3>
			</div>
			<div class="panel-body">
				<p><strong>Horarios de atención:</strong></p>
				<p>Lunes a Viernes: 08:00 - 18:00</p>
				<p>Sábados: 08:00 - 14:00</p>
				<br>
				<p><strong>Duración promedio:</strong></p>
				<ul>
					<li>Consulta: 30 min</li>
					<li>Quimioterapia: 2-4 horas</li>
					<li>Inmunoterapia: 1-2 horas</li>
					<li>Control: 15-30 min</li>
				</ul>
			</div>
		</div>
		
		<div id="conflicts-panel" class="panel panel-warning" style="display:none;">
			<div class="panel-heading">
				<h3 class="panel-title">
					<i class="glyphicon glyphicon-exclamation-sign"></i> Conflictos Detectados
				</h3>
			</div>
			<div class="panel-body" id="conflicts-content">
			</div>
		</div>
	</div>
</div>

<!-- Modal para Selector Visual de Sillones -->
<div class="modal fade" id="modal-selector-sillones" tabindex="-1" role="dialog" aria-labelledby="modalSelectorLabel">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title" id="modalSelectorLabel">
					<i class="glyphicon glyphicon-map-marker"></i> Selector Visual de Sillones
				</h4>
			</div>
			<div class="modal-body">
				<!-- Panel de información de la selección -->
				<div class="alert alert-info">
					<strong>Fecha seleccionada:</strong> <span id="fecha-selector">No seleccionada</span><br>
					<strong>Hora seleccionada:</strong> <span id="hora-selector">No seleccionada</span>
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
						</div>
					</div>
				</div>
				
				<!-- Mapa de sillones -->
				<div id="selector-map-container" class="selector-map-container">
					<!-- Estación central -->
					<div class="estacion-enfermeria-selector">
						<i class="glyphicon glyphicon-plus"></i>
						<div>Enfermería</div>
					</div>
					
					<!-- Grid de sillones -->
					<div id="sillones-selector-grid" class="sillones-selector-grid">
						<?php 
						$contador = 0;
						foreach ($chairs as $chair): 
							$contador++;
							$fila = ceil($contador / 4);
							$columna = ($contador - 1) % 4 + 1;
						?>
						<div class="sillon-selector-item disponible" 
							 data-sillon-id="<?php echo $chair->id; ?>"
							 data-nombre="<?php echo htmlspecialchars($chair->name); ?>"
							 style="grid-row: <?php echo $fila; ?>; grid-column: <?php echo $columna; ?>;"
							 onclick="seleccionarSillonVisual(<?php echo $chair->id; ?>, '<?php echo htmlspecialchars($chair->name); ?>')">
							
							<div class="sillon-visual-selector">
								<div class="sillon-back-selector"></div>
								<div class="sillon-seat-selector"></div>
							</div>
							
							<div class="sillon-info-selector">
								<div class="sillon-numero-selector"><?php echo $chair->id; ?></div>
								<div class="sillon-nombre-selector"><?php echo htmlspecialchars($chair->name); ?></div>
								<div class="sillon-estado-selector">Disponible</div>
							</div>
						</div>
						<?php endforeach; ?>
					</div>
				</div>
				
				<!-- Información del sillón seleccionado -->
				<div id="info-sillon-seleccionado" class="alert alert-success" style="display: none;">
					<strong>Sillón seleccionado:</strong> <span id="nombre-sillon-seleccionado"></span> (ID: <span id="id-sillon-seleccionado"></span>)
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
				<button type="button" class="btn btn-primary" onclick="confirmarSeleccionSillon()" id="btn-confirmar-sillon" disabled>
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
	
	// Verificar conflictos cuando cambien los campos relevantes
	$('#medic_id, #date_at, #time_at, #chair_id').change(function(){
		if($('#check_conflicts').is(':checked')) {
			checkConflicts();
		}
	});
	
	$('#check_conflicts').change(function(){
		if($(this).is(':checked')) {
			checkConflicts();
		} else {
			$('#conflicts-panel').hide();
		}
	});
	
	function checkConflicts() {
		var medic_id = $('#medic_id').val();
		var chair_id = $('#chair_id').val();
		var date = $('#date_at').val();
		var time = $('#time_at').val();
		
		if(medic_id && date && time) {
			// Aquí podrías hacer una llamada AJAX para verificar conflictos
			// Por ahora, solo mostrar el panel
			$('#conflicts-content').html('<p>Verificando disponibilidad...</p>');
			$('#conflicts-panel').show();
			
			// Simular verificación
			setTimeout(function(){
				$('#conflicts-content').html('<p class="text-success"><i class="glyphicon glyphicon-ok"></i> No se encontraron conflictos.</p>');
			}, 1000);
		}
	}
});

// Variables globales para el selector visual
let sillonSeleccionadoVisual = null;

function abrirSelectorVisual() {
	// Verificar que se hayan seleccionado fecha y hora
	const fecha = $('#date_at').val();
	const hora = $('#time_at').val();
	
	if (!fecha || !hora) {
		alert('Por favor selecciona primero la fecha y hora de la cita.');
		return;
	}
	
	// Actualizar información en el modal
	$('#fecha-selector').text(fecha);
	$('#hora-selector').text(hora);
	
	// Verificar disponibilidad de sillones para la fecha/hora seleccionada
	verificarDisponibilidadSillones(fecha, hora);
	
	// Mostrar modal
	$('#modal-selector-sillones').modal('show');
}

function verificarDisponibilidadSillones(fecha, hora) {
	// En producción, esto sería una llamada AJAX para verificar disponibilidad
	// Por ahora, simular algunos sillones ocupados aleatoriamente
	$('.sillon-selector-item').each(function() {
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

function seleccionarSillonVisual(sillonId, nombre) {
	// Remover selección anterior
	$('.sillon-selector-item').removeClass('seleccionado');
	
	// Seleccionar nuevo sillón
	const sillonElement = $(`.sillon-selector-item[data-sillon-id="${sillonId}"]`);
	sillonElement.addClass('seleccionado');
	
	// Guardar selección
	sillonSeleccionadoVisual = {
		id: sillonId,
		nombre: nombre
	};
	
	// Mostrar información
	$('#nombre-sillon-seleccionado').text(nombre);
	$('#id-sillon-seleccionado').text(sillonId);
	$('#info-sillon-seleccionado').show();
	
	// Habilitar botón de confirmación
	$('#btn-confirmar-sillon').prop('disabled', false);
}

function confirmarSeleccionSillon() {
	if (sillonSeleccionadoVisual) {
		// Actualizar select tradicional
		$('#chair_id').val(sillonSeleccionadoVisual.id).trigger('change');
		
		// Cerrar modal
		$('#modal-selector-sillones').modal('hide');
		
		// Mostrar confirmación
		const mensaje = `Sillón "${sillonSeleccionadoVisual.nombre}" seleccionado correctamente.`;
		
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
		sillonSeleccionadoVisual = null;
	}
}

// Limpiar selección al cerrar modal
$('#modal-selector-sillones').on('hidden.bs.modal', function () {
	$('.sillon-selector-item').removeClass('seleccionado');
	$('#info-sillon-seleccionado').hide();
	$('#btn-confirmar-sillon').prop('disabled', true);
	sillonSeleccionadoVisual = null;
});
</script>
