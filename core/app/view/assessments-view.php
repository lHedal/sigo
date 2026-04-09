<?php
// Verificar que el usuario esté logueado
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["user_id"])) {
    Core::redir("./");
    exit;
}

// Obtener parámetros de filtrado y búsqueda
$search_criteria = [];
if (!empty($_GET['patient_name'])) {
    $search_criteria['pacient_name'] = $_GET['patient_name'];
}
if (!empty($_GET['diagnosis'])) {
    $search_criteria['diagnosis'] = $_GET['diagnosis'];
}
if (!empty($_GET['status'])) {
    $search_criteria['status'] = $_GET['status'];
}
if (!empty($_GET['medic_id'])) {
    $search_criteria['medic_id'] = $_GET['medic_id'];
}
if (!empty($_GET['date_from'])) {
    $search_criteria['date_from'] = $_GET['date_from'];
}
if (!empty($_GET['date_to'])) {
    $search_criteria['date_to'] = $_GET['date_to'];
}

// Obtener evaluaciones
$assessments = !empty($search_criteria) 
    ? InitialAssessmentData::search($search_criteria)
    : InitialAssessmentData::getAllWithDetails();

// Obtener lista de médicos para filtro
$medics = MedicData::getAll();

// Obtener estadísticas
$statistics = InitialAssessmentData::getStatistics();
?>

