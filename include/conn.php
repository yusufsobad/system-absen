<?php
(!defined('AUTHPATH'))?exit:'';

class conn extends _error{
	public function connect(){
		$server = constant("SERVER");
		$user = constant("USERNAME");
		$pass = constant("PASSWORD");
		$database = constant("DB_NAME");

		$conn=new mysqli($server,$user,$pass,$database);
		mysqli_connect($server,$user,$pass) or parent::_connect();
		$conn->select_db($database) or parent::_database();
		
		return $conn;
	}
}
?>