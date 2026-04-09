<?php
class OncologyWaitlistData {
    public static $tablename = "oncology_waitlist";

    public function __construct(){
        $this->treatment_type = "";
        $this->priority_level = 1;
        $this->duration_minutes = 60;
        $this->notes = "";
        $this->status = "pending";
        $this->created_at = "NOW()";
    }

    public function getPacient(){ 
        return PacientData::getById($this->pacient_id); 
    }

    public function getReservation(){ 
        return $this->reservation_id ? ReservationData::getById($this->reservation_id) : null; 
    }    public function add(){
        $sql = "INSERT INTO ".self::$tablename." (pacient_id, treatment_type, priority_level, requested_date, requested_time, duration_minutes, notes, status, created_at) ";
        $sql .= "VALUES ($this->pacient_id, \"$this->treatment_type\", $this->priority_level, \"$this->requested_date\", \"$this->requested_time\", $this->duration_minutes, \"$this->notes\", \"$this->status\", NOW())";
        return Executor::doit($sql);
    }

    public function update(){
        $sql = "UPDATE ".self::$tablename." SET pacient_id=$this->pacient_id, treatment_type=\"$this->treatment_type\", priority_level=$this->priority_level, requested_date=\"$this->requested_date\", requested_time=\"$this->requested_time\", duration_minutes=$this->duration_minutes, notes=\"$this->notes\", status=\"$this->status\"";
        if($this->assigned_at != "") $sql .= ", assigned_at=\"$this->assigned_at\"";
        if($this->reservation_id) $sql .= ", reservation_id=$this->reservation_id";
        $sql .= " WHERE id=$this->id";
        return Executor::doit($sql);
    }    public function assignReservation($reservation_id){
        $this->reservation_id = $reservation_id;
        $this->status = "assigned";
        $this->assigned_at = date('Y-m-d H:i:s');
        $this->update();
    }

    public static function getById($id){
        $sql = "SELECT * FROM ".self::$tablename." WHERE id=$id";
        $query = Executor::doit($sql);
        if($query && $query[0]){
            return Model::one($query[0], new OncologyWaitlistData());
        }
        return null;
    }

    public static function getAll(){
        $sql = "SELECT * FROM ".self::$tablename." ORDER BY priority_level DESC, created_at ASC";
        $query = Executor::doit($sql);
        if($query && $query[0]){
            return Model::many($query[0], new OncologyWaitlistData());
        }
        return array();
    }

    public static function getPending(){
        $sql = "SELECT * FROM ".self::$tablename." WHERE status='pending' ORDER BY priority_level DESC, created_at ASC";
        $query = Executor::doit($sql);
        if($query && $query[0]){
            return Model::many($query[0], new OncologyWaitlistData());
        }
        return array();
    }

    public static function getByPacientId($pacient_id){
        $sql = "SELECT * FROM ".self::$tablename." WHERE pacient_id=$pacient_id ORDER BY created_at DESC";
        $query = Executor::doit($sql);
        if($query && $query[0]){
            return Model::many($query[0], new OncologyWaitlistData());
        }
        return array();
    }

    public static function delById($id){
        $sql = "DELETE FROM ".self::$tablename." WHERE id=$id";
        Executor::doit($sql);
    }
}
?>
