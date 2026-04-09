# Manual Operativo - Sistema de Gestion Oncologica

Este documento es una guia de operacion diaria para personal de coordinacion, enfermeria, recepcion, soporte y jefatura medica.

No reemplaza la instalacion tecnica. La instalacion y configuracion de servidor se mantienen en README.md.

## 1) Objetivo del manual

Este manual define:

- Como abrir el sistema al inicio de jornada.
- Como ejecutar procesos clinicos y administrativos sin saltar pasos.
- Como cerrar jornada dejando trazabilidad.
- Que hacer ante incidentes frecuentes.
- A quien escalar cada problema segun impacto.

## 2) Roles operativos sugeridos

- Administrador del sistema:
  - Gestiona usuarios, configuracion general y notificaciones.
  - Ejecuta verificaciones tecnicas cuando hay fallas.
- Personal de recepcion:
  - Registra pacientes.
  - Agenda, modifica o cancela reservas.
  - Apoya en lista de espera.
- Personal medico:
  - Revisa agenda propia.
  - Registra evaluaciones iniciales.
  - Actualiza estado de atencion.
- Soporte tecnico:
  - Mantiene disponibilidad del sistema.
  - Verifica base de datos, permisos, correo y logs.

## 3) Inicio de jornada (apertura)

Ejecutar en este orden:

