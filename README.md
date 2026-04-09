# Sistema de Gestion Oncologica

Manual operativo detallado para instalar, poner en marcha, validar y mantener el sistema en ambientes Windows (XAMPP) o Linux (Apache + PHP + MariaDB/MySQL).

El objetivo de este documento es que cualquier persona tecnica del equipo pueda levantar el sistema de forma predecible, con criterios claros de verificacion y pasos concretos para resolver fallas comunes.

## Documentacion adicional

- Manual operativo (flujo diario y procedimientos por rol): `MANUAL_OPERATIVO.md`
- Verificador automatico de requisitos y permisos: `preflight_check.php`

## 1) Alcance del sistema

Este proyecto cubre los siguientes modulos funcionales:

- Gestion de pacientes.
- Gestion de medicos oncologos.
- Agenda y reservas.
- Lista de espera oncologica.
- Gestion visual y operativa de sillones de tratamiento.
- Evaluacion medica inicial.
- Notificaciones por correo (cola + procesador).

## 2) Componentes clave del proyecto

Archivos y carpetas que debes conocer antes de instalar:

- `index.php`: punto de entrada principal de la aplicacion web.
- `logout.php`: cierre de sesion.
- `core/`: logica principal (controladores, modelos, vistas, acciones, layout).
- `setup_database.php`: instalador y verificador de estructura base + import de esquemas funcionales.
- `oncology_schema.sql`: estructura y datos iniciales del modulo oncologico (sillones, lista de espera, etc.).
- `notification_schema.sql`: estructura y datos iniciales de notificaciones.
- `notification_processor.php`: script CLI para procesar cola de notificaciones.
- `bootstrap/`, `dist/`, `plugins/`, `font-awesome/`, `js/`: recursos frontend.
- `vendor/` y `fpdf/`: dependencias.

## 3) Requisitos tecnicos minimos

Recomendado:

- PHP 8.0 o superior.
- MariaDB 10.x o MySQL 5.7/8.x.
- Apache 2.4.

Requisitos de configuracion PHP:

- `mysqli` habilitado.
- `mbstring` habilitado.
- `openssl` habilitado (necesario para SMTP TLS/SSL).
- `allow_url_fopen` habilitado (recomendado por algunas librerias).

En Windows con XAMPP:

- Apache y MySQL deben quedar en estado Running.
- Ruta recomendada del proyecto: `C:\xampp\htdocs\hito_oncology`.

En Linux:

- Servicio web y base de datos activos.
- Permisos correctos sobre la carpeta del proyecto para el usuario del servidor web (ejemplo: `www-data`).

## 4) Preparacion del entorno

### 4.1 Copiar el proyecto

Ubica el proyecto dentro de la raiz publica del servidor web.

Windows (XAMPP):

```powershell
Copy-Item -Path "D:\origen\hito_oncology" -Destination "C:\xampp\htdocs\" -Recurse
```

Linux (ejemplo):

```bash
sudo cp -R /origen/hito_oncology /var/www/html/
```

### 4.2 Confirmar rutas y archivos criticos

Antes de ejecutar instalacion, confirma que existen:

- `setup_database.php`
- `oncology_schema.sql`
- `notification_schema.sql`
- `core/controller/Database.php`

Si alguno falta, la instalacion no quedara completa.

## 5) Configuracion de base de datos (obligatorio)

El sistema usa, por defecto:

- Host: `localhost`
- Puerto: `3306`
- Usuario: `root`
- Password: vacio
- Base de datos: `oncology_db`

Debes validar estos valores en dos archivos para mantener consistencia:

- `core/controller/Database.php`
- `setup_database.php`

Si cambias credenciales en uno y en el otro no, vas a tener fallos intermitentes (por ejemplo, instalador funcional pero login sin conexion, o al reves).

## 6) Instalacion inicial de la base de datos

### 6.1 Ejecutar instalador en navegador

Abre:

```text
http://localhost/hito_oncology/setup_database.php
```

El script realiza, en este orden:

1. Conexion a MySQL.
2. Creacion/verificacion de BD `oncology_db`.
3. Creacion/verificacion de tablas base (usuarios, pacientes, medicos, reservas, evaluaciones).
4. Import de `oncology_schema.sql`.
5. Import de `notification_schema.sql`.
6. Verificacion/creacion de usuario administrador por defecto.
7. Resumen final de tablas.

### 6.2 Resultado esperado

Debe mostrarse un bloque final indicando que la configuracion termino correctamente y un enlace para entrar al sistema.

### 6.3 Nota importante sobre re-ejecucion

