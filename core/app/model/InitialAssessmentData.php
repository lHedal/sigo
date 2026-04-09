<?php
/**
 * Modelo para manejar evaluaciones iniciales oncológicas
 * Maneja toda la información de evaluación médica pre-tratamiento
 */

class InitialAssessmentData {
    public static $tablename = "initial_assessment";
    
    // Propiedades del modelo
    public $id;
    public $pacient_id;
    public $evaluating_medic_id;
    public $evaluation_date;
    public $created_by;
    
    // Historia oncológica
    public $primary_diagnosis;
    public $tumor_stage;
    public $date_of_diagnosis;
    public $previous_treatments;
    public $family_history;
    
    // Estado funcional y síntomas
    public $ecog_performance_status;
    public $weight_loss;
    public $current_symptoms;
    public $pain_scale;
    public $symptoms_other;
    
    // Evaluación psicosocial
    public $support_system;
    public $psychological_state;
    public $coping_mechanisms;
    
    // Plan de tratamiento
    public $proposed_treatment;
    public $treatment_goals;
    public $estimated_duration;
    public $treatment_priority;
    public $treatment_notes;
    
    // Consentimientos y preocupaciones
    public $consents;
    public $patient_concerns;
    
    // Seguimiento
    public $next_appointment;
    public $follow_up_type;
    public $pending_studies;
    public $referrals;
    
    // Resumen y estado
    public $medical_summary;
    public $recommendations;
    public $status;
    public $created_at;
    public $updated_at;

    public function __construct(){
        $this->status = "draft";
        $this->evaluation_date = date('Y-m-d');
        $this->created_at = "NOW()";
        $this->updated_at = "NOW()";
    }

    /**
     * Agregar nueva evaluación inicial
     */
    public function add(){
        $sql = "INSERT INTO " . self::$tablename . " (
            pacient_id, evaluating_medic_id, evaluation_date, created_by,
            primary_diagnosis, tumor_stage, date_of_diagnosis, previous_treatments, family_history,
            ecog_performance_status, weight_loss, current_symptoms, pain_scale, symptoms_other,
            support_system, psychological_state, coping_mechanisms,
            proposed_treatment, treatment_goals, estimated_duration, treatment_priority, treatment_notes,
            consents, patient_concerns,
            next_appointment, follow_up_type, pending_studies, referrals,
            medical_summary, recommendations, status, created_at
        ) VALUES (
            '$this->pacient_id', '$this->evaluating_medic_id', '$this->evaluation_date', '$this->created_by',
            '$this->primary_diagnosis', '$this->tumor_stage', '$this->date_of_diagnosis', '$this->previous_treatments', '$this->family_history',
            '$this->ecog_performance_status', '$this->weight_loss', '$this->current_symptoms', '$this->pain_scale', '$this->symptoms_other',
            '$this->support_system', '$this->psychological_state', '$this->coping_mechanisms',
            '$this->proposed_treatment', '$this->treatment_goals', '$this->estimated_duration', '$this->treatment_priority', '$this->treatment_notes',
            '$this->consents', '$this->patient_concerns',
            '$this->next_appointment', '$this->follow_up_type', '$this->pending_studies', '$this->referrals',
            '$this->medical_summary', '$this->recommendations', '$this->status', NOW()
        )";
        
