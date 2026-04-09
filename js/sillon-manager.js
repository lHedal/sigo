/**
 * Sistema Visual de Gestión de Sillones Oncológicos
 * Funcionalidades tipo cine para la gestión intuitiva de sillones
 * 
 * @author Sistema Oncológico
 * @version 1.0
 */

// Configuración global del sistema
const SillonManager = {
    // Configuraciones
    config: {
        autoRefresh: true,
        refreshInterval: 120000, // 2 minutos
        animationDuration: 300,
        gridSize: 20,
        snapToGrid: true
    },
    
    // Estados del sistema
    state: {
        selectedSillon: null,
        editMode: 'move',
        layoutChanged: false,
        availabilityData: {}
    },
    
    // Inicialización del sistema
    init: function() {
        this.initEventListeners();
        this.loadLayoutData();
        this.startAutoRefresh();
        console.log('Sistema de gestión visual de sillones inicializado');
    },
    
    // Configurar event listeners globales
    initEventListeners: function() {
        // Escuchar cambios en formularios de reservas
        $(document).on('change', '#date_at, #time_at', function() {
            SillonManager.updateAvailability();
        });
        
        // Keyboard shortcuts
        $(document).on('keydown', function(e) {
            if (e.ctrlKey) {
                switch(e.keyCode) {
                    case 77: // Ctrl+M - Abrir mapa
                        e.preventDefault();
                        SillonManager.openMapView();
                        break;
                    case 76: // Ctrl+L - Configurar layout
                        e.preventDefault();
                        SillonManager.openLayoutConfig();
                        break;
                }
            }
        });
    },
    
    // Cargar datos del layout desde localStorage o servidor
    loadLayoutData: function() {
        try {
            const savedLayout = localStorage.getItem('sillonLayout');
            if (savedLayout) {
                this.state.layoutData = JSON.parse(savedLayout);
                this.applyLayoutData();
            }
        } catch (error) {
            console.warn('No se pudo cargar el layout guardado:', error);
        }
    },
    
    // Aplicar datos de layout a los elementos visuales
    applyLayoutData: function() {
        if (!this.state.layoutData) return;
        
        // Aplicar posiciones de sillones
        this.state.layoutData.sillones?.forEach(sillon => {
            const elemento = $(`.sillon-designer[data-sillon-id="${sillon.id}"]`);
            if (elemento.length) {
                elemento.css({
                    left: sillon.x + 'px',
                    top: sillon.y + 'px'
                });
                elemento.attr('data-x', sillon.x).attr('data-y', sillon.y);
                
                // Aplicar color si está definido
                if (sillon.color && sillon.color !== 'default') {
                    elemento.addClass('color-' + sillon.color);
                }
            }
        });
        
        // Aplicar posición de estación de enfermería
        if (this.state.layoutData.estacion) {
            const estacion = $('#nursing-station');
            if (estacion.length) {
                estacion.css({
                    left: this.state.layoutData.estacion.x + 'px',
                    top: this.state.layoutData.estacion.y + 'px'
                });
                estacion.attr('data-x', this.state.layoutData.estacion.x)
                       .attr('data-y', this.state.layoutData.estacion.y);
            }
        }
    },
    
    // Verificar disponibilidad de sillones en tiempo real
    updateAvailability: function(fecha, hora) {
        if (!fecha || !hora) {
            fecha = $('#date_at').val();
            hora = $('#time_at').val();
        }
        
        if (!fecha || !hora) return;
        
        // En producción, esto sería una llamada AJAX real
        this.simulateAvailabilityCheck(fecha, hora);
    },
    
    // Simular verificación de disponibilidad (reemplazar con AJAX real)
    simulateAvailabilityCheck: function(fecha, hora) {
        $('.sillon-selector-item, .sillon-item').each(function() {
            const sillonId = $(this).data('sillon-id');
            const ocupado = Math.random() < 0.25; // 25% probabilidad de estar ocupado
            
            if (ocupado) {
                $(this).removeClass('disponible').addClass('ocupado');
                $(this).find('.sillon-estado, .sillon-estado-selector').text('Ocupado');
                $(this).css('pointer-events', 'none');
            } else {
                $(this).removeClass('ocupado').addClass('disponible');
                $(this).find('.sillon-estado, .sillon-estado-selector').text('Disponible');
                $(this).css('pointer-events', 'auto');
            }
        });
    },
    
    // Funciones de navegación
    openMapView: function() {
        window.location.href = './?view=sillonmap';
    },
    
    openLayoutConfig: function() {
        window.location.href = './?view=sillonlayout';
    },
    
    // Auto-refresh del sistema
    startAutoRefresh: function() {
        if (!this.config.autoRefresh) return;
        
        setInterval(() => {
            if (!this.state.selectedSillon && !this.state.layoutChanged) {
                this.updateAvailability();
            }
        }, this.config.refreshInterval);
    },
    
    // Funciones de selección de sillones
    selectSillon: function(sillonId, elemento) {
        // Deseleccionar anterior
        $('.sillon-item, .sillon-designer').removeClass('selected seleccionado');
        
        // Seleccionar nuevo
        elemento.addClass('selected seleccionado');
        this.state.selectedSillon = sillonId;
        
        // Disparar evento personalizado
        $(document).trigger('sillonSelected', {
            sillonId: sillonId,
            elemento: elemento
        });
    },
    
    // Funciones de notificación
    showNotification: function(message, type = 'success', duration = 3000) {
        const alertClass = `alert-${type}`;
        const notification = $(`
            <div class="alert ${alertClass} alert-dismissible sillon-notification" 
                 style="position: fixed; top: 20px; right: 20px; z-index: 9999; width: 300px;">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <strong>${type === 'success' ? '¡Éxito!' : type === 'warning' ? '¡Atención!' : '¡Error!'}</strong> ${message}
            </div>
        `);
        
        $('body').append(notification);
        
        // Auto-remover
        setTimeout(() => {
            notification.fadeOut(() => notification.remove());
        }, duration);
    },
    
    // Funciones de plantillas de layout
    applyTemplate: function(templateName) {
        const sillones = $('.sillon-designer');
        
        switch (templateName) {
            case 'hospital':
                this.applyHospitalTemplate(sillones);
                break;
            case 'cine':
                this.applyCinemaTemplate(sillones);
                break;
            case 'circular':
                this.applyCircularTemplate(sillones);
                break;
            case 'enfermeria':
                this.applyNursingTemplate(sillones);
                break;
        }
        
        this.state.layoutChanged = true;
    },
    
    applyHospitalTemplate: function(sillones) {
        const centerX = 400;
        const centerY = 300;
        const cols = 4;
        
        sillones.each(function(index) {
            const row = Math.floor(index / cols);
            const col = index % cols;
            const x = centerX - 150 + (col * 100);
            const y = centerY - 100 + (row * 120);
            
            $(this).animate({
                left: x + 'px',
                top: y + 'px'
            }, SillonManager.config.animationDuration);
            
            $(this).attr('data-x', x).attr('data-y', y);
        });
    },
    
    applyCinemaTemplate: function(sillones) {
        const centerX = 400;
        const startY = 80;
        const cols = 6;
        
        sillones.each(function(index) {
            const row = Math.floor(index / cols);
            const col = index % cols;
            const x = centerX - (cols * 35) + (col * 70);
            const y = startY + (row * 90);
            
            $(this).animate({
                left: x + 'px',
                top: y + 'px'
            }, SillonManager.config.animationDuration);
            
            $(this).attr('data-x', x).attr('data-y', y);
        });
    },
    
    applyCircularTemplate: function(sillones) {
        const centerX = 400;
        const centerY = 300;
        const radius = 180;
        const angleStep = (2 * Math.PI) / sillones.length;
        
        sillones.each(function(index) {
            const angle = index * angleStep;
            const x = centerX + (radius * Math.cos(angle)) - 40;
            const y = centerY + (radius * Math.sin(angle)) - 50;
            
            $(this).animate({
                left: x + 'px',
                top: y + 'px'
            }, SillonManager.config.animationDuration);
            
            $(this).attr('data-x', x).attr('data-y', y);
        });
    },
    
    applyNursingTemplate: function(sillones) {
        // Disposición optimizada para supervisión de enfermería
        const centerX = 400;
        const centerY = 300;
        const radius = 120;
        
        sillones.each(function(index) {
            const angle = (index * 60) * (Math.PI / 180); // 60 grados entre sillones
            const layerRadius = radius + (Math.floor(index / 6) * 60);
            const x = centerX + (layerRadius * Math.cos(angle)) - 40;
            const y = centerY + (layerRadius * Math.sin(angle)) - 50;
            
            $(this).animate({
                left: x + 'px',
                top: y + 'px'
            }, SillonManager.config.animationDuration);
            
            $(this).attr('data-x', x).attr('data-y', y);
        });
    },
    
    // Guardar layout actual
    saveLayout: function() {
        const layoutData = {
            sillones: [],
            estacion: {},
            timestamp: new Date().toISOString()
        };
        
        // Recopilar datos de sillones
        $('.sillon-designer').each(function() {
            const elemento = $(this);
            layoutData.sillones.push({
                id: elemento.data('sillon-id'),
                x: parseInt(elemento.attr('data-x')) || 0,
                y: parseInt(elemento.attr('data-y')) || 0,
                name: elemento.find('.sillon-label').text(),
                color: SillonManager.getColorClass(elemento),
                rotation: elemento.data('rotation') || 0
            });
        });
        
        // Datos de la estación
        const station = $('#nursing-station');
        if (station.length) {
            layoutData.estacion = {
                x: parseInt(station.attr('data-x')) || 350,
                y: parseInt(station.attr('data-y')) || 250
            };
        }
        
        // Guardar en localStorage
        localStorage.setItem('sillonLayout', JSON.stringify(layoutData));
        this.state.layoutData = layoutData;
        this.state.layoutChanged = false;
        
        this.showNotification('Layout guardado exitosamente');
        
        // En producción, también enviar al servidor
        this.syncWithServer(layoutData);
    },
    
    // Obtener clase de color de un elemento
    getColorClass: function(elemento) {
        const classes = ['green', 'orange', 'purple', 'red'];
        for (let color of classes) {
            if (elemento.hasClass('color-' + color)) {
                return color;
            }
        }
        return 'default';
    },
    
    // Sincronizar con servidor (placeholder para implementación real)
    syncWithServer: function(layoutData) {
        // En producción, hacer llamada AJAX para guardar en base de datos
        console.log('Sincronizando layout con servidor:', layoutData);
        
        /*
        $.ajax({
            url: './index.php?action=savelayout',
            method: 'POST',
            data: { layout: JSON.stringify(layoutData) },
            success: function(response) {
                console.log('Layout sincronizado con servidor');
            },
            error: function(xhr, status, error) {
                SillonManager.showNotification('Error al sincronizar con servidor', 'danger');
            }
        });
        */
    },
    
    // Funciones de validación
    validateLayout: function() {
        const sillones = $('.sillon-designer');
        const issues = [];
        
        // Verificar superposiciones
        sillones.each(function(i) {
            const pos1 = $(this).position();
            sillones.each(function(j) {
                if (i !== j) {
                    const pos2 = $(this).position();
                    const distance = Math.sqrt(
                        Math.pow(pos1.left - pos2.left, 2) + 
                        Math.pow(pos1.top - pos2.top, 2)
                    );
                    if (distance < 80) {
                        issues.push(`Sillones ${i+1} y ${j+1} están muy cerca`);
                    }
                }
            });
        });
        
        return issues;
    },
    
    // Funciones de exportación
    exportLayout: function(format = 'json') {
        const layoutData = this.state.layoutData || this.getCurrentLayoutData();
        
        switch (format) {
            case 'json':
                this.downloadJSON(layoutData, 'sillon-layout.json');
                break;
            case 'image':
                this.exportAsImage();
                break;
        }
    },
    
    downloadJSON: function(data, filename) {
        const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = filename;
        a.click();
        URL.revokeObjectURL(url);
    },
    
    exportAsImage: function() {
        // Usar html2canvas o similar para exportar como imagen
        console.log('Función de exportación como imagen no implementada aún');
    },
    
    // Obtener datos actuales del layout
    getCurrentLayoutData: function() {
        const layoutData = { sillones: [], estacion: {} };
        
        $('.sillon-designer').each(function() {
            const elemento = $(this);
            layoutData.sillones.push({
                id: elemento.data('sillon-id'),
                x: parseInt(elemento.attr('data-x')) || 0,
                y: parseInt(elemento.attr('data-y')) || 0,
                name: elemento.find('.sillon-label').text(),
                color: SillonManager.getColorClass(elemento)
            });
        });
        
        const station = $('#nursing-station');
        if (station.length) {
            layoutData.estacion = {
                x: parseInt(station.attr('data-x')) || 350,
                y: parseInt(station.attr('data-y')) || 250
            };
        }
        
        return layoutData;
    }
};

