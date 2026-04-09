<?php
// Verificar que el usuario esté logueado
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["user_id"])) {
    Core::redir("./");
    exit;
}

// Obtener sillones existentes
$sillones = OncologyChairData::getAll();
?>

<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <i class="fa fa-cogs"></i> Configuración del Layout
            <small>Diseñar disposición física de sillones</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="./"><i class="fa fa-home"></i> Inicio</a></li>
            <li><a href="./?view=sillonmap"><i class="fa fa-map-o"></i> Mapa de Sillones</a></li>
            <li class="active">Configurar Layout</li>
        </ol>
    </section>

    <section class="content">
        <!-- Sistema de notificaciones integrado -->
        <div id="sistema-notificaciones" style="display: none;">
            <div class="alert" id="notificacion-container">
                <button type="button" class="close" onclick="ocultarNotificacion()">
                    <span aria-hidden="true">&times;</span>
                </button>
                <div id="notificacion-content">
                    <i id="notificacion-icon"></i>
                    <span id="notificacion-text"></span>
                </div>
            </div>
        </div>

        <!-- Panel de herramientas -->
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-wrench"></i> Herramientas de Diseño</h3>
                        <div class="box-tools pull-right">
                            <button type="button" id="test-drag" class="btn btn-info btn-sm">
                                <i class="fa fa-hand-rock-o"></i> Test Drag
                            </button>
                            <button type="button" id="guardar-posiciones-exactas" class="btn btn-success" onclick="guardarPosicionesDefinitivas()">
                                <i class="fa fa-save"></i> Guardar Posiciones
                            </button>
                            <button type="button" class="btn btn-warning btn-sm" onclick="resetearLayout()">
                                <i class="fa fa-refresh"></i> Resetear
                            </button>
                            <button type="button" class="btn btn-primary btn-sm" onclick="verificarPosiciones()">
                                <i class="fa fa-check"></i> Verificar
                            </button>
                            <a href="./?view=sillonmap" class="btn btn-info btn-sm">
                                <i class="fa fa-eye"></i> Ver Mapa
                            </a>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Modo de Disposición:</label>
                                    <select class="form-control" id="plantilla-base" onchange="aplicarPlantilla()">
                                        <option value="oncology">Oncología Personalizada</option>
                                    </select>
                                    <small class="text-muted">
                                        <i class="fa fa-info-circle"></i> Modo único optimizado para el centro oncológico
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Tamaño del Área:</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="area-width" value="800" min="400" max="1200">
                                        <span class="input-group-addon">x</span>
                                        <input type="number" class="form-control" id="area-height" value="600" min="300" max="900">
                                        <span class="input-group-addon">px</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Modo de Edición:</label>
                                    <div class="btn-group btn-group-justified">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-default active" onclick="cambiarModo('move')" id="btn-move">
                                                <i class="fa fa-arrows"></i> Mover
                                            </button>
                                        </div>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-default" onclick="cambiarModo('add')" id="btn-add">
                                                <i class="fa fa-plus"></i> Añadir
                                            </button>
                                        </div>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-default" onclick="cambiarModo('delete')" id="btn-delete">
                                                <i class="fa fa-trash"></i> Eliminar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Grilla de Ajuste:</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="grid-size" value="20" min="10" max="50">
                                        <span class="input-group-addon">px</span>
                                        <div class="input-group-addon">
                                            <input type="checkbox" id="snap-to-grid" checked> Ajustar
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Área de diseño principal -->
        <div class="row">
            <div class="col-md-9">
                <div class="box box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-paint-brush"></i> Área de Diseño</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-sm btn-default" onclick="centrarVista()">
                                <i class="fa fa-crosshairs"></i> Centrar
                            </button>
                            <button type="button" class="btn btn-sm btn-default" onclick="toggleGrid()">
                                <i class="fa fa-th"></i> Grid
                            </button>
                        </div>
                    </div>
                    
                    <!-- Información del modo actual -->
                    <div id="modo-info-container" class="alert alert-info" style="margin: 10px; display: none;">
                        <i class="fa fa-info-circle"></i>
                        <span id="modo-info">Modo actual: Mover</span>
                    </div>
                    <div class="box-body" style="padding: 0;">
                        <div id="design-container" class="design-container">
                            <!-- Área de trabajo -->
                            <div id="design-area" class="design-area">
                                <!-- Grid de fondo -->
                                <div id="background-grid" class="background-grid"></div>
                                
                                <!-- Estación central de enfermería -->
                                <div id="nursing-station" class="nursing-station draggable" 
                                     data-x="350" data-y="250">
                                    <i class="fa fa-user-md"></i>
                                    <div>Enfermería</div>
                                </div>

                                <!-- Sillones existentes -->
                                <?php foreach ($sillones as $index => $sillon): ?>
                                <div class="sillon-designer draggable" 
                                     data-sillon-id="<?php echo $sillon->id; ?>"
                                     data-x="<?php echo 100 + ($index % 4) * 150; ?>"
                                     data-y="<?php echo 100 + floor($index / 4) * 120; ?>"
                                     onclick="seleccionarSillonDesigner(<?php echo $sillon->id; ?>)">
                                    <div class="sillon-visual-designer">
                                        <div class="sillon-back-designer"></div>
                                        <div class="sillon-seat-designer"></div>
                                    </div>
                                    <div class="sillon-label"><?php echo htmlspecialchars($sillon->name); ?></div>
                                    <div class="sillon-id">ID: <?php echo $sillon->id; ?></div>
                                    <div class="sillon-badge-real">REAL</div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Panel lateral de propiedades -->
            <div class="col-md-3">
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-list"></i> Propiedades</h3>
                    </div>
                    <div class="box-body">
                        <div id="no-selection" class="text-center text-muted">
                            <i class="fa fa-mouse-pointer fa-2x"></i>
                            <p>Selecciona un elemento para editar sus propiedades</p>
                        </div>

                        <div id="sillon-properties" style="display: none;">
                            <h4>Propiedades del Sillón</h4>
                            <div class="form-group">
                                <label>ID:</label>
                                <input type="text" class="form-control" id="prop-sillon-id" readonly>
                            </div>
                            <div class="form-group">
                                <label>Nombre:</label>
                                <input type="text" class="form-control" id="prop-sillon-name">
                            </div>
                            <div class="form-group">
                                <label>Posición X:</label>
                                <input type="number" class="form-control" id="prop-sillon-x">
                            </div>
                            <div class="form-group">
                                <label>Posición Y:</label>
                                <input type="number" class="form-control" id="prop-sillon-y">
                            </div>
                            <div class="form-group">
                                <label>Rotación:</label>
                                <select class="form-control" id="prop-sillon-rotation">
                                    <option value="0">0° (Normal)</option>
                                    <option value="90">90° (Derecha)</option>
                                    <option value="180">180° (Invertido)</option>
                                    <option value="270">270° (Izquierda)</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Color:</label>
                                <select class="form-control" id="prop-sillon-color">
                                    <option value="default">Azul (Default)</option>
                                    <option value="green">Verde</option>
                                    <option value="orange">Naranja</option>
                                    <option value="purple">Morado</option>
                                    <option value="red">Rojo</option>
                                </select>
                            </div>
                            <button type="button" class="btn btn-primary btn-block" onclick="aplicarPropiedades()">
                                <i class="fa fa-check"></i> Aplicar Cambios
                            </button>
                            <button type="button" class="btn btn-danger btn-block" onclick="eliminarSillon()">
                                <i class="fa fa-trash"></i> Eliminar Sillón
                            </button>
                        </div>

                        <div id="station-properties" style="display: none;">
                            <h4>Estación de Enfermería</h4>
                            <div class="form-group">
                                <label>Posición X:</label>
                                <input type="number" class="form-control" id="prop-station-x">
                            </div>
                            <div class="form-group">
                                <label>Posición Y:</label>
                                <input type="number" class="form-control" id="prop-station-y">
                            </div>
                            <button type="button" class="btn btn-primary btn-block" onclick="aplicarPropiedadesStation()">
                                <i class="fa fa-check"></i> Aplicar Cambios
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Lista de sillones -->
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-list-ul"></i> Sillones Disponibles</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-xs btn-success" onclick="mostrarFormNuevoSillon()">
                                <i class="fa fa-plus"></i> Nuevo
                            </button>
                        </div>
                    </div>
                    <div class="box-body" style="max-height: 300px; overflow-y: auto;">
                        <ul id="lista-sillones" class="list-unstyled">
                            <?php foreach ($sillones as $sillon): ?>
                            <li class="sillon-list-item" data-sillon-id="<?php echo $sillon->id; ?>">
                                <div class="sillon-list-info">
                                    <strong><?php echo htmlspecialchars($sillon->name); ?></strong>
                                    <small class="text-muted">ID: <?php echo $sillon->id; ?></small>
                                </div>
                                <div class="sillon-list-actions">
                                    <button type="button" class="btn btn-xs btn-primary" 
                                            onclick="centrarEnSillon(<?php echo $sillon->id; ?>)">
                                        <i class="fa fa-crosshairs"></i>
                                    </button>
                                </div>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal para crear nuevo sillón -->
