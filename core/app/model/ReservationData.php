<?php
class ReservationData {
	public static $tablename = "reservation";

	public function __construct(){
		$this->title = "";
		$this->note = "";
		$this->date_at = "";
		$this->time_at = "";
		$this->pacient_id = "";
		$this->medic_id = "";
		$this->status_id = "1";
		$this->chair_id = "";
		$this->created_at = "NOW()";
	}

	// Relaciones
	public function getPacient(){ return PacientData::getById($this->pacient_id); }
	public function getMedic(){ return MedicData::getById($this->medic_id); }
	public function getChair(){ 
		return $this->chair_id ? OncologyChairData::getById($this->chair_id) : null; 
	}
	
	public function getStatus(){
		$status = new stdClass();
		switch($this->status_id){
			case 1:
				$status->name = "Pendiente";
				break;
			case 2:
				$status->name = "Confirmada";
				break;
			case 3:
				$status->name = "Cancelada";
				break;
			default:
				$status->name = "Desconocido";
		}
		return $status;
	}

	public function add(){
		$chair_field = "";
		$chair_value = "";
		if(isset($this->chair_id) && $this->chair_id != null) {
			$chair_field = ",chair_id";
			$chair_value = ",$this->chair_id";
		}
		
		$sql = "INSERT INTO ".self::$tablename." (title, note, medic_id, date_at, time_at, pacient_id, status_id$chair_field) ";
		$sql .= "VALUES (\"$this->title\", \"$this->note\", $this->medic_id, \"$this->date_at\", \"$this->time_at\", $this->pacient_id, $this->status_id$chair_value)";
		return Executor::doit($sql);
	}

	public function update(){
		$chair_field = "";
		if(isset($this->chair_id) && $this->chair_id != null) {
			$chair_field = ",chair_id=$this->chair_id";
		}
		
		$sql = "UPDATE ".self::$tablename." SET title=\"$this->title\", pacient_id=$this->pacient_id, medic_id=$this->medic_id, date_at=\"$this->date_at\", time_at=\"$this->time_at\", note=\"$this->note\", status_id=$this->status_id$chair_field WHERE id=$this->id";
		Executor::doit($sql);
	}

	public function del(){
		$sql = "DELETE FROM ".self::$tablename." WHERE id=$this->id";
		Executor::doit($sql);
	}

	public static function delById($id){
		$sql = "DELETE FROM ".self::$tablename." WHERE id=$id";
		Executor::doit($sql);
	}

	public static function getById($id){
		$sql = "SELECT * FROM ".self::$tablename." WHERE id=$id";
		$query = Executor::doit($sql);
		return Model::one($query[0], new ReservationData());
	}

	public static function getAll(){
		$sql = "SELECT * FROM ".self::$tablename." WHERE date(date_at)>=date(NOW()) ORDER BY date_at, time_at";
		$query = Executor::doit($sql);
		return Model::many($query[0], new ReservationData());
	}

	public static function getByMedicId($id){
		$sql = "SELECT * FROM ".self::$tablename." WHERE medic_id=$id ORDER BY date_at, time_at";
		$query = Executor::doit($sql);
		return Model::many($query[0], new ReservationData());
	}

	public static function getByPacientId($id){
		$sql = "SELECT * FROM ".self::$tablename." WHERE pacient_id=$id ORDER BY date_at, time_at";
		$query = Executor::doit($sql);
		return Model::many($query[0], new ReservationData());
	}

	// Métodos específicos para oncología
	public static function getOncologyReservations($date = null){
		// Obtener todas las reservaciones (asumiendo que todos los médicos son oncólogos)
		$sql = "SELECT r.* FROM ".self::$tablename." r 
				INNER JOIN medic m ON r.medic_id = m.id 
				WHERE 1=1";
		if($date) {
			$sql .= " AND r.date_at = '$date'";
		}
		$sql .= " ORDER BY r.date_at, r.time_at";
		$query = Executor::doit($sql);
		return Model::many($query[0], new ReservationData());
	}

	public static function getByChairAndDate($chair_id, $date){
		$sql = "SELECT * FROM ".self::$tablename." WHERE chair_id=$chair_id AND date_at='$date' ORDER BY time_at";
		$query = Executor::doit($sql);
		return Model::many($query[0], new ReservationData());
	}

	public static function getByDateRange($start_date, $end_date){
		$sql = "SELECT * FROM ".self::$tablename." WHERE date_at >= '$start_date' AND date_at <= '$end_date' ORDER BY date_at, time_at";
		$query = Executor::doit($sql);
		return Model::many($query[0], new ReservationData());
	}

	// Verificar conflictos de horarios
	public static function checkTimeConflict($medic_id, $date, $time, $duration = 60, $exclude_id = null){
		$end_time = date('H:i:s', strtotime($time) + ($duration * 60));
		$sql = "SELECT * FROM ".self::$tablename." WHERE medic_id=$medic_id AND date_at='$date' AND status_id=1 AND (
			(time_at <= '$time' AND DATE_ADD(CONCAT(date_at, ' ', time_at), INTERVAL 60 MINUTE) > '$time')
			OR (time_at < '$end_time' AND time_at >= '$time')
		)";
		if($exclude_id) {
			$sql .= " AND id != $exclude_id";
		}
		$query = Executor::doit($sql);
		return Model::many($query[0], new ReservationData());
	}

	// Verificar conflictos de sillones
	public static function checkChairConflict($chair_id, $date, $time, $duration = 60, $exclude_id = null){
		$end_time = date('H:i:s', strtotime($time) + ($duration * 60));
		$sql = "SELECT * FROM ".self::$tablename." WHERE chair_id=$chair_id AND date_at='$date' AND status_id=1 AND (
			(time_at <= '$time' AND DATE_ADD(CONCAT(date_at, ' ', time_at), INTERVAL 60 MINUTE) > '$time')
			OR (time_at < '$end_time' AND time_at >= '$time')
		)";
		if($exclude_id) {
			$sql .= " AND id != $exclude_id";
		}
		$query = Executor::doit($sql);
		return Model::many($query[0], new ReservationData());
	}
}
?>