<?php
declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '1');

$rootDir = __DIR__;
$isCli = (PHP_SAPI === 'cli');
$fixMode = false;

if ($isCli) {
    global $argv;
    $fixMode = in_array('--fix', $argv ?? [], true);
} else {
    $fixMode = isset($_GET['fix']) && $_GET['fix'] === '1';
}

$results = [];

function addResult(array &$results, string $status, string $check, string $detail, string $hint = ''): void
{
    $results[] = [
        'status' => $status,
        'check' => $check,
        'detail' => $detail,
        'hint' => $hint,
    ];
}

function checkWritableDirectory(string $path, bool $fixMode): array
{
    if (!file_exists($path)) {
        if ($fixMode) {
            if (!@mkdir($path, 0755, true)) {
                return ['FAIL', 'No existe y no se pudo crear'];
            }
        } else {
            return ['FAIL', 'No existe'];
        }
    }

    if (!is_dir($path)) {
        return ['FAIL', 'La ruta existe pero no es un directorio'];
    }

    $testFile = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . '.preflight_write_test_' . uniqid('', true) . '.tmp';
    $writeOk = @file_put_contents($testFile, 'ok');

    if ($writeOk === false) {
        return ['FAIL', 'No tiene permisos de escritura'];
    }

    @unlink($testFile);
    return ['OK', 'Existe y permite escritura'];
}

function html(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

// 1) PHP runtime checks.
if (version_compare(PHP_VERSION, '8.0.0', '>=')) {
    addResult($results, 'OK', 'PHP Version', 'Version detectada: ' . PHP_VERSION);
} else {
    addResult($results, 'FAIL', 'PHP Version', 'Version detectada: ' . PHP_VERSION, 'Se requiere PHP 8.0 o superior.');
}

$requiredExtensions = ['mysqli', 'mbstring', 'openssl'];
foreach ($requiredExtensions as $extension) {
    if (extension_loaded($extension)) {
        addResult($results, 'OK', 'Extension PHP', $extension . ' habilitada');
    } else {
        addResult($results, 'FAIL', 'Extension PHP', $extension . ' no habilitada', 'Habilita la extension en php.ini y reinicia servicios.');
    }
}

$allowUrlFopen = filter_var((string) ini_get('allow_url_fopen'), FILTER_VALIDATE_BOOLEAN);
if ($allowUrlFopen) {
    addResult($results, 'OK', 'PHP ini', 'allow_url_fopen habilitado');
} else {
    addResult($results, 'WARN', 'PHP ini', 'allow_url_fopen deshabilitado', 'No siempre bloquea el sistema, pero se recomienda habilitarlo.');
}

// 2) Required project files.
$requiredFiles = [
    '.htaccess',
    'index.php',
    'logout.php',
    'setup_database.php',
    'oncology_schema.sql',
    'notification_schema.sql',
    'notification_processor.php',
    'core/autoload.php',
    'core/controller/Database.php',
    'core/app/init.php',
    'core/app/layouts/layout.php',
    'core/app/model/NotificationData.php',
    'core/app/model/NotificationService.php',
];

foreach ($requiredFiles as $relativePath) {
    $fullPath = $rootDir . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);
    if (is_file($fullPath)) {
        addResult($results, 'OK', 'Archivo requerido', $relativePath . ' encontrado');
    } else {
        addResult($results, 'FAIL', 'Archivo requerido', $relativePath . ' no encontrado', 'Restaura el archivo antes de instalar/operar.');
    }
}

// 3) SQL schema sanity checks.
$sqlFiles = ['oncology_schema.sql', 'notification_schema.sql'];
foreach ($sqlFiles as $sqlFile) {
    $fullPath = $rootDir . DIRECTORY_SEPARATOR . $sqlFile;

    if (!is_file($fullPath)) {
        continue;
    }

    $content = @file_get_contents($fullPath);
    if ($content === false || trim($content) === '') {
        addResult($results, 'FAIL', 'Esquema SQL', $sqlFile . ' vacio o no legible', 'Verifica permisos y contenido del archivo SQL.');
        continue;
    }

    $createCount = preg_match_all('/CREATE\s+TABLE/i', $content, $matches);
    if ($createCount > 0) {
        addResult($results, 'OK', 'Esquema SQL', $sqlFile . ' contiene ' . $createCount . ' sentencias CREATE TABLE');
    } else {
        addResult($results, 'WARN', 'Esquema SQL', $sqlFile . ' no contiene CREATE TABLE detectables', 'Revisa si el archivo SQL corresponde al sistema actual.');
    }
}