<div class="modal fade" id="modal-nuevo-sillon" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">
                    <i class="fa fa-plus"></i> Crear Nuevo Sillón Real
                    <small style="color: #27ae60;">💾 Se guardará permanentemente en la base de datos</small>
                </h4>
            </div>
            <div class="modal-body">
                <form id="form-nuevo-sillon">
                    <div class="form-group">
                        <label>Nombre del Sillón:</label>
                        <input type="text" class="form-control" id="nuevo-sillon-nombre" required
                               placeholder="Ej: Sillón Principal, Sillón VIP, etc.">
                    </div>
                    <div class="form-group">
                        <label>Descripción (Opcional):</label>
                        <textarea class="form-control" id="nuevo-sillon-descripcion" rows="2"
                                  placeholder="Descripción adicional del sillón..."></textarea>
                    </div>
                    <div class="form-group">
                        <label>Color del Sillón:</label>
                        <select class="form-control" id="nuevo-sillon-color">
                            <option value="default">🔵 Azul (Por defecto)</option>
                            <option value="green">🟢 Verde</option>
                            <option value="orange">🟠 Naranja</option>
                            <option value="purple">🟣 Púrpura</option>
                            <option value="red">🔴 Rojo</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Posición Inicial:</label>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-addon">X:</span>
                                    <input type="number" class="form-control" id="nuevo-sillon-x" 
                                           placeholder="Horizontal" value="200" min="0" max="800">
                                    <span class="input-group-addon">px</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-addon">Y:</span>
                                    <input type="number" class="form-control" id="nuevo-sillon-y" 
                                           placeholder="Vertical" value="200" min="0" max="600">
                                    <span class="input-group-addon">px</span>
                                </div>
                            </div>
                        </div>
                        <small class="text-muted">
                            <i class="fa fa-info-circle"></i> Puedes ajustar la posición después arrastrando el sillón
                        </small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="crearNuevoSillon()" id="btn-crear-sillon">
                    <i class="fa fa-plus"></i> Crear Sillón Real
                </button>
            </div>
        </div>
    </div>