// Funciones específicas para el selector visual en modals
const SillonSelector = {
    selectedSillon: null,
    
    open: function(fecha, hora, modalId = '#modal-selector-sillones') {
        if (!fecha || !hora) {
            SillonManager.showNotification('Por favor selecciona fecha y hora primero', 'warning');
            return;
        }
        
        // Actualizar información en el modal
        $(modalId + ' #fecha-selector, ' + modalId + ' #fecha-selector-edit').text(fecha);
        $(modalId + ' #hora-selector, ' + modalId + ' #hora-selector-edit').text(hora);
        
        // Verificar disponibilidad
        SillonManager.updateAvailability(fecha, hora);
        
        // Mostrar modal
        $(modalId).modal('show');
    },
    
    select: function(sillonId, nombre, modalId = '#modal-selector-sillones') {
        // Remover selección anterior
        $(modalId + ' .sillon-selector-item').removeClass('seleccionado');
        
        // Seleccionar nuevo sillón
        const sillonElement = $(modalId + ` .sillon-selector-item[data-sillon-id="${sillonId}"]`);
        sillonElement.addClass('seleccionado');
        
        // Guardar selección
        this.selectedSillon = { id: sillonId, nombre: nombre };
        
        // Mostrar información
        $(modalId + ' #nombre-sillon-seleccionado, ' + modalId + ' #nombre-sillon-seleccionado-edit').text(nombre);
        $(modalId + ' #id-sillon-seleccionado, ' + modalId + ' #id-sillon-seleccionado-edit').text(sillonId);
        $(modalId + ' #info-sillon-seleccionado, ' + modalId + ' #info-sillon-seleccionado-edit').show();
        
        // Habilitar botón de confirmación
        $(modalId + ' #btn-confirmar-sillon, ' + modalId + ' #btn-confirmar-sillon-edit').prop('disabled', false);
    },
    
    confirm: function(modalId = '#modal-selector-sillones') {
        if (!this.selectedSillon) return;
        
        // Actualizar select tradicional
        $('#chair_id').val(this.selectedSillon.id).trigger('change');
        
        // Cerrar modal
        $(modalId).modal('hide');
        
        // Mostrar confirmación
        SillonManager.showNotification(
            `Sillón "${this.selectedSillon.nombre}" seleccionado correctamente.`
        );
        
        // Limpiar selección
        this.selectedSillon = null;
    },
    
    cancel: function(modalId = '#modal-selector-sillones') {
        $(modalId + ' .sillon-selector-item').removeClass('seleccionado');
        $(modalId + ' #info-sillon-seleccionado, ' + modalId + ' #info-sillon-seleccionado-edit').hide();
        $(modalId + ' #btn-confirmar-sillon, ' + modalId + ' #btn-confirmar-sillon-edit').prop('disabled', true);
        this.selectedSillon = null;
    }
};