        return Executor::doit($sql);
    }

    /**
     * Actualizar evaluación existente
     */
    public function update(){
        $sql = "UPDATE " . self::$tablename . " SET 
            pacient_id='$this->pacient_id',
            evaluating_medic_id='$this->evaluating_medic_id',
            evaluation_date='$this->evaluation_date',
            primary_diagnosis='$this->primary_diagnosis',
            tumor_stage='$this->tumor_stage',
            date_of_diagnosis='$this->date_of_diagnosis',
            previous_treatments='$this->previous_treatments',
            family_history='$this->family_history',
            ecog_performance_status='$this->ecog_performance_status',
            weight_loss='$this->weight_loss',
            current_symptoms='$this->current_symptoms',
            pain_scale='$this->pain_scale',
            symptoms_other='$this->symptoms_other',
            support_system='$this->support_system',
            psychological_state='$this->psychological_state',
            coping_mechanisms='$this->coping_mechanisms',
            proposed_treatment='$this->proposed_treatment',
            treatment_goals='$this->treatment_goals',
            estimated_duration='$this->estimated_duration',
            treatment_priority='$this->treatment_priority',
            treatment_notes='$this->treatment_notes',
            consents='$this->consents',
            patient_concerns='$this->patient_concerns',
            next_appointment='$this->next_appointment',
            follow_up_type='$this->follow_up_type',
            pending_studies='$this->pending_studies',
            referrals='$this->referrals',
            medical_summary='$this->medical_summary',
            recommendations='$this->recommendations',
            status='$this->status',
            updated_at=NOW()
            WHERE id='$this->id'";
            
        return Executor::doit($sql);
    }

    /**
     * Eliminar evaluación
     */
    public function del(){
        $sql = "DELETE FROM " . self::$tablename . " WHERE id='$this->id'";
        return Executor::doit($sql);
    }

    /**
     * Obtener evaluación por ID
     */
    public static function getById($id){
        $sql = "SELECT * FROM " . self::$tablename . " WHERE id='$id'";
        $query = Executor::doit($sql);
        return Model::one($query[0], new InitialAssessmentData());
    }

    /**
     * Obtener todas las evaluaciones
     */
    public static function getAll(){
        $sql = "SELECT * FROM " . self::$tablename . " ORDER BY created_at DESC";
        $query = Executor::doit($sql);
        return Model::many($query[0], new InitialAssessmentData());
    }

    /**
     * Obtener evaluaciones por paciente
     */
    public static function getAllByPacient($pacient_id){
        $sql = "SELECT * FROM " . self::$tablename . " WHERE pacient_id='$pacient_id' ORDER BY created_at DESC";
        $query = Executor::doit($sql);
        return Model::many($query[0], new InitialAssessmentData());
    }

    /**
     * Obtener evaluaciones por médico
     */
    public static function getAllByMedic($medic_id){
        $sql = "SELECT * FROM " . self::$tablename . " WHERE evaluating_medic_id='$medic_id' ORDER BY created_at DESC";
        $query = Executor::doit($sql);
        return Model::many($query[0], new InitialAssessmentData());
    }

    /**
     * Obtener evaluaciones por estado
     */
    public static function getAllByStatus($status){
        $sql = "SELECT * FROM " . self::$tablename . " WHERE status='$status' ORDER BY created_at DESC";
        $query = Executor::doit($sql);
        return Model::many($query[0], new InitialAssessmentData());
    }

    /**
     * Contar evaluaciones por estado
     */
    public static function countByStatus($status){
        $sql = "SELECT COUNT(*) as total FROM " . self::$tablename . " WHERE status='$status'";
        $query = Executor::doit($sql);
        if($query[0]->num_rows > 0){
            $r = $query[0]->fetch_array();
            return $r[0];
        }
        return 0;
    }

    /**
     * Obtener estadísticas generales
     */
    public static function getGeneralStats(){
        $stats = array();
        $stats['total'] = self::countByStatus('completed') + self::countByStatus('draft') + self::countByStatus('in_progress');
        $stats['completed'] = self::countByStatus('completed');
        $stats['draft'] = self::countByStatus('draft');
        $stats['in_progress'] = self::countByStatus('in_progress');
        return $stats;
    }

    /**
     * Obtener todas las evaluaciones con detalles de paciente y médico
     */
    public static function getAllWithDetails(){
        $sql = "SELECT 
            ia.*,
            p.name as patient_name,
            p.lastname as patient_lastname,
            p.no as patient_dni,
            CONCAT(p.name, ' ', p.lastname) as patient_full_name,
            m.name as medic_name,
            m.lastname as medic_lastname,
            CONCAT(m.name, ' ', m.lastname) as medic_full_name,
            m.email as medic_email
        FROM " . self::$tablename . " ia
        LEFT JOIN pacient p ON ia.pacient_id = p.id
        LEFT JOIN medic m ON ia.evaluating_medic_id = m.id
        ORDER BY ia.created_at DESC";
        
        $query = Executor::doit($sql);
        
        $assessments = array();
        if($query[0]->num_rows > 0){
            while($r = $query[0]->fetch_assoc()){
                $assessments[] = $r;
            }
        }
        return $assessments;
    }

    /**
     * Buscar evaluaciones
     */
    public static function search($term, $status = null){
        // Validar que $term sea un string, no un array
        if(is_array($term)){
            $term = implode(' ', $term);
        }
        $term = (string)$term;
        
        // Validar que $status sea un string, no un array  
        if(is_array($status)){
            $status = implode('', $status);
        }
        if($status){
            $status = (string)$status;
        }
        
        $sql = "SELECT 
            ia.*,
            p.name as patient_name,
            p.lastname as patient_lastname,
            CONCAT(p.name, ' ', p.lastname) as patient_full_name,
            m.name as medic_name,
            m.lastname as medic_lastname,
            CONCAT(m.name, ' ', m.lastname) as medic_full_name
        FROM " . self::$tablename . " ia
        LEFT JOIN pacient p ON ia.pacient_id = p.id
        LEFT JOIN medic m ON ia.evaluating_medic_id = m.id
        WHERE (
            p.name LIKE '%$term%' OR 
            p.lastname LIKE '%$term%' OR 
            p.no LIKE '%$term%' OR
            ia.primary_diagnosis LIKE '%$term%' OR
            ia.medical_summary LIKE '%$term%'
        )";
        
        if($status){
            $sql .= " AND ia.status = '$status'";
        }
        
        $sql .= " ORDER BY ia.created_at DESC";
        
        $query = Executor::doit($sql);
        
        $assessments = array();
        if($query[0]->num_rows > 0){
            while($r = $query[0]->fetch_assoc()){
                $assessments[] = $r;
            }
        }
        return $assessments;
    }

    /**
     * Obtener evaluaciones recientes (últimos 30 días)
     */
    public static function getRecent($limit = 10){
        $sql = "SELECT 
            ia.*,
            p.name as patient_name,
            p.lastname as patient_lastname,
            CONCAT(p.name, ' ', p.lastname) as patient_full_name
        FROM " . self::$tablename . " ia
        LEFT JOIN pacient p ON ia.pacient_id = p.id
        WHERE ia.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        ORDER BY ia.created_at DESC
        LIMIT $limit";
        
        $query = Executor::doit($sql);
        
        $assessments = array();
        if($query[0]->num_rows > 0){
            while($r = $query[0]->fetch_assoc()){
                $assessments[] = $r;
            }
        }
        return $assessments;
    }

    /**
     * Contar evaluaciones por médico
     */
    public static function countByMedic($medic_id){
        $sql = "SELECT COUNT(*) as total FROM " . self::$tablename . " WHERE evaluating_medic_id='$medic_id'";
        $query = Executor::doit($sql);
        if($query[0]->num_rows > 0){
            $r = $query[0]->fetch_array();
            return $r[0];
        }
        return 0;
    }

    /**
     * Contar evaluaciones por paciente
     */
    public static function countByPacient($pacient_id){
        $sql = "SELECT COUNT(*) as total FROM " . self::$tablename . " WHERE pacient_id='$pacient_id'";
        $query = Executor::doit($sql);
        if($query[0]->num_rows > 0){
            $r = $query[0]->fetch_array();
            return $r[0];
        }
        return 0;
    }

    /**
     * Obtener evaluaciones por rango de fechas
     */
    public static function getByDateRange($start_date, $end_date){
        $sql = "SELECT 
            ia.*,
            p.name as patient_name,
            p.lastname as patient_lastname,
            CONCAT(p.name, ' ', p.lastname) as patient_full_name,
            m.name as medic_name,
            m.lastname as medic_lastname
        FROM " . self::$tablename . " ia
        LEFT JOIN pacient p ON ia.pacient_id = p.id
        LEFT JOIN medic m ON ia.evaluating_medic_id = m.id
        WHERE DATE(ia.created_at) BETWEEN '$start_date' AND '$end_date'
        ORDER BY ia.created_at DESC";
        
        $query = Executor::doit($sql);
        
        $assessments = array();
        if($query[0]->num_rows > 0){
            while($r = $query[0]->fetch_assoc()){
                $assessments[] = $r;
            }
        }
        return $assessments;
    }

    /**
     * Obtener estadísticas generales de evaluaciones
     */
    public static function getStatistics(){
        $stats = array();
        
        // Total de evaluaciones
        $sql_total = "SELECT COUNT(*) as total FROM " . self::$tablename;
        $query_total = Executor::doit($sql_total);
        $stats['total'] = 0;
        if($query_total[0]->num_rows > 0){
            $r = $query_total[0]->fetch_array();
            $stats['total'] = $r[0];
        }
        
        // Evaluaciones por estado
        $sql_status = "SELECT 
            status, 
            COUNT(*) as count 
        FROM " . self::$tablename . " 
        GROUP BY status";
        
        $query_status = Executor::doit($sql_status);
        $stats['by_status'] = array(
            'draft' => 0,
            'completed' => 0,
            'reviewed' => 0
        );
        
        if($query_status[0]->num_rows > 0){
            while($r = $query_status[0]->fetch_assoc()){
                $stats['by_status'][$r['status']] = $r['count'];
            }
        }
        
        // Evaluaciones del mes actual
        $current_month = date('Y-m');
        $sql_month = "SELECT COUNT(*) as total FROM " . self::$tablename . " 
                      WHERE DATE_FORMAT(created_at, '%Y-%m') = '$current_month'";
        $query_month = Executor::doit($sql_month);
        $stats['this_month'] = 0;
        if($query_month[0]->num_rows > 0){
            $r = $query_month[0]->fetch_array();
            $stats['this_month'] = $r[0];
        }
        
        // Evaluaciones de los últimos 30 días
        $sql_recent = "SELECT COUNT(*) as total FROM " . self::$tablename . " 
                       WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
        $query_recent = Executor::doit($sql_recent);
        $stats['recent'] = 0;
        if($query_recent[0]->num_rows > 0){
            $r = $query_recent[0]->fetch_array();
            $stats['recent'] = $r[0];
        }
        
        // Evaluaciones por médico (top 5)
        $sql_medics = "SELECT 
            m.name, 
            m.lastname,
            COUNT(ia.id) as count
        FROM " . self::$tablename . " ia
        LEFT JOIN medic m ON ia.evaluating_medic_id = m.id
        WHERE ia.evaluating_medic_id IS NOT NULL
        GROUP BY ia.evaluating_medic_id, m.name, m.lastname
        ORDER BY count DESC
        LIMIT 5";
        
        $query_medics = Executor::doit($sql_medics);
        $stats['by_medic'] = array();
        if($query_medics[0]->num_rows > 0){
            while($r = $query_medics[0]->fetch_assoc()){
                $stats['by_medic'][] = array(
                    'name' => $r['name'] . ' ' . $r['lastname'],
                    'count' => $r['count']
                );
            }
        }
        
        // Evaluaciones por performance status
        $sql_performance = "SELECT 
            ecog_performance_status, 
            COUNT(*) as count 
        FROM " . self::$tablename . " 
        WHERE ecog_performance_status IS NOT NULL 
        GROUP BY ecog_performance_status 
        ORDER BY ecog_performance_status";
        
        $query_performance = Executor::doit($sql_performance);
        $stats['by_performance'] = array();
        if($query_performance[0]->num_rows > 0){
            while($r = $query_performance[0]->fetch_assoc()){
                $stats['by_performance'][$r['ecog_performance_status']] = $r['count'];
            }
        }
        
        return $stats;
    }
}
?>