</div>

<!-- CSS Personalizado -->
<style>
/* Sistema de notificaciones integrado */
#sistema-notificaciones {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    max-width: 450px;
    min-width: 300px;
}

#notificacion-container {
    border-radius: 8px;
    border: none;
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    margin-bottom: 0;
    padding: 15px 20px;
    animation: slideInRight 0.3s ease-out;
}

#notificacion-container .close {
    position: absolute;
    top: 8px;
    right: 12px;
    font-size: 18px;
    font-weight: bold;
    opacity: 0.7;
    color: inherit;
}

#notificacion-container .close:hover {
    opacity: 1;
}

#notificacion-content {
    display: flex;
    align-items: flex-start;
    gap: 10px;
}

#notificacion-icon {
    font-size: 16px;
    margin-top: 2px;
    flex-shrink: 0;
}

#notificacion-text {
    flex: 1;
    line-height: 1.4;
    font-size: 14px;
}

/* Animaciones */
@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes slideOutRight {
    from {
        transform: translateX(0);
        opacity: 1;
    }
    to {
        transform: translateX(100%);
        opacity: 0;
    }
}

.design-container {
    height: 600px;
    overflow: auto;
    border: 2px solid #ddd;
    background: #f8f8f8;
    position: relative;
}

.design-area {
    position: relative;
    width: 800px;
    height: 600px;
    background: linear-gradient(135deg, #ffffff 0%, #f0f0f0 100%);
    margin: 0 auto;
}

.background-grid {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image: 
        linear-gradient(90deg, rgba(0,0,0,0.1) 1px, transparent 1px),
        linear-gradient(rgba(0,0,0,0.1) 1px, transparent 1px);
    background-size: 20px 20px;
    z-index: 0;
}

.nursing-station {
    position: absolute;
    width: 100px;
    height: 100px;
    background: #3498db;
    color: white;
    border-radius: 50%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    cursor: move;
    z-index: 5;
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    border: 3px solid #2980b9;
    user-select: none;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
}

.nursing-station.dragging {
    opacity: 0.8;
    transform: scale(1.05);
    z-index: 999 !important;
    box-shadow: 0 8px 25px rgba(0,0,0,0.3);
}

.nursing-station i {
    font-size: 20px;
    margin-bottom: 5px;
}

.sillon-designer {
    position: absolute;
    width: 80px;
    height: 100px;
    cursor: move !important;
    z-index: 10;
    transition: transform 0.2s ease;
    user-select: none;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
}

.sillon-designer:hover {
    transform: scale(1.1);
    cursor: move !important;
}

.sillon-designer.selected {
    box-shadow: 0 0 15px rgba(243, 156, 18, 0.8);
    z-index: 15;
}

.sillon-designer.dragging {
    opacity: 0.8;
    transform: scale(1.05);
    z-index: 999 !important;
    box-shadow: 0 8px 25px rgba(0,0,0,0.3);
}

.sillon-visual-designer {
    position: relative;
    width: 60px;
    height: 70px;
    margin: 0 auto;
}

.sillon-back-designer {
    position: absolute;
    top: 0;
    left: 10px;
    width: 40px;
    height: 45px;
    background: #3498db;
    border: 2px solid #2980b9;
    border-radius: 8px 8px 0 0;
}

.sillon-seat-designer {
    position: absolute;
    bottom: 15px;
    left: 5px;
    width: 50px;
    height: 30px;
    background: #3498db;
    border: 2px solid #2980b9;
    border-radius: 5px;
}

.sillon-label {
    text-align: center;
    font-size: 10px;
    font-weight: bold;
    color: #2c3e50;
    margin-top: 5px;
}

.sillon-id {
    text-align: center;
    font-size: 8px;
    color: #7f8c8d;
}

/* Badge para sillones reales */
.sillon-badge-real {
    position: absolute;
    top: -5px;
    right: -5px;
    background: #27ae60;
    color: white;
    font-size: 6px;
    font-weight: bold;
    padding: 1px 3px;
    border-radius: 3px;
    z-index: 10;
    box-shadow: 0 1px 3px rgba(0,0,0,0.3);
}

/* Colores para sillones */
.sillon-designer.color-green .sillon-back-designer,
.sillon-designer.color-green .sillon-seat-designer {
    background: #2ecc71;
    border-color: #27ae60;
}

.sillon-designer.color-orange .sillon-back-designer,
.sillon-designer.color-orange .sillon-seat-designer {
    background: #f39c12;
    border-color: #d68910;
}

.sillon-designer.color-purple .sillon-back-designer,
.sillon-designer.color-purple .sillon-seat-designer {
    background: #9b59b6;
    border-color: #8e44ad;
}

.sillon-designer.color-red .sillon-back-designer,
.sillon-designer.color-red .sillon-seat-designer {
    background: #e74c3c;
    border-color: #c0392b;
}

/* Lista de sillones */
.sillon-list-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px;
    border-bottom: 1px solid #eee;
}

