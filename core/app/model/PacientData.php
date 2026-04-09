<?php
class PacientData {
	public static $tablename = "pacient";
	public function __construct(){
		$this->title = "";
		$this->email = "";
		$this->image = "";
		$this->password = "";
		$this->is_public = "0";
		$this->created_at = "NOW()";
	}

	public function add(){
		$sql = "insert into ".self::$tablename." (no,image,name,lastname,gender,day_of_birth,address, cp, pob, phone,email,password,sick,medicaments,alergy,created_at) ";
		$sql .= "value (\"$this->no\",\"$this->image\",\"$this->name\",\"$this->lastname\",\"$this->gender\",\"$this->day_of_birth\",\"$this->address\",\"$this->cp\",\"$this->pob\",\"$this->phone\",\"$this->email\",\"$this->password\",\"$this->sick\",\"$this->medicaments\",\"$this->alergy\",$this->created_at)";
		Executor::doit($sql);
	}

	public static function delById($id){
		$sql = "delete from ".self::$tablename." where id=$id";
		Executor::doit($sql);
	}
	public function del(){
		$sql = "delete from ".self::$tablename." where id=$this->id";
		Executor::doit($sql);
	}

// partiendo de que ya tenemos creado un objecto PacientData previamente utilizamos el contexto
	public function update_active(){
		$sql = "update ".self::$tablename." set last_active_at=NOW() where id=$this->id";
		Executor::doit($sql);
	}


	public function update(){
		$sql = "update ".self::$tablename." set no=\"$this->no\",image=\"$this->image\",name=\"$this->name\",lastname=\"$this->lastname\",address=\"$this->address\",phone=\"$this->phone\",email=\"$this->email\",password=\"$this->password\",gender=\"$this->gender\",day_of_birth=\"$this->day_of_birth\",sick=\"$this->sick\",medicaments=\"$this->medicaments\",alergy=\"$this->alergy\",cp=\"$this->cp\",pob=\"$this->pob\" where id=$this->id";
		Executor::doit($sql);
	}

	public static function getById($id){
		$sql = "select * from ".self::$tablename." where id=$id";
		$query = Executor::doit($sql);
		return Model::one($query[0],new PacientData());
	}

	public static function getAll(){
		$sql = "select * from ".self::$tablename." where is_active = 1 order by name, lastname";
		$query = Executor::doit($sql);
		return Model::many($query[0],new PacientData());
	}

	public static function getAllActive(){
		$sql = "select * from ".self::$tablename." where is_active = 1 order by name, lastname";
		$query = Executor::doit($sql);
		return Model::many($query[0],new PacientData());
	}
	public static function getAllInactive(){
		$sql = "select * from ".self::$tablename." where is_active = 0 order by name, lastname";
		$query = Executor::doit($sql);
		return Model::many($query[0],new PacientData());
	}


	public function getUnreads(){ return MessageData::getUnreadsByClientId($this->id); }


	public static function getLike($q){
		$sql = "select * from ".self::$tablename." where title like '%$q%' or email like '%$q%'";
		$query = Executor::doit($sql);
		return Model::many($query[0],new PacientData());
	}


}

?>