El instalador es seguro para estructura (usa verificaciones), pero algunos inserts de esquemas funcionales pueden repetirse si lo ejecutas muchas veces en una misma BD productiva.

Recomendacion operativa:

- Ejecutar `setup_database.php` una vez en instalacion inicial.
- Si necesitas re-ejecutar en productivo, hacerlo con respaldo previo de la BD.

## 7) Primer acceso y credenciales por defecto

Accede a:

```text
http://localhost/hito_oncology/
```

Credenciales iniciales:

- Usuario: `admin`
- Contrasena: `admin`

Despues del primer login, cambia la clave del administrador en cuanto sea posible.

## 8) Flujo recomendado de puesta en marcha funcional

Una vez dentro como administrador, se recomienda seguir este orden para evitar errores de operacion:

1. Crear o validar medicos.
2. Crear o validar pacientes.
3. Revisar sillones activos.
4. Registrar una reserva de prueba.
5. Probar lista de espera.
6. Probar evaluacion inicial.
7. Configurar SMTP y ejecutar prueba de notificaciones.

Rutas utiles para validacion:

- `./?view=oncologydashboard`
- `./?view=pacients`
- `./?view=medics`
- `./?view=assessments`
- `./?view=oncologywaitlist`
- `./?view=oncologychairs`
- `./?view=sillonmap`
- `./?view=sillonlayout`
- `./?view=oncologycalendar`
- `./?view=notifications`
- `./?view=notificationconfig`
- `./?view=notificationqueue`

## 9) Directorios de escritura y permisos

Durante la operacion, el sistema necesita escribir en ciertas rutas:

- `logs/`: usado por `notification_processor.php` para el archivo `notification_processor.log`.
- `core/app/data/`: usado para persistir el layout visual de sillones (`sillon_layout.json`).

En el estado actual, esos directorios pueden no existir al inicio. El sistema intenta crearlos cuando corresponde, pero en servidores con politicas de permisos estrictas conviene crearlos manualmente antes de operar.

Windows (PowerShell):

```powershell
New-Item -ItemType Directory -Force -Path "C:\xampp\htdocs\hito_oncology\logs"
New-Item -ItemType Directory -Force -Path "C:\xampp\htdocs\hito_oncology\core\app\data"
```

Linux:

```bash
mkdir -p /var/www/html/hito_oncology/logs
mkdir -p /var/www/html/hito_oncology/core/app/data
chown -R www-data:www-data /var/www/html/hito_oncology/logs /var/www/html/hito_oncology/core/app/data
chmod -R 775 /var/www/html/hito_oncology/logs /var/www/html/hito_oncology/core/app/data
```

Nota sobre imagenes:

- Algunas vistas referencian rutas bajo `storage/` (por ejemplo, `storage/pacients/` y `storage/medics/`).
- Si tu flujo de trabajo usa fotos de pacientes/medicos, crea tambien esas carpetas y asigna permisos de escritura.

## 10) Configuracion de notificaciones (SMTP)

### 10.1 Configurar desde interfaz

Ingresar a:

- `./?view=notificationconfig`

Completar al menos:

- Servidor SMTP.
- Puerto.
- Seguridad (`tls`, `ssl` o `none`).
- Usuario SMTP.
- Clave SMTP.
- Correo remitente.
- Nombre remitente.

### 10.2 Validar que la cola procese

El envio automatico depende del script CLI:

- `notification_processor.php`

Este script:

1. Solo corre en CLI.
2. Revisa si notificaciones y autoenvio estan habilitados.
3. Procesa pendientes de `notification_queue`.
4. Registra ejecucion en `logs/notification_processor.log`.

Ejecucion manual en Windows (XAMPP):

```powershell
cd C:\xampp\htdocs\hito_oncology
C:\xampp\php\php.exe notification_processor.php
```

Ejecucion manual en Linux:

```bash
cd /var/www/html/hito_oncology
php notification_processor.php
```

### 10.3 Automatizar ejecucion

Linux (cron cada 10 minutos):

```bash
*/10 * * * * /usr/bin/php /var/www/html/hito_oncology/notification_processor.php
```

Windows (Programador de tareas):

1. Crear tarea basica.
2. Frecuencia: cada 10 minutos.
3. Programa/script: `C:\xampp\php\php.exe`.
4. Argumentos: `C:\xampp\htdocs\hito_oncology\notification_processor.php`.
5. Iniciar en: `C:\xampp\htdocs\hito_oncology`.

## 11) Checklist de validacion post-instalacion

Despues de instalar, ejecuta esta lista y marca cada punto:

