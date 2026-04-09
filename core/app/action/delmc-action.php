<?php
$medic_id = isset($_GET['medic_id']) ? intval($_GET['medic_id']) : 0;
$category_id = isset($_GET['cat_id']) ? intval($_GET['cat_id']) : 0;

if ($medic_id > 0 && $category_id > 0) {
    MedicCategoryData::delByMedicAndCategory($medic_id, $category_id);
    Core::alert('Area removida del medico');
}

if ($medic_id > 0) {
    Core::redir('./?view=editmedic&id=' . $medic_id);
} else {
    Core::redir('./?view=medics');
}
?>