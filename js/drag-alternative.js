// Sistema de drag and drop - Versión autosuficiente
console.log('=== CARGANDO SISTEMA DRAG DROP ALTERNATIVO ===');

// === SISTEMA DE NOTIFICACIONES INTEGRADO ===
function mostrarNotificacion(mensaje, tipo = 'info', duracion = 4000) {
    console.log('📢 Notificación:', tipo, mensaje);
    
    const container = jQuery('#sistema-notificaciones');
    const alertContainer = jQuery('#notificacion-container');
    const icon = jQuery('#notificacion-icon');
    const text = jQuery('#notificacion-text');
    
    // Configurar iconos y estilos según el tipo
    let iconClass = '';
    let alertClass = 'alert-info';
    
    switch(tipo) {
        case 'success':
            iconClass = 'fa fa-check-circle';
            alertClass = 'alert-success';
            break;
        case 'error':
            iconClass = 'fa fa-exclamation-triangle';
            alertClass = 'alert-danger';
            break;
        case 'warning':
            iconClass = 'fa fa-exclamation-circle';
            alertClass = 'alert-warning';
            break;
        case 'info':
        default:
            iconClass = 'fa fa-info-circle';
            alertClass = 'alert-info';
            break;
    }
    
    // Actualizar contenido
    icon.attr('class', iconClass);
    text.html(mensaje);
    alertContainer.attr('class', `alert ${alertClass}`);
    
    // Mostrar notificación
    container.fadeIn(300);
    
    // Auto-ocultar después del tiempo especificado
    if (duracion > 0) {
        setTimeout(() => {
            ocultarNotificacion();
        }, duracion);
    }
}

function ocultarNotificacion() {
    jQuery('#sistema-notificaciones').fadeOut(300);
}

// Variables globales
let modoEdicion = 'move';
let sillonSeleccionado = null;
let layoutModificado = false;

// Funciones para aplicar propiedades (requeridas por botones onclick)
function aplicarPropiedades() {
    console.log('🎨 Aplicando propiedades al sillón:', sillonSeleccionado);
    
    if (!sillonSeleccionado) {
        mostrarNotificacion('Por favor selecciona un sillón primero', 'warning');
        return;
    }
    
    // Obtener valores del formulario
    const nombre = jQuery('#prop-sillon-nombre').val() || `Sillón ${sillonSeleccionado}`;
    const color = jQuery('#prop-sillon-color').val() || 'default';
    const rotacion = jQuery('#prop-sillon-rotacion').val() || 0;
    
    // Aplicar al sillón seleccionado
    const sillon = jQuery(`.sillon-designer[data-sillon-id="${sillonSeleccionado}"]`);
    
    if (sillon.length) {
        // Actualizar nombre
        sillon.find('.sillon-label').text(nombre);
        
        // Limpiar clases de color previas
        sillon.removeClass('color-green color-orange color-purple color-red color-default');
        
        // Aplicar nueva clase de color
        if (color !== 'default') {
            sillon.addClass('color-' + color);
        }
        
        // Aplicar rotación
        if (rotacion && rotacion !== '0') {
            sillon.css('transform', `rotate(${rotacion}deg)`);
            sillon.attr('data-rotation', rotacion);
        } else {
            sillon.css('transform', '');
            sillon.removeAttr('data-rotation');
        }
        
        console.log('✅ Propiedades aplicadas exitosamente');
        
        // Guardar cambios
        layoutModificado = true;
        if (typeof guardarLayout === 'function') {
            guardarLayout();
        }
        
        mostrarNotificacion('¡Propiedades aplicadas correctamente!', 'success');
    } else {
        mostrarNotificacion('Error: No se encontró el sillón seleccionado', 'error');
    }
}

