<?php
$medic = MedicData::getById($_SESSION['medic_id']);
?>

<div class="row">
    <div class="col-md-12">
        <div class="page-header">
            <h1>
                <i class="fa fa-cog"></i> Configuración de Perfil Médico
                <small>Dr. <?php echo $medic->name . " " . $medic->lastname; ?></small>
            </h1>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-user-md"></i> Información Personal
                </h3>
            </div>
            <div class="panel-body">
                <form class="form-horizontal" method="post" action="index.php?action=updatemedic" role="form">
                    <div class="form-group">
                        <label for="name" class="col-lg-3 control-label">Nombre*</label>
                        <div class="col-md-8">
                            <input type="text" name="name" value="<?php echo $medic->name; ?>" 
                                   class="form-control" id="name" placeholder="Nombre" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="lastname" class="col-lg-3 control-label">Apellido*</label>
                        <div class="col-md-8">
                            <input type="text" name="lastname" value="<?php echo $medic->lastname; ?>" 
                                   class="form-control" id="lastname" placeholder="Apellido" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="email" class="col-lg-3 control-label">Email*</label>
                        <div class="col-md-8">
                            <input type="email" name="email" value="<?php echo $medic->email; ?>" 
                                   class="form-control" id="email" placeholder="Email" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone" class="col-lg-3 control-label">Teléfono</label>
                        <div class="col-md-8">
                            <input type="text" name="phone" value="<?php echo $medic->phone; ?>" 
                                   class="form-control" id="phone" placeholder="Teléfono">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="category_id" class="col-lg-3 control-label">Especialidad</label>
                        <div class="col-md-8">
                            <?php $categories = CategoryData::getAll(); ?>
                            <select name="category_id" class="form-control" id="category_id">
                                <option value="">-- Seleccionar Especialidad --</option>
                                <?php foreach($categories as $category): ?>
                                    <option value="<?php echo $category->id; ?>" 
                                            <?php echo ($medic->category_id == $category->id) ? 'selected' : ''; ?>>
                                        <?php echo $category->name; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="address" class="col-lg-3 control-label">Dirección</label>
                        <div class="col-md-8">
                            <textarea name="address" class="form-control" id="address" 
                                      rows="3" placeholder="Dirección completa"><?php echo $medic->address; ?></textarea>
                        </div>
                    </div>
                    
                    <hr>
                    <h4><i class="fa fa-key"></i> Cambiar Contraseña</h4>
                    
                    <div class="form-group">
                        <label for="current_password" class="col-lg-3 control-label">Contraseña Actual</label>
                        <div class="col-md-8">
                            <input type="password" name="current_password" class="form-control" 
                                   id="current_password" placeholder="Contraseña actual">
                            <p class="help-block">Solo necesaria si deseas cambiar la contraseña</p>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="new_password" class="col-lg-3 control-label">Nueva Contraseña</label>
                        <div class="col-md-8">
                            <input type="password" name="new_password" class="form-control" 
                                   id="new_password" placeholder="Nueva contraseña">
                            <p class="help-block">Mínimo 6 caracteres</p>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password" class="col-lg-3 control-label">Confirmar Contraseña</label>
                        <div class="col-md-8">
                            <input type="password" name="confirm_password" class="form-control" 
                                   id="confirm_password" placeholder="Confirmar nueva contraseña">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="col-lg-offset-3 col-lg-8">
                            <input type="hidden" name="medic_id" value="<?php echo $medic->id; ?>">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fa fa-save"></i> Actualizar Perfil
                            </button>
                            <a href="index.php?view=medichome" class="btn btn-default btn-lg">
                                <i class="fa fa-arrow-left"></i> Volver al Dashboard
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <!-- Información del perfil -->
        <div class="panel panel-info">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-info-circle"></i> Información del Perfil
                </h3>
            </div>
            <div class="panel-body">
                <div class="medic-avatar text-center">
                    <i class="fa fa-user-md fa-5x text-muted"></i>
                    <h4>Dr. <?php echo $medic->name . " " . $medic->lastname; ?></h4>
                    <p class="text-muted"><?php echo $medic->email; ?></p>
                </div>
                
                <hr>
                
                <h5><i class="fa fa-chart-line"></i> Estadísticas</h5>
                <ul class="list-unstyled">
                    <?php
                    // Obtener estadísticas del médico
                    $reservations = ReservationData::getByMedicId($medic->id);
                    $total_reservations = $reservations ? count($reservations) : 0;
                    
                    $completed = 0;
                    $today_count = 0;
                    $today = date('Y-m-d');
                    
                    if($reservations) {
                        foreach($reservations as $res) {
                            if($res->status_id == 3) $completed++;
                            if($res->date_at == $today) $today_count++;
                        }
                    }
                    ?>
                    <li><strong>Total de Citas:</strong> <?php echo $total_reservations; ?></li>
                    <li><strong>Citas Completadas:</strong> <?php echo $completed; ?></li>
                    <li><strong>Citas Hoy:</strong> <?php echo $today_count; ?></li>
                    <li><strong>Última Actualización:</strong> <?php echo date('d/m/Y H:i'); ?></li>
                </ul>
            </div>
        </div>
        
        <!-- Acceso rápido -->
        <div class="panel panel-success">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-link"></i> Acceso Rápido
                </h3>
            </div>
            <div class="panel-body">
                <div class="list-group">
                    <a href="index.php?view=medicreservations" class="list-group-item">
                        <i class="fa fa-calendar"></i> Mis Citas
                    </a>
                    <a href="index.php?view=pacients" class="list-group-item">
                        <i class="fa fa-users"></i> Mis Pacientes
                    </a>
                    <a href="index.php?view=oncologywaitlist" class="list-group-item">
                        <i class="fa fa-clock-o"></i> Lista de Espera
                    </a>
                    <a href="index.php?view=oncologychairs" class="list-group-item">
                        <i class="fa fa-bed"></i> Sillones
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Validar contraseñas
document.querySelector('form').addEventListener('submit', function(e) {
    var newPassword = document.getElementById('new_password').value;
    var confirmPassword = document.getElementById('confirm_password').value;
    var currentPassword = document.getElementById('current_password').value;
    
    // Si se intenta cambiar la contraseña
    if(newPassword || confirmPassword || currentPassword) {
        if(!currentPassword) {
            alert('Debes ingresar tu contraseña actual para cambiarla.');
            e.preventDefault();
            return false;
        }
        
        if(newPassword !== confirmPassword) {
            alert('Las contraseñas nuevas no coinciden.');
            e.preventDefault();
            return false;
        }
        
        if(newPassword.length < 6) {
            alert('La nueva contraseña debe tener al menos 6 caracteres.');
            e.preventDefault();
            return false;
        }
    }
});
</script>

<style>
.medic-avatar {
    padding: 20px;
    background: #f8f9fa;
    border-radius: 10px;
    margin-bottom: 15px;
}

.list-group-item {
    border: none;
    padding: 10px 15px;
}

.list-group-item:hover {
    background-color: #f0f0f0;
}
</style>
