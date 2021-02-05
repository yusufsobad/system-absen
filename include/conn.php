<?php
(!defined('AUTHPATH'))?exit:'';

class conn extends _error{
	public static function connect(){
		global $DB_NAME;

		$server = constant("SERVER");
		$user = constant("USERNAME");
		$pass = constant("PASSWORD");
		$database = $DB_NAME;//constant("DB_NAME");

		$conn=new mysqli($server,$user,$pass,$database);
		mysqli_connect($server,$user,$pass) or parent::_connect();
		$conn->select_db($database) or parent::_database();
		
		return $conn;
	}
}
?>