function aplicarPropiedadesStation() {
    console.log('🏥 Aplicando propiedades a la estación de enfermería');
    
    // Obtener valores del formulario
    const nombre = jQuery('#prop-station-nombre').val() || 'Estación de Enfermería';
    const color = jQuery('#prop-station-color').val() || 'default';
    
    // Aplicar a la estación
    const station = jQuery('.nursing-station');
    
    if (station.length) {
        // Actualizar nombre
        station.find('.station-label').text(nombre);
        
        // Limpiar clases de color previas
        station.removeClass('color-green color-orange color-purple color-red color-default');
        
        // Aplicar nueva clase de color
        if (color !== 'default') {
            station.addClass('color-' + color);
        }
        
        console.log('✅ Propiedades de estación aplicadas exitosamente');
        
        // Guardar cambios
        layoutModificado = true;
        if (typeof guardarLayout === 'function') {
            guardarLayout();
        }
        
        mostrarNotificacion('¡Propiedades de estación aplicadas correctamente!', 'success');
    } else {
        mostrarNotificacion('Error: No se encontró la estación de enfermería', 'error');
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
    
    // Cargar propiedades actuales del sillón
    const nombre = sillon.find('.sillon-label').text() || `Sillón ${sillonId}`;
    const rotacion = sillon.attr('data-rotation') || 0;
    let color = 'default';
    
    // Detectar color actual
    const colorClasses = ['green', 'orange', 'purple', 'red'];
    for (let c of colorClasses) {
        if (sillon.hasClass('color-' + c)) {
            color = c;
            break;
        }
    }
    
    // Actualizar formulario
    jQuery('#prop-sillon-nombre').val(nombre);
    jQuery('#prop-sillon-color').val(color);
    jQuery('#prop-sillon-rotacion').val(rotacion);
    
    console.log('📋 Propiedades cargadas:', { nombre, color, rotacion });
}

// ========== SISTEMA DE AÑADIR SILLONES ==========

// Variable para tracking de nuevos sillones
let contadorSillones = 0;

// Función para mostrar el formulario de nuevo sillón
function mostrarFormNuevoSillon() {
    console.log('📝 Mostrando formulario para nuevo sillón...');
    
    // Limpiar formulario
    jQuery('#nuevo-sillon-nombre').val('');
    jQuery('#nuevo-sillon-descripcion').val('');
    jQuery('#nuevo-sillon-x').val(200);
    jQuery('#nuevo-sillon-y').val(200);
    jQuery('#nuevo-sillon-color').val('default');
    
    // Mostrar modal
    jQuery('#modal-nuevo-sillon').modal('show');
}

// Función para crear un nuevo sillón REAL (en base de datos)
function crearNuevoSillon() {
    console.log('🛋️ Creando nuevo sillón en base de datos...');
    
    // Obtener valores del formulario
    const nombre = jQuery('#nuevo-sillon-nombre').val().trim();
    const descripcion = jQuery('#nuevo-sillon-descripcion').val().trim();
    const x = parseInt(jQuery('#nuevo-sillon-x').val()) || 200;
    const y = parseInt(jQuery('#nuevo-sillon-y').val()) || 200;
    const color = jQuery('#nuevo-sillon-color').val() || 'default';
    
    // Validaciones
    if (!nombre) {
        mostrarNotificacion('El nombre del sillón es requerido', 'warning');
        jQuery('#nuevo-sillon-nombre').focus();
        return;
    }
    
    if (nombre.length < 3) {
        mostrarNotificacion('El nombre debe tener al menos 3 caracteres', 'warning');
        jQuery('#nuevo-sillon-nombre').focus();
        return;
    }
    
    // Verificar si ya existe un sillón con el mismo nombre
    let nombreExiste = false;
    jQuery('.sillon-designer .sillon-label').each(function() {
        if (jQuery(this).text().toLowerCase() === nombre.toLowerCase()) {
            nombreExiste = true;
        }
    });
    
    if (nombreExiste) {
        if (!confirm(`⚠️ Ya existe un sillón con el nombre "${nombre}". ¿Crear de todos modos?`)) {
            return;
        }
    }
    
    // Mostrar loading
    const btnCrear = jQuery('#btn-crear-sillon');
    const textoOriginal = btnCrear.html();
    btnCrear.html('<i class="fa fa-spinner fa-spin"></i> Creando...').prop('disabled', true);
    
    // Enviar AJAX para crear sillón real
    jQuery.ajax({
        url: 'index.php?action=addchairfromdesigner',
        method: 'POST',
        data: {
            name: nombre,
            description: descripcion || `Sillón creado desde diseñador visual en posición ${x}, ${y}`
        },
        dataType: 'json',
        success: function(response) {
            console.log('✅ Respuesta del servidor:', response);
            
            if (response.success) {
                // Crear elemento visual con el ID real de la base de datos
                const nuevoSillonHTML = crearElementoSillonReal(
                    response.chair_id, 
                    response.chair.name, 
                    x, 
                    y, 
                    color
                );
                
                // Añadir al área de diseño
                jQuery('#design-area').append(nuevoSillonHTML);
                
                // Configurar drag and drop para el nuevo sillón
                configurarDragParaSillon(response.chair_id);
                
                // Cerrar modal
                jQuery('#modal-nuevo-sillon').modal('hide');
                
                // Seleccionar el nuevo sillón automáticamente
                setTimeout(() => {
                    seleccionarSillonDesigner(response.chair_id);
                }, 100);
                
                // Marcar layout como modificado
                layoutModificado = true;
                
                // Mostrar confirmación exitosa
                const mensaje = `<strong>¡Sillón creado exitosamente!</strong><br>
                📋 Nombre: ${response.chair.name} &nbsp; 🆔 ID: ${response.chair_id}<br>
                📍 Posición: ${x}, ${y} &nbsp; 🎨 Color: ${color}<br>
                💾 Guardado permanentemente en la base de datos`;
                
                mostrarNotificacion(mensaje, 'success', 6000);
                
                // Cambiar a modo mover
                cambiarModo('move');
                
            } else {
                mostrarNotificacion('Error al crear sillón: ' + response.message, 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('❌ Error AJAX:', error);
            mostrarNotificacion('Error de conexión al crear el sillón. Inténtalo de nuevo.', 'error');
        },
        complete: function() {
            // Restaurar botón
            btnCrear.html(textoOriginal).prop('disabled', false);
        }
    });
}

// Función para crear elemento HTML de sillón real (con ID de BD)
function crearElementoSillonReal(id, nombre, x, y, color) {
    const colorClass = color !== 'default' ? `color-${color}` : '';
    
    return `
        <div class="sillon-designer ${colorClass}" 
             data-sillon-id="${id}" 
             data-x="${x}" 
             data-y="${y}"
             style="left: ${x}px; top: ${y}px; position: absolute;"
             onclick="seleccionarSillonDesigner(${id})">
            <div class="sillon-visual-designer">
                <div class="sillon-back-designer"></div>
                <div class="sillon-seat-designer"></div>
            </div>
            <div class="sillon-label">${nombre}</div>
            <div class="sillon-id">#${id}</div>
            <div class="sillon-badge-real">REAL</div>
        </div>
    `;
}

// Función para generar un ID único para nuevos sillones
function generarIdSillon() {
    // Obtener todos los IDs existentes
    const idsExistentes = [];
    jQuery('.sillon-designer').each(function() {
        const id = jQuery(this).data('sillon-id');
        if (id) idsExistentes.push(parseInt(id));
    });
    
    // Encontrar el siguiente ID disponible
    let nuevoId = 1;
    while (idsExistentes.includes(nuevoId)) {
        nuevoId++;
    }
    
    console.log('🆔 Nuevo ID generado:', nuevoId);
    return nuevoId;
}

// Función para crear el HTML de un nuevo sillón
function crearElementoSillon(id, nombre, x, y, color) {
    const colorClass = color !== 'default' ? `color-${color}` : '';
    
    return `
        <div class="sillon-designer ${colorClass}" 
             data-sillon-id="${id}" 
             data-x="${x}" 
             data-y="${y}"
             style="left: ${x}px; top: ${y}px; position: absolute;"
             onclick="seleccionarSillonDesigner(${id})">
            <div class="sillon-visual-designer">
                <div class="sillon-back-designer"></div>
                <div class="sillon-seat-designer"></div>
            </div>
            <div class="sillon-label">${nombre}</div>
            <div class="sillon-id">#${id}</div>
        </div>
    `;
}

// Función para configurar drag and drop en un sillón específico
function configurarDragParaSillon(sillonId) {
    const sillon = jQuery(`.sillon-designer[data-sillon-id="${sillonId}"]`);
    
    if (typeof jQuery.fn.draggable !== 'undefined') {
        try {
            sillon.draggable({
                containment: "#design-area",
                grid: [10, 10],
                cursor: "move",
                zIndex: 1000,
                start: function(event, ui) {
                    console.log('🚀 INICIO ARRASTRE - Sillón:', sillonId);
                    jQuery(this).addClass('dragging');
                },
                drag: function(event, ui) {
                    jQuery(this).attr('data-x', ui.position.left);
                    jQuery(this).attr('data-y', ui.position.top);
                },
                stop: function(event, ui) {
                    console.log('🎯 FIN ARRASTRE - Sillón:', sillonId, 'Posición:', ui.position);
                    jQuery(this).removeClass('dragging');
                    
                    // Guardar posición
                    if (typeof guardarPosicionSillon === 'function') {
                        guardarPosicionSillon(sillonId, ui.position.left, ui.position.top);
                    } else {
                        // Fallback: guardar directamente
                        let savedPositions = JSON.parse(localStorage.getItem('sillonPositions') || '{}');
                        savedPositions[sillonId] = { x: ui.position.left, y: ui.position.top };
                        localStorage.setItem('sillonPositions', JSON.stringify(savedPositions));
                        console.log('💾 Posición guardada en localStorage');
                    }
                    layoutModificado = true;
                }
            });
            
            console.log('✅ Drag configurado para sillón:', sillonId);
        } catch (e) {
            console.error('Error configurando drag para sillón:', sillonId, e);
        }
    } else {
        console.warn('⚠️ jQuery UI no disponible para configurar drag en sillón:', sillonId);
    }
}

// Función mejorada para cambiar modo CON DEBUGGING
function cambiarModo(modo) {
    console.log('🔄 CAMBIANDO MODO:', modo);
    console.log('📊 Estado anterior:', modoEdicion);
    
    modoEdicion = modo;
    
    // Actualizar botones
    jQuery('.btn-mode').removeClass('active');
    jQuery(`#btn-${modo}`).addClass('active');
    
    console.log('✅ Botones actualizados, modo =', modo);
    
    // Actualizar área de diseño
    const designArea = jQuery('#design-area');
    designArea.removeClass('mode-move mode-add mode-delete mode-edit');
    designArea.addClass(`mode-${modo}`);
    
    console.log('✅ Clases CSS actualizadas');
    
    // Limpiar TODOS los event listeners previos
    designArea.off('click.addMode').off('click.deleteMode').off('click.debug');
    jQuery('.sillon-designer').off('click.deleteMode');
    
    console.log('✅ Event listeners previos limpiados completamente');
    
    // Configurar comportamiento según el modo
    switch(modo) {
        case 'add':
            console.log('➕ Configurando modo AÑADIR...');
            jQuery('#design-area').css('cursor', 'crosshair');
            configurarModoAñadir();
            break;
        case 'delete':
            console.log('🗑️ Configurando modo ELIMINAR...');
            jQuery('#design-area').css('cursor', 'not-allowed');
            configurarModoEliminar();
            break;
        case 'move':
        case 'edit':
        default:
            console.log('🔄 Configurando modo MOVER/EDITAR...');
            jQuery('#design-area').css('cursor', 'default');
            break;
    }
    
    // Mostrar información del modo actual
    mostrarInfoModo(modo);
    
    console.log('✅ MODO CAMBIADO EXITOSAMENTE A:', modo);
}

// Configurar modo añadir (click para colocar sillón)
function configurarModoAñadir() {
    console.log('➕ Configurando modo añadir...');
    
    // Remover TODOS los event listeners previos
    jQuery('#design-area').off('click.addMode').off('click.debug');
    
    // Añadir event listener ESPECÍFICO para modo añadir
    jQuery('#design-area').on('click.addMode', function(e) {
        // SIEMPRE ejecutar si estamos en modo add
        if (modoEdicion !== 'add') {
            console.log('❌ No estamos en modo add, ignorando click');
            return;
        }
        
        console.log('🖱️ CLICK EN MODO AÑADIR - PROCESANDO...');
        console.log('Target clicked:', e.target);
        console.log('Target classes:', e.target.className);
        console.log('Target ID:', e.target.id);
        
        // Prevenir propagación para evitar conflictos
        e.preventDefault();
        e.stopPropagation();
        
        // Obtener el elemento clickeado y sus padres
        const clickedElement = jQuery(e.target);
        const isDesignArea = e.target.id === 'design-area' || clickedElement.hasClass('design-area');
        const isBackgroundGrid = clickedElement.hasClass('background-grid');
        const isSillon = clickedElement.closest('.sillon-designer').length > 0;
        const isNursingStation = clickedElement.closest('.nursing-station').length > 0;
        
        console.log('🔍 Análisis del click:');
        console.log('- Es área de diseño:', isDesignArea);
        console.log('- Es grid de fondo:', isBackgroundGrid);
        console.log('- Es sillón:', isSillon);
        console.log('- Es estación:', isNursingStation);
        
        // Verificar si el click es válido para crear sillón
        const clickValido = (isDesignArea || isBackgroundGrid) && !isSillon && !isNursingStation;
        console.log('🎯 Click válido para crear sillón:', clickValido);
        
        if (clickValido) {
            console.log('✅ CREANDO SILLÓN...');
            
            const rect = this.getBoundingClientRect();
            const x = e.clientX - rect.left - 40; // Centrar sillón
            const y = e.clientY - rect.top - 50;
            
            console.log('📍 Posición calculada:', { x, y });
            console.log('📏 Rect del contenedor:', rect);
            console.log('🖱️ Posición del mouse:', { clientX: e.clientX, clientY: e.clientY });
            
            // Llamar a crear sillón
            crearSillonRapido(x, y);
            
        } else {
            console.log('❌ Click en elemento existente o inválido - no se crea sillón');
            
            if (isSillon) {
                console.log('ℹ️ Clickeaste en un sillón existente');
            } else if (isNursingStation) {
                console.log('ℹ️ Clickeaste en la estación de enfermería');
            } else {
                console.log('ℹ️ Área no válida para crear sillón');
            }
        }
    });
    
    console.log('✅ Event listener configurado para modo añadir');
    
    mostrarNotificacion(`
        <strong>➕ MODO AÑADIR ACTIVADO</strong><br>
        ✅ Click en área vacía = Crear sillón real<br>
        ✅ Botón "Nuevo" = Opciones avanzadas<br>
        💾 Se guardan permanentemente en BD
    `, 'info', 5000);
}

// Función para crear sillón rápido REAL (con click en área vacía)
function crearSillonRapido(x, y) {
    console.log('⚡ INICIANDO crearSillonRapido...');
    console.log('📍 Posición recibida:', { x, y });
    
    const nuevoId = generarIdSillon();
    const nombre = `Sillón ${nuevoId}`;
    
    console.log('🆔 ID generado:', nuevoId);
    console.log('🏷️ Nombre:', nombre);
    
    // Confirmar creación
    const confirmar = confirm(`➕ ¿Crear sillón "${nombre}" en posición ${Math.round(x)}, ${Math.round(y)}?\n\n✅ Se guardará permanentemente en la base de datos`);
    console.log('❓ Usuario confirmó:', confirmar);
    
    if (!confirmar) {
        console.log('❌ Usuario canceló la creación');
        return;
    }
    
    console.log('🚀 Enviando AJAX para crear sillón...');
    
    // Enviar AJAX para crear sillón real
    jQuery.ajax({
        url: 'index.php?action=addchairfromdesigner',
        method: 'POST',
        data: {
            name: nombre,
            description: `Sillón creado rápidamente desde diseñador visual en posición ${Math.round(x)}, ${Math.round(y)}`
        },
        dataType: 'json',
        beforeSend: function() {
            console.log('📤 Enviando datos al servidor...');
        },
        success: function(response) {
            console.log('✅ Respuesta del servidor recibida:', response);
            
            if (response.success) {
                console.log('🎉 Sillón creado exitosamente en BD');
                
                // Crear elemento visual con el ID real
                const nuevoSillonHTML = crearElementoSillonReal(
                    response.chair_id, 
                    response.chair.name, 
                    x, 
                    y, 
                    'default'
                );
                
                console.log('🎨 HTML generado:', nuevoSillonHTML);
                
                // Añadir al área de diseño
                jQuery('#design-area').append(nuevoSillonHTML);
                console.log('✅ Sillón añadido al DOM');
                
                // Configurar drag
                configurarDragParaSillon(response.chair_id);
                console.log('✅ Drag configurado');
                
                // Seleccionar automáticamente
                setTimeout(() => {
                    seleccionarSillonDesigner(response.chair_id);
                    console.log('✅ Sillón seleccionado automáticamente');
                }, 100);
                
                layoutModificado = true;
                
                // Mostrar confirmación
                mostrarNotificacion(`<strong>✅ Sillón "${response.chair.name}" creado!</strong><br>🆔 ID: ${response.chair_id} &nbsp; 💾 Guardado permanentemente`, 'success', 4000);
                
            } else {
                console.error('❌ Error del servidor:', response.message);
                mostrarNotificacion('Error al crear sillón: ' + response.message, 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('❌ Error AJAX completo:', { xhr, status, error });
            console.error('❌ Response text:', xhr.responseText);
            mostrarNotificacion('Error de conexión. Inténtalo de nuevo.', 'error');
        }
    });
}

// Configurar modo eliminar
function configurarModoEliminar() {
    console.log('🗑️ Configurando modo eliminar...');
    
    // Event listener para eliminar sillones con click
    jQuery('.sillon-designer').off('click.deleteMode');
    jQuery('.sillon-designer').on('click.deleteMode', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const sillonId = jQuery(this).data('sillon-id');
        const nombre = jQuery(this).find('.sillon-label').text();
        
        if (confirm(`🗑️ ¿Eliminar "${nombre}" (ID: ${sillonId})?`)) {
            jQuery(this).fadeOut(300, function() {
                jQuery(this).remove();
            });
            
            // Limpiar selección si era el sillón seleccionado
            if (sillonSeleccionado === sillonId) {
                sillonSeleccionado = null;
                jQuery('#sillon-properties').hide();
            }
            
            layoutModificado = true;
            console.log('🗑️ Sillón eliminado:', sillonId);
        }
    });
    
    mostrarNotificacion(`
        <strong>🗑️ MODO ELIMINAR ACTIVADO</strong><br>
        ❌ Haz click en cualquier sillón para eliminarlo<br>
        ⚠️ <strong>¡Ten cuidado!</strong> La eliminación es inmediata tras confirmar
    `, 'warning', 5000);
}

// Función para eliminar sillón seleccionado (botón) - REAL de la base de datos
function eliminarSillonSeleccionado() {
    if (!sillonSeleccionado) {
        mostrarNotificacion('Por favor selecciona un sillón primero', 'warning');
        return;
    }
    
    const sillon = jQuery(`.sillon-designer[data-sillon-id="${sillonSeleccionado}"]`);
    const nombre = sillon.find('.sillon-label').text();
    const esReal = sillon.find('.sillon-badge-real').length > 0;
    
    const tipoSillon = esReal ? 'REAL (se eliminará de la base de datos)' : 'temporal';
    
    if (!confirm(`🗑️ ¿Eliminar "${nombre}" (ID: ${sillonSeleccionado})?\n\n⚠️ Tipo: ${tipoSillon}\n⚠️ Esta acción NO se puede deshacer`)) {
        return;
    }
    
    if (esReal) {
        // Eliminar sillón real de la base de datos
        jQuery.ajax({
            url: `index.php?action=delchairfromdesigner&id=${sillonSeleccionado}`,
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Eliminar visualmente
                    sillon.fadeOut(300, function() {
                        sillon.remove();
                    });
                    
                    // Limpiar selección
                    sillonSeleccionado = null;
                    jQuery('#sillon-properties').hide();
                    
                    layoutModificado = true;
                    
                    mostrarNotificacion(`Sillón "${nombre}" eliminado exitosamente de la base de datos`, 'success');
                } else {
                    mostrarNotificacion('Error al eliminar sillón: ' + response.message, 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('❌ Error AJAX:', error);
                mostrarNotificacion('Error de conexión al eliminar el sillón', 'error');
            }
        });
    } else {
        // Eliminar sillón temporal solo visualmente
        sillon.fadeOut(300, function() {
            sillon.remove();
        });
        
        sillonSeleccionado = null;
        jQuery('#sillon-properties').hide();
        layoutModificado = true;
        
        mostrarNotificacion(`Sillón temporal "${nombre}" eliminado`, 'success');
    }
}

// Función para mostrar información del modo actual
function mostrarInfoModo(modo) {
    const infoTextos = {
        'move': '🔄 MODO MOVER: Arrastra sillones para reposicionarlos',
        'add': '➕ MODO AÑADIR: Click en área vacía para crear sillón real o usa "Nuevo" para opciones avanzadas',
        'delete': '🗑️ MODO ELIMINAR: Click en cualquier sillón para eliminarlo',
        'edit': '✏️ MODO EDITAR: Selecciona sillón para editar propiedades'
    };
    
    const infoTexto = infoTextos[modo] || 'Modo desconocido';
    
    // Mostrar en consola
    console.log('ℹ️', infoTexto);
    
    // Mostrar en interfaz
    const infoContainer = jQuery('#modo-info-container');
    const infoElement = jQuery('#modo-info');
    
    if (infoContainer.length && infoElement.length) {
        // Actualizar clases según el modo
        infoContainer.removeClass('alert-info alert-success alert-warning alert-danger');
        
        switch(modo) {
            case 'move':
            case 'edit':
                infoContainer.addClass('alert-info');
                break;
            case 'add':
                infoContainer.addClass('alert-success');
                break;
            case 'delete':
                infoContainer.addClass('alert-danger');
                break;
        }
        
        // Actualizar texto y mostrar
        infoElement.text(infoTexto);
        infoContainer.show();
        
        // Auto-ocultar después de 5 segundos (excepto en modo add/delete)
        if (modo === 'move' || modo === 'edit') {
            setTimeout(() => {
                infoContainer.fadeOut(1000);
            }, 5000);
        }
    }
}

// ========== FUNCIONES DE AYUDA Y UTILIDADES ==========

// Función para mostrar ayuda contextual
function mostrarAyuda() {
    const ayudaTexto = `🏥 SISTEMA DE GESTIÓN DE SILLONES - AYUDA

📋 MODOS DE TRABAJO:
• 🔄 MOVER: Arrastra sillones para reposicionarlos
• ➕ AÑADIR: Click en área vacía o botón "Nuevo" para crear sillones REALES
• 🗑️ ELIMINAR: Click en sillón para eliminarlo
• ✏️ EDITAR: Selecciona sillón para modificar propiedades

🎯 CREAR SILLONES REALES:
• Click en área vacía = Sillón rápido (se guarda en BD)
• Botón "Nuevo" = Sillón personalizado (se guarda en BD)
• 💾 TODOS los sillones se guardan permanentemente
• ✅ Aparecerán en TODAS las vistas del sistema

🎨 PERSONALIZACIÓN:
• Cambia colores en panel de propiedades
• Rota sillones 0°, 90°, 180°, 270°
• Renombra sillones fácilmente

💾 PERSISTENCIA:
• Sillones guardados en base de datos
• Posiciones guardadas automáticamente
• Sincronización con todo el sistema

🚨 IMPORTANTE:
• Los sillones creados son PERMANENTES
• Se pueden usar para reservas reales
• Eliminarlos los borra de la base de datos

¿Necesitas más ayuda? Consulta la documentación completa.`;

    mostrarNotificacion(ayudaTexto.replace(/\n/g, '<br>'), 'info', 8000);
}

// Función para resetear todo el layout
function resetearLayout() {
    if (!confirm('⚠️ ¿Resetear todo el layout?\n\n❌ Esto eliminará TODOS los sillones añadidos\n❌ Se perderán todas las posiciones personalizadas\n\n¿Continuar?')) {
        return;
    }
    
    if (!confirm('🚨 ÚLTIMA CONFIRMACIÓN\n\n¿Estás SEGURO de que quieres resetear todo?')) {
        return;
    }
    
    // Limpiar localStorage
    localStorage.removeItem('sillonPositions');
    localStorage.removeItem('stationPosition');
    
    // Eliminar sillones añadidos dinámicamente y resetear posiciones
    jQuery('.sillon-designer').each(function() {
        const sillonId = jQuery(this).data('sillon-id');
        // Si es un sillón añadido dinámicamente (ID alto), eliminarlo
        if (parseInt(sillonId) > 20) {
            jQuery(this).remove();
        } else {
            // Resetear posición de sillones originales
            const index = parseInt(sillonId) - 1;
            const x = 100 + (index % 4) * 150;
            const y = 100 + Math.floor(index / 4) * 120;
            
            jQuery(this).css({
                left: x + 'px',
                top: y + 'px'
            });
            jQuery(this).attr('data-x', x).attr('data-y', y);
        }
    });
    
    // Resetear estación de enfermería
    jQuery('.nursing-station').css({
        left: '350px',
        top: '250px'
    });
    jQuery('.nursing-station').attr('data-x', 350).attr('data-y', 250);
    
    // Limpiar selección
    sillonSeleccionado = null;
    jQuery('#sillon-properties').hide();
    jQuery('.sillon-designer').removeClass('selected');
    
    layoutModificado = false;
    
    console.log('🔄 Layout reseteado completamente');
    alert('✅ Layout reseteado exitosamente\n\n🔄 Todas las posiciones han vuelto a su estado original');
}

// Función para contar elementos en el layout
function mostrarEstadisticas() {
    const totalSillones = jQuery('.sillon-designer').length;
    const sillonesOriginales = jQuery('.sillon-designer').filter(function() {
        return parseInt(jQuery(this).data('sillon-id')) <= 20;
    }).length;
    const sillonesAñadidos = totalSillones - sillonesOriginales;
    
    const colores = {
        'default': 0,
        'green': 0,
        'orange': 0,
        'purple': 0,
        'red': 0
    };
    
    jQuery('.sillon-designer').each(function() {
        const elemento = jQuery(this);
        let color = 'default';
        
        if (elemento.hasClass('color-green')) color = 'green';
        else if (elemento.hasClass('color-orange')) color = 'orange';
        else if (elemento.hasClass('color-purple')) color = 'purple';
        else if (elemento.hasClass('color-red')) color = 'red';
        
        colores[color]++;
    });
    
    const estadisticas = `📊 ESTADÍSTICAS DEL LAYOUT

🛋️ SILLONES:
• Total: ${totalSillones}
• Originales: ${sillonesOriginales}
• Añadidos: ${sillonesAñadidos}

🎨 DISTRIBUCIÓN POR COLORES:
• 🔵 Azul: ${colores.default}
• 🟢 Verde: ${colores.green}
• 🟠 Naranja: ${colores.orange}
• 🟣 Púrpura: ${colores.purple}
• 🔴 Rojo: ${colores.red}

📍 LAYOUT:
• Modificado: ${layoutModificado ? 'Sí' : 'No'}
• Modo actual: ${modoEdicion.toUpperCase()}`;

    alert(estadisticas);
}

// Agregar botones de utilidades si no existen
function agregarBotonesUtilidades() {
    // Verificar si ya existen
    if (jQuery('#btn-ayuda').length > 0) return;
    
    // Crear botones adicionales
    const botonesHTML = `
        <div class="btn-group" style="margin-left: 10px;">
            <button type="button" class="btn btn-sm btn-info" onclick="mostrarAyuda()" id="btn-ayuda">
                <i class="fa fa-question-circle"></i> Ayuda
            </button>
            <button type="button" class="btn btn-sm btn-warning" onclick="mostrarEstadisticas()" id="btn-stats">
                <i class="fa fa-bar-chart"></i> Stats
            </button>
            <button type="button" class="btn btn-sm btn-danger" onclick="resetearLayout()" id="btn-reset">
                <i class="fa fa-refresh"></i> Reset
            </button>
            <button type="button" class="btn btn-sm btn-success" onclick="testModoAñadir()" id="btn-test-add">
                <i class="fa fa-plus-circle"></i> Test Add
            </button>
            <button type="button" class="btn btn-sm btn-primary" onclick="testCrearSillon()" id="btn-test-create">
                <i class="fa fa-plus"></i> Test Create
            </button>
        </div>
    `;
    
    // Añadir a la sección de herramientas
    jQuery('.box-tools').first().append(botonesHTML);
    
    console.log('🔧 Botones de utilidades añadidos');
}

// ========== FUNCIÓN DE TEST PARA MODO AÑADIR ==========

function testModoAñadir() {
    console.log('🧪 INICIANDO TEST DE MODO AÑADIR');
    
    // Verificar estado actual
    console.log('📊 Estado actual:');
    console.log('- Modo:', modoEdicion);
    console.log('- jQuery disponible:', typeof jQuery !== 'undefined');
    console.log('- Design area existe:', jQuery('#design-area').length);
    
    // Cambiar a modo añadir
    console.log('🔄 Cambiando a modo añadir...');
    cambiarModo('add');
    
    // Verificar que el modo cambió
    console.log('✅ Modo actual después del cambio:', modoEdicion);
    
    // Verificar event listeners
    const designArea = jQuery('#design-area')[0];
    if (designArea) {
        const events = jQuery._data(designArea, "events");
        console.log('📋 Event listeners registrados:', events);
        
        if (events && events.click) {
            console.log('✅ Click listeners encontrados:', events.click.length);
            events.click.forEach((handler, index) => {
                console.log(`  ${index}: namespace="${handler.namespace}"`);
            });
        } else {
            console.log('❌ No se encontraron click listeners');
        }
    }
    
    // Test directo del event listener
    console.log('🖱️ Simulando click en posición 300,200...');
    const rect = designArea.getBoundingClientRect();
    const evento = new MouseEvent('click', {
        clientX: rect.left + 300,
        clientY: rect.top + 200,
        bubbles: true,
        cancelable: true
    });
    
    setTimeout(() => {
        designArea.dispatchEvent(evento);
        console.log('✅ Evento dispatched');
    }, 500);
    
    // Mostrar instrucciones
    alert(`🧪 TEST DE MODO AÑADIR EJECUTADO

📊 Información en consola
🖱️ Click simulado enviado
⏰ Revisa los logs en la consola

💡 Si no funciona:
1. Verifica que esté en modo 'add'
2. Haz click manual en área vacía
3. Revisa errores en consola

🔧 PRUEBA MANUAL:
- Ejecuta: crearSillonRapido(300, 200)
- Debe aparecer confirmación`);
}

// ========== FUNCIÓN DE TEST DIRECTO ==========

function testCrearSillon() {
    console.log('🧪 TEST DIRECTO - Creando sillón en 300,200');
    crearSillonRapido(300, 200);
}

// Función para cargar jQuery UI dinámicamente si no está disponible
function loadJQueryUI() {
    return new Promise((resolve, reject) => {
        if (typeof jQuery !== 'undefined' && typeof jQuery.fn.draggable !== 'undefined') {
            console.log('✅ jQuery UI ya disponible');
            resolve();
            return;
        }
        
        console.log('📦 Cargando jQuery UI dinámicamente...');
        
        // Cargar CSS
        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = 'https://code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css';
        document.head.appendChild(link);
        
        // Cargar JS
        const script = document.createElement('script');
        script.src = 'https://code.jquery.com/ui/1.12.1/jquery-ui.min.js';
        script.onload = function() {
            console.log('✅ jQuery UI cargado exitosamente');
            resolve();
        };
        script.onerror = function() {
            console.error('❌ Error cargando jQuery UI');
            reject();
        };
        document.head.appendChild(script);
    });
}

// Función principal para inicializar drag and drop
function initializeDragAndDrop() {
    console.log('🔄 Inicializando drag and drop...');
    
    if (typeof jQuery === 'undefined') {
        console.error('❌ jQuery no disponible');
        return;
    }
    
    if (typeof jQuery.fn.draggable === 'undefined') {
        console.log('⚠️ draggable no disponible, intentando cargar jQuery UI...');
        loadJQueryUI().then(() => {
            initializeDragAndDrop();
        }).catch(() => {
            console.error('❌ No se pudo cargar jQuery UI');
        });
        return;
    }
    
    console.log('✅ jQuery UI detectado, configurando elementos...');
    
    // Limpiar eventos previos
    jQuery('.sillon-designer, .nursing-station').off('.draggable');
    
    // Destruir draggable existente
    try {
        if (jQuery('.sillon-designer').hasClass('ui-draggable')) {
            jQuery('.sillon-designer').draggable('destroy');
        }
        if (jQuery('.nursing-station').hasClass('ui-draggable')) {
            jQuery('.nursing-station').draggable('destroy');
        }
    } catch (e) {
        console.log('No hay elementos draggable previos que destruir');
    }
    
    // Configurar drag para sillones
    jQuery('.sillon-designer').each(function() {
        const $sillon = jQuery(this);
        const sillonId = $sillon.data('sillon-id');
        console.log('⚙️ Configurando sillón:', sillonId);
        
        try {
            $sillon.draggable({
                containment: "#design-area",
                grid: [10, 10],
                cursor: "move",
                zIndex: 1000,
                start: function(event, ui) {
                    console.log('🚀 INICIO ARRASTRE - Sillón:', sillonId);
                    jQuery(this).addClass('dragging');
                },
                drag: function(event, ui) {
                    jQuery(this).attr('data-x', ui.position.left);
                    jQuery(this).attr('data-y', ui.position.top);
                },
                stop: function(event, ui) {
                    console.log('🎯 FIN ARRASTRE - Sillón:', sillonId, 'Posición:', ui.position);
                    jQuery(this).removeClass('dragging');
                    
                    // Guardar posición
                    if (typeof guardarPosicionSillon === 'function') {
                        guardarPosicionSillon(sillonId, ui.position.left, ui.position.top);
                    } else {
                        // Fallback: guardar directamente
                        let savedPositions = JSON.parse(localStorage.getItem('sillonPositions') || '{}');
                        savedPositions[sillonId] = { x: ui.position.left, y: ui.position.top };
                        localStorage.setItem('sillonPositions', JSON.stringify(savedPositions));
                        console.log('💾 Posición guardada en localStorage');
                    }
                    layoutModificado = true;
                }
            });
        } catch (e) {
            console.error('Error configurando draggable para sillón:', sillonId, e);
        }
    });
    
    // Configurar drag para estación
    jQuery('.nursing-station').each(function() {
        const $station = jQuery(this);
        console.log('⚙️ Configurando estación de enfermería');
        
        try {
            $station.draggable({
                containment: "#design-area",
                grid: [10, 10],
                cursor: "move",
                zIndex: 1000,
                start: function(event, ui) {
                    console.log('🚀 INICIO ARRASTRE - Estación');
                    jQuery(this).addClass('dragging');
                },
                drag: function(event, ui) {
                    jQuery(this).attr('data-x', ui.position.left);
                    jQuery(this).attr('data-y', ui.position.top);
                },
                stop: function(event, ui) {
                    console.log('🎯 FIN ARRASTRE - Estación. Posición:', ui.position);
                    jQuery(this).removeClass('dragging');
                    
                    let stationPosition = { x: ui.position.left, y: ui.position.top };
                    localStorage.setItem('stationPosition', JSON.stringify(stationPosition));
                    console.log('🏥 Posición de estación guardada');
                    
                    layoutModificado = true;
                }
            });
        } catch (e) {
            console.error('Error configurando draggable para estación:', e);
        }
    });
    
    console.log('✅ Drag and drop configurado correctamente');
}

// Inicialización cuando el documento esté listo
if (typeof jQuery !== 'undefined') {
    jQuery(document).ready(function($) {
        console.log('📄 Documento listo - Sistema alternativo...');
        
        // Botón de test
        $('#test-drag').click(function() {
            console.log('🧪 Reiniciando funcionalidad de arrastre...');
            initializeDragAndDrop();
            alert('¡Sistema alternativo reiniciado!');
        });
        
        // Configurar otros botones y eventos globales
        setupGlobalEvents();
        
        // Configurar modo inicial
        setTimeout(() => {
            cambiarModo('move'); // Iniciar en modo mover
        }, 200);
        
        // Intentar cargar jQuery UI e inicializar
        loadJQueryUI().then(() => {
            setTimeout(() => {
                console.log('⏰ Ejecutando inicialización alternativa...');
                initializeDragAndDrop();
                
                const sillones = $('.sillon-designer').length;
                const stations = $('.nursing-station').length;
                console.log(`📊 Elementos encontrados: ${sillones} sillones, ${stations} estaciones`);
                
                // Cargar posiciones guardadas
                if (typeof cargarPosicionesGuardadas === 'function') {
                    cargarPosicionesGuardadas();
                }
                
                // Agregar botones de utilidades
                agregarBotonesUtilidades();
            }, 500);
        }).catch(() => {
            console.error('❌ No se pudo inicializar el sistema alternativo');
        });
    });
} else {
    console.error('❌ jQuery no disponible para sistema alternativo');
}

// Configurar eventos globales adicionales
function setupGlobalEvents() {
    console.log('⚙️ Configurando eventos globales...');
    
    // Hacer las funciones globalmente accesibles
    window.aplicarPropiedades = aplicarPropiedades;
    window.aplicarPropiedadesStation = aplicarPropiedadesStation;
    window.seleccionarSillonDesigner = seleccionarSillonDesigner;
    window.mostrarFormNuevoSillon = mostrarFormNuevoSillon;
    window.crearNuevoSillon = crearNuevoSillon;
    window.cambiarModo = cambiarModo;
    window.eliminarSillonSeleccionado = eliminarSillonSeleccionado;
    window.mostrarAyuda = mostrarAyuda;
    window.mostrarEstadisticas = mostrarEstadisticas;
    window.resetearLayout = resetearLayout;
    window.testModoAñadir = testModoAñadir;
    window.testCrearSillon = testCrearSillon;
    
    // Event listeners adicionales
    jQuery(document).on('click', '.sillon-designer', function() {
        const sillonId = jQuery(this).data('sillon-id');
        if (sillonId) {
            seleccionarSillonDesigner(sillonId);
        }
    });
    
    // Configurar botones de modo
    jQuery('#btn-mode-move').click(function() {
        modoEdicion = 'move';
        jQuery('.btn-mode').removeClass('active');
        jQuery(this).addClass('active');
        console.log('🔄 Modo cambiado a: move');
    });
    
    jQuery('#btn-mode-edit').click(function() {
        modoEdicion = 'edit';
        jQuery('.btn-mode').removeClass('active');
        jQuery(this).addClass('active');
        console.log('✏️ Modo cambiado a: edit');
    });
    
    // DEBUGGING: Añadir listeners para todos los botones de modo
    jQuery('#btn-move').click(function() {
        console.log('🖱️ Click en botón MOVER detectado');
        cambiarModo('move');
    });
    
    jQuery('#btn-add').click(function() {
        console.log('🖱️ Click en botón AÑADIR detectado');
        cambiarModo('add');
    });
    
    jQuery('#btn-delete').click(function() {
        console.log('🖱️ Click en botón ELIMINAR detectado');
        cambiarModo('delete');
    });
    
    // Test de área de diseño - SOLO PARA DEBUG, REMOVER EN PRODUCCIÓN
    // jQuery('#design-area').on('click.debug', function(e) {
    //     console.log('🖱️ CLICK DETECTADO EN DESIGN-AREA');
    //     console.log('Modo actual:', modoEdicion);
    //     console.log('Target:', e.target);
    //     console.log('Coordenadas:', e.clientX, e.clientY);
    // });
    
    // Funciones de plantilla
    jQuery('[data-template]').click(function() {
        const template = jQuery(this).data('template');
        if (typeof SillonManager !== 'undefined' && SillonManager.applyTemplate) {
            SillonManager.applyTemplate(template);
        } else {
            console.log('📐 Aplicando plantilla básica:', template);
            aplicarPlantillaBasica(template);
        }
    });
    
    console.log('✅ Eventos globales configurados');
}

// Función básica de plantillas si SillonManager no está disponible
function aplicarPlantillaBasica(template) {
    const sillones = jQuery('.sillon-designer');
    const centerX = 400;
    const centerY = 300;
    
    switch(template) {
        case 'hospital':
            sillones.each(function(index) {
                const row = Math.floor(index / 4);
                const col = index % 4;
                const x = centerX - 150 + (col * 100);
                const y = centerY - 100 + (row * 120);
                
                jQuery(this).css({ left: x + 'px', top: y + 'px' });
                jQuery(this).attr('data-x', x).attr('data-y', y);
            });
            break;
            
        case 'cine':
            sillones.each(function(index) {
                const row = Math.floor(index / 6);
                const col = index % 6;
                const x = centerX - 210 + (col * 70);
                const y = 80 + (row * 90);
                
                jQuery(this).css({ left: x + 'px', top: y + 'px' });
                jQuery(this).attr('data-x', x).attr('data-y', y);
            });
            break;
            
        default:
            console.log('Plantilla no reconocida:', template);
    }
    
    layoutModificado = true;
    console.log('📐 Plantilla aplicada:', template);
}

console.log('✅ Script alternativo cargado');
