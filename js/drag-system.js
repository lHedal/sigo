// Sistema de drag and drop para sillones - Versión compatible
console.log('=== CARGANDO SISTEMA DRAG DROP ===');

// Variables globales
let modoEdicion = 'move';
let sillonSeleccionado = null;
let layoutModificado = false;

// Función principal para inicializar drag and drop
function initializeDragAndDrop() {
    console.log('🔄 Inicializando drag and drop...');
    
    // Verificar que jQuery UI esté disponible con mejor detección
    if (typeof jQuery === 'undefined') {
        console.error('❌ jQuery no disponible');
        return;
    }
    
    if (typeof jQuery.ui === 'undefined' && typeof jQuery.fn.draggable === 'undefined') {
        console.error('❌ jQuery UI no disponible, esperando...');
        // Solo intentar 3 veces más para evitar bucle infinito
        if (!window.jqueryUIRetries) window.jqueryUIRetries = 0;
        if (window.jqueryUIRetries < 3) {
            window.jqueryUIRetries++;
            setTimeout(initializeDragAndDrop, 2000);
        } else {
            console.error('❌ jQuery UI no se pudo cargar después de varios intentos');
        }
        return;
    }
    
    console.log('✅ jQuery UI detectado, iniciando drag...');
    
    // Reset del contador
    window.jqueryUIRetries = 0;
    
    // Limpiar cualquier evento previo
    jQuery('.sillon-designer, .nursing-station').off('.draggable');
    
    // Destruir draggable existente
    if (jQuery('.sillon-designer').hasClass('ui-draggable')) {
        jQuery('.sillon-designer').draggable('destroy');
    }
    if (jQuery('.nursing-station').hasClass('ui-draggable')) {
        jQuery('.nursing-station').draggable('destroy');
    }
    
    // Configurar drag para sillones
    jQuery('.sillon-designer').each(function() {
        const $sillon = jQuery(this);
        console.log('⚙️ Configurando sillón:', $sillon.data('sillon-id'));
        
        try {
            $sillon.draggable({
                containment: "#design-area",
                grid: [10, 10],
                cursor: "move",
                zIndex: 1000,
                start: function(event, ui) {
                    console.log('🚀 INICIO ARRASTRE - Sillón:', jQuery(this).data('sillon-id'));
                    jQuery(this).addClass('dragging');
                },
                drag: function(event, ui) {
                    // Actualizar posición en tiempo real
                    jQuery(this).attr('data-x', ui.position.left);
                    jQuery(this).attr('data-y', ui.position.top);
                },
                stop: function(event, ui) {
                    console.log('🎯 FIN ARRASTRE - Sillón:', jQuery(this).data('sillon-id'), 'Posición:', ui.position);
                    jQuery(this).removeClass('dragging');
                    
                    // Guardar posición usando la función del archivo principal
                    const sillonId = jQuery(this).data('sillon-id');
                    if (sillonId && typeof guardarPosicionSillon === 'function') {
                        guardarPosicionSillon(sillonId, ui.position.left, ui.position.top);
                    } else {
                        console.log('💾 Guardando posición del sillón', sillonId);
                        // Fallback: guardar directamente en localStorage
                        let savedPositions = JSON.parse(localStorage.getItem('sillonPositions') || '{}');
                        savedPositions[sillonId] = { x: ui.position.left, y: ui.position.top };
                        localStorage.setItem('sillonPositions', JSON.stringify(savedPositions));
                    }
                    layoutModificado = true;
                }
            });
        } catch (e) {
            console.error('Error configurando draggable para sillón:', e);
        }
    });
    
    // Configurar drag para estación de enfermería
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
                    
                    // Guardar posición de la estación
                    let stationPosition = { x: ui.position.left, y: ui.position.top };
                    localStorage.setItem('stationPosition', JSON.stringify(stationPosition));
                    console.log('🏥 Posición de estación guardada:', stationPosition);
                    
                    layoutModificado = true;
                }
            });
        } catch (e) {
            console.error('Error configurando draggable para estación:', e);
        }
    });
    
    console.log('✅ Drag and drop configurado correctamente');
}

// Sistema de inicialización robusto
jQuery(document).ready(function($) {
    console.log('📄 Documento listo - Inicializando sistema...');
    
    // Función simple para verificar y inicializar
    function tryInitialize() {
        if (typeof $.fn.draggable !== 'undefined') {
            console.log('✅ jQuery UI disponible, iniciando...');
            
            // Botón de test
            $('#test-drag').click(function() {
                console.log('🧪 Reiniciando funcionalidad de arrastre...');
                initializeDragAndDrop();
                alert('¡Funcionalidad de arrastre reiniciada!');
            });
            
            // Inicializar después de un breve delay
            setTimeout(function() {
                console.log('⏰ Ejecutando inicialización...');
                initializeDragAndDrop();
                
                // Verificar elementos
                const sillones = $('.sillon-designer').length;
                const stations = $('.nursing-station').length;
                console.log(`📊 Elementos encontrados: ${sillones} sillones, ${stations} estaciones`);
                
            }, 500);
        } else {
            console.log('⏳ jQuery UI aún no disponible, esperando...');
            setTimeout(tryInitialize, 1000);
        }
    }
    
    // Iniciar verificación
    tryInitialize();
});

// Inicialización adicional cuando la ventana termina de cargar
window.addEventListener('load', function() {
    console.log('🌐 Ventana cargada completamente');
    
    // Solo intentar una vez más después de que todo esté cargado
    setTimeout(function() {
        if (typeof jQuery !== 'undefined' && typeof jQuery.fn.draggable !== 'undefined') {
            console.log('🔄 Ejecutando re-inicialización de seguridad...');
            initializeDragAndDrop();
        } else {
            console.log('⚠️ jQuery UI aún no disponible en window.load');
        }
    }, 1000);
});

console.log('✅ Script de drag drop cargado');
