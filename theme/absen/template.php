 
 <?php

(!defined('THEMEPATH'))?exit:'';

abstract class absen_control{

	private static $_group = array();

	private static function _get_group($divisi=0){
		$group = self::$_group;

		foreach ($group as $key => $val) {
			if(in_array($divisi, $val)){
				return $key;
			}
		}

		return 0;
	}

	protected static function _control($args=array()){
		$args = static::$data;
		$args = $args['data'];

		$_group = array();
		$group = array(); $work = array(); $notwork = array(); 
		$outcity = array(); $dayoff = array(); $permit = array();

		$group[0] = 'Internship';
		$_group[0] = array(0);

		foreach ($args['group'] as $key => $val) {
			$data = unserialize($val['meta_note']);
			if(isset($data['data'])){
				$group[$val['ID']] = $val['meta_value'];
				$_group[$val['ID']] = $data['data'];
			}
		}

		self::$_group = $_group;

		foreach ($args['user'] as $key => $val) {
			if(empty($val['type'])){
				$notwork[$val['no_induk']] = array(
					'name'	=> empty($val['_nickname'])?'no name':$val['_nickname'],
					'image'	=> !empty($val['notes_pict'])?$val['notes_pict']:'no-profile.jpg',
					'group'	=> self::_get_group($val['divisi'])
				);
			}

			if($val['type']==1){
				$grp = self::_get_group($val['divisi']);
				
				if(!isset($work[$grp])){
					$work[$grp] = array();
				}

				$work[$grp][$val['no_induk']] = array(
					'name'	=> empty($val['_nickname'])?'no name':$val['_nickname'],
					'class'	=> 'col-md-20',
					'time'	=> substr($val['time_in'],0,5),
					'image'	=> !empty($val['notes_pict'])?$val['notes_pict']:'no-profile.jpg',
				);
			}

			if($val['type']==3){
				$dayoff[$val['no_induk']] = array(
					'name'	=> empty($val['_nickname'])?'no name':$val['_nickname'],
					'image'	=> !empty($val['notes_pict'])?$val['notes_pict']:'no-profile.jpg',
					'group'	=> self::_get_group($val['divisi']),
					'class'	=> 'col-md-6'
				);
			}

			if($val['type']==4){
				$permit[$val['no_induk']] = array(
					'name'	=> empty($val['_nickname'])?'no name':$val['_nickname'],
					'image'	=> !empty($val['notes_pict'])?$val['notes_pict']:'no-profile.jpg',
					'group'	=> self::_get_group($val['divisi']),
					'class'	=> 'col-md-6'
				);
			}

			if($val['type']==5){
				$outcity[$val['no_induk']] = array(
					'name'	=> empty($val['_nickname'])?'no name':$val['_nickname'],
					'image'	=> !empty($val['notes_pict'])?$val['notes_pict']:'no-profile.jpg',
					'group'	=> self::_get_group($val['divisi']),
					'class'	=> 'col-md-6'
				);
			}
		}

		ob_start();
		self::_json();
		$json = ob_get_clean();

		$json = str_replace("[%group%]", json_encode($group), $json);
		$json = str_replace("[%work%]", json_encode($work), $json);
		$json = str_replace("[%notwork%]", json_encode($notwork), $json);
		$json = str_replace("[%outcity%]", json_encode($outcity), $json);
		$json = str_replace("[%dayoff%]", json_encode($dayoff), $json);
		$json = str_replace("[%permit%]", json_encode($permit), $json);

		echo $json;
		self::_layout();
		self::_animation();

	}

	private static function _json(){
		?>
			<script type="text/javascript">
				var group = [%group%];
				var work = [%work%];
				var notwork = [%notwork%];
				var outcity = [%outcity%];
				var dayoff = [%dayoff%];
				var permit = [%permit%];

			</script>
		<?php
	}