// 4) Writable directories.
$directoryChecks = [
    ['path' => 'logs', 'required' => true],
    ['path' => 'core/app/data', 'required' => true],
    ['path' => 'storage', 'required' => false],
    ['path' => 'storage/pacients', 'required' => false],
    ['path' => 'storage/medics', 'required' => false],
];

foreach ($directoryChecks as $dirCheck) {
    $relative = $dirCheck['path'];
    $required = (bool) $dirCheck['required'];

    $fullPath = $rootDir . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relative);
    [$status, $detail] = checkWritableDirectory($fullPath, $fixMode);

    if ($status === 'OK') {
        addResult($results, 'OK', 'Permisos directorio', $relative . ': ' . $detail);
        continue;
    }

    if ($required) {
        addResult(
            $results,
            'FAIL',
            'Permisos directorio',
            $relative . ': ' . $detail,
            $fixMode
                ? 'Corrige permisos del sistema de archivos para el usuario del servidor web.'
                : 'Ejecuta con --fix o ?fix=1 para crear rutas faltantes y vuelve a validar.'
        );
    } else {
        addResult(
            $results,
            'WARN',
            'Permisos directorio',
            $relative . ': ' . $detail,
            $fixMode
                ? 'Si usas imagenes de pacientes o medicos, corrige permisos de escritura.'
                : 'Opcional, pero recomendado si el flujo incluye carga de imagenes.'
        );
    }
}

// 5) Database connectivity checks.
$databaseFile = $rootDir . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'controller' . DIRECTORY_SEPARATOR . 'Database.php';

if (is_file($databaseFile)) {
    require_once $databaseFile;
}

if (!class_exists('Database')) {
    addResult($results, 'FAIL', 'Base de datos', 'No se pudo cargar la clase Database', 'Revisa core/controller/Database.php.');
} elseif (!extension_loaded('mysqli')) {
    addResult($results, 'FAIL', 'Base de datos', 'mysqli no disponible para probar conexion', 'Habilita mysqli y vuelve a ejecutar preflight.');
} else {
    try {
        $db = new Database();
        $host = (string) ($db->host ?? 'localhost');
        $user = (string) ($db->user ?? 'root');
        $pass = (string) ($db->pass ?? '');
        $port = (int) ($db->port ?? 3306);
        $dbName = (string) ($db->ddbb ?? 'oncology_db');

        $con = @new mysqli($host, $user, $pass, '', $port);

        if ($con->connect_error) {
            addResult(
                $results,
                'FAIL',
                'Base de datos',
                'No se pudo conectar a MySQL: ' . $con->connect_error,
                'Verifica servicio MySQL, host, usuario, clave y puerto.'
            );
        } else {
            addResult($results, 'OK', 'Base de datos', 'Conexion a MySQL exitosa (' . $host . ':' . $port . ')');

            $safeDbName = str_replace('`', '', $dbName);
            $createSql = "CREATE DATABASE IF NOT EXISTS `" . $safeDbName . "` CHARACTER SET utf8 COLLATE utf8_general_ci";

            if ($con->query($createSql)) {
                addResult($results, 'OK', 'Base de datos', 'Se pudo crear/verificar la base ' . $safeDbName);
            } else {
                addResult(
                    $results,
                    'FAIL',
                    'Base de datos',
                    'No se pudo crear/verificar la base ' . $safeDbName . ': ' . $con->error,
                    'Revisa permisos del usuario MySQL para CREATE DATABASE.'
                );
            }

            if ($con->select_db($safeDbName)) {
                addResult($results, 'OK', 'Base de datos', 'Se pudo seleccionar la base ' . $safeDbName);
            } else {
                addResult(
                    $results,
                    'FAIL',
                    'Base de datos',
                    'No se pudo seleccionar la base ' . $safeDbName . ': ' . $con->error,
                    'Revisa existencia de base y permisos del usuario MySQL.'
                );
            }

            $con->close();
        }
    } catch (Throwable $error) {
        addResult(
            $results,
            'FAIL',
            'Base de datos',
            'Error inesperado en validacion de DB: ' . $error->getMessage(),
            'Revisa configuracion en core/controller/Database.php.'
        );
    }
}

