<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Metodo no permitido'
    ]);
    exit;
}

$chair_id = isset($_POST['chair_id']) ? intval($_POST['chair_id']) : 0;
$position_x = isset($_POST['position_x']) ? intval($_POST['position_x']) : 0;
$position_y = isset($_POST['position_y']) ? intval($_POST['position_y']) : 0;

if ($chair_id <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'ID de sillon invalido'
    ]);
    exit;
}

$con = Database::getCon();
$db_info = $con->query('SELECT DATABASE() as db_name')->fetch_assoc();
$db_name = $db_info['db_name'];

function ensureChairColumn($con, $db_name, $column_name, $definition) {
    $db_name_safe = $con->real_escape_string($db_name);
    $column_safe = $con->real_escape_string($column_name);

    $check_sql = "SELECT COUNT(*) as total FROM information_schema.COLUMNS WHERE TABLE_SCHEMA='" . $db_name_safe . "' AND TABLE_NAME='oncology_chair' AND COLUMN_NAME='" . $column_safe . "'";
    $check_result = $con->query($check_sql);
    $row = $check_result ? $check_result->fetch_assoc() : ['total' => 0];

    if ((int)$row['total'] === 0) {
        $con->query("ALTER TABLE oncology_chair ADD COLUMN " . $column_name . " " . $definition);
    }
}

ensureChairColumn($con, $db_name, 'position_x', 'INT NOT NULL DEFAULT 0');
ensureChairColumn($con, $db_name, 'position_y', 'INT NOT NULL DEFAULT 0');

$result = Executor::doit("UPDATE oncology_chair SET position_x=$position_x, position_y=$position_y WHERE id=$chair_id");

if ($result && isset($result[0]) && $result[0]) {
    echo json_encode([
        'success' => true,
        'message' => 'Posicion guardada'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'No se pudo guardar la posicion'
    ]);
}
?>