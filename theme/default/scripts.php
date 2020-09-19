<?php
(!defined('THEMEPATH'))?exit:'';

class theme_script{
	public function _get_($func='',$idx=array()){
		if(is_callable(array($this,$func))){
			$script = self::$func($idx);
		}else{
			$script = array();
		}
		
		return $script;
	}
	
	private function lokasi(){
		return 'theme/default/asset/';
	}
// BEGIN PAGE LEVEL STYLES ---->
	
	private function _css_page_level($idx=array()){
		$loc = $this->lokasi();
		$css = array(
			'themes-login-soft'	=> $loc.'css/pages/login-soft.css',
			'themes-search'		=> $loc.'css/pages/search.css'
		);
		
		$check = array_filter($idx);
		if(!empty($check)){
			foreach($idx as $key){
				$css[$key];
			}
		}
		
		return $css;
	}
	
	
// BEGIN PAGE STYLES ---->	
	private function _css_page($idx=array()){
		$loc = $this->lokasi();
		$css = array(
			'themes-task'		=> $loc.'css/pages/tasks.css',
		);
		
		$check = array_filter($idx);
		if(!empty($check)){
			foreach($idx as $key){
				$css[$key];
			}
		}
		
		return $css;
	}
	
// BEGIN THEME STYLES ---->
	private function _css_theme($idx=array()){
		$loc = $this->lokasi();
		$css = array(
			'themes-component'		=> $loc.'css/global/components.css',
			'themes-plugin'			=> $loc.'css/global/plugins.css',
			'themes-layout'			=> $loc.'css/layout/css/layout.css',
			'themes-themes'			=> $loc.'css/layout/css/themes/darkblue.css',
			'themes-custom'			=> $loc.'css/layout/css/custom.css',
		);
		
		$check = array_filter($idx);
		if(!empty($check)){
			foreach($idx as $key){
				$js[$key];
			}
		}
		
		return $css;
	}	

// BEGIN PAGE LEVEL SCRIPTS ---->
	private function _js_page_level($idx=array()){
		$loc = $this->lokasi();
		$js = array( 
			'themes-metronic'		=> $loc.'js/global/metronic.js',
			'themes-layout'			=> $loc.'js/layout/layout.js',
			'themes-quick-sidebar'	=> $loc.'js/layout/quick-sidebar.js',
			'themes-demo'			=> $loc.'js/layout/demo.js',
			'themes-index'			=> $loc.'js/pages/index.js',
			'themes-task'			=> $loc.'js/pages/tasks.js',
			'themes-login-soft'		=> $loc.'js/pages/login-soft.js',
			'themes-ui-toastr'		=> $loc.'js/pages/ui-toastr.js',
			'themes-modal'			=> $loc.'js/pages/ui-extended-modals.js',
			'themes-editable'		=> $loc.'js/pages/form-editable.js',
//			'themes-picker'			=> $loc.'js/pages/components-pickers.js',
			'themes-dropdown'		=> $loc.'js/pages/components-dropdowns.js',
			'themes-editor'			=> $loc.'js/pages/components-editors.js',
			'themes-contextmenu'	=> $loc.'js/pages/contextmenu.js',
		);
		
		$check = array_filter($idx);
		if(!empty($check)){
			foreach($idx as $key){
				$js[$key];
			}
		}
		
		return $js;
	}	
}