// 6) Apache rewrite check (only web mode).
if (!$isCli) {
    if (function_exists('apache_get_modules')) {
        $modules = array_map('strtolower', apache_get_modules());
        if (in_array('mod_rewrite', $modules, true)) {
            addResult($results, 'OK', 'Apache', 'mod_rewrite detectado');
        } else {
            addResult($results, 'WARN', 'Apache', 'mod_rewrite no detectado', 'Puede afectar rutas limpias o redirecciones por .htaccess.');
        }
    } else {
        addResult($results, 'WARN', 'Apache', 'No fue posible verificar mod_rewrite automaticamente', 'Si hay errores de ruta, valida AllowOverride y mod_rewrite.');
    }
}

$summary = ['OK' => 0, 'WARN' => 0, 'FAIL' => 0];
foreach ($results as $item) {
    if (isset($summary[$item['status']])) {
        $summary[$item['status']]++;
    }
}

if ($isCli) {
    echo "\n=== PREFLIGHT CHECK - HITO ONCOLOGY ===\n";
    echo 'Fecha: ' . date('Y-m-d H:i:s') . "\n";
    echo 'Modo fix: ' . ($fixMode ? 'ON' : 'OFF') . "\n\n";

    foreach ($results as $item) {
        echo '[' . $item['status'] . '] ' . $item['check'] . ' - ' . $item['detail'] . "\n";
        if ($item['hint'] !== '') {
            echo '      Sugerencia: ' . $item['hint'] . "\n";
        }
    }

    echo "\n=== RESUMEN ===\n";
    echo 'OK: ' . $summary['OK'] . "\n";
    echo 'WARN: ' . $summary['WARN'] . "\n";
    echo 'FAIL: ' . $summary['FAIL'] . "\n\n";

    if (!$fixMode) {
        echo "Tip: ejecuta con --fix para crear directorios faltantes antes de revalidar.\n";
    }

    exit($summary['FAIL'] > 0 ? 1 : 0);
}

$baseUrl = strtok((string) ($_SERVER['REQUEST_URI'] ?? ''), '?');
$fixUrl = $baseUrl . '?fix=1';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Preflight Check - Hito Oncology</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 24px; color: #1f2937; }
        h1 { margin-bottom: 4px; }
        .meta { color: #4b5563; margin-bottom: 18px; }
        .summary { margin: 16px 0; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; }
        .ok { color: #166534; }
        .warn { color: #92400e; }
        .fail { color: #991b1b; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #e5e7eb; padding: 8px; text-align: left; vertical-align: top; }
        th { background: #f3f4f6; }
        .badge { display: inline-block; min-width: 48px; text-align: center; padding: 2px 8px; border-radius: 999px; font-size: 12px; font-weight: bold; }
        .badge-ok { background: #dcfce7; color: #166534; }
        .badge-warn { background: #fef3c7; color: #92400e; }
        .badge-fail { background: #fee2e2; color: #991b1b; }
        .actions { margin: 18px 0; }
        .button { display: inline-block; padding: 8px 12px; border-radius: 6px; text-decoration: none; border: 1px solid #9ca3af; color: #111827; }
    </style>
</head>
<body>
    <h1>Preflight Check - Hito Oncology</h1>
    <div class="meta">Fecha: <?php echo html(date('Y-m-d H:i:s')); ?> | Modo fix: <?php echo $fixMode ? 'ON' : 'OFF'; ?></div>

    <div class="summary">
        <strong>Resumen:</strong>
        <span class="ok">OK: <?php echo (int) $summary['OK']; ?></span> |
        <span class="warn">WARN: <?php echo (int) $summary['WARN']; ?></span> |
        <span class="fail">FAIL: <?php echo (int) $summary['FAIL']; ?></span>
    </div>

    <div class="actions">
        <?php if (!$fixMode): ?>
            <a class="button" href="<?php echo html($fixUrl); ?>">Re-ejecutar con fix=1</a>
        <?php else: ?>
            <a class="button" href="<?php echo html($baseUrl); ?>">Ejecutar sin fix</a>
        <?php endif; ?>
    </div>

    <table>
        <thead>
            <tr>
                <th>Estado</th>
                <th>Chequeo</th>
                <th>Detalle</th>
                <th>Sugerencia</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($results as $item): ?>
                <tr>
                    <td>
                        <?php if ($item['status'] === 'OK'): ?>
                            <span class="badge badge-ok">OK</span>
                        <?php elseif ($item['status'] === 'WARN'): ?>
                            <span class="badge badge-warn">WARN</span>
                        <?php else: ?>
                            <span class="badge badge-fail">FAIL</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo html($item['check']); ?></td>
                    <td><?php echo html($item['detail']); ?></td>
                    <td><?php echo html($item['hint']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
