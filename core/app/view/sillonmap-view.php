<?php
// Verificar que el usuario esté logueado
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["user_id"])) {
    Core::redir("./");
    exit;
}

// Obtener sillones de oncología
$sillones = OncologyChairData::getAll();
$reservaciones = ReservationData::getAll();

// Crear un array para rastrear sillones ocupados
$sillonesOcupados = array();
foreach ($reservaciones as $reservacion) {
    if ($reservacion->chair_id && $reservacion->status_id == 1) {
        $sillonesOcupados[$reservacion->chair_id] = $reservacion;
    }
}
?>

<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <i class="fa fa-map-o"></i> Mapa Visual de Sillones
            <small>Gestión visual tipo cine</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="./"><i class="fa fa-home"></i> Inicio</a></li>
            <li class="active">Mapa de Sillones</li>
        </ol>
    </section>

    <section class="content">
        <!-- Panel de control superior -->
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-dashboard"></i> Panel de Control</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-info btn-sm" onclick="actualizarMapa()">
                                <i class="fa fa-refresh"></i> Actualizar
                            </button>
                            <a href="./?view=sillonlayout" class="btn btn-warning btn-sm">
                                <i class="fa fa-cogs"></i> Configurar Layout
                            </a>
                            <a href="./?view=chairschedule" class="btn btn-success btn-sm">
                                <i class="fa fa-calendar"></i> Horarios
                            </a>
                        </div>
                    </div>
                    <div class="box-body">
                        <!-- Leyenda de estados -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="sillon-legend">
                                    <div class="legend-item">
                                        <div class="sillon-mini disponible"></div>
                                        <span>Disponible</span>
                                    </div>
                                    <div class="legend-item">
                                        <div class="sillon-mini ocupado"></div>
                                        <span>Ocupado</span>
                                    </div>
                                    <div class="legend-item">
                                        <div class="sillon-mini mantenimiento"></div>
                                        <span>Mantenimiento</span>
                                    </div>
                                    <div class="legend-item">
                                        <div class="sillon-mini seleccionado"></div>
                                        <span>Seleccionado</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Área del mapa de sillones -->
        <div class="row">
            <div class="col-md-12">
                <div class="box box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-sitemap"></i> Disposición Física de Sillones</h3>
                        <div class="box-tools pull-right">
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-default" onclick="cambiarVista('grid')">
                                    <i class="fa fa-th"></i> Cuadrícula
                                </button>
                                <button type="button" class="btn btn-sm btn-default" onclick="cambiarVista('theater')">
                                    <i class="fa fa-video-camera"></i> Teatro
                                </button>
                                <button type="button" class="btn btn-sm btn-default active" onclick="cambiarVista('hospital')">
                                    <i class="fa fa-hospital-o"></i> Hospital
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <div id="sillon-map-container" class="sillon-map-container">
                            <!-- Área de enfermería central -->
                            <div class="area-central">
                                <div class="estacion-enfermeria">
                                    <i class="fa fa-user-md"></i>
                                    <div>Estación de Enfermería</div>
                                </div>
                            </div>

                            <!-- Grid de sillones -->
                            <div id="sillones-grid" class="sillones-grid hospital-layout">
                                <?php 
                                $contador = 0;
                                foreach ($sillones as $sillon): 
                                    $contador++;
                                    $isOcupado = isset($sillonesOcupados[$sillon->id]);
                                    $estadoClass = $isOcupado ? 'ocupado' : 'disponible';
                                    $estadoTexto = $isOcupado ? 'Ocupado' : 'Disponible';
                                    
                                    // Posicionamiento automático en grid hospitalario
                                    $fila = ceil($contador / 4);
                                    $columna = ($contador - 1) % 4 + 1;
                                ?>
                                <div class="sillon-item <?php echo $estadoClass; ?>" 
                                     data-sillon-id="<?php echo $sillon->id; ?>"
                                     data-nombre="<?php echo htmlspecialchars($sillon->name); ?>"
                                     data-estado="<?php echo $estadoTexto; ?>"
                                     style="grid-row: <?php echo $fila; ?>; grid-column: <?php echo $columna; ?>;"
                                     onclick="seleccionarSillon(<?php echo $sillon->id; ?>)">
                                    
                                    <div class="sillon-visual">
                                        <div class="sillon-back"></div>
                                        <div class="sillon-seat"></div>
                                        <div class="sillon-arms"></div>
                                    </div>
                                    
                                    <div class="sillon-info">
                                        <div class="sillon-numero"><?php echo $sillon->id; ?></div>
                                        <div class="sillon-nombre"><?php echo htmlspecialchars($sillon->name); ?></div>
                                        <div class="sillon-estado"><?php echo $estadoTexto; ?></div>
                                        <?php if ($isOcupado): ?>
                                            <div class="paciente-info">
                                                <?php 
                                                $reservacion = $sillonesOcupados[$sillon->id];
                                                $paciente = PacientData::getById($reservacion->pacient_id);
                                                if ($paciente):
                                                ?>
                                                <small><?php echo htmlspecialchars($paciente->name . ' ' . $paciente->lastname); ?></small>
                                                <small><?php echo date('H:i', strtotime($reservacion->date_at . ' ' . $reservacion->time_at)); ?></small>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel de información del sillón seleccionado -->
        <div class="row" id="panel-sillon-info" style="display: none;">
            <div class="col-md-12">
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-info-circle"></i> Información del Sillón</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-sm btn-default" onclick="cerrarInfoSillon()">
                                <i class="fa fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="box-body" id="info-sillon-content">
                        <!-- Contenido dinámico -->
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- CSS Personalizado para el mapa de sillones -->
<style>
.sillon-legend {
    display: flex;
    justify-content: center;
    gap: 20px;
    margin-bottom: 10px;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 5px;
}