<section class="content">
    <div class="row">
        <!-- Estadísticas rápidas -->
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-aqua">
                <div class="inner">
                    <h3><?php echo $statistics['total']; ?></h3>
                    <p>Total Evaluaciones</p>
                </div>
                <div class="icon">
                    <i class="fa fa-stethoscope"></i>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-green">
                <div class="inner">
                    <h3><?php echo $statistics['by_status']['completed'] ?? 0; ?></h3>
                    <p>Completadas</p>
                </div>
                <div class="icon">
                    <i class="fa fa-check"></i>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-yellow">
                <div class="inner">
                    <h3><?php echo $statistics['by_status']['draft'] ?? 0; ?></h3>
                    <p>Borradores</p>
                </div>
                <div class="icon">
                    <i class="fa fa-edit"></i>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-red">
                <div class="inner">
                    <h3><?php echo $statistics['recent'] ?? 0; ?></h3>
                    <p>Últimos 30 días</p>
                </div>
                <div class="icon">
                    <i class="fa fa-calendar"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <i class="fa fa-list"></i> Evaluaciones Iniciales Oncológicas
                    </h3>
                    <div class="box-tools pull-right">
                        <a href="index.php?view=initialassessment" class="btn btn-success btn-sm">
                            <i class="fa fa-plus"></i> Nueva Evaluación
                        </a>
                        <button type="button" class="btn btn-default btn-sm" data-toggle="collapse" data-target="#search-filters">
                            <i class="fa fa-filter"></i> Filtros
                        </button>
                    </div>
                </div>

                <!-- Panel de filtros -->
                <div class="collapse" id="search-filters">
                    <div class="box-body border-bottom">
                        <form method="GET" action="" class="form-horizontal">
                            <input type="hidden" name="view" value="assessments">
                            
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="patient_name" class="control-label">Paciente</label>
                                        <input type="text" name="patient_name" id="patient_name" class="form-control" 
                                               placeholder="Nombre del paciente" value="<?php echo $_GET['patient_name'] ?? ''; ?>">
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="diagnosis" class="control-label">Diagnóstico</label>
                                        <input type="text" name="diagnosis" id="diagnosis" class="form-control" 
                                               placeholder="Diagnóstico" value="<?php echo $_GET['diagnosis'] ?? ''; ?>">
                                    </div>
                                </div>
                                
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="status" class="control-label">Estado</label>
                                        <select name="status" id="status" class="form-control">
                                            <option value="">Todos</option>
                                            <option value="draft" <?php echo ($_GET['status'] ?? '') == 'draft' ? 'selected' : ''; ?>>Borrador</option>
                                            <option value="completed" <?php echo ($_GET['status'] ?? '') == 'completed' ? 'selected' : ''; ?>>Completada</option>
                                            <option value="reviewed" <?php echo ($_GET['status'] ?? '') == 'reviewed' ? 'selected' : ''; ?>>Revisada</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="medic_id" class="control-label">Médico</label>
                                        <select name="medic_id" id="medic_id" class="form-control">
                                            <option value="">Todos</option>
                                            <?php foreach($medics as $medic): ?>
                                            <option value="<?php echo $medic->id; ?>" 
                                                    <?php echo ($_GET['medic_id'] ?? '') == $medic->id ? 'selected' : ''; ?>>
                                                Dr. <?php echo $medic->name . " " . $medic->lastname; ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="control-label">&nbsp;</label>
                                        <div>
                                            <button type="submit" class="btn btn-primary btn-block">
                                                <i class="fa fa-search"></i> Buscar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="date_from" class="control-label">Desde</label>
                                        <input type="date" name="date_from" id="date_from" class="form-control" 
                                               value="<?php echo $_GET['date_from'] ?? ''; ?>">
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="date_to" class="control-label">Hasta</label>
                                        <input type="date" name="date_to" id="date_to" class="form-control" 
                                               value="<?php echo $_GET['date_to'] ?? ''; ?>">
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label">&nbsp;</label>
                                        <div>
                                            <a href="index.php?view=assessments" class="btn btn-default btn-block">
                                                <i class="fa fa-refresh"></i> Limpiar Filtros
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                        </form>
                    </div>
                </div>

                <div class="box-body">
                    <?php if (!empty($assessments)): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="assessments-table">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Paciente</th>
                                    <th>Edad</th>
                                    <th>Diagnóstico</th>
                                    <th>Estadio</th>
                                    <th>ECOG</th>
                                    <th>Prioridad</th>
                                    <th>Médico</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($assessments as $assessment): ?>
                                <tr>
                                    <td>
                                        <small><?php echo date('d/m/Y', strtotime($assessment['evaluation_date'])); ?></small>
                                    </td>
                                    <td>
                                        <strong><?php echo $assessment['patient_name'] . " " . $assessment['patient_lastname']; ?></strong>
                                        <?php if (!empty($assessment['patient_rut'])): ?>
                                        <br><small class="text-muted">RUT: <?php echo $assessment['patient_rut']; ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (isset($assessment['patient_age'])): ?>
                                        <span class="label label-default"><?php echo $assessment['patient_age']; ?> años</span>
                                        <?php else: ?>
                                        <span class="text-muted">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span title="<?php echo htmlspecialchars($assessment['primary_diagnosis']); ?>">
                                            <?php echo strlen($assessment['primary_diagnosis']) > 30 
                                                ? substr($assessment['primary_diagnosis'], 0, 30) . '...' 
                                                : $assessment['primary_diagnosis']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if (!empty($assessment['tumor_stage'])): ?>
                                        <span class="label label-info"><?php echo $assessment['tumor_stage']; ?></span>
                                        <?php else: ?>
                                        <span class="text-muted">N/E</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="label label-default">
                                            <?php echo $assessment['ecog_performance_status']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php
                                        $priority_class = '';
                                        switch($assessment['treatment_priority']) {
                                            case 5: $priority_class = 'label-danger'; break;
                                            case 4: $priority_class = 'label-warning'; break;
                                            case 3: $priority_class = 'label-info'; break;
                                            default: $priority_class = 'label-default';
                                        }
                                        ?>
                                        <span class="label <?php echo $priority_class; ?>">
                                            <?php echo $assessment['treatment_priority']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <small>Dr. <?php echo $assessment['medic_name'] . " " . $assessment['medic_lastname']; ?></small>
                                    </td>
                                    <td>
                                        <?php
                                        $status_class = '';
                                        $status_text = '';
                                        switch($assessment['status']) {
                                            case 'draft':
                                                $status_class = 'label-warning';
                                                $status_text = 'Borrador';
                                                break;
                                            case 'completed':
                                                $status_class = 'label-success';
                                                $status_text = 'Completada';
                                                break;
                                            case 'reviewed':
                                                $status_class = 'label-primary';
                                                $status_text = 'Revisada';
                                                break;
                                            case 'archived':
                                                $status_class = 'label-default';
                                                $status_text = 'Archivada';
                                                break;
                                            default:
                                                $status_class = 'label-default';
                                                $status_text = $assessment['status'];
                                        }
                                        ?>
                                        <span class="label <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown">
                                                <i class="fa fa-cogs"></i> <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a href="index.php?view=initialassessment&id=<?php echo $assessment['id']; ?>">
                                                        <i class="fa fa-eye"></i> Ver/Editar
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="index.php?view=pacients&opt=one&id=<?php echo $assessment['pacient_id']; ?>">
                                                        <i class="fa fa-user"></i> Ver Paciente
                                                    </a>
                                                </li>
                                                <?php if ($assessment['status'] == 'draft'): ?>
                                                <li class="divider"></li>
                                                <li>
                                                    <a href="#" onclick="deleteAssessment(<?php echo $assessment['id']; ?>)" class="text-danger">
                                                        <i class="fa fa-trash"></i> Eliminar Borrador
                                                    </a>
                                                </li>
                                                <?php endif; ?>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="alert alert-info text-center">
                        <h4><i class="fa fa-info-circle"></i> Sin Resultados</h4>
                        <p>No se encontraron evaluaciones iniciales que coincidan con los criterios de búsqueda.</p>
                        <a href="index.php?view=initialassessment" class="btn btn-success">
                            <i class="fa fa-plus"></i> Crear Primera Evaluación
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CSS personalizado -->
<style>
.small-box {
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.small-box .inner h3 {
    font-weight: bold;
}

.table-responsive {
    max-height: 600px;
    overflow-y: auto;
}

.table thead th {
    background-color: #f4f4f4;
    position: sticky;
    top: 0;
    z-index: 10;
}

.label {
    font-size: 11px;
}

.btn-group .dropdown-menu {
    min-width: 160px;
}

.border-bottom {
    border-bottom: 1px solid #f4f4f4 !important;
    padding-bottom: 20px;
    margin-bottom: 20px;
}

.alert h4 {
    margin-top: 0;
}

#search-filters {
    background-color: #f9f9f9;
}

