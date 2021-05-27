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
			var stsnow = true;
			var stsganti = true;
			var stsabsen = true;
			var stsclick = true;

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

				if(stsnow){
					if(time=="20:0"){
						stsnow = false;
						var data = "ajax=_checkAlpha&object=report_absen&data=0";
						sobad_ajax('#my',data,'html',false,'','');
					}
				}

				if(stsabsen){
					if(time=="17:30"){
						stsabsen = false;
						for(var g in group){
							if(group[g]['punish']==0){

								for(var w in work[g]){
									data = [w,0,0];
									data = "ajax=_send&object=absensi&data="+JSON.stringify(data);

									//pause slide to animation
									$('#multiSlider').multislider('pause');
									sobad_ajax('#absensi',data,set_absen,false);
								}
							}
						}
					}
				}

				if(time=="20:0"){
					$('#video-profile')[0].pause();
				}

				if(stsganti){
					if(time=="1:0"){
						stsganti = false;
						//var data = "ajax=_checkGantiJam&object=report_absen&data=0";
						//sobad_ajax('#my',data,'html',false,'','');
					}
				}

			},1000);

			//Fullscreen
			//launchIntoFullscreen(document.documentElement); // the whole page

			jQuery(document).ready(function() {     
				$("#qrscanner").focus();
				$("#qrscanner").on('change',function(){
					setcookie("sidemenu","absensi");

					// Check value
					stsclick = true;
					if(this.value=="")return console.log('undefined');

					// --------- Jika value L || C || R -> Klik tombol modal
					if(this.value=="L" || this.value=="C" || this.value=="R"){
						var arrModal = {"L":"absen_left","C":"absen_center","R":"absen_right"};
						var reqModal = 0;

						if(this.value in arrModal){
							reqModal = $('#'+arrModal[this.value]).attr('data-request');	
							if(typeof reqModal !== 'undefined')send_request(reqModal);
						}

						$('#qrscanner').val('');
						return '';
					}

					// Absensi Karyawan
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

			jQuery(document).ready(function() { 
				$("#qrscanner").focus();
				$("#qrscanner").on('keyup',function(){
					var _load = '<div class="modal-loading"><div class="position-load"><img src="asset/img/loading-spinner-grey.gif" alt="" class="loading"><span> &nbsp;&nbsp;Loading... </span></div></div>';
						$('#myModal>.modal-dialog>.modal-content>.modal-body>.box-loading').html(_load);

					if(this.value!='' && stsclick==true){
						stsclick = false;
						setTimeout(function(){
							$('#qrscanner').change();
						},800);
					}else{
						this.value = '';
					}
				});
			});

			//Voice Aktif
		/*	try {
			  var SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
			  var recognition = new SpeechRecognition();
			}
			catch(e) {
			  console.error(e);
			  $('.no-browser-support').show();
			  $('.app').hide();
			}
		*/

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
			<style type="text/css">
				#myModal .modal-dialog {
				    margin-top: 38%;
				}

				#myModal .modal-dialog .box-loading{
					position: absolute;
				    display: block;
				    width: 95%;
				    height: 100%;
				    z-index: 10;
				    top: 0;
				}

				#myModal .box-loading>.modal-loading{
					background: rgba(0,0,0,0.4);
				    color: #fff;
				    height: inherit;
				    width: 100%;
				}

				.modal-loading>.position-load {
				    transform: translate(42%, 70px);
				}
			</style>
			<input id="qrscanner" type="text" value="" style="opacity:0;position: absolute;">
		<?php
		print(sobad_absen::load_here($args));
	}

}