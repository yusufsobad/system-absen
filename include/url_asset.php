<?php
/*
Version 1.1.2
*/
(!defined('DEFPATH'))?exit:'';

class sobad_asset{
	private function _name_file($dir){
		if(is_dir($dir)){
			if($handle = opendir($dir)){
				$i = 0;
				while(($file = readdir($handle)) !== false){
					if($file == "."){
						continue;
					}
					if($file == ".."){
						continue;
					}
					
					$list[$i] = $file;
					$i += 1;
				}
				closedir($handle);
				
				return $list;
			}
		}
	}

	public function _js_file(){
		$dir = "asset/js/";
		$list = self::_name_file($dir);
		if(count($list)>0){
			for($i=0;$i<count($list);$i++){
				echo '<script src="'.$dir.$list[$i].'"></script>';
			}
		}
	}

	public function _css_file(){
		$dir = "asset/css/";
		$list = self::_name_file($dir);
		if(count($list)>0){
			for($i=0;$i<count($list);$i++){
				echo '<link rel="stylesheet" type="text/css" href="'.$dir.$list[$i].'">';
			}
		}
	}

	public function _url_set(){

		echo '<!-- --custom stylesheet-- -->';
		self::_css_file();
		echo '<!-- -function javascript- -->';
		self::_js_file();
	}

	public function _vendor_css(){
		global $reg_script_css;
		
		foreach($reg_script_css as $key => $val){
			echo "<!-- $key CSS -->";
			echo '<link rel="stylesheet" type="text/css" href="'.$val.'">';
		}
	}

	public function _vendor_js(){
		global $reg_script_js;

		foreach($reg_script_js as $key => $val){
			echo "<!-- $key JS -->";
			echo '<script src="'.$val.'"></script>';
		}
	}

	public function _script_head(){
		global $reg_script_head;

		echo "<!-- Script Head Sobad -->";
		foreach($reg_script_head as $key => $val){
			echo $val;
		}
	}

	public function _script_foot(){
		global $reg_script_foot;

		echo "<!-- Script Foot Sobad -->";
		foreach($reg_script_foot as $key => $val){
			echo $val;
		}
	}

	public static function _pages($dir = "pages/"){
		$pages = self::_name_file($dir);
		if(count($pages)>0){
			for($i=0;$i<count($pages);$i++){
				if(is_dir($dir.$pages[$i])){
					if(file_exists($dir.$pages[$i]."/index.php")){
						require_once $dir.$pages[$i]."/index.php";
					}
				}
			}
		}else{
			die("halaman gagal dimuat!!!");
		}
	}

	public static function ajax_conv_json($args){
		$args = json_decode($args,true);
		$data = array();

		$filter = false;
		if(isset($_SESSION[_prefix.'input_form'])){
			$_filter = $_SESSION[_prefix.'input_form'];
			$filter = true;
		}
		
		if (is_array($args) || is_object($args)){	
			foreach($args as $key => $val){
				$name = stripcslashes($val['name']);
				$data[$name] = stripcslashes($val['value']);

				if($filter){
					if(isset($_filter[$name])){
						$data[$name] = formatting::sanitize($data[$name],$_filter[$name]);
					}
				}
			}
		
			return $data;
		}
		
		return array();
	}

	public static function ajax_conv_array_json($args){
		$args = json_decode($args,true);
		$data = array();

		$filter = false;
		if(isset($_SESSION[_prefix.'input_form'])){
			$_filter = $_SESSION[_prefix.'input_form'];
			$filter = true;
		}
		
		if (is_array($args) || is_object($args)){	
			foreach($args as $key => $val){
				$name = stripcslashes($val['name']);
				if(!array_key_exists($name,$data)){
					$data[$name] = array();
				}

				if($filter){
					if(isset($_filter[$name])){
						$val['value'] = formatting::sanitize($val['value'],$_filter[$name]);
					}
				}
				
				array_push($data[$name],stripcslashes($val['value']));
			}
			
			return $data;
		}
		
		return array();
	}

	public static function handling_upload_file($name='',$target_dir='upload'){
		$err = new _error();

		if(empty($name))die($err->_alert_db("index FILE not found!!!"));

		$_name = basename($_FILES[$name]["name"]);
		$target_file = $target_dir . '/' . $_name;
		$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

		// Check if image file is a actual image or fake image
		if(in_array($imageFileType,array('jpg','jpeg','bmp','png','gif'))) {
			$check = getimagesize($_FILES[$name]["tmp_name"]);
			
			if($check === false) {
				die($err->_alert_db("Fake Image Upload!!!"));
			}
		}

		// Check file size
		if ($_FILES[$name]["size"] > 2000000) {
			die($err->_alert_db("Ukuran File terlalu besar (2MB)!!!"));
		}

		// Check if file already exists
		$_files = self::_check_filename($target_dir,$_name);

		// if everything is ok, try to upload file
		if (move_uploaded_file($_FILES[$name]["tmp_name"], $_files['target'])) {
			return $_files['name'];
		} else {
			die($err->_alert_db("Sorry, there was an error uploading your file.!!!"));	
		}
	}

	private static function _check_filename($target_dir='',$name='',$extend=0){
		$_info = pathinfo($name);
		$_basename = $_info['basename'];
		$_name = $_info['filename'];
		$_ext = $_info['extension'];

		if(!empty($extend)){
			$_name .= '-'.$extend;
			$_basename = $_name.'.'.$_ext;
		}

		$target_file = $target_dir . '/' . $_basename;
		if(file_exists($target_file)){
			$extend += 1;
			return self::_check_filename($target_dir,$name,$extend);
		}

		return array(
			'name'		=> $_basename,
			'target'	=> $target_file
		);
	}

}