.form-group {
    margin-bottom: 15px;
}

.dropdown-menu > li > a {
    padding: 8px 15px;
}

.dropdown-menu > li > a:hover {
    background-color: #f5f5f5;
}

.text-danger {
    color: #d73925 !important;
}

.text-muted {
    color: #777 !important;
}
</style>

<!-- JavaScript para funcionalidades -->
<script>
jQuery(document).ready(function() {
    // Inicializar DataTables si está disponible
    if (jQuery.fn.DataTable) {
        jQuery('#assessments-table').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
            },
            "pageLength": 25,
            "order": [[ 0, "desc" ]], // Ordenar por fecha descendente
            "columnDefs": [
                { "orderable": false, "targets": -1 } // Deshabilitar orden en columna acciones
            ]
        });
    }
    
    // Auto-envío del formulario de filtros al cambiar selects
    jQuery('#status, #medic_id').on('change', function() {
        jQuery(this).closest('form').submit();
    });
});

// Función para eliminar borrador
function deleteAssessment(id) {
    if (confirm('¿Está seguro de que desea eliminar este borrador de evaluación?\n\nEsta acción no se puede deshacer.')) {
        jQuery.ajax({
            url: 'index.php?action=deleteassessment',
            method: 'POST',
            data: { assessment_id: id },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    mostrarNotificacion('Borrador eliminado exitosamente', 'success');
                    setTimeout(function() {
                        window.location.reload();
                    }, 1500);
                } else {
                    mostrarNotificacion('Error al eliminar: ' + response.message, 'error');
                }
            },
            error: function() {
                mostrarNotificacion('Error de conexión al eliminar borrador', 'error');
            }
        });
    }
}

// Función para mostrar notificaciones
function mostrarNotificacion(mensaje, tipo) {
    if (typeof window.mostrarNotificacion === 'function') {
        window.mostrarNotificacion(mensaje, tipo);
    } else {
        alert(mensaje);
    }
}
</script>