.sillon-list-item:hover {
    background: #f5f5f5;
}

/* Modo de edición */
.design-area.mode-add {
    cursor: crosshair;
}

.design-area.mode-delete .sillon-designer:hover {
    background: rgba(231, 76, 60, 0.3);
    cursor: not-allowed;
}

/* Draggable helpers */
.draggable {
    user-select: none;
}

.dragging {
    opacity: 0.7;
    z-index: 20;
}
</style>

<!-- JavaScript para funcionalidad -->
<!-- Usar versión compatible con el sistema existente -->
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css">

<!-- Verificación inmediata de jQuery UI -->
<script>
// Verificar jQuery UI inmediatamente
setTimeout(function() {
    console.log('🔍 Verificando jQuery UI...');
    console.log('jQuery disponible:', typeof jQuery !== 'undefined');
    console.log('jQuery.ui disponible:', typeof jQuery.ui !== 'undefined');
    console.log('jQuery.fn.draggable disponible:', typeof jQuery.fn.draggable !== 'undefined');
    
    if (typeof jQuery !== 'undefined' && typeof jQuery.fn.draggable !== 'undefined') {
        console.log('✅ jQuery UI está disponible');
        if (jQuery.ui && jQuery.ui.version) {
            console.log('📦 Versión jQuery UI:', jQuery.ui.version);
        }
    } else {
        console.error('❌ jQuery UI NO está disponible');
    }
}, 100);
</script>

