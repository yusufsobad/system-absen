<?php
(!defined('THEMEPATH'))?exit:'';

class absen_script{
	public function _get_($func='',$idx=array()){
		if(is_callable(array($this,$func))){
			$script = self::$func($idx);
		}else{
			$script = array();
		}
		
		return $script;
	}
	
	private function lokasi(){
		return 'theme/absen/asset/';
	}
// BEGIN PAGE LEVEL STYLES ---->
	
	private function _css_page_style($idx=array()){
		$loc = $this->lokasi();
		$css = array(
			'themes-style'	=> $loc.'css/style.css',
		);
		
		$check = array_filter($idx);
		if(!empty($check)){
			foreach($idx as $key){
				$css[$key];
			}
		}
		
		return $css;
	}
}