<?php
class MedicData {
	public static $tablename = "medic";
	
	public function __construct(){
		$this->no = "";
		$this->name = "";
		$this->lastname = "";
		$this->username = "";
		$this->email = "";
		$this->password = "";
		$this->category_id = "";
		$this->is_active = "1";
		$this->created_at = "NOW()";
	}

	public function getCategory(){ 
		return CategoryData::getById($this->category_id); 
	}

	public function add(){
		$sql = "INSERT INTO ".self::$tablename." (no, name, lastname, username, email, password, category_id, is_active, created_at) ";
		$sql .= "VALUES (\"$this->no\", \"$this->name\", \"$this->lastname\", \"$this->username\", \"$this->email\", \"$this->password\", $this->category_id, $this->is_active, $this->created_at)";
		Executor::doit($sql);
	}

	public function update(){
		$sql = "UPDATE ".self::$tablename." SET no=\"$this->no\", name=\"$this->name\", lastname=\"$this->lastname\", username=\"$this->username\", email=\"$this->email\", password=\"$this->password\", category_id=$this->category_id, is_active=$this->is_active WHERE id=$this->id";
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
		return Model::one($query[0], new MedicData());
	}

	public static function getAll(){
		$sql = "SELECT * FROM ".self::$tablename." ORDER BY lastname ASC";
		$query = Executor::doit($sql);
		return Model::many($query[0], new MedicData());
	}

	// Método específico para oncología - obtener médicos oncólogos
	public static function getOncologyMedics(){
		// Obtener todos los médicos activos (asumiendo que todos son oncólogos en este sistema)
		$sql = "SELECT m.* FROM ".self::$tablename." m 
				WHERE m.is_active=1 
				ORDER BY m.lastname ASC";
		$query = Executor::doit($sql);
		return Model::many($query[0], new MedicData());
	}

	// Método para obtener médicos por categoría (útil para oncología)
	public static function getAllByCategory($category_id){
		// Como no hay tabla category, retornamos todos los médicos activos
		$sql = "SELECT * FROM ".self::$tablename." WHERE is_active=1 ORDER BY lastname ASC";
		$query = Executor::doit($sql);
		return Model::many($query[0], new MedicData());
	}

	// Método para búsqueda (útil para selección de médicos)
	public static function getLike($q){
		$sql = "SELECT * FROM ".self::$tablename." WHERE name LIKE '%$q%' OR lastname LIKE '%$q%' OR email LIKE '%$q%'";
		$query = Executor::doit($sql);
		return Model::many($query[0], new MedicData());
	}
}
?>