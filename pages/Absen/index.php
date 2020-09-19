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

			function load_animation(data){
				// start
				var idx = document.getElementById("employee-animation");
				var _idx = data['id'];
				var _grp = notwork[_idx]['group'];

				layout_user(idx,notwork[_idx]);
				$('div#employee-animation').css("z-index","10");
				$('#slider-notwork>div:nth-child(1)').remove();

				// Check group
				if(typeof work[_grp] === 'undefined'){
					work[_grp] = [];

					idx = document.getElementById("employee-work");
					var a = cWork(idx,_grp);
				}else{
					var a = document.getElementById("workgroup-"+_grp);
					a = a.getElementsByClassName("absen-work-content")[0];
				}

				work[_grp][_idx] = notwork[_idx];
				work[_grp][_idx]['time'] = data['data']['date'];
				work[_grp][_idx]['class'] = 'col-md-20 opac-none';

				layout_user(a,work[_grp][_idx]);

				// get posisi group
				var _pos = $('#workgroup-'+_grp).position();
				$('#employee-animation>.absen-content').animate({top:(_pos.top+57)+'px',left:(_pos.left+30)+'px',width:"7.2%"},'slow',function(){

				//set normal
					$("#workgroup-"+_grp+" .opac-none").removeClass("opac-none");
					$('div#employee-animation').css("z-index","0");
					$('#employee-animation').html('');

				//pause slide to animation
					notwork[_idx] = '';
					$('#multiSlider').multislider('unPause');

				// Set Karyawan Masuk
					$("#total-work-absen").text(work.length);
				});
			}

			function set_absen(data,id){
				if(data['data']!=null){
					$('#slider-notwork>div:nth-child(1)').before($('#absen-notwork-'+data['id']));
					load_animation(data);
				}

				if(data['status']){
					if(data['msg']!=''){
						toastr.success(data['msg']);
					}
				}
			}
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