	protected static function _layout(){
		?>
			<script type="text/javascript">
				function set_total_absen(){
					var m = 0;
					for(g in work){
						for(w in work[g]){
							m += 1;
						}
					}

					$("#total-work-absen").text(m);
				}

				function layout_user(id,arr){
					var _class = '';

					if("class" in arr){
						_class = arr['class'];
					}

					var args = ['div',[['class','absen-content '+_class]],''];
					var a = ceBefore(id,args);

					args = ['div',[['class','image-content']],''];
					var b = ceAppend(a,args);

					args = ['img',[['src','asset/img/user/'+arr['image']]],''];
					ceAppend(b,args);

					args = ['div',[['class','employee name-content']],arr['name']];
					ceAppend(a,args);

					if("time" in arr){
						args = ['div',[['class','employee time-content']],arr['time']];
						ceAppend(a,args);
					}
				}

				function cElement(arr){
					if(arr!=''){
						var a = document.createElement(arr[0]);

						if(arr[1] !=''){
							for(i=0;i<arr[1].length;i++){
								a.setAttribute(arr[1][i][0],arr[1][i][1]);
							}
						}

						if(arr[2]!=''){
							if(typeof(arr[2])!='function'){
								a.innerHTML = arr[2];
							}else{
								arr[2](a);
							}
						}

						return  a;
					}
				}

				function ceBefore(id,arr){
					if(arr!=''){
						var a = cElement(arr);
						return  id.insertBefore(a,id.childNodes[0]);
					}
				}

				function ceAppend(id,arr){
					if(arr!=''){
						var a = cElement(arr);
						return  id.appendChild(a);
					}
				}

				function load_absen(){
					notWork();
					Work();
					outCity();
					dayOff();
					_permit();
				}

				function notWork(){
					var args = '';var _idx = '';var a = '';
					var idx = document.getElementById("slider-notwork");

					for(var i in notwork){
						args = ['div',[['id','absen-notwork-'+i],['class','item'],['data-induk',i]],''];
						a = ceAppend(idx,args);

						layout_user(a,notwork[i]);
					}
				}

				function Work(){
					var args = '';var a = '';
					var idx = document.getElementById("employee-work");

					for(var i in work){

						a = cWork(idx,i);

						for(j in work[i]){
							layout_user(a,work[i][j]);
						}
					}
				}

				function cWork(idx,grp){
					var a = '';

					args = ['div',[['id','workgroup-'+grp],['class','col-md-6']],''];
					a = ceAppend(idx,args);

					args = ['div',[['class','employee title-content']],group[grp]];
					ceAppend(a,args);

					args = ['div',[['class','row absen-work-content']],''];
					a = ceAppend(a,args);

					return a;
				}

				function outCity(){
					var args = '';var display = 'none';var size = 0;
					var idx = document.getElementById("employee-excity");

					for(o in outcity){
						size += 1;
					}

					if(size>0){
						display = 'block';
					}

					args = ['div',[['class','employee title-content'],['style','display:'+display]],'Luar Kota'];
					ceAppend(idx,args);

					args = ['div',[['class','row'],['style','height:auto;display:'+display]],''];
					idx = ceAppend(idx,args);

					for(var i in outcity){
						layout_user(idx,outcity[i]);
					}
				}

				function dayOff(){
					var args = '';var display = 'none';var size = 0;
					var idx = document.getElementById("employee-excity");

					for(o in dayoff){
						size += 1;
					}

					if(size>0){
						display = 'block';
					}

					args = ['div',[['class','employee title-content'],['style','margin-top: 20px;display:'+display]],'Cuti'];
					ceAppend(idx,args);

					args = ['div',[['class','row'],['style','height:auto;display:'+display]],''];
					idx = ceAppend(idx,args);

					for(var i in dayoff){
						layout_user(idx,dayoff[i]);
					}
				}

				function _permit(){
					var args = '';var display = 'none';var size = 0;
					var idx = document.getElementById("employee-excity");

					for(o in permit){
						size += 1;
					}

					if(size>0){
						display = 'block';
					}

					args = ['div',[['class','employee title-content'],['style','margin-top: 20px;display:'+display]],'Izin'];
					ceAppend(idx,args);

					args = ['div',[['class','row'],['style','height:auto;display:'+display]],''];
					idx = ceAppend(idx,args);

					for(var i in permit){
						layout_user(idx,permit[i]);
					}
				}

				load_absen();
				set_total_absen();

			</script>
		<?php
	}

