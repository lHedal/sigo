<?php
include "core/app/model/OncologyChairData.php";
include "core/app/model/ReservationData.php";

// Obtener reservas de oncología
$reservations = ReservationData::getOncologyReservations();
$chairs = OncologyChairData::getAll();

// Preparar datos para FullCalendar
$events = [];
foreach($reservations as $r) {
    $chair = $r->getChair();
    $chairName = $chair ? $chair->name : 'Sin sillón';
    
    $events[] = [
        'id' => $r->id,
        'title' => $r->getPacient()->name . ' ' . $r->getPacient()->lastname . ' - ' . $chairName,
        'start' => $r->date_at . 'T' . $r->time_at,
        'end' => $r->date_at . 'T' . date('H:i:s', strtotime($r->time_at . ' +' . ($r->duration ?: 60) . ' minutes')),
        'resourceId' => $chair ? 'chair' . $chair->id : 'nochair',
        'color' => $r->status_id == 1 ? '#f39c12' : ($r->status_id == 2 ? '#00a65a' : '#dd4b39'),
        'description' => $r->title,
        'pacient' => $r->getPacient()->name . ' ' . $r->getPacient()->lastname,
        'medic' => $r->getMedic()->name . ' ' . $r->getMedic()->lastname
    ];
}

$thejson = $events;
?>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Calendario de Oncología - Sillones</h3>
                    <div class="box-tools pull-right">
                        <a href="index.php?view=newreservation" class="btn btn-primary btn-sm">
                            <i class="fa fa-plus"></i> Nueva Cita
                        </a>
                        <a href="index.php?view=oncologywaitlist" class="btn btn-warning btn-sm">
                            <i class="fa fa-clock-o"></i> Lista de Espera
                        </a>
                    </div>
                </div>
                <div class="box-body">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal para detalles del evento -->
<div class="modal fade" id="eventModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Detalles de la Cita</h4>
            </div>
            <div class="modal-body" id="eventDetails">
                <!-- Los detalles se cargan aquí -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <a href="#" id="editEventBtn" class="btn btn-primary">Editar</a>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#calendar').fullCalendar({
        schedulerLicenseKey: 'CC-Attribution-NonCommercial-NoDerivatives',
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'resourceTimelineDay,resourceTimelineWeek,month'
        },
        defaultView: 'resourceTimelineDay',
        height: 600,
        slotDuration: '00:30:00',
        minTime: '08:00:00',
        maxTime: '18:00:00',
        businessHours: {
            start: '08:00',
            end: '18:00',
            dow: [1, 2, 3, 4, 5, 6] // Lunes a Sábado
        },
        resourceAreaWidth: '200px',
        resources: [
            <?php foreach($chairs as $chair): ?>
            { 
                id: 'chair<?php echo $chair->id; ?>', 
                title: '<?php echo $chair->name; ?>',
                eventColor: '#3c8dbc'
            },
            <?php endforeach; ?>
            { 
                id: 'nochair', 
                title: 'Sin sillón asignado',
                eventColor: '#dd4b39'
            }
        ],
        events: <?php echo json_encode($thejson); ?>,
        eventClick: function(event) {
            showEventDetails(event);
        },
        select: function(start, end, jsEvent, view, resource) {
            // Redirigir a nueva cita con datos pre-llenados
            var startDate = start.format('YYYY-MM-DD');
            var startTime = start.format('HH:mm:ss');
            var chairId = resource.id.replace('chair', '');
            
            if(resource.id !== 'nochair') {
                window.location.href = 'index.php?view=newreservation&date=' + startDate + 
                                     '&time=' + startTime + '&chair_id=' + chairId;
            } else {
                window.location.href = 'index.php?view=newreservation&date=' + startDate + 
                                     '&time=' + startTime;
            }
        },
        selectable: true,
        selectHelper: true
    });
});

function showEventDetails(event) {
    var details = '<div class="row">' +
        '<div class="col-sm-6"><strong>Paciente:</strong></div>' +
        '<div class="col-sm-6">' + event.pacient + '</div>' +
        '</div><div class="row">' +
        '<div class="col-sm-6"><strong>Médico:</strong></div>' +
        '<div class="col-sm-6">' + event.medic + '</div>' +
        '</div><div class="row">' +
        '<div class="col-sm-6"><strong>Inicio:</strong></div>' +
        '<div class="col-sm-6">' + event.start.format('DD/MM/YYYY HH:mm') + '</div>' +
        '</div><div class="row">' +
        '<div class="col-sm-6"><strong>Fin:</strong></div>' +
        '<div class="col-sm-6">' + event.end.format('DD/MM/YYYY HH:mm') + '</div>' +
        '</div><div class="row">' +
        '<div class="col-sm-6"><strong>Descripción:</strong></div>' +
        '<div class="col-sm-6">' + (event.description || 'Sin descripción') + '</div>' +
        '</div>';
    
    $('#eventDetails').html(details);
    $('#editEventBtn').attr('href', 'index.php?view=editreservation&id=' + event.id);
    $('#eventModal').modal('show');
}
</script>

<style>
.fc-resource-area th {
    text-align: center;
    background-color: #f4f4f4;
}

.fc-timeline-event {
    border-radius: 3px;
    font-size: 12px;
}

.fc-resource-area {
    border-right: 1px solid #ddd;
}
</style>
