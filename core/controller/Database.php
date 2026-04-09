<?php
class Database {
	public static $db;
	public static $con;
	
	public $user;
	public $pass;
	public $host;
	public $port;
	public $ddbb;
	
	function __construct(){
		$this->user = "root";
		$this->pass = "";
		$this->host = "localhost";
		$this->port = 3306; // Puerto fijo donde está MySQL
		$this->ddbb = "oncology_db";
	}

	function connect(){
		// Primero intentar conectar sin especificar base de datos
		$con = @new mysqli($this->host, $this->user, $this->pass, "", $this->port);
		
		if ($con->connect_error) {
			die("Error de conexión a MySQL: " . $con->connect_error . " (Puerto: " . $this->port . ")");
		}
		
		// Crear base de datos si no existe
		$con->query("CREATE DATABASE IF NOT EXISTS `" . $this->ddbb . "` CHARACTER SET utf8 COLLATE utf8_general_ci");
		
		// Seleccionar la base de datos
		$con->select_db($this->ddbb);
		
		// Configurar charset
		$con->set_charset("utf8");
		
		return $con;
	}

	public static function getCon(){
		if(self::$con == null && self::$db == null){
			self::$db = new Database();
			self::$con = self::$db->connect();
		}
		return self::$con;
	}
}
?>
