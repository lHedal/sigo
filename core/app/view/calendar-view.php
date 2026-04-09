<?php
// Vista del calendario general del sistema
?>
<div class="row">
    <div class="col-md-12">
        <div class="page-header">
            <h1>
                <i class="fa fa-calendar"></i> Calendario General
                <small>Vista completa de citas y eventos</small>
            </h1>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-calendar"></i> Calendario de Citas
                </h3>
            </div>
            <div class="panel-body">
                <div id="calendar"></div>
            </div>
        </div>
    </div>
</div>

<!-- Incluir FullCalendar -->
<link href="plugins/fullcalendar/fullcalendar.css" rel="stylesheet">
<script src="plugins/fullcalendar/lib/moment.min.js"></script>
<script src="plugins/fullcalendar/fullcalendar.min.js"></script>
<script src="plugins/fullcalendar/lang/es.js"></script>

<script>
$(document).ready(function() {
    $('#calendar').fullCalendar({
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,agendaWeek,agendaDay'
        },
        lang: 'es',
        defaultView: 'month',
        editable: false,
        eventLimit: true,
        events: function(start, end, timezone, callback) {
            // Cargar eventos desde el servidor
            $.ajax({
                url: 'index.php?action=getcalendarevents',
                type: 'GET',
                data: {
                    start: start.format(),
                    end: end.format()
                },
                success: function(data) {
                    var events = [];
                    try {
                        if (typeof data === 'string') {
                            data = JSON.parse(data);
                        }
                        if (data && data.length) {
                            events = data;
                        }
                    } catch(e) {
                        console.log('Error parsing calendar data:', e);
                    }
                    callback(events);
                },
                error: function() {
                    callback([]);
                }
            });
        },
        eventClick: function(event) {
            if (event.url) {
                window.open(event.url, '_blank');
                return false;
            }
        }
    });
});
</script>