<script src="js/sillon-manager.js"></script>
<script src="js/drag-alternative.js"></script>
<script>
// Variables básicas
let layoutData = {};

// Funciones requeridas por el HTML
function seleccionarSillonDesigner(sillonId) {
    console.log('Sillón seleccionado:', sillonId);
    // Limpiar selección anterior
    jQuery('.sillon-designer').removeClass('selected');
    
    // Seleccionar nuevo sillón
    const sillon = jQuery(`.sillon-designer[data-sillon-id="${sillonId}"]`);
    sillon.addClass('selected');
    sillonSeleccionado = sillonId;
}

function centrarVista() {
    console.log('Centrando vista...');
    const container = jQuery('.design-container')[0];
    if (container) {
        container.scrollTo({
            left: (container.scrollWidth - container.clientWidth) / 2,
            top: (container.scrollHeight - container.clientHeight) / 2,
            behavior: 'smooth'
        });
    }
}

// function cambiarModo(modo) {
//     console.log('Cambiando modo a:', modo);
//     modoEdicion = modo;
// }
// ^^^ COMENTADO: Primera función duplicada - usar función del drag-alternative.js

function guardarLayout() {
    console.log('Guardando layout...');
    if (typeof mostrarNotificacion === 'function') {
        mostrarNotificacion('Layout guardado exitosamente', 'success');
    } else {
        alert('Layout guardado exitosamente');
    }
}

function resetearLayout() {
    if (confirm('¿Estás seguro de que quieres resetear el layout?')) {
        console.log('Reseteando layout...');
        location.reload();
    }
}

function aplicarPlantilla() {
    console.log('Aplicando plantilla...');
}

function mostrarFormNuevoSillon() {
    console.log('Mostrando formulario de nuevo sillón...');
}

function eliminarSillonSeleccionado() {
    if (sillonSeleccionado) {
        if (confirm('¿Eliminar este sillón?')) {
            jQuery(`.sillon-designer[data-sillon-id="${sillonSeleccionado}"]`).remove();
            sillonSeleccionado = null;
        }
    }
}

// Función para seleccionar sillón (requerida por onclick)
function seleccionarSillonDesigner(sillonId) {
    console.log('Sillón seleccionado:', sillonId);
    // Limpiar selección anterior
    jQuery('.sillon-designer').removeClass('selected');
    
    // Seleccionar nuevo sillón
    const sillon = jQuery(`.sillon-designer[data-sillon-id="${sillonId}"]`);
    sillon.addClass('selected');
    sillonSeleccionado = sillonId;
    
    // Mostrar propiedades si existe el panel
    jQuery('#prop-sillon-id').text(sillonId);
    jQuery('#sillon-properties').show();
    jQuery('#station-properties').hide();
}

