<?php
include "core/app/model/OncologyWaitlistData.php";
include "core/app/model/OncologyChairData.php";
include "core/app/model/OncologySchedulingService.php";

$waitlist_items = OncologyWaitlistData::getAll();
?>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="btn-group pull-right">
                <a href="index.php?view=newoncologywaitlist" class="btn btn-default">
                    <i class='fa fa-plus'></i> Agregar a Lista de Espera
                </a>
                <button type="button" class="btn btn-success" onclick="processWaitlist()">
                    <i class='fa fa-magic'></i> Procesar Lista Automáticamente
                </button>
            </div>
            <h1>Lista de Espera - Oncología</h1>
            <br>
            
            <?php if(count($waitlist_items) > 0): ?>
            <div class="box box-primary">
                <div class="box-body">
                    <table class="table table-bordered table-hover oncology-waitlist-table">
                        <thead>
                            <tr>
                                <th>Prioridad</th>
                                <th>Paciente</th>
                                <th>Tipo de Tratamiento</th>
                                <th>Fecha Solicitada</th>
                                <th>Hora Solicitada</th>
                                <th>Duración</th>
                                <th>Estado</th>
                                <th>Fecha Creación</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($waitlist_items as $item): ?>
                            <tr class="<?php echo $item->status == 'pending' ? 'warning' : ($item->status == 'assigned' ? 'success' : ''); ?>">
                                <td>                                    <span class="label <?php 
                                        echo $item->priority_level == 5 ? 'label-danger' : 
                                            ($item->priority_level == 4 ? 'label-danger' : 
                                            ($item->priority_level == 3 ? 'label-warning' : 
                                            ($item->priority_level == 2 ? 'label-info' : 'label-default'))); 
                                    ?>">
                                        <?php 
                                        $priorities = [
                                            1 => 'Baja', 
                                            2 => 'Media', 
                                            3 => 'Alta', 
                                            4 => 'Urgente',
                                            5 => 'Crítica'
                                        ];
                                        echo isset($priorities[$item->priority_level]) ? $priorities[$item->priority_level] : 'Desconocida'; 
                                        ?>
                                    </span>
                                </td>
                                <td><?php echo $item->getPacient()->name . " " . $item->getPacient()->lastname; ?></td>
                                <td><?php echo $item->treatment_type; ?></td>
                                <td><?php echo $item->requested_date; ?></td>
                                <td><?php echo substr($item->requested_time, 0, 5); ?></td>
                                <td><?php echo $item->duration_minutes; ?> min</td>
                                <td>
                                    <span class="label <?php 
                                        echo $item->status == 'pending' ? 'label-warning' : 
                                            ($item->status == 'assigned' ? 'label-success' : 
                                            ($item->status == 'completed' ? 'label-info' : 'label-default')); 
                                    ?>">
                                        <?php 
                                        $statuses = [
                                            'pending' => 'Pendiente', 
                                            'assigned' => 'Asignado', 
                                            'completed' => 'Completado', 
                                            'cancelled' => 'Cancelado'
                                        ];
                                        echo $statuses[$item->status]; 
                                        ?>
                                    </span>
                                </td>
                                <td><?php echo date('d/m/Y H:i', strtotime($item->created_at)); ?></td>
                                <td style="width:120px;">
                                    <?php if($item->status == 'pending'): ?>
                                    <button class="btn btn-xs btn-success" onclick="autoAssign(<?php echo $item->id; ?>)" title="Asignar Automáticamente">
                                        <i class="fa fa-magic"></i>
                                    </button>
                                    <?php endif; ?>
                                    
                                    <?php if($item->status == 'assigned' && $item->reservation_id): ?>
                                    <a href="index.php?view=editreservation&id=<?php echo $item->reservation_id; ?>" class="btn btn-xs btn-warning" title="Ver Cita">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    <?php endif; ?>
                                    
                                    <a href="index.php?view=editoncologywaitlist&id=<?php echo $item->id; ?>" class="btn btn-xs btn-warning" title="Editar">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <a href="index.php?action=deloncologywaitlist&id=<?php echo $item->id; ?>" class="btn btn-xs btn-danger" onclick="return confirm('¿Está seguro?')" title="Eliminar">
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
                    <p class="text-center">No hay elementos en la lista de espera de oncología.</p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<script>
function autoAssign(waitlistId) {
    if (confirm('¿Desea asignar automáticamente esta cita?')) {
        $.ajax({
            url: 'index.php?action=autoassignoncology',
            method: 'POST',
            data: { waitlist_id: waitlistId },
            success: function(response) {
                var result = JSON.parse(response);
                if (result.success) {
                    alert('Cita asignada exitosamente');
                    location.reload();
                } else {
                    alert('No se pudo asignar la cita: ' + result.message);
                }
            },
            error: function() {
                alert('Error al procesar la solicitud');
            }
        });
    }
}

function processWaitlist() {
    if (confirm('¿Desea procesar automáticamente toda la lista de espera?')) {
        $.ajax({
            url: 'index.php?action=processwaitlist',
            method: 'POST',
            success: function(response) {
                var result = JSON.parse(response);
                alert('Se asignaron ' + result.assigned_count + ' citas automáticamente');
                location.reload();
            },
            error: function() {
                alert('Error al procesar la lista de espera');
            }
        });
    }
}

$(document).ready(function() {
    // Initialize DataTable with unique class to avoid conflicts
    $('.oncology-waitlist-table').DataTable({
        "pageLength": 25,
        "language": {
            "sProcessing":    "Procesando...",
            "sLengthMenu":    "Mostrar _MENU_ registros",
            "sZeroRecords":   "No se encontraron resultados",
            "sEmptyTable":    "Ningún dato disponible en esta tabla",
            "sInfo":          "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "sInfoEmpty":     "Mostrando registros del 0 al 0 de un total de 0 registros",
            "sInfoFiltered":  "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix":   "",
            "sSearch":        "Buscar:",
            "sUrl":           "",
            "sInfoThousands":  ",",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
                "sFirst":    "Primero",
                "sLast":    "Último",
                "sNext":    "Siguiente",
                "sPrevious": "Anterior"
            },
            "oAria": {
                "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            }
        },
        "order": [[ 0, "desc" ], [ 7, "asc" ]]
    });
});
</script>
