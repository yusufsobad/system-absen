<?php
(!defined('AUTHPATH'))?exit:'';

if(!class_exists('_component')){

	class _component {	
		public function __construct(){

			// get func libs
			require_once 'include/convTo.php';

			// get include
			require_once 'include/currency.php';
			require_once 'include/language.php';
			require_once 'include/url_asset.php';
			require_once 'include/reg_array.php';
			require_once 'include/class_db.php';
			require_once 'include/sanitize.php';

			// get themes
			require_once 'theme/index.php';

			// get class page
			require_once 'include/_pages.php';

			// get script
			require_once 'scripts.php';
		}
	}

}

if(!class_exists('_libs_')){

	// get library php
	class _libs_ extends option_library{
		private $loc_lib = '';

		public function __construct($args=array()){
			$this->loc_lib = dirname(__FILE__).'/include/libs/';

			if(!is_array($args)){
				$this->get_library($args);
				return '';
			}
			
			$check = array_filter($args);
			if(empty($check)){
				return '';
			}
			
			self::get_libraries($args);
		}
		
		private function check_option($folder=''){
			$args = $this->get_libs();
			if(!array_key_exists($folder,$args)){
				return false;
			}
			
			return true;
		}
		
		private function get_library($libs=''){
			if(self::check_option($libs)){
				require_once $this->loc_lib . $libs .'/'. $this->get_option_lib($libs);
			}
		}

		private function get_libraries($libs=array()){
			foreach ($libs as $key => $val) {
				if(self::check_option($val)){
					require_once $this->loc_lib . $val .'/'. $this->get_option_lib($val);
				}
			}
		}	
	}

}