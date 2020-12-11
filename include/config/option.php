<?php
(!defined('AUTHPATH'))?exit:'';

class option_library{
	protected function get_libs(){
		//pendaftaran Library
		$args = array(
			'dompdf'			=> 'autoload.php',
			'phpmailer'			=> 'class.phpmailer.php',
			'richtexteditor'	=> 'include_rte.php',
			'sharedGettext'		=> 'autoload.php',
			'createpdf'			=> 'html2pdf/html2pdf.class.php'
		);
		
		return $args;
	}
	
	protected function get_option_lib($libs=''){		
		$args = self::get_libs();
		if(array_key_exists($libs,$args)){
			return $args[$libs];
		}

		return '';
	}
}