// Función para guardar posiciones de sillones (llamada desde drag-system.js)
function guardarPosicionSillon(sillonId, x, y) {
    console.log(`💾 Guardando sillón ${sillonId} en posición EXACTA ${x}, ${y}`);
    
    // Actualizar los atributos data-x y data-y del elemento
    const sillon = jQuery(`.sillon-designer[data-sillon-id="${sillonId}"]`);
    sillon.attr('data-x', x);
    sillon.attr('data-y', y);
    
    // Asegurar que la posición visual coincida EXACTAMENTE
    sillon.css({
        'left': x + 'px',
        'top': y + 'px',
        'position': 'absolute'
    });
    
    // Guardar en localStorage para persistencia temporal
    let savedPositions = JSON.parse(localStorage.getItem('sillonPositions') || '{}');
    savedPositions[sillonId] = { x: parseInt(x), y: parseInt(y) };
    localStorage.setItem('sillonPositions', JSON.stringify(savedPositions));
    
    // NUEVO: Enviar al servidor para guardado permanente
    guardarPosicionEnServidor(sillonId, x, y);
    
    console.log('✅ Posición guardada exitosamente y aplicada EXACTAMENTE');
}

// NUEVA función para guardar posiciones en el servidor
function guardarPosicionEnServidor(sillonId, x, y) {
    jQuery.ajax({
        url: 'index.php?action=saveChairPosition',
        method: 'POST',
        data: {
            chair_id: sillonId,
            position_x: parseInt(x),
            position_y: parseInt(y)
        },
        success: function(response) {
            console.log(`📡 Posición del sillón ${sillonId} guardada en servidor`);
        },
        error: function(xhr, status, error) {
            console.warn(`⚠️ Guardado de servidor falló para sillón ${sillonId}, usando localStorage`);
        }
    });
}

// Función para cargar posiciones guardadas MEJORADA
function cargarPosicionesGuardadas() {
    console.log('📂 Cargando posiciones guardadas con precisión exacta...');
    
    // Cargar posiciones de sillones desde localStorage
    const savedPositions = JSON.parse(localStorage.getItem('sillonPositions') || '{}');
    aplicarPosicionesExactas(savedPositions);
    
    // Cargar posición de la estación
    const stationPosition = JSON.parse(localStorage.getItem('stationPosition') || '{}');
    if (stationPosition.x !== undefined && stationPosition.y !== undefined) {
        const station = jQuery('.nursing-station');
        if (station.length) {
            station.attr('data-x', stationPosition.x);
            station.attr('data-y', stationPosition.y);
            station.css({
                left: stationPosition.x + 'px',
                top: stationPosition.y + 'px',
                position: 'absolute'
            });
            console.log('🏥 Restaurada posición EXACTA de estación:', stationPosition);
        }
    }
}

// NUEVA función para aplicar posiciones de manera exacta y consistente
function aplicarPosicionesExactas(positions) {
    Object.keys(positions).forEach(sillonId => {
        const position = positions[sillonId];
        const sillon = jQuery(`.sillon-designer[data-sillon-id="${sillonId}"]`);
        
        if (sillon.length && position.x !== undefined && position.y !== undefined) {
            // Convertir a enteros para precisión
            const x = parseInt(position.x);
            const y = parseInt(position.y);
            
            // Asegurar posición EXACTA
            sillon.attr('data-x', x);
            sillon.attr('data-y', y);
            sillon.css({
                left: x + 'px',
                top: y + 'px',
                position: 'absolute'
            });
            console.log(`📍 Restaurada posición EXACTA del sillón ${sillonId}: ${x}, ${y}`);
        }
    });
}

// Otras funciones requeridas
function centrarVista() {
    console.log('Centrando vista...');
    const container = jQuery('.design-container')[0];
    if (container) {
        container.scrollTo({
            left: (container.scrollWidth - container.clientWidth) / 2,
            top: (container.scrollHeight - container.clientHeight) / 2,
            behavior: 'smooth'
        });
    }
}

// function cambiarModo(modo) {
//     console.log('Cambiando modo a:', modo);
//     modoEdicion = modo;
// }
// ^^^ COMENTADO: Segunda función duplicada - usar función del drag-alternative.js

