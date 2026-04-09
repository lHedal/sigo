<?php
include "core/app/model/OncologyChairData.php";

$chairs = OncologyChairData::getAll();
?>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="btn-group pull-right">
                <a href="index.php?view=newoncologychair" class="btn btn-default">
                    <i class='fa fa-plus'></i> Nuevo Sillón
                </a>
            </div>
            <h1>Gestión de Sillones - Oncología</h1>
            <br>
            
            <?php if(count($chairs) > 0): ?>
            <div class="box box-primary">
                <div class="box-body">
                    <table class="table table-bordered table-hover oncologychairs-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Estado</th>
                                <th>Fecha Creación</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($chairs as $chair): ?>
                            <tr>
                                <td><?php echo $chair->id; ?></td>
                                <td><?php echo $chair->name; ?></td>
                                <td><?php echo $chair->description; ?></td>
                                <td>
                                    <span class="label <?php echo $chair->is_active ? 'label-success' : 'label-danger'; ?>">
                                        <?php echo $chair->is_active ? 'Activo' : 'Inactivo'; ?>
                                    </span>
                                </td>
                                <td><?php echo date('d/m/Y H:i', strtotime($chair->created_at)); ?></td>
                                <td style="width:120px;">
                                    <a href="index.php?view=editoncologychair&id=<?php echo $chair->id; ?>" class="btn btn-xs btn-warning" title="Editar">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <a href="index.php?view=chairschedule&id=<?php echo $chair->id; ?>" class="btn btn-xs btn-info" title="Ver Horarios">
                                        <i class="fa fa-calendar"></i>
                                    </a>
                                    <a href="index.php?action=deloncologychair&id=<?php echo $chair->id; ?>" class="btn btn-xs btn-danger" onclick="return confirm('¿Está seguro?')" title="Eliminar">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php else: ?>
            <div class="box box-primary">
                <div class="box-body">
                    <p class="text-center">No hay sillones registrados.</p>
                </div>
            </div>
            <?php endif; ?>

<script>
$(document).ready(function() {
    $('.oncologychairs-table').DataTable({
        "pageLength": 25,
        "language": {
            "lengthMenu": "Mostrar _MENU_ registros por página",
            "zeroRecords": "No se encontraron registros",
            "info": "Mostrando página _PAGE_ de _PAGES_",
            "infoEmpty": "No hay registros disponibles",
            "infoFiltered": "(filtrado de _MAX_ registros totales)",
            "search": "Buscar:",
            "paginate": {
                "first": "Primero",
                "last": "Último",
                "next": "Siguiente",
                "previous": "Anterior"
            }
        }
    });
});
</script>

        </div>
    </div>
</section>
