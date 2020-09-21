<?php
(!defined('AUTHPATH'))?exit:'';

// ---------------------------------------
// Set Time Jakarta ----------------------
// ---------------------------------------
ini_set('date.timezone', 'Asia/Jakarta');



require dirname(__FILE__).'/connection.php';
require dirname(__FILE__).'/../err.php';
require dirname(__FILE__).'/../conn.php';
require dirname(__FILE__).'/../class_db/sync_db.php';

function get_home(){
	return $_SERVER['SERVER_NAME'].'/system-absen';
}

function get_page_url(){
	$url = '';
	if(defined('load_menu')){
		$url = constant('load_menu');
	}

	return $url;
}

function get_home_func($key=''){

	if(class_exists($key)){
		return $key;
	}

	global $reg_page;

	$prefix = constant("_prefix");
	$page = $_SESSION[$prefix."page"];

	$func = $reg_page[$page]['page'];
	$object = new $func();
	$object->_reg();

	global $reg_sidebar;

	return get_side_active($reg_sidebar,$key);
}

function get_side_active($args=array(),$func=''){	
	foreach ($args as $key => $val) {
		if(empty($func)){
			if($val['status']=='active'){
				return $val['func'];
				break;
			}
		}else{	
			if($key==$func){
				return $val['func'];
				break;
			}
		}

		if($val['child']!=null){
			$child = get_side_active($val['child'],$func);

			if(!empty($child)){
				return $child;
			}
		}
	}

	return false;
}

class hostname{
	public function __construct(){

		$server = get_home();
		$host = self::get_hostname();

		if($server!=$host){
			die('Halaman ini tidak di akses tidak dengan semestinya!!!');
		}

	// Include File Component	
		require dirname(__FILE__).'/defined.php';
		require dirname(__FILE__).'/option.php';
		require dirname(__FILE__).'/../../function.php';

	// Get Define
		new _config_define();	
	}

	private function get_hostname(){
		$where = "WHERE config_name='siteurl'";
		$host = self::_get_about($where,array('config_value'));
		return $host[0]['config_value'];
	}

	private function _get_about($where='',$args=array()){
		$email = array();
		$q = sobad_db::_select_table($where,'abs-about',$args);
		if($q!==0){
			while($r=$q->fetch_assoc()){
				$item = array();
				foreach($r as $key => $val){
					$item[$key] = $val;
				}
				
				$email[] = $item;
			}
		}
		
		return $email;
	}
}