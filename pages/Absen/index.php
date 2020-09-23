<?php
(!defined('DEFPATH'))?exit:'';

include 'absen.php';

$args = array();
$args['absen'] = array(
	'page'	=> 'absen_sobad',
	'theme'	=> 'absen',
	'home'	=> true
);
reg_hook('reg_page',$args);

class absen_sobad{

	public function _reg(){
		$url = get_page_url();
		if(!empty($url)){
			if($url=='login'){
				$pages = new sobad_page('login');
				$pages->_get();
			}else{
				_error::_page404();
			}

		}else{
			$GLOBALS['body'] = 'absen';
			self::script_login();
		}
	}

	private function script_login(){
		$vendor = new vendor_script;
		$theme = new absen_script;

		// url script jQuery - Vendor
		$get_jquery = $vendor->_get_('_js_core',array('jquery-core'));
		$style['jQuery-core'] = '<script src="'.$get_jquery['jquery-core'].'"></script>';

		// url script css ----->
		$css = array_merge(
				$vendor->_get_('_css_global'),
				$vendor->_get_('_css_font'),
				$vendor->_get_('_css_page_level',array('bootstrap-toastr')),
				$theme->_get_('_css_page_style')
			);
		
		// url script css ----->
		$js = array_merge(
				$vendor->_get_('_js_core'),
				$vendor->_get_('_js_page_level',array('bootstrap-toastr')),
				$vendor->_get_('_js_multislider')
			);

		unset($js['jquery-core']);
		
		ob_start();
		self::load_script();
		$script['absen'] = ob_get_clean();

		reg_hook("reg_script_css",$css);
		reg_hook("reg_script_js",$js);
		reg_hook("reg_script_foot",$script);
		reg_hook("reg_script_head",$style);
	}

	private function load_script(){
		?>
			<script>
			var reload = true;

			setInterval(function(){
				var currentdate = new Date(); 
				var time = currentdate.getHours() + ":" + currentdate.getMinutes();

				if(reload){
					if(time=="5:0"){
						reload = false;
						location.reload(true);
					}
				}else{
					var _now = currentdate.getHours() * 60 + currentdate.getMinutes();
					if(_now>302){
						reload = true;
					}
				}

			},1000);

			jQuery(document).ready(function() {     
				$("#qrscanner").focus();
				$("#qrscanner").on('change',function(){
					setcookie("sidemenu","absensi");
					data = "ajax=_send&object=absensi&data="+this.value;

					//pause slide to animation
					$('#multiSlider').multislider('pause');

					this.value = '';
					sobad_ajax('#absensi',data,set_absen,false);
				});
			});

			$('body.absen').on('click',function(){
				$("#qrscanner").focus();
			});

			$('#multiSlider').multislider({
				duration:750,
				interval: 1500,
			});
			</script>
		<?php
	}

	public function _page(){
		//sobad_db::_update_file_list();

		$args = array(
			'object'	=> 'absensi',
			'func'		=> 'layout',
			'data'		=> absensi::_data_employee(),
			'status'	=> absensi::_status()
		);
		
		?> 
			<input id="qrscanner" type="text" value="" style="opacity:0;position: absolute;">
		<?php
		print(sobad_absen::load_here($args));
	}

}