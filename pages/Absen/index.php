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
			var m = 0;
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

			//Fullscreen
			//launchIntoFullscreen(document.documentElement); // the whole page

			jQuery(document).ready(function() {     
				$("#qrscanner").focus();
				$("#qrscanner").on('change',function(){
					setcookie("sidemenu","absensi");

					if(typeof notwork[this.value] == 'undefined'){
						var _group = null;
					}else{
						var _group = notwork[this.value]['group'];
					}

					var _pos_grp = Object.keys(work).length;
					var _pos_user = 1;

					if(typeof work[_group] === 'undefined'){
						_pos_grp += 1;
						if(typeof group[_group] != 'undefined'){
							group[_group]['position'] = _pos_grp;
						}
					}else{
						_pos_user = Object.keys(work[_group]).length;
						_pos_user += 1;
						_pos_grp = group[_group]['position'];
					}

					data = [this.value,_pos_user,_pos_grp];
					data = "ajax=_send&object=absensi&data="+JSON.stringify(data);

					//pause slide to animation
					$('#multiSlider').multislider('pause');

					this.value = '';
					sobad_ajax('#absensi',data,set_absen,false);
				});
			});

			//Voice Aktif
			try {
			  var SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
			  var recognition = new SpeechRecognition();
			}
			catch(e) {
			  console.error(e);
			  $('.no-browser-support').show();
			  $('.app').hide();
			}

			$('body.absen').on('click',function(){
				$("#qrscanner").focus();
			});

			$('#multiSlider').multislider({
				duration:750,
				interval: 1500,
			});

			for(var i in work){
				$('#workgroup-'+i).multislider({
					duration:750,
					interval: 1500,
				});

				if(Object.keys(work[i]).length<13){
					$('#workgroup-'+i).multislider('pause');
				}
			}

			for(var j in work){
				m = Object.keys(work[j]).length;

				if(group[j]['group']==2){
					var idx = "employee-exclude";
				}else{
					var idx = "employee-work";
				}

				$('#'+idx+'>div:nth-child('+group[j]['position']+')').before($('#workgroup-'+j));
				for(var k in work[j]){
					$('#workgroup-'+j+'>.MS-content>div:nth-child('+(m-work[j][k]['position'])+')').after($('#work-'+k));
				}
			}

			if(Object.keys(notwork).length < 10){
				$('#multiSlider').multislider('pause');
				$('#multiSlider .MS-controls').css('opacity',0);
			}

			setTimeout(function(){
				$('#video-profile')[0].play();
			},3000);
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