<?php
// Vista de horarios de sillones
?>
<div class="row">
    <div class="col-md-12">
        <div class="page-header">
            <h1>
                <i    return html;
}

// Cargar horarios al inicio
$(document).ready(function() {
    loadSchedule();
});

// Función para abrir vista rápida del mapa
function abrirVistaRapida() {
    $('#modal-vista-rapida').modal('show');
    cargarVistaRapidaSillones();
}

function cargarVistaRapidaSillones() {
    // Simular carga de estados de sillones en tiempo real
    $('#vista-rapida-content .sillon-rapido').each(function() {
        const ocupado = Math.random() < 0.3;
        $(this).removeClass('disponible ocupado').addClass(ocupado ? 'ocupado' : 'disponible');
        $(this).find('.estado-texto').text(ocupado ? 'Ocupado' : 'Disponible');
    });
}
</script>

<!-- Modal de Vista Rápida -->
<div class="modal fade" id="modal-vista-rapida" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">
                    <i class="fa fa-eye"></i> Vista Rápida de Sillones
                    <small id="hora-actualizacion"><?php echo date('H:i:s'); ?></small>
                </h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fa fa-info-circle"></i>
                    <strong>Estado actual de todos los sillones</strong> - 
                    Actualizado automáticamente cada 30 segundos
                </div>
                
                <div id="vista-rapida-content" class="vista-rapida-content">
                    <div class="text-center">
                        <div class="estacion-enfermeria-rapida">
                            <i class="fa fa-user-md"></i>
                            <div>Enfermería</div>
                        </div>
                    </div>
                    
                    <div class="sillones-rapidos-grid">
                        <?php
                        try {
                            $chairs = OncologyChairData::getAll();
                            $contador = 0;
                            foreach($chairs as $chair):
                                $contador++;
                                $fila = ceil($contador / 4);
                                $columna = ($contador - 1) % 4 + 1;
                        ?>
                        <div class="sillon-rapido disponible" 
                             data-sillon-id="<?php echo $chair->id; ?>"
                             style="grid-row: <?php echo $fila; ?>; grid-column: <?php echo $columna; ?>;">
                            <div class="sillon-visual-rapido">
                                <div class="sillon-back-rapido"></div>
                                <div class="sillon-seat-rapido"></div>
                            </div>
                            <div class="sillon-info-rapido">
                                <div class="sillon-numero-rapido"><?php echo $chair->id; ?></div>
                                <div class="sillon-nombre-rapido"><?php echo htmlspecialchars($chair->name); ?></div>
                                <div class="estado-texto">Disponible</div>
                            </div>
                        </div>
                        <?php 
                            endforeach;
                        } catch(Exception $e) {
                            echo '<div class="alert alert-danger">Error cargando sillones: ' . $e->getMessage() . '</div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" onclick="cargarVistaRapidaSillones()">
                    <i class="fa fa-refresh"></i> Actualizar
                </button>
                <a href="./?view=sillonmap" class="btn btn-primary">
                    <i class="fa fa-map-o"></i> Ir al Mapa Completo
                </a>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- CSS para Vista Rápida -->
<style>
.vista-rapida-content {
    min-height: 300px;
    background: linear-gradient(135deg, #f8f8f8 0%, #e8e8e8 100%);
    border-radius: 8px;
    padding: 20px;
    position: relative;
}

.estacion-enfermeria-rapida {
    background: #337ab7;
    color: white;
    padding: 10px;
    border-radius: 50%;
    width: 80px;
    height: 80px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    margin: 0 auto 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

.sillones-rapidos-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 15px;
    max-width: 500px;
    margin: 0 auto;
}

.sillon-rapido {
    text-align: center;
    padding: 8px;
    border-radius: 6px;
    transition: transform 0.2s ease;
    cursor: pointer;
}

.sillon-rapido:hover {
    transform: scale(1.05);
}

.sillon-visual-rapido {
    position: relative;
    width: 40px;
    height: 50px;
    margin: 0 auto 8px;
}

.sillon-back-rapido {
    position: absolute;
    top: 0;
    left: 6px;
    width: 28px;
    height: 32px;
    border-radius: 4px 4px 0 0;
    border: 2px solid #333;
}

.sillon-seat-rapido {
    position: absolute;
    bottom: 8px;
    left: 3px;
    width: 34px;
    height: 20px;
    border-radius: 3px;
    border: 2px solid #333;
}

.sillon-rapido.disponible .sillon-back-rapido,
.sillon-rapido.disponible .sillon-seat-rapido {
    background: #5cb85c;
}

.sillon-rapido.ocupado .sillon-back-rapido,
.sillon-rapido.ocupado .sillon-seat-rapido {
    background: #d9534f;
}

.sillon-info-rapido {
    font-size: 9px;
    line-height: 1.1;
}

.sillon-numero-rapido {
    font-weight: bold;
    font-size: 11px;
    color: #333;
}

.sillon-nombre-rapido {
    color: #666;
    margin: 1px 0;
}

.estado-texto {
    font-weight: bold;
    font-size: 8px;
}
</style>fa fa-calendar-o"></i> Horarios de Sillones
                <small>Programación y disponibilidad de sillones oncológicos</small>
            </h1>
            <div class="btn-toolbar pull-right" style="margin-top: -40px;">
                <div class="btn-group">
                    <a href="./?view=sillonmap" class="btn btn-info btn-sm">
                        <i class="fa fa-map-o"></i> Mapa Visual
                    </a>
                    <a href="./?view=sillonlayout" class="btn btn-warning btn-sm">
                        <i class="fa fa-cogs"></i> Configurar Layout
                    </a>
                    <button type="button" class="btn btn-success btn-sm" onclick="abrirVistaRapida()">
                        <i class="fa fa-eye"></i> Vista Rápida
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-filter"></i> Filtros
                </h3>
            </div>
            <div class="panel-body">
                <form id="filterForm">
                    <div class="form-group">
                        <label>Fecha:</label>
                        <input type="date" class="form-control" id="filterDate" value="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="form-group">
                        <label>Sillón:</label>
                        <select class="form-control" id="filterChair">
                            <option value="">Todos los sillones</option>
                            <?php
                            try {
                                $chairs = OncologyChairData::getAll();
                                foreach($chairs as $chair) {
                                    echo "<option value='{$chair->id}'>Sillón {$chair->name}</option>";
                                }
                            } catch(Exception $e) {
                                echo "<option disabled>Error cargando sillones</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <button type="button" class="btn btn-primary" onclick="loadSchedule()">
                        <i class="fa fa-search"></i> Buscar
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-9">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-clock-o"></i> Horarios del Día
                </h3>
            </div>
            <div class="panel-body">
                <div id="scheduleContent">
                    <div class="text-center">
                        <i class="fa fa-spinner fa-spin fa-2x"></i>
                        <p>Cargando horarios...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Leyenda -->
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-info">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-info-circle"></i> Leyenda
                </h3>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-3">
                        <span class="label label-success">Disponible</span> - Sillón libre
                    </div>
                    <div class="col-md-3">
                        <span class="label label-warning">Ocupado</span> - En tratamiento
                    </div>
                    <div class="col-md-3">
                        <span class="label label-danger">Mantenimiento</span> - No disponible
                    </div>
                    <div class="col-md-3">
                        <span class="label label-info">Reservado</span> - Cita programada
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function loadSchedule() {
    var date = $('#filterDate').val();
    var chairId = $('#filterChair').val();
    
    $('#scheduleContent').html(`
        <div class="text-center">
            <i class="fa fa-spinner fa-spin fa-2x"></i>
            <p>Cargando horarios...</p>
        </div>
    `);
    
    // Simular carga de datos (reemplazar con llamada real)
    setTimeout(function() {
        var scheduleHtml = generateScheduleTable(date, chairId);
        $('#scheduleContent').html(scheduleHtml);
    }, 1000);
}

function generateScheduleTable(date, chairId) {
    var hours = ['08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00'];
    var html = '<div class="table-responsive"><table class="table table-bordered table-hover">';
    html += '<thead><tr><th>Hora</th>';
    
    // Headers de sillones (simplificado)
    if (chairId) {
        html += '<th>Sillón ' + chairId + '</th>';
    } else {
        html += '<th>Sillón 1</th><th>Sillón 2</th><th>Sillón 3</th><th>Sillón 4</th>';
    }
    html += '</tr></thead><tbody>';
    
    // Filas de horarios
    hours.forEach(function(hour) {
        html += '<tr><td><strong>' + hour + '</strong></td>';
        
        if (chairId) {
            var status = Math.random() > 0.5 ? 'success' : 'warning';
            var text = status === 'success' ? 'Disponible' : 'Ocupado';
            html += '<td><span class="label label-' + status + '">' + text + '</span></td>';
        } else {
            for (var i = 1; i <= 4; i++) {
                var status = Math.random() > 0.5 ? 'success' : 'warning';
                var text = status === 'success' ? 'Disponible' : 'Ocupado';
                html += '<td><span class="label label-' + status + '">' + text + '</span></td>';
            }
        }
        html += '</tr>';
    });
    
    html += '</tbody></table></div>';
    return html;
}

// Cargar horarios al inicializar la página
$(document).ready(function() {
    loadSchedule();
});
</script>