function guardarLayout() {
    console.log('💾 Guardando layout completo con posiciones EXACTAS...');
    
    const sillones = [];
    jQuery('.sillon-designer').each(function() {
        const sillon = jQuery(this);
        const id = sillon.data('sillon-id');
        
        // Obtener posición EXACTA de los atributos data
        const x = parseInt(sillon.attr('data-x')) || 0;
        const y = parseInt(sillon.attr('data-y')) || 0;
        
        // También verificar posición CSS como respaldo
        const cssX = parseInt(sillon.css('left')) || 0;
        const cssY = parseInt(sillon.css('top')) || 0;
        
        // Usar la posición más precisa
        const finalX = x || cssX;
        const finalY = y || cssY;
        
        sillones.push({
            id: id,
            x: finalX,
            y: finalY
        });
        
        console.log(`📊 Sillón ${id}: data=(${x},${y}) css=(${cssX},${cssY}) final=(${finalX},${finalY})`);
    });
    
    // Guardar layout completo
    localStorage.setItem('layoutCompleto', JSON.stringify(sillones));
    
    // Actualizar todas las posiciones individuales también
    sillones.forEach(sillon => {
        let savedPositions = JSON.parse(localStorage.getItem('sillonPositions') || '{}');
        savedPositions[sillon.id] = { x: sillon.x, y: sillon.y };
        localStorage.setItem('sillonPositions', JSON.stringify(savedPositions));
    });
    
    if (typeof mostrarNotificacion === 'function') {
        mostrarNotificacion(`✅ Layout guardado con precisión exacta<br>📊 ${sillones.length} sillones con posiciones preservadas`, 'success');
    } else {
        alert(`✅ Layout guardado exitosamente con ${sillones.length} sillones y posiciones exactas`);
    }
    console.log('💾 Layout guardado con posiciones exactas:', sillones);
}

function resetearLayout() {
    if (confirm('¿Estás seguro de que quieres resetear el layout?')) {
        // Limpiar todas las posiciones guardadas
        localStorage.removeItem('sillonPositions');
        localStorage.removeItem('stationPosition');
        localStorage.removeItem('layoutCompleto');
        
        console.log('🧹 Layout reseteado, recargando página...');
        location.reload();
    }
}

function aplicarPlantilla() {
    console.log('Aplicando plantilla...');
}

// Inicializar al cargar la página
jQuery(document).ready(function() {
    setTimeout(function() {
        cargarPosicionesGuardadas();
    }, 1000);
});

// Función para ocultar notificaciones (para botón X)
function ocultarNotificacion() {
    const container = jQuery('#sistema-notificaciones');
    container.children('.alert').css('animation', 'slideOutRight 0.3s ease-in');
    setTimeout(() => {
        container.fadeOut(300);
    }, 200);
}

// NUEVA: Función para guardar posiciones definitivas
function guardarPosicionesDefinitivas() {
    console.log('🔒 Guardando posiciones DEFINITIVAS...');
    
    let posicionesExactas = {};
    let totalSillones = 0;
    
    jQuery('.sillon-designer').each(function() {
        const sillon = jQuery(this);
        const id = sillon.data('sillon-id');
        
        // Obtener posición más precisa
        const dataX = parseInt(sillon.attr('data-x'));
        const dataY = parseInt(sillon.attr('data-y'));
        const cssX = parseInt(sillon.css('left'));
        const cssY = parseInt(sillon.css('top'));
        
        const finalX = !isNaN(dataX) ? dataX : cssX;
        const finalY = !isNaN(dataY) ? dataY : cssY;
        
        posicionesExactas[id] = { x: finalX, y: finalY };
        totalSillones++;
        
        // Asegurar que la posición esté aplicada EXACTAMENTE
        sillon.attr('data-x', finalX);
        sillon.attr('data-y', finalY);
        sillon.css({
            left: finalX + 'px',
            top: finalY + 'px',
            position: 'absolute'
        });
        
        console.log(`📍 Sillón ${id}: Posición definitiva (${finalX}, ${finalY})`);
    });
    
    // Guardar en localStorage
    localStorage.setItem('sillonPositions', JSON.stringify(posicionesExactas));
    localStorage.setItem('posicionesDefinitivas', JSON.stringify(posicionesExactas));
    localStorage.setItem('ultimoGuardado', new Date().toISOString());
    
    // Mostrar confirmación detallada
    if (typeof mostrarNotificacion === 'function') {
        mostrarNotificacion(`
            <strong>🔒 POSICIONES GUARDADAS DEFINITIVAMENTE</strong><br>
            📊 ${totalSillones} sillones con coordenadas exactas<br>
            💾 Guardado en localStorage para persistencia<br>
            ✅ Las posiciones se mantendrán EXACTAMENTE como las configuraste
        `, 'success', 6000);
    } else {
        alert(`✅ Posiciones guardadas definitivamente!\n\n📊 ${totalSillones} sillones guardados\n🔒 Las posiciones se mantendrán exactamente como las configuraste`);
    }
    
    console.log('🔒 Posiciones definitivas guardadas:', posicionesExactas);
}