1. Verificar que el servidor web y la base de datos estan activos.
2. Abrir el sistema en navegador: [http://localhost/hito_oncology/](http://localhost/hito_oncology/)
3. Ingresar con usuario autorizado.
4. Revisar Dashboard de oncologia.
5. Validar que se cargan:
   - pacientes,
   - medicos,
   - calendario,
   - lista de espera,
   - modulo de notificaciones.
6. Si hay alertas visibles, documentarlas antes de iniciar atencion.

## 4) Procedimientos operativos clave

### 4.1 Registrar un paciente

1. Ir a la vista de pacientes.
2. Seleccionar nuevo paciente.
3. Completar datos obligatorios:
   - identificacion,
   - nombres,
   - apellidos,
   - fecha de nacimiento,
   - contacto,
   - credenciales segun politica interna.
4. Confirmar guardado.
5. Verificar que aparece en listado.
6. Si se usa correo de bienvenida, revisar cola/historial de notificaciones.

Control de calidad:

- No registrar pacientes duplicados con mismo documento.
- Validar telefono y correo antes de guardar.

### 4.2 Registrar un medico

1. Ir a la vista de medicos.
2. Seleccionar nuevo medico.
3. Registrar datos de identificacion y categoria.
4. Guardar.
5. Confirmar que aparece en listado.

Control de calidad:

- Confirmar que el medico queda activo.
- Confirmar que tiene categoria correcta para agenda.

### 4.3 Crear una reserva

1. Buscar al paciente.
2. Seleccionar fecha y hora.
3. Seleccionar medico.
4. Si aplica, asociar sillon.
5. Guardar reserva.
6. Revisar calendario y estado de la reserva.

Control de calidad:

- No duplicar reserva para mismo paciente en mismo bloque horario.
- Confirmar estado inicial esperado (pendiente o equivalente).

### 4.4 Gestionar lista de espera oncologica

1. Abrir lista de espera.
2. Registrar prioridad y notas clinicas/administrativas.
3. Guardar.
4. Cuando exista cupo, asignar reserva desde lista.
5. Confirmar que el registro cambia de estado.

Control de calidad:

- Registrar siempre motivo de prioridad.
- Mantener notas breves y utiles para triage.

### 4.5 Usar mapa y layout de sillones

1. Ir a mapa de sillones para ver ocupacion.
2. Ir a configurar layout solo cuando corresponda.
3. Guardar posiciones y validar mensaje de confirmacion.
4. Volver al mapa para confirmar persistencia.

Control de calidad:

- No redisenar layout en horario de alta demanda sin aviso.
- Verificar que cambios no oculten sillones activos.

### 4.6 Registrar evaluacion inicial

1. Abrir modulo de evaluaciones.
2. Seleccionar nueva evaluacion.
3. Completar:
   - diagnostico primario,
   - estado funcional,
   - sintomas,
   - plan propuesto,
   - resumen medico.
4. Guardar borrador o completar.
5. Confirmar estado final en listado.

Control de calidad:

- No cerrar evaluacion sin resumen medico.
- Confirmar paciente y medico correctos antes de guardar.

### 4.7 Operar notificaciones

1. Revisar configuracion SMTP.
2. Confirmar que notificaciones y envio automatico estan habilitados.
3. Revisar cola de notificaciones.
4. Si hay pendientes acumulados, pedir ejecucion del procesador.
5. Revisar historial para confirmar envio o fallo.

Control de calidad:

- Documentar errores de envio repetitivos.
- No cambiar credenciales SMTP sin ticket o autorizacion.

## 5) Cierre de jornada

1. Revisar reservas pendientes del dia siguiente.
2. Revisar lista de espera sin asignar.
3. Revisar fallos de notificacion.
4. Registrar incidentes operativos del turno.
5. Cerrar sesion de todos los usuarios activos.

## 6) Incidentes frecuentes y accion inmediata

### 6.1 No permite iniciar sesion

Accion inmediata:

1. Confirmar usuario y clave.
2. Probar con usuario administrador.
3. Si persiste, escalar a soporte tecnico.

Escalamiento:

- Soporte tecnico revisa base de datos y estado de servicios.

### 6.2 No guarda layout de sillones

Accion inmediata:

1. Reintentar guardado.
2. Actualizar pagina y validar si se guardo.
3. Si no persiste, registrar incidente.

Escalamiento:

- Soporte revisa permisos de escritura en core/app/data.

### 6.3 No se envian correos

Accion inmediata:

1. Verificar configuracion SMTP.
2. Revisar cola de notificaciones.
3. Solicitar ejecucion manual del procesador.

Escalamiento:

- Soporte revisa logs/notification_processor.log y salida SMTP.

### 6.4 Sistema lento o sin respuesta

Accion inmediata:

1. Confirmar si afecta solo a un usuario o a todos.
2. Anotar hora exacta del problema.
3. Evitar reinicios manuales sin coordinacion.

Escalamiento:

- Soporte revisa carga de servidor, base de datos y logs.

## 7) Checklist diario de operacion

Completar al inicio y cierre:

- [ ] Sistema accesible.
- [ ] Login operativo.
- [ ] Dashboard cargando.
- [ ] Pacientes y medicos visibles.
- [ ] Calendario operativo.
- [ ] Lista de espera operativa.
- [ ] Mapa de sillones operativo.
- [ ] Cola de notificaciones sin atasco.
- [ ] Incidentes registrados.

## 8) Checklist semanal de supervision

- [ ] Revisar cuentas activas y permisos.
- [ ] Revisar volumen de reservas vs capacidad de sillones.
- [ ] Revisar pendientes en lista de espera.
- [ ] Revisar fallos recurrentes de notificaciones.
- [ ] Confirmar respaldo de base de datos.

## 9) Control preventivo tecnico (preflight)

El proyecto incluye un verificador automatico para validar requisitos tecnicos y permisos antes de instalar o cuando se detectan incidencias estructurales.

Archivo:

- preflight_check.php

Uso en navegador:

- [http://localhost/hito_oncology/preflight_check.php](http://localhost/hito_oncology/preflight_check.php)
- [http://localhost/hito_oncology/preflight_check.php?fix=1](http://localhost/hito_oncology/preflight_check.php?fix=1)

Uso por consola:

- php preflight_check.php
- php preflight_check.php --fix

Que valida:

- Version y extensiones de PHP.
- Archivos obligatorios del proyecto.
- Conexion a MySQL y acceso a la base objetivo.
- Directorios de escritura criticos (logs y core/app/data).
- Directorios de almacenamiento de imagenes (storage) como recomendacion.

## 10) Regla de oro operativa

Si hay duda entre velocidad y trazabilidad, priorizar trazabilidad.

Toda accion sensible (cambios de agenda, cancelaciones, reasignaciones y cambios de configuracion) debe quedar registrada y comunicada al equipo del turno siguiente.
