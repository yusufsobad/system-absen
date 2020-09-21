<?php
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

session_start();

if(!isset($_POST['ajax'])){
	include 'err.php';

	$err = new _error();
	$err = $err->_alert_db("ajax not load");
	die($err);
}else{
	$key = $_POST['object'];
	$key = str_replace("sobad_","",$key);
	$func = str_replace("sobad_","",$_POST['ajax']);

	define('AUTHPATH',$_SERVER['SERVER_NAME']);
	require 'config/hostname.php';

	// Include File Component
	require dirname(__FILE__).'/config/defined.php';
	require dirname(__FILE__).'/config/option.php';
	require dirname(__FILE__).'/../function.php';

	// Get Define
	new _config_define();

	// get file component
	new _component();

	// include pages
	$asset = sobad_asset::_pages("../pages/");

	// get Themes
	sobad_themes();

	$key = get_home_func($key);

	$value = isset($_POST['data']) ? $_POST['data'] : "";

	$data['class'] = $key;
	$data['func'] = $func;
	$data['data'] = $value;

	if(!class_exists($key)){
		$ajax = array(
			'status' => "failed",
			'msg'	 => "object not found!!!",
			'func'	 => 'sobad_'.$key
		);
		$ajax = json_encode($ajax);
			
		return print_r($ajax);
	}

	define('_object',$key);
	sobad_ajax::_get($data);
}

class sobad_ajax{
	public static function _get($args=array()){
		$check = array_filter($args);
		if(empty($check)){
			$ajax = array(
				'status' => "error",
				'msg'	 => "data not found!!!",
				'func'	 => ''
			);
			$ajax = json_encode($ajax);
			
			return print_r($ajax);
		}

		$_class = $args['class'];
		$_func = $args['func'];
		$data = $args['data'];

		if(!is_callable(array($_class,$_func))){
			$ajax = array(
				'status' => "failed",
				'msg'	 => "request not found!!!",
				'func'	 => 'sobad_'.$_func
			);
			$ajax = json_encode($ajax);
			
			return print_r($ajax);
		}
		
		$object = new $_class();
		$msg = $object->{$_func}($data);

		if(empty($msg)){
			$ajax = array(
				'status' => "error",
				'msg'	 => "ada kesalahan pada pemrosesan data!!!",
				'func'	 => 'sobad_'.$_func
			);
			$ajax = json_encode($ajax);
			
			return print_r($ajax);
		}
		
		$ajax = array(
			'status' => "success",
			'msg'    => "success",
			'data'	 => $msg,
			'func'	 => 'sobad_'.$_func
		);
		
		$ajax = json_encode($ajax);		
		return print_r($ajax);
	}
}