.sillon-mini {
    width: 20px;
    height: 20px;
    border-radius: 3px;
    border: 2px solid #333;
}

.sillon-map-container {
    min-height: 500px;
    background: linear-gradient(135deg, #f5f5f5 0%, #e8e8e8 100%);
    border: 2px solid #ddd;
    border-radius: 10px;
    padding: 20px;
    position: relative;
    overflow: auto;
}

.area-central {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 10;
}

.estacion-enfermeria {
    background: #3498db;
    color: white;
    padding: 15px;
    border-radius: 50%;
    text-align: center;
    min-width: 120px;
    min-height: 120px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.estacion-enfermeria i {
    font-size: 24px;
    margin-bottom: 5px;
}

.sillones-grid {
    display: grid;
    gap: 20px;
    padding: 20px;
    position: relative;
}

.sillones-grid.hospital-layout {
    grid-template-columns: repeat(4, 1fr);
    max-width: 800px;
    margin: 0 auto;
}

.sillon-item {
    position: relative;
    cursor: pointer;
    transition: all 0.3s ease;
    padding: 10px;
    border-radius: 8px;
    text-align: center;
    border: 2px solid transparent;
}

.sillon-item:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.sillon-item.seleccionado {
    border-color: #f39c12 !important;
    box-shadow: 0 0 15px rgba(243, 156, 18, 0.5);
}

.sillon-visual {
    position: relative;
    width: 60px;
    height: 70px;
    margin: 0 auto 10px;
}

.sillon-back {
    position: absolute;
    top: 0;
    left: 10px;
    width: 40px;
    height: 45px;
    border-radius: 8px 8px 0 0;
    border: 2px solid #333;
}

.sillon-seat {
    position: absolute;
    bottom: 15px;
    left: 5px;
    width: 50px;
    height: 30px;
    border-radius: 5px;
    border: 2px solid #333;
}

.sillon-arms {
    position: absolute;
    bottom: 20px;
    left: 0;
    width: 60px;
    height: 20px;
}

.sillon-arms::before,
.sillon-arms::after {
    content: '';
    position: absolute;
    width: 8px;
    height: 20px;
    background: #333;
    border-radius: 2px;
}

.sillon-arms::before {
    left: 0;
}

.sillon-arms::after {
    right: 0;
}

/* Estados de los sillones */
.sillon-item.disponible .sillon-back,
.sillon-item.disponible .sillon-seat {
    background: #2ecc71;
}

.sillon-mini.disponible {
    background: #2ecc71;
}

.sillon-item.ocupado .sillon-back,
.sillon-item.ocupado .sillon-seat {
    background: #e74c3c;
}

.sillon-mini.ocupado {
    background: #e74c3c;
}

.sillon-item.mantenimiento .sillon-back,
.sillon-item.mantenimiento .sillon-seat {
    background: #f39c12;
}

.sillon-mini.mantenimiento {
    background: #f39c12;
}

.sillon-mini.seleccionado {
    background: #f39c12;
    box-shadow: 0 0 5px rgba(243, 156, 18, 0.8);
}

.sillon-info {
    font-size: 11px;
    line-height: 1.2;
}

.sillon-numero {
    font-weight: bold;
    font-size: 14px;
    color: #2c3e50;
}

.sillon-nombre {
    color: #7f8c8d;
    margin: 2px 0;
}

.sillon-estado {
    font-weight: bold;
    margin: 2px 0;
}

.paciente-info {
    background: rgba(0,0,0,0.1);
    padding: 2px 4px;
    border-radius: 3px;
    margin-top: 5px;
}

.paciente-info small {
    display: block;
    line-height: 1.1;
}

/* Responsivo */
@media (max-width: 768px) {
    .sillones-grid.hospital-layout {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .sillon-legend {
        flex-wrap: wrap;
        gap: 10px;
    }
    
    .estacion-enfermeria {
        min-width: 80px;
        min-height: 80px;
        padding: 10px;
    }
}
</style>

<!-- JavaScript para interactividad -->
<script src="js/sillon-manager.js"></script>
<script>
let sillonSeleccionado = null;

function seleccionarSillon(sillonId) {
    // Remover selección anterior
    if (sillonSeleccionado) {
        document.querySelector(`[data-sillon-id="${sillonSeleccionado}"]`).classList.remove('seleccionado');
    }
    
    // Seleccionar nuevo sillón
    const sillonElement = document.querySelector(`[data-sillon-id="${sillonId}"]`);
    sillonElement.classList.add('seleccionado');
    sillonSeleccionado = sillonId;
    
    // Mostrar información del sillón
    mostrarInfoSillon(sillonId);
}

function mostrarInfoSillon(sillonId) {
    const sillonElement = document.querySelector(`[data-sillon-id="${sillonId}"]`);
    const nombre = sillonElement.dataset.nombre;
    const estado = sillonElement.dataset.estado;
    
    let contenido = `
        <div class="row">
            <div class="col-md-3">
                <strong>Sillón ID:</strong> ${sillonId}
            </div>
            <div class="col-md-3">
                <strong>Nombre:</strong> ${nombre}
            </div>
            <div class="col-md-3">
                <strong>Estado:</strong> 
                <span class="label ${estado === 'Disponible' ? 'label-success' : 'label-danger'}">
                    ${estado}
                </span>
            </div>
            <div class="col-md-3">
                <div class="btn-group">
                    <a href="./?view=newreservation&chair_id=${sillonId}" class="btn btn-sm btn-primary">
                        <i class="fa fa-plus"></i> Nueva Reserva
                    </a>
                    <a href="./?view=editoncologychair&id=${sillonId}" class="btn btn-sm btn-warning">
                        <i class="fa fa-edit"></i> Editar
                    </a>
                </div>
            </div>
        </div>
    `;
    
    document.getElementById('info-sillon-content').innerHTML = contenido;
    document.getElementById('panel-sillon-info').style.display = 'block';
    
    // Scroll suave al panel de información
    document.getElementById('panel-sillon-info').scrollIntoView({ 
        behavior: 'smooth', 
        block: 'nearest' 
    });
}

function cerrarInfoSillon() {
    document.getElementById('panel-sillon-info').style.display = 'none';
    if (sillonSeleccionado) {
        document.querySelector(`[data-sillon-id="${sillonSeleccionado}"]`).classList.remove('seleccionado');
        sillonSeleccionado = null;
    }
}

function cambiarVista(tipo) {
    const grid = document.getElementById('sillones-grid');
    
    // Remover clases anteriores
    grid.classList.remove('hospital-layout', 'theater-layout', 'grid-layout');
    
    // Añadir nueva clase
    grid.classList.add(tipo + '-layout');
    
    // Actualizar botones activos
    document.querySelectorAll('.btn-group .btn').forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');
}

function actualizarMapa() {
    // Recargar la página para actualizar estados
    window.location.reload();
}

// Auto-actualización cada 2 minutos
setInterval(function() {
    if (!sillonSeleccionado) {
        actualizarMapa();
    }
}, 120000);

// Inicialización
document.addEventListener('DOMContentLoaded', function() {
    console.log('Mapa de sillones inicializado');
});
</script>