// Inicializar cuando el DOM esté listo
$(document).ready(function() {
    // Inicializar sistema principal
    SillonManager.init();
    
    // Configurar event handlers para modals
    $('.modal[id*="selector-sillones"]').on('hidden.bs.modal', function() {
        SillonSelector.cancel(this.id);
    });
    
    // Funciones globales para compatibilidad con código existente
    window.abrirSelectorVisual = function() {
        const fecha = $('#date_at').val();
        const hora = $('#time_at').val();
        SillonSelector.open(fecha, hora);
    };
    
    window.abrirSelectorVisualEdit = function() {
        const fecha = $('#date_at').val();
        const hora = $('#time_at').val();
        SillonSelector.open(fecha, hora, '#modal-selector-sillones-edit');
    };
    
    window.seleccionarSillonVisual = function(sillonId, nombre) {
        SillonSelector.select(sillonId, nombre);
    };
    
    window.seleccionarSillonVisualEdit = function(sillonId, nombre) {
        SillonSelector.select(sillonId, nombre, '#modal-selector-sillones-edit');
    };
    
    window.confirmarSeleccionSillon = function() {
        SillonSelector.confirm();
    };
    
    window.confirmarSeleccionSillonEdit = function() {
        SillonSelector.confirm('#modal-selector-sillones-edit');
    };
    
    console.log('Sistema visual de sillones completamente inicializado');
});

// Exportar para uso en otros scripts
window.SillonManager = SillonManager;
window.SillonSelector = SillonSelector;
