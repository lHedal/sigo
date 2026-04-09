<?php
class UserData {
	public static $tablename = "user";

	public function __construct(){
		$this->name = "";
		$this->lastname = "";
		$this->username = "";
		$this->password = "";
		$this->is_active = "0";
		$this->created_at = "NOW()";
	}

	public function add(){
		$sql = "insert into ".self::$tablename." (name,lastname,username,password,is_active,is_admin,created_at, view_reports, view_users, edit_users, view_pacients, edit_pacients, view_medics, edit_medics, view_reservations, edit_reservations) ";
		$sql .= "value (\"$this->name\",\"$this->lastname\",\"$this->username\",\"$this->password\",$this->is_active,$this->is_admin,$this->created_at,$this->view_reports,$this->view_users,$this->edit_users,$this->view_pacients,$this->edit_pacients,$this->view_medics,$this->edit_medics,$this->view_reservations,$this->edit_reservations)";
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

// partiendo de que ya tenemos creado un objecto UserData previamente utilizamos el contexto
	public function update(){
		$sql = "update ".self::$tablename." set name=\"$this->name\",lastname=\"$this->lastname\",username=\"$this->username\",password=\"$this->password\",is_active=$this->is_active,is_admin=$this->is_admin,view_reports=$this->view_reports,view_users=$this->view_users,edit_users=$this->edit_users,view_pacients=$this->view_pacients,edit_pacients=$this->edit_pacients,view_medics=$this->view_medics,edit_medics=$this->edit_medics,view_reservations=$this->view_reservations,edit_reservations=$this->edit_reservations where id=$this->id";
		Executor::doit($sql);
	}

	public function update_passwd(){
		$sql = "update ".self::$tablename." set password=\"$this->password\" where id=$this->id";
		Executor::doit($sql);
	}

	public static function getById($id){
		$sql = "select * from ".self::$tablename." where id=$id";
		$query = Executor::doit($sql);
		return Model::one($query[0],new UserData());
	}



	public static function getAll(){
		$sql = "select * from ".self::$tablename." order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new UserData());
	}


	public static function getLike($q){
		$sql = "select * from ".self::$tablename." where title like '%$q%' or content like '%$q%'";
		$query = Executor::doit($sql);
		return Model::many($query[0],new UserData());
	}


}

?>