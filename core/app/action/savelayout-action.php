<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Metodo no permitido'
    ]);
    exit;
}

$layout = isset($_POST['layout']) ? $_POST['layout'] : '';

if ($layout === '') {
    echo json_encode([
        'success' => false,
        'message' => 'No se recibio layout'
    ]);
    exit;
}

$dir = __DIR__ . '/../data';
if (!is_dir($dir)) {
    mkdir($dir, 0755, true);
}

$file = $dir . '/sillon_layout.json';
$result = file_put_contents($file, $layout, LOCK_EX);

if ($result !== false) {
    echo json_encode([
        'success' => true,
        'message' => 'Layout guardado'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'No se pudo guardar el layout'
    ]);
}
?>