	private static function _animation(){
		?>
			<script type="text/javascript">
				function load_animation(data){
					// start
					var idx = document.getElementById("employee-animation");
					var _idx = data['id'];

					if(typeof notwork[_idx] === 'undefined'){
						toastr.error("ID tidak terdaftar!!!");
						return '';
					}

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
					$('#employee-animation>.absen-content').animate({top:(_pos.top+57)+'px',left:(_pos.left+40)+'px',width:"7.2%"},'slow',function(){

					//set normal
						$("#workgroup-"+_grp+" .opac-none").removeClass("opac-none");
						$('div#employee-animation').css("z-index","0");
						$('#employee-animation').html('');

					//pause slide to animation
						delete notwork[_idx];

					// Set Karyawan Masuk
						set_total_absen();

					// Check jumlah notwork
						var m = Object.keys(notwork).length;

						if(m<10){

							if(m<1){
								$('#absen-notwork').animate({height:'0px'},2000);
							}
						}
					});
				}

				function back_animation(data){
					// start
					var idx = document.getElementById("employee-animation");
					var _idx = data['id'];
					var _grp = 0;
					var _qty = 0;
					var _pos = 0;

					//Get Group
					for(var i in work){
						_pos = -1;
						for(var j in work[i]){
							_pos += 1;
							if(j==_idx){
								_grp = i;
								_qty = Object.keys(work[_grp]).length;
								break;
							}
						}

						if(j==_idx){
							break;
						}
					}
			
					_pos = (_qty-_pos);				
					//Get position Group
					var _pos_grp = $("#workgroup-"+_grp).position();
					layout_user(idx,work[_grp][_idx]);
					
					$("#workgroup-"+_grp+">.row>.absen-content:nth-child("+_pos+")").css("opacity","0");
					$('div#employee-animation>.absen-content').css("top",((_pos_grp.top+57) + (Math.floor(_pos/5)*93)) + "px");
					$('div#employee-animation>.absen-content').css("left",((_pos_grp.left+45) + ((_pos-1)*73)) + "px");

					$('#employee-animation').animate({"z-index":"10"},'slow',function(){
						//Add notwork
						notwork[_idx] = {"group":_grp,"name":work[_grp][_idx]['name'],"image":work[_grp][_idx]['image']};

						//animation back
						$('#employee-animation>.absen-content').animate({top:"86.5%",left:"90px",width:"6.6%"},'slow',function(){

						//Add notwork
							var a = document.getElementById("slider-notwork");
							a = ceBefore(a,['div',[['id','absen-notwork-'+_idx],['class','item'],['data-induk',_idx]],'']);
							layout_user(a,notwork[_idx]);

						//set normal
							$('div#employee-animation').css("z-index","0");
							$('#employee-animation').html('');

						//pause slide to animation
							$("#workgroup-"+_grp+">.row>.absen-content:nth-child("+_pos+")").remove();
							delete work[_grp][_idx];

						//hidden workgroup
							var m = Object.keys(work[_grp]).length;
							if(m<1){
								$("#workgroup-"+_grp).remove();
							}

						// Set Karyawan Masuk
							set_total_absen();
						});
					});
				}

				function set_absen(data,id){
					if(data['data']!=null){
						if(data['data']['type']==1){
							$('#slider-notwork>div:nth-child(1)').before($('#absen-notwork-'+data['id']));
							load_animation(data);
						}else{
							back_animation(data);
						}
					}

					var m = Object.keys(notwork).length;

					if(m<10){
						$('#multiSlider').multislider('pause');
						$('#multiSlider .MS-controls').css('opacity',0);
					}else{
						$('#multiSlider').multislider('unPause');
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
}