// NUEVA: Función para verificar posiciones actuales
function verificarPosiciones() {
    console.log('🔍 Verificando posiciones actuales...');
    
    let reporte = '🔍 REPORTE DE POSICIONES:\n\n';
    let totalSillones = 0;
    let inconsistencias = 0;
    
    jQuery('.sillon-designer').each(function() {
        const sillon = jQuery(this);
        const id = sillon.data('sillon-id');
        const nombre = sillon.find('.sillon-label').text();
        
        const dataX = parseInt(sillon.attr('data-x')) || 0;
        const dataY = parseInt(sillon.attr('data-y')) || 0;
        const cssX = parseInt(sillon.css('left')) || 0;
        const cssY = parseInt(sillon.css('top')) || 0;
        
        const consistente = (dataX === cssX && dataY === cssY);
        totalSillones++;
        
        if (!consistente) {
            inconsistencias++;
        }
        
        reporte += `📍 ${nombre} (ID:${id}):\n`;
        reporte += `   Data: (${dataX}, ${dataY})\n`;
        reporte += `   CSS:  (${cssX}, ${cssY})\n`;
        reporte += `   ${consistente ? '✅ Consistente' : '⚠️ Inconsistente'}\n\n`;
        
        console.log(`Sillón ${id}: data=(${dataX},${dataY}) css=(${cssX},${cssY}) consistente=${consistente}`);
    });
    
    // Verificar localStorage
    const savedPositions = JSON.parse(localStorage.getItem('sillonPositions') || '{}');
    const ultimoGuardado = localStorage.getItem('ultimoGuardado');
    
    reporte += `📊 RESUMEN:\n`;
    reporte += `• Total sillones: ${totalSillones}\n`;
    reporte += `• Inconsistencias: ${inconsistencias}\n`;
    reporte += `• Posiciones en localStorage: ${Object.keys(savedPositions).length}\n`;
    reporte += `• Último guardado: ${ultimoGuardado || 'Nunca'}\n`;
    
    alert(reporte);
    
    if (inconsistencias > 0) {
        if (confirm(`⚠️ Se detectaron ${inconsistencias} inconsistencias.\n\n¿Corregir automáticamente usando datos más precisos?`)) {
            corregirInconsistencias();
        }
    }
}

// NUEVA: Función para corregir inconsistencias de posiciones
function corregirInconsistencias() {
    console.log('🔧 Corrigiendo inconsistencias de posiciones...');
    
    let corregidos = 0;
    
    jQuery('.sillon-designer').each(function() {
        const sillon = jQuery(this);
        const id = sillon.data('sillon-id');
        
        const dataX = parseInt(sillon.attr('data-x')) || 0;
        const dataY = parseInt(sillon.attr('data-y')) || 0;
        const cssX = parseInt(sillon.css('left')) || 0;
        const cssY = parseInt(sillon.css('top')) || 0;
        
        // Usar la posición CSS como más precisa (drag and drop actualiza CSS primero)
        if (dataX !== cssX || dataY !== cssY) {
            sillon.attr('data-x', cssX);
            sillon.attr('data-y', cssY);
            corregidos++;
            console.log(`🔧 Corregido sillón ${id}: (${dataX},${dataY}) → (${cssX},${cssY})`);
        }
    });
    
    if (corregidos > 0) {
        // Guardar correcciones
        guardarPosicionesDefinitivas();
        
        if (typeof mostrarNotificacion === 'function') {
            mostrarNotificacion(`🔧 ${corregidos} inconsistencias corregidas y guardadas`, 'success');
        } else {
            alert(`✅ ${corregidos} inconsistencias corregidas y guardadas`);
        }
    }
}

</script>
</div>
</div>