1. Carga la URL principal sin error HTTP 500.
2. Login administrador exitoso con `admin/admin`.
3. Dashboard visible y menu lateral operativo.
4. Alta de medico sin errores.
5. Alta de paciente sin errores.
6. Creacion de reserva sin errores.
7. Visualizacion de lista de espera.
8. Visualizacion de evaluaciones y creacion de una nueva.
9. Acceso al mapa de sillones y guardado de layout.
10. Configuracion SMTP guardada.
11. Ejecucion de `notification_processor.php` sin excepciones.
12. Aparicion de trazas en `logs/notification_processor.log`.

Si uno de estos pasos falla, revisa la seccion de troubleshooting antes de continuar.

## 12) Respaldo y restauracion

### 12.1 Respaldo de base de datos

Windows (XAMPP, ejemplo):

```powershell
$fecha = Get-Date -Format yyyy-MM-dd
& "C:\xampp\mysql\bin\mysqldump.exe" -u root -p oncology_db > "C:\respaldos\oncology_db_$fecha.sql"
```

Linux:

```bash
mysqldump -u root -p oncology_db > /backups/oncology_db_$(date +%F).sql
```

### 12.2 Restauracion

Windows:

```powershell
C:\xampp\mysql\bin\mysql.exe -u root -p oncology_db < C:\respaldos\oncology_db_2025-01-31.sql
```

Linux:

```bash
mysql -u root -p oncology_db < /backups/oncology_db_2025-01-31.sql
```

## 13) Troubleshooting detallado

### 13.1 Error de conexion a base de datos

Sintomas:

- Mensajes de conexion fallida en pantalla.
- Login no responde o redirige sin autenticar.

Verificaciones:

1. Confirmar que MySQL/MariaDB esta levantado.
2. Revisar host/usuario/password/puerto y nombre BD en:
   - `core/controller/Database.php`
   - `setup_database.php`
3. Confirmar puerto real de MySQL (3306 por defecto).

### 13.2 setup_database.php falla o queda incompleto

Verificaciones:

1. Confirmar existencia de:
   - `oncology_schema.sql`
   - `notification_schema.sql`
2. Revisar permisos de lectura del proyecto.
3. Reintentar con BD limpia solo en ambiente de pruebas.

### 13.3 No se envian correos

Sintomas:

- Cola crece en pendiente.
- Sin correos en destino.

Verificaciones:

1. Revisar configuracion SMTP en `./?view=notificationconfig`.
2. Confirmar usuario/clave SMTP validos.
3. Ejecutar manualmente `notification_processor.php`.
4. Revisar `logs/notification_processor.log`.
5. Validar conectividad saliente hacia el host SMTP (firewall/puerto).

### 13.4 No se guarda layout de sillones

Sintoma:

- Mensaje de error al guardar posiciones.

Verificaciones:

1. Confirmar existencia/escritura en `core/app/data/`.
2. Revisar permisos del usuario del servidor web.

### 13.5 Error 404 o vista vacia

Verificaciones:

1. Confirmar que Apache permite `.htaccess` (mod_rewrite).
2. Confirmar URL de acceso correcta (`/hito_oncology/`).
3. Verificar que la vista solicitada exista fisicamente en `core/app/view/`.

## 14) Recomendaciones para produccion

Para ambientes productivos:

1. Cambiar inmediatamente credenciales por defecto (`admin/admin`).
2. Cambiar usuario `root` por usuario dedicado de BD con permisos acotados.
3. Desactivar visualizacion de errores PHP en pantalla.
4. Forzar HTTPS en el virtual host o proxy.
5. Configurar backups programados de BD.
6. Rotar y monitorear `logs/notification_processor.log`.
7. Definir procedimiento formal de despliegue y rollback.

## 15) Operacion diaria sugerida

Rutina recomendada para equipos de soporte:

1. Revisar si el procesador de notificaciones corrio segun horario.
2. Revisar cola de notificaciones y errores fallidos.
3. Revisar ocupacion y estado de sillones.
4. Revisar reservas del dia y lista de espera.
5. Confirmar que no hay errores de conexion en los primeros accesos del dia.

## 16) Datos de referencia rapida

- URL base local: `http://localhost/hito_oncology/`
- Instalador: `http://localhost/hito_oncology/setup_database.php`
- BD por defecto: `oncology_db`
- Usuario inicial: `admin`
- Contrasena inicial: `admin`
- Procesador CLI: `notification_processor.php`
- Log de procesador: `logs/notification_processor.log`

---

Si vas a cambiar credenciales, rutas o nombres de base de datos para un entorno nuevo, documenta esos cambios en este mismo README antes de pasar el sistema al siguiente equipo. Eso evita despliegues inconsistentes y reduce mucho el tiempo de soporte.
