<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Core::redir('./?view=medics');
    exit;
}

$medic_id = isset($_POST['medic_id']) ? intval($_POST['medic_id']) : 0;
$category_id = isset($_POST['category_id']) ? intval($_POST['category_id']) : 0;

if ($medic_id <= 0 || $category_id <= 0) {
    Core::alert('Debe seleccionar un medico y un area validos');
    Core::redir('./?view=medics');
    exit;
}

if (!MedicCategoryData::exists($medic_id, $category_id)) {
    Executor::doit("INSERT INTO medic_category (medic_id, category_id) VALUES (" . $medic_id . ", " . $category_id . ")");
    Core::alert('Area agregada al medico');
} else {
    Core::alert('El medico ya tiene esa area asignada');
}

Core::redir('./?view=editmedic&id=' . $medic_id);
?>