/**
 * Validaciones para formularios del sistema oncológico
 * Incluye validaciones para RUT chileno, nombres, teléfonos, etc.
 */

// Validación de RUT chileno
function validarRUT(rut) {
    // Remover puntos y guión
    rut = rut.replace(/\./g, '').replace('-', '');
    
    if (rut.length < 8 || rut.length > 9) {
        return false;
    }
    
    const body = rut.slice(0, -1);
    const dv = rut.slice(-1).toUpperCase();
    
    // Verificar que el cuerpo sea numérico
    if (!/^\d+$/.test(body)) {
        return false;
    }
    
    // Calcular dígito verificador
    let suma = 0;
    let multiplicador = 2;
    
    for (let i = body.length - 1; i >= 0; i--) {
        suma += parseInt(body.charAt(i)) * multiplicador;
        multiplicador = multiplicador === 7 ? 2 : multiplicador + 1;
    }
    
    const resto = suma % 11;
    const dvCalculado = resto === 0 ? '0' : resto === 1 ? 'K' : (11 - resto).toString();
    
    return dv === dvCalculado;
}

// Formatear RUT mientras se escribe
function formatearRUT(input) {
    let rut = input.value.replace(/[^0-9kK]/g, ''); // Solo números y K
    
    if (rut.length <= 1) {
        input.value = rut.toUpperCase();
        return;
    }
    
    const body = rut.slice(0, -1);
    const dv = rut.slice(-1).toUpperCase();
    
    // Agregar puntos cada 3 dígitos desde la derecha
    let bodyFormatted = '';
    for (let i = 0; i < body.length; i++) {
        if (i > 0 && (body.length - i) % 3 === 0) {
            bodyFormatted += '.';
        }
        bodyFormatted += body[i];
    }
    
    input.value = bodyFormatted + (bodyFormatted ? '-' : '') + dv;
}

