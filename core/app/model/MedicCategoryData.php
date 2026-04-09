<?php
class MedicCategoryData {
	public static $tablename = "medic_category";

	public function __construct(){
		$this->medic_id = "";
		$this->category_id = "";
	}

	public function add(){
		$sql = "insert into ".self::$tablename." (medic_id, category_id) ";
		$sql .= "values ($this->medic_id, $this->category_id)";
		return Executor::doit($sql);
	}

	public static function delByMedicAndCategory($medic_id, $category_id){
		$sql = "delete from ".self::$tablename." where medic_id=$medic_id and category_id=$category_id";
		Executor::doit($sql);
	}

	public static function getAllByMedic($medic_id){
		$sql = "select * from ".self::$tablename." where medic_id=$medic_id";
		$query = Executor::doit($sql);
		return Model::many($query[0], new MedicCategoryData());
	}

	public static function getAllByCategory($category_id){
		$sql = "select * from ".self::$tablename." where category_id=$category_id";
		$query = Executor::doit($sql);
		return Model::many($query[0], new MedicCategoryData());
	}

	public static function getAll(){
		$sql = "select * from ".self::$tablename;
		$query = Executor::doit($sql);
		return Model::many($query[0], new MedicCategoryData());
	}
	
	public static function exists($medic_id, $category_id){
		$sql = "select * from ".self::$tablename." where medic_id=$medic_id and category_id=$category_id";
		$query = Executor::doit($sql);
		return $query[0]->num_rows > 0;
	}
}
?>
