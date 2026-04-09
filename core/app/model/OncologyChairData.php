<?php
class OncologyChairData {
    public static $tablename = "oncology_chair";
    
    // Propiedades públicas
    public $id;
    public $name;
    public $description;
    public $is_active;
    public $created_at;

    public function __construct(){
        $this->name = "";
        $this->description = "";
        $this->is_active = 1;
        $this->created_at = "NOW()";
    }

    public function add(){
        $sql = "INSERT INTO ".self::$tablename." (name, description, is_active, created_at) ";
        $sql .= "VALUES (\"$this->name\", \"$this->description\", $this->is_active, $this->created_at)";
        return Executor::doit($sql);
    }

    public function update(){
        $sql = "UPDATE ".self::$tablename." SET name=\"$this->name\", description=\"$this->description\", is_active=$this->is_active WHERE id=$this->id";
        Executor::doit($sql);
    }

    public static function getById($id){
        $sql = "SELECT * FROM ".self::$tablename." WHERE id=$id";
        $query = Executor::doit($sql);
        return Model::one($query[0], new OncologyChairData());
    }

    public static function getAll(){
        $sql = "SELECT * FROM ".self::$tablename." WHERE is_active=1 ORDER BY name";
        $query = Executor::doit($sql);
        return Model::many($query[0], new OncologyChairData());
    }

    public static function getAvailableChairs($date, $start_time, $end_time){
        $sql = "SELECT * FROM ".self::$tablename." WHERE is_active=1 AND id NOT IN (
                    SELECT DISTINCT chair_id FROM reservation 
                    WHERE chair_id IS NOT NULL 
                    AND date_at='$date' 
                    AND status_id=1 
                    AND (
                        (time_at <= '$start_time' AND DATE_ADD(CONCAT(date_at, ' ', time_at), INTERVAL duration MINUTE) > '$start_time')
                        OR (time_at < '$end_time' AND time_at >= '$start_time')
                    )
                )";
        $query = Executor::doit($sql);
        return Model::many($query[0], new OncologyChairData());
    }

    public function del(){
        $sql = "DELETE FROM ".self::$tablename." WHERE id=$this->id";
        Executor::doit($sql);
    }

    public static function delById($id){
        $sql = "DELETE FROM ".self::$tablename." WHERE id=$id";
        Executor::doit($sql);
    }

    public static function getActiveChairs(){
        $sql = "SELECT * FROM ".self::$tablename." WHERE is_active=1 ORDER BY name";
        $query = Executor::doit($sql);
        return Model::many($query[0], new OncologyChairData());
    }
}
?>