// Validar solo letras y espacios
function soloLetras(event) {
    const char = String.fromCharCode(event.which);
    if (!/[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/.test(char)) {
        event.preventDefault();
        return false;
    }
    return true;
}

// Validar solo números
function soloNumeros(event) {
    const char = String.fromCharCode(event.which);
    if (!/[0-9]/.test(char)) {
        event.preventDefault();
        return false;
    }
    return true;
}

// Validar teléfono chileno
function validarTelefono(telefono) {
    // Remover espacios y caracteres especiales excepto +
    telefono = telefono.replace(/[\s\-()\[\]]/g, '');
    
    // Patrones para teléfonos chilenos
    const patterns = [
        /^\+56[2-9]\d{8}$/,     // +56 + código área + número (fijo)
        /^\+569\d{8}$/,         // +56 9 + número (celular)
        /^56[2-9]\d{8}$/,       // 56 + código área + número (fijo)
        /^569\d{8}$/,           // 56 9 + número (celular)
        /^[2-9]\d{8}$/,         // código área + número (fijo sin código país)
        /^9\d{8}$/              // 9 + número (celular sin código país)
    ];
    
    return patterns.some(pattern => pattern.test(telefono));
}

// Formatear teléfono
function formatearTelefono(input) {
    let telefono = input.value.replace(/[^\d+]/g, '');
    
    if (telefono.length === 0) {
        input.value = '';
        return;
    }
    
    // Si no empieza con +, agregarlo
    if (!telefono.startsWith('+') && !telefono.startsWith('56')) {
        telefono = '+56' + telefono;
    } else if (telefono.startsWith('56') && !telefono.startsWith('+56')) {
        telefono = '+' + telefono;
    }
    
    // Formatear según longitud
    if (telefono.startsWith('+569') && telefono.length === 12) {
        // Celular: +56 9 XXXX XXXX
        input.value = '+56 9 ' + telefono.substring(4, 8) + ' ' + telefono.substring(8);
    } else if (telefono.startsWith('+56') && telefono.length >= 11 && telefono.length <= 12) {
        // Fijo: +56 X XXXX XXXX o +56 XX XXXX XXXX
        const code = telefono.substring(3, telefono.length - 7);
        const number = telefono.substring(telefono.length - 7);
        input.value = '+56 ' + code + ' ' + number.substring(0, 4) + ' ' + number.substring(4);
    } else {
        input.value = telefono;
    }
}

// Validar email
function validarEmail(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}

// Inicializar validaciones cuando el documento esté listo
$(document).ready(function() {
      // Validación de RUT en campos de cédula
    $('input[name="no"]').on('input', function() {
        formatearRUT(this);
        
        const rut = this.value;
        const isValid = rut.length > 8 && validarRUT(rut);
        
        $(this).removeClass('is-valid is-invalid');
        if (rut.length > 8) {
            $(this).addClass(isValid ? 'is-valid' : 'is-invalid');
        }
        
        // Mostrar mensaje de validación
        const feedback = $(this).siblings('.invalid-feedback, .valid-feedback');
        feedback.remove();
        
        if (rut.length > 8) {
            if (isValid) {
                $(this).after('<div class="valid-feedback">✓ RUT válido</div>');
            } else {
                $(this).after('<div class="invalid-feedback">✗ RUT inválido</div>');
            }
        }
    });
    
    // Permitir solo caracteres válidos en RUT
    $('input[name="no"]').on('keypress', function(e) {
        const char = String.fromCharCode(e.which);
        if (!/[0-9kK.\-]/.test(char) && e.which !== 8) {
            e.preventDefault();
            return false;
        }
        return true;
    });
    
    // Validación de nombres (solo letras y espacios)
    $('input[name="name"], input[name="lastname"]').on('keypress', function(e) {
        return soloLetras(e);
    });
    
    // Validación en tiempo real para nombres
    $('input[name="name"], input[name="lastname"]').on('input', function() {
        const value = this.value.trim();
        $(this).removeClass('is-valid is-invalid');
        
        const feedback = $(this).siblings('.invalid-feedback, .valid-feedback');
        feedback.remove();
        
        if (value.length > 0) {
            if (value.length >= 2 && /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(value)) {
                $(this).addClass('is-valid');
                $(this).after('<div class="valid-feedback">✓ Válido</div>');
            } else {
                $(this).addClass('is-invalid');
                if (value.length < 2) {
                    $(this).after('<div class="invalid-feedback">✗ Mínimo 2 caracteres</div>');
                } else {
                    $(this).after('<div class="invalid-feedback">✗ Solo letras y espacios</div>');
                }
            }
        }
    });
      // Validación de teléfono
    $('input[name="phone"]').on('input', function() {
        formatearTelefono(this);
        
        const telefono = this.value;
        const isValid = telefono.length > 10 && validarTelefono(telefono);
        
        $(this).removeClass('is-valid is-invalid');
        const feedback = $(this).siblings('.invalid-feedback, .valid-feedback');
        feedback.remove();
        
        if (telefono.length > 5) {
            if (isValid) {
                $(this).addClass('is-valid');
                $(this).after('<div class="valid-feedback">✓ Teléfono válido</div>');
            } else {
                $(this).addClass('is-invalid');
                $(this).after('<div class="invalid-feedback">✗ Formato de teléfono inválido</div>');
            }
        }
    });
    
    $('input[name="phone"]').on('keypress', function(e) {
        // Permitir números, +, -, (, ), espacios y backspace
        const char = String.fromCharCode(e.which);
        if (!/[0-9+\-() ]/.test(char) && e.which !== 8) {
            e.preventDefault();
            return false;
        }
        return true;
    });
    
    // Validación de email en tiempo real
    $('input[name="email"]').on('input', function() {
        const email = this.value.trim();
        $(this).removeClass('is-valid is-invalid');
        
        const feedback = $(this).siblings('.invalid-feedback, .valid-feedback');
        feedback.remove();
        
        if (email.length > 0) {
            const isValid = validarEmail(email);
            if (isValid) {
                $(this).addClass('is-valid');
                $(this).after('<div class="valid-feedback">✓ Email válido</div>');
            } else {
                $(this).addClass('is-invalid');
                $(this).after('<div class="invalid-feedback">✗ Formato de email inválido</div>');
            }
        }
    });
      // Validación de código postal (solo números)
    $('input[name="cp"]').on('keypress', function(e) {
        return soloNumeros(e);
    });
    
    // Validación en tiempo real para código postal
    $('input[name="cp"]').on('input', function() {
        const value = this.value.trim();
        $(this).removeClass('is-valid is-invalid');
        
        const feedback = $(this).siblings('.invalid-feedback, .valid-feedback');
        feedback.remove();
        
        if (value.length > 0) {
            if (/^\d{4,7}$/.test(value)) {
                $(this).addClass('is-valid');
                $(this).after('<div class="valid-feedback">✓ Código postal válido</div>');
            } else {
                $(this).addClass('is-invalid');
                $(this).after('<div class="invalid-feedback">✗ Debe contener 4-7 dígitos</div>');
            }
        }
    });
    
    // Capitalizar primera letra en campos de texto
    $('input[name="name"], input[name="lastname"], input[name="pob"]').on('input', function() {
        const words = this.value.split(' ');
        const capitalizedWords = words.map(word => {
            if (word.length > 0) {
                return word.charAt(0).toUpperCase() + word.slice(1).toLowerCase();
            }
            return word;
        });
        this.value = capitalizedWords.join(' ');
    });
      // Validación del formulario antes de enviar
    $('#addpacient, #addmedic').on('submit', function(e) {
        let isValid = true;
        let errors = [];
        const form = $(this);
        
        // Limpiar estilos previos
        form.find('.is-invalid').removeClass('is-invalid');
        
        // Validar RUT
        const rutField = form.find('input[name="no"]');
        const rut = rutField.val();
        if (!rut || !validarRUT(rut)) {
            rutField.addClass('is-invalid');
            errors.push('RUT inválido o vacío');
            isValid = false;
        }
        
        // Validar nombre
        const nameField = form.find('input[name="name"]');
        const name = nameField.val().trim();
        if (name.length < 2 || !/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(name)) {
            nameField.addClass('is-invalid');
            errors.push('Nombre debe tener al menos 2 caracteres y solo letras');
            isValid = false;
        }
        
        // Validar apellido
        const lastnameField = form.find('input[name="lastname"]');
        const lastname = lastnameField.val().trim();
        if (lastname.length < 2 || !/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(lastname)) {
            lastnameField.addClass('is-invalid');
            errors.push('Apellido debe tener al menos 2 caracteres y solo letras');
            isValid = false;
        }
        
        // Validar teléfono
        const phoneField = form.find('input[name="phone"]');
        const telefono = phoneField.val();
        if (!telefono || !validarTelefono(telefono)) {
            phoneField.addClass('is-invalid');
            errors.push('Teléfono inválido o vacío');
            isValid = false;
        }
        
        // Validar email
        const emailField = form.find('input[name="email"]');
        const email = emailField.val();
        if (!email || !validarEmail(email)) {
            emailField.addClass('is-invalid');
            errors.push('Email inválido o vacío');
            isValid = false;
        }
        
        // Validar fecha de nacimiento (solo para pacientes)
        if (form.attr('id') === 'addpacient') {
            const birthField = form.find('input[name="day_of_birth"]');
            const birthDate = birthField.val();
            if (!birthDate) {
                birthField.addClass('is-invalid');
                errors.push('Fecha de nacimiento requerida');
                isValid = false;
            } else {
                const birth = new Date(birthDate);
                const today = new Date();
                const age = today.getFullYear() - birth.getFullYear();
                if (age < 0 || age > 120) {
                    birthField.addClass('is-invalid');
                    errors.push('Fecha de nacimiento inválida');
                    isValid = false;
                }
            }
            
            // Validar contraseña
            const passwordField = form.find('input[name="password"]');
            const password = passwordField.val();
            if (!password || password.length < 6) {
                passwordField.addClass('is-invalid');
                errors.push('Contraseña debe tener al menos 6 caracteres');
                isValid = false;
            }
        }
        
        if (!isValid) {
            e.preventDefault();
            
            // Mostrar alerta con todos los errores
            let errorMessage = 'Por favor corrija los siguientes errores:\n\n';
            errors.forEach((error, index) => {
                errorMessage += `${index + 1}. ${error}\n`;
            });
            
            alert(errorMessage);
            
            // Enfocar el primer campo con error
            form.find('.is-invalid').first().focus();
            
            return false;
        }
        
        // Si todo está bien, mostrar mensaje de envío
        const submitBtn = form.find('button[type="submit"]');
        submitBtn.prop('disabled', true).text('Guardando...');
        
        return true;
    });
});

// Estilos CSS para los campos de validación
$(document).ready(function() {
    $('head').append(`        <style>
            .is-valid {
                border-color: #28a745 !important;
                padding-right: calc(1.5em + 0.75rem) !important;
                background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8' viewBox='0 0 8 8'%3e%3cpath fill='%2328a745' d='m2.3 6.73.8-.8 2.3-2.3.8.8L3.1 7.4l-.8-.8L1.5 6l.8-.8z'/%3e%3c/svg%3e") !important;
                background-repeat: no-repeat !important;
                background-position: right calc(0.375em + 0.1875rem) center !important;
                background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem) !important;
                box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25) !important;
            }
            
            .is-invalid {
                border-color: #dc3545 !important;
                padding-right: calc(1.5em + 0.75rem) !important;
                background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='none' stroke='%23dc3545' viewBox='0 0 12 12'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath d='m5.8 4.6 1.4 1.4m0-1.4-1.4 1.4'/%3e%3c/svg%3e") !important;
                background-repeat: no-repeat !important;
                background-position: right calc(0.375em + 0.1875rem) center !important;
                background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem) !important;
                box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
            }
            
            .valid-feedback {
                display: block;
                color: #28a745;
                font-size: 0.875em;
                margin-top: 0.25rem;
                font-weight: 500;
            }
            
            .invalid-feedback {
                display: block;
                color: #dc3545;
                font-size: 0.875em;
                margin-top: 0.25rem;
                font-weight: 500;
            }
            
            /* Animación suave para transiciones */
            .form-control {
                transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out !important;
            }
            
            /* Mejora visual para campos requeridos */
            .form-control[required] {
                border-left: 3px solid #007bff;
            }
            
            .form-control[required]:focus {
                border-left: 3px solid #0056b3;
            }
            
            /* Estilo para mensajes de ayuda */
            .help-block {
                color: #6c757d;
                font-size: 0.8em;
                margin-top: 0.25rem;
            }
        </style>
    `);
});
