 
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
		$day = date('w');
		$args = static::$data;
		$args = $args['data'];

		$_group = array();
		$group = array(); $work = array(); $notwork = array(); 
		$outcity = array(); $dayoff = array(); $permit = array();
		$sick = array(); $tugas = array(); $libur = array();$wfh = array();

		$group[0]['name'] = 'Internship';
		$group[0]['group'] = 2;
		$group[0]['punish'] = 1;
		$_group[0] = array(0);

		foreach ($args['group'] as $key => $val) {
			$data = $val['meta_note'];
			if(isset($data['data'])){
				$group[$val['ID']] = array('name' => $val['meta_value']);
				$_group[$val['ID']] = $data['data'];
			}

			if(isset($data['status'])){
				if(in_array(2,$data['status'])){
					$group[$val['ID']]['group'] = 2;
				}else{
					$group[$val['ID']]['group'] = 1;
				}

				if(in_array(3,$data['status'])){
					$group[$val['ID']]['punish'] = 1;
				}else{
					$group[$val['ID']]['punish'] = 0;
				}
			}
		}

		self::$_group = $_group;

		$pos = 0;
		foreach ($args['user'] as $key => $val) {
			if(empty($val['type']) || $val['type']==2){
				$notwork[$val['no_induk']] = array(
					'name'	=> empty($val['_nickname'])?'no name':$val['_nickname'],
					'image'	=> !empty($val['notes_pict'])?$val['notes_pict']:'no-profile.jpg',
					'group'	=> self::_get_group($val['divisi'])
				);
			}

			if($val['type']==1){
				$_worktime = empty($val['shift'])?$val['work_time']:$val['shift'];
				$_work = sobad_work::get_id($_worktime,array('time_in','time_out','status'),"AND days='$day'");
				$grp = self::_get_group($val['divisi']);

				$check = array_filter($_work);
				if(empty($check)){
					$_work = array(
						'time_in'	=> '08:00:00',
						'time_out'	=> '16:00:00'
					);
				}else{
					$_work = $_work[0];
				}
				
				if(!isset($work[$grp])){
					$work[$grp] = array();
				}

				$time = substr($val['time_in'],0,5);
				$waktu = $time;

				$pos = $val['note']['pos_user'];
/*				
				if($pos==1){
					$waktu = '<span style="color:green;">'.$time.'</span>';
				}
*/				
				if($_work['status']){
					if($val['time_in']>=$_work['time_in']){
						$waktu = '<span style="color:red;">'.$time.'</span>';
					}
				}

				$work[$grp][$val['no_induk']] = array(
					'name'		=> empty($val['_nickname'])?'no name':$val['_nickname'],
					'class'		=> '',
					'time'		=> $waktu,
					'image'		=> !empty($val['notes_pict'])?$val['notes_pict']:'no-profile.jpg',
					'position'	=> $pos
				);

				$group[$grp]['position'] = isset($val['note']['pos_group'])?$val['note']['pos_group']:1;
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

			if($val['type']==6){
				$libur[$val['no_induk']] = array(
					'name'	=> empty($val['_nickname'])?'no name':$val['_nickname'],
					'image'	=> !empty($val['notes_pict'])?$val['notes_pict']:'no-profile.jpg',
					'group'	=> self::_get_group($val['divisi']),
					'class'	=> 'col-md-6'
				);
			}

			if($val['type']==7){
				$tugas[$val['no_induk']] = array(
					'name'	=> empty($val['_nickname'])?'no name':$val['_nickname'],
					'image'	=> !empty($val['notes_pict'])?$val['notes_pict']:'no-profile.jpg',
					'group'	=> self::_get_group($val['divisi']),
					'class'	=> 'col-md-6'
				);
			}

			if($val['type']==8){
				$sick[$val['no_induk']] = array(
					'name'	=> empty($val['_nickname'])?'no name':$val['_nickname'],
					'image'	=> !empty($val['notes_pict'])?$val['notes_pict']:'no-profile.jpg',
					'group'	=> self::_get_group($val['divisi']),
					'class'	=> 'col-md-6'
				);
			}

			if($val['type']==10){
				$wfh[$val['no_induk']] = array(
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
		$json = str_replace("[%libur%]", json_encode($libur), $json);
		$json = str_replace("[%tugas%]", json_encode($tugas), $json);
		$json = str_replace("[%sick%]", json_encode($sick), $json);
		$json = str_replace("[%wfh%]", json_encode($wfh), $json);

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
				var tugas = [%tugas%];
				var holiday = [%libur%];
				var sick = [%sick%];
				var wfh = [%wfh%];
				var _request = '';
				var _timeout = '';

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

					m = 0;
					for(g in dayoff){
						m += 1;
					}

					$("#absen-dayoff").text(m);

					m = 0;
					for(g in permit){
						m += 1;
					}

					$("#absen-permit").text(m);

					m = 0;
					for(g in outcity){
						m += 1;
					}

					$("#absen-outcity").text(m);
				}

				function launchIntoFullscreen(element) {
					if(element.requestFullscreen) {
						element.requestFullscreen();
					} else if(element.mozRequestFullScreen) {
						element.mozRequestFullScreen();
					} else if(element.webkitRequestFullscreen) {
						element.webkitRequestFullscreen();
					} else if(element.msRequestFullscreen) {
						element.msRequestFullscreen();
					}
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

					if("note" in arr){
						args = ['div',[['class','employee note-content']],arr['note']];
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
					_sick();
					_tugas();
					_holiday();
					_workFromHome();
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
					var args = '';var a = '';var b = '';var w = 2;
					var idx = '';

					for(var i in work){

						switch(group[i]['group']){
							case 0:
								idx = document.getElementById("internship-work");
								break;

							case 1:
								idx = document.getElementById("employee-work");
								break;

							case 2:
								idx = document.getElementById("employee-exclude");
								break;

							default:
								idx = document.getElementById("employee-work")
								break;
						}

						a = cWork(idx,i);
						w = Object.keys(work[i]).length;

						if(w<2){
							w = 2;
						}else if(w<12){
							w = w
						}else{
							w = 12
						}

						w = 'user-work-'+w;

						for(j in work[i]){
							if(group[i]['punish']==0){
								work[i][j]['time'] = '';
							}

							args = ['div',[['id','work-'+j],['class','item '+w]],''];
							b = ceBefore(a,args);

							layout_user(b,work[i][j]);
						}
					}
				}

				function cWork(idx,grp){
					var a = '';var col = 12;
					var w = Object.keys(work[grp]).length;

					if(w<2){
						col = 2;
					}else if(w<12){
						col = Object.keys(work[grp]).length;
					}else{
						col = 12
					}


					args = ['div',[['id','workgroup-'+grp],['class','work-slider carousel slide col-md-'+col],['data-ride','carousel']],''];
					a = ceAppend(idx,args);

					args = ['div',[['class','employee title-content']],group[grp]['name']];
					ceAppend(a,args);

					args = ['div',[['class','MS-content'],['role','listbox']],''];
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

					args = ['div',[['id','title-outcity'],['class','employee title-content'],['style','display:'+display]],'Luar Kota'];
					ceAppend(idx,args);

					args = ['div',[['id','user-outcity'],['class','row'],['style','height:auto;display:'+display]],''];
					idx = ceAppend(idx,args);

					for(var i in outcity){
						args = ['div',[['id','absen-outcity-'+i],['class','item'],['data-induk',i]],''];
						a = ceAppend(idx,args);

						layout_user(a,outcity[i]);
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

					args = ['div',[['id','title-dayoff'],['class','employee title-content'],['style','margin-top: 20px;display:'+display]],'Cuti'];
					ceAppend(idx,args);

					args = ['div',[['id','user-dayoff'],['class','row'],['style','height:auto;display:'+display]],''];
					idx = ceAppend(idx,args);

					for(var i in dayoff){
						args = ['div',[['id','absen-dayoff-'+i],['class','item'],['data-induk',i]],''];
						a = ceAppend(idx,args);

						layout_user(a,dayoff[i]);
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

					args = ['div',[['id','title-permit'],['class','employee title-content'],['style','margin-top: 20px;display:'+display]],'Izin'];
					ceAppend(idx,args);

					args = ['div',[['id','user-permit'],['class','row'],['style','height:auto;display:'+display]],''];
					idx = ceAppend(idx,args);

					for(var i in permit){
						args = ['div',[['id','absen-permit-'+i],['class','item'],['data-induk',i]],''];
						a = ceAppend(idx,args);

						layout_user(a,permit[i]);
					}
				}

				function _sick(){
					var args = '';var display = 'none';var size = 0;
					var idx = document.getElementById("employee-excity");

					for(o in sick){
						size += 1;
					}

					if(size>0){
						display = 'block';
					}

					args = ['div',[['id','title-sick'],['class','employee title-content'],['style','margin-top: 20px;display:'+display]],'Sakit'];
					ceAppend(idx,args);

					args = ['div',[['id','user-sick'],['class','row'],['style','height:auto;display:'+display]],''];
					idx = ceAppend(idx,args);

					for(var i in sick){
						args = ['div',[['id','absen-sick-'+i],['class','item'],['data-induk',i]],''];
						a = ceAppend(idx,args);

						layout_user(a,sick[i]);
					}
				}

				function _tugas(){
					var args = '';var display = 'none';var size = 0;
					var idx = document.getElementById("employee-excity");

					for(o in tugas){
						size += 1;
					}

					if(size>0){
						display = 'block';
					}

					args = ['div',[['id','title-tugas'],['class','employee title-content'],['style','margin-top: 20px;display:'+display]],'Tugas Luar'];
					ceAppend(idx,args);

					args = ['div',[['id','user-tugas'],['class','row'],['style','height:auto;display:'+display]],''];
					idx = ceAppend(idx,args);

					for(var i in tugas){
						args = ['div',[['id','absen-tugas-'+i],['class','item'],['data-induk',i]],''];
						a = ceAppend(idx,args);

						layout_user(a,tugas[i]);
					}
				}

				function _holiday(){
					var args = '';var display = 'none';var size = 0;
					var idx = document.getElementById("employee-excity");

					for(o in holiday){
						size += 1;
					}

					if(size>0){
						display = 'block';
					}

					args = ['div',[['id','title-holiday'],['class','employee title-content'],['style','margin-top: 20px;display:'+display]],'Libur'];
					ceAppend(idx,args);

					args = ['div',[['id','user-holiday'],['class','row'],['style','height:auto;display:'+display]],''];
					idx = ceAppend(idx,args);

					for(var i in holiday){
						args = ['div',[['id','absen-holiday-'+i],['class','item'],['data-induk',i]],''];
						a = ceAppend(idx,args);

						layout_user(a,holiday[i]);
					}
				}

				function _workFromHome(){
					var args = '';var display = 'none';var size = 0;
					var idx = document.getElementById("employee-excity");

					for(o in wfh){
						size += 1;
					}

					if(size>0){
						display = 'block';
					}

					args = ['div',[['id','title-wfh'],['class','employee title-content'],['style','margin-top: 20px;display:'+display]],'WFH'];
					ceAppend(idx,args);

					args = ['div',[['id','user-wfh'],['class','row'],['style','height:auto;display:'+display]],''];
					idx = ceAppend(idx,args);

					for(var i in wfh){
						args = ['div',[['id','absen-wfh-'+i],['class','item'],['data-induk',i]],''];
						a = ceAppend(idx,args);

						layout_user(a,wfh[i]);
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
					var col = 2;
					var idx = document.getElementById("employee-animation");
					var _idx = data['id'];

					if(typeof data['data']['from'] === 'undefined'){
						data['data']['from'] = "1";
					}

						if(data['data']['from']=="1" || data['data']['from']==1){
							var _notwork = notwork;
						

						}else if(data['data']['from']=="3" || data['data']['from']==3){
							var _docID = 'user-dayoff';
							var _idxcls = 'absen-dayoff-'+_idx;
							var _notwork = dayoff;
						
						}else if(data['data']['from']=="4" || data['data']['from']==4){
							var _docID = 'user-permit';
							var _idxcls = 'absen-permit-'+_idx;
							var _notwork = permit;
						
						}else if(data['data']['from']=="5" || data['data']['from']==5){
							var _docID = 'user-outcity';
							var _idxcls = 'absen-outcity-'+_idx;
							var _notwork = outcity;

						}else if(data['data']['from']=="8" || data['data']['from']==8){
							var _docID = 'user-sick';
							var _idxcls = 'absen-sick-'+_idx;
							var _notwork = sick;
						}else if(data['data']['from']=="10" || data['data']['from']==10){
							var _docID = 'user-wfh';
							var _idxcls = 'absen-wfh-'+_idx;
							var _notwork = wfh;
						}

					if(typeof _notwork[_idx] === 'undefined'){
						toastr.error("ID tidak terdaftar!!!");
						return '';
					}

					var _grp = _notwork[_idx]['group'];

					layout_user(idx,_notwork[_idx]);
					$('div#employee-animation').css("z-index","10");

					if(data['data']['from']!="1"){
						var _cnt = Object.keys(_notwork).length;
						for(var k = 1; k <= _cnt; k++){
							if($('#'+_docID+'>.item:nth-child('+k+')').attr('id') == _idxcls){
								_cnt = k;
								break;
							}
						}

						var _pos_animate = $('#'+_docID).position();
						var _width_work = $('#employee-work').width();

						var _top = _pos_animate.top + (Math.floor(_cnt/2) * 90);
						var _left = (_width_work + _pos_animate.left) + ((_cnt%2) * 78);

						$('#'+_docID+'>.item:nth-child('+_cnt+')').remove();
						$('div#employee-animation>.absen-content').css("top",_top + "px");
						$('div#employee-animation>.absen-content').css("left",_left+ "px");
					}else{
						$('#slider-notwork>div:nth-child(1)').remove();
					}

					// Check group
					if(typeof work[_grp] === 'undefined'){
						work[_grp] = [];

						switch(group[_grp]['group']){
							case 0:
								idx = document.getElementById("internship-work");
								break;

							case 1:
								idx = document.getElementById("employee-work");
								break;

							case 2:
								idx = document.getElementById("employee-exclude");
								break;

							default:
								idx = document.getElementById("employee-work")
								break;
						}

						var a = cWork(idx,_grp);
					}else{
						var a = document.getElementById("workgroup-"+_grp);
						a = a.getElementsByClassName("MS-content")[0];

						if(Object.keys(work[_grp]).length>11){
							$('#workgroup-'+_grp).multislider({
								duration:750,
								interval: 1500,
							});

							$('#workgroup-'+_grp).multislider('pause');

							col = 12;
						}else{
							col = Object.keys(work[_grp]).length;

							if(col > 1){
								$('#workgroup-'+_grp).addClass('col-md-'+(col+1));
								$('#workgroup-'+_grp).removeClass('col-md-'+col);

								$('#workgroup-'+_grp+' .MS-content .item').addClass('user-work-'+(col+1));
								$('#workgroup-'+_grp+' .MS-content .item').removeClass('user-work-'+col);
							}

							if(col < 12){
								col = col+1;
							}
						}
					}

					work[_grp][_idx] = _notwork[_idx];

					if(group[_grp]['punish']==1){
						work[_grp][_idx]['time'] = data['data']['date'];
					}else{
						work[_grp][_idx]['time'] = '';
					}

					work[_grp][_idx]['class'] = 'opac-none';

					var _pos = Object.keys(work[_grp]).length;
					work[_grp][_idx]['position'] = _pos;

					args = ['div',[['id','work-'+_idx],['class','item user-work-'+col]],''];
					b = ceBefore(a,args);
					layout_user(b,work[_grp][_idx]);

					// get posisi group
					var _pos = $('#workgroup-'+_grp).position();
					$('#employee-animation>.absen-content').animate({top:(_pos.top+57)+'px',left:(_pos.left+40)+'px',width:"7.2%"},'slow',function(){

					//set normal
						$("#workgroup-"+_grp+" .opac-none").removeClass("opac-none");
						$('div#employee-animation').css("z-index","0");
						$('#employee-animation').html('');

					//pause slide to animation
							if(data['data']['from']=="1" || data['data']['from']==1){
								delete notwork[_idx];

							}else if(data['data']['from']=="3" || data['data']['from']==3){
								delete dayoff[_idx];

								if(Object.keys(dayoff).length<=0){
									$('#title-dayoff').hide();
									$('#user-dayoff').hide();
								}

							}else if(data['data']['from']=="4" || data['data']['from']==4){
								delete permit[_idx];

								if(Object.keys(permit).length<=0){
									$('#title-permit').hide();
									$('#user-permit').hide();
								}

							}else if(data['data']['from']=="5" || data['data']['from']==5){
								delete outcity[_idx];

								if(Object.keys(outcity).length<=0){
									$('#title-outcity').hide();
									$('#user-outcity').hide();
								}
							}else if(data['data']['from']=="8" || data['data']['from']==8){
								delete sick[_idx];

								if(Object.keys(sick).length<=0){
									$('#title-sick').hide();
									$('#user-sick').hide();
								}
							}else if(data['data']['from']=="10" || data['data']['from']==10){
								delete wfh[_idx];

								if(Object.keys(wfh).length<=0){
									$('#title-wfh').hide();
									$('#user-wfh').hide();
								}
							}

					// Set Karyawan Masuk
						set_total_absen();

					// Check jumlah notwork
						var m = Object.keys(notwork).length;

						if(Object.keys(work[_grp]).length>12){
							$('#workgroup-'+_grp).multislider('unPause');
						}

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

					if(typeof data['data']['from'] === "undefined"){
						//Get Group
						for(var i in work){
							for(var j in work[i]){
								if(j==_idx){
									_pos = work[i][_idx]['position'];
									_grp = i;
									_qty = Object.keys(work[_grp]).length;
									break;
								}
							}

							if(j==_idx){
								break;
							}
						}

						if(Object.keys(work[_grp]).length>12){
							$('#workgroup-'+_grp).multislider('pause');
							$('#workgroup-'+_grp+'>.MS-content>div:nth-child(1)').before($('#work-'+_idx));
							_pos = 1;
						}else{
							_pos = (_qty-_pos);
						}

						//Get position Group
						work[_grp][_idx]['class'] = '';
						var _pos_grp = $("#workgroup-"+_grp).position();	
						layout_user(idx,work[_grp][_idx]);

						$("#workgroup-"+_grp+" #work-"+_idx).css("opacity","0");
						$('div#employee-animation>.absen-content').css("top",(_pos_grp.top+57) + "px");
						$('div#employee-animation>.absen-content').css("left",((_pos_grp.left+45) + (_pos*73))+ "px");
					}else{
						var _notwork = '';
						if(_idx in permit){
							var _docID = 'user-permit';
							var _idxcls = 'absen-permit-'+_idx;
							var _notwork = permit;
						}else if(_idx in outcity){
							var _docID = 'user-outcity';
							var _idxcls = 'absen-outcity-'+_idx;
							var _notwork = outcity;
						}else if(_idx in sick){
							var _docID = 'user-sick';
							var _idxcls = 'absen-sick-'+_idx;
							var _notwork = sick;
						}

						var _grp = _notwork[_idx]['group'];
						work[_grp][_idx] = _notwork[_idx];

						layout_user(idx,_notwork[_idx]);
						$('div#employee-animation').css("z-index","10");

						var _cnt = Object.keys(_notwork).length;
						for(var k = 1; k <= _cnt; k++){
							if($('#'+_docID+'>.item:nth-child('+k+')').attr('id') == _idxcls){
								_cnt = k;
								break;
							}
						}

						var _pos_animate = $('#'+_docID).position();
						var _width_work = $('#employee-work').width();

						var _top = _pos_animate.top + (Math.floor(_cnt/2) * 90);
						var _left = (_width_work + _pos_animate.left) + ((_cnt%2) * 78);

						$('#'+_docID+'>.item:nth-child('+_cnt+')').remove();
						$('div#employee-animation>.absen-content').css("top",_top + "px");
						$('div#employee-animation>.absen-content').css("left",_left+ "px");
					}

					$('#employee-animation').animate({"z-index":"2"},'slow',function(){
						if(data['data']['type']==2){
							back_outwork(_idx,_grp);
						}else{
							back_permit(_idx,_grp,data['data']['to']);
						}

					});
				}

				function back_outwork(_idx,_grp){
					var ma = Object.keys(notwork).length;

					//Add notwork
					notwork[_idx] = {"group":_grp,"name":work[_grp][_idx]['name'],"image":work[_grp][_idx]['image']};

					// Check jumlah notwork
					var mb = Object.keys(notwork).length;

					if(ma==0 && mb>0){
						$('#absen-notwork').animate({height:'15%'},2000);
					}

					//animation back
					$('#employee-animation>.absen-content').animate({top:"86.5%",left:"90px",width:"6.6%"},'slow',function(){

						//Add notwork
						var a = document.getElementById("slider-notwork");
						a = ceBefore(a,['div',[['id','absen-notwork-'+_idx],['class','item'],['data-induk',_idx]],'']);
						layout_user(a,notwork[_idx]);

						back_user_animation(_idx,_grp);
					});
				}

				function back_permit(_idx,_grp,_to){
					var _dt_user = {"class":"col-md-6","group":_grp,"name":work[_grp][_idx]['name'],"image":work[_grp][_idx]['image']}

					//Add notwork
						if(_to==3 || _to=='3'){
							dayoff[_idx] = _dt_user;
							var _docID = 'user-dayoff';
							var _idxcls = 'absen-dayoff' + _idx;
							var _cnt = Object.keys(dayoff).length;

							if(_cnt==1){
								$('#title-dayoff').show();
								$('#'+_docID).show();
							}

						}else if(_to==4 || _to=='4'){
							permit[_idx] = _dt_user;
							var _docID = 'user-permit';
							var _idxcls = 'absen-permit-' + _idx;
							var _cnt = Object.keys(permit).length;

							if(_cnt==1){
								$('#title-permit').show();
								$('#'+_docID).show();
							}

						}else if(_to==5 || _to=='5'){
							outcity[_idx] = _dt_user;
							var _docID = 'user-outcity';
							var _idxcls = 'absen-outcity-' + _idx;
							var _cnt = Object.keys(outcity).length;

							if(_cnt==1){
								$('#title-outcity').show();
								$('#'+_docID).show();
							}

						}else if(_to==8 || _to=='8'){
							sick[_idx] = _dt_user;
							var _docID = 'user-sick';
							var _idxcls = 'absen-sick-' + _idx;
							var _cnt = Object.keys(sick).length;

							if(_cnt==1){
								$('#title-sick').show();
								$('#'+_docID).show();
							}

						}

					//Get position
					var _pos_animate = $('#'+_docID).position();
					var _width_work = $('#employee-work').width();

					var _top = _pos_animate.top + (Math.floor(_cnt/2) * 90);
					var _left = (_width_work + _pos_animate.left) + ((_cnt%2) * 78);
					_pos_animate = {top:_top+"px",left:_left+"px",width:"7.2%"}

					//animation back
					$('#employee-animation>.absen-content').animate(_pos_animate,'slow',function(){

						//Add user
						var a = document.getElementById(_docID);
						var _args = ['div',[['id',_idxcls],['class','item'],['data-induk',_idx]],''];
						a = ceAppend(a,_args);

						layout_user(a,_dt_user);

						back_user_animation(_idx,_grp);
					});
				}

				function back_user_animation(_idx,_grp){
					//set normal
					$('div#employee-animation').css("z-index","0");
					$('#employee-animation').html('');

					//delete user
					$("#workgroup-"+_grp+" #work-"+_idx).remove();
					delete work[_grp][_idx];

					//play slider
					if(Object.keys(work[_grp]).length>12){
						$('#workgroup-'+_grp).multislider('unPause');
					}else{
						$('#workgroup-'+_grp).multislider('pause');
						var col = Object.keys(work[_grp]).length;

						if(col>1){	
							$('#workgroup-'+_grp).addClass('col-md-'+col);
							$('#workgroup-'+_grp).removeClass('col-md-'+(col+1));

							$('#workgroup-'+_grp+' .MS-content .item').addClass('user-work-'+col);
							$('#workgroup-'+_grp+' .MS-content .item').removeClass('user-work-'+(col+1));
						}
					}

					//hidden workgroup
					var m = Object.keys(work[_grp]).length;
					if(m<1){
						$("#workgroup-"+_grp).remove();
					}

					// Set Karyawan Masuk
					set_total_absen();
				}

				function set_absen(data,id){
					if(data['data']!=null){
						if(typeof data['absen'] !== "undefined"){
							$('#myModal').modal('hide');

							if(data['data']['type']==1){
								$('#slider-notwork>div:nth-child(1)').before($('#absen-notwork-'+data['id']));
								load_animation(data);
							}else{
								back_animation(data);
							}
						}

						if(typeof data['modal'] !== 'undefined'){
							if(_timeout!=''){
								clearTimeout(_timeout);
								_timeout = '';
							}

							_request = data['id'];
							$('#myModal .modal-content>.modal-body').html(data['msg']);
							$('#myModal').modal('show');

							if(typeof data['timeout'] === 'undefined'){
								data['timeout'] = 1 * 60 * 2000;
							}

							//Aktif Microphone
							//setting_microphone();
							//recognition.start();

							_timeout = setTimeout(function(){ $('#myModal').modal('hide'); }, data['timeout']);
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

				function setting_microphone(){
					var noteTextarea = $('#note-textarea');
					var instructions = $('#recording-instructions');

					recognition.continuous = true;

					recognition.onresult = function(event) {
					  // event is a SpeechRecognitionEvent object.
					  // It holds all the lines we have captured so far. 
					  // We only need the current one.
					  var current = event.resultIndex;

					  // Get a transcript of what was said.
					  var transcript = event.results[current][0].transcript;

					  // Add the current transcript to the contents of our Note.
					  // There is a weird bug on mobile, where everything is repeated twice.
					  // There is no official solution so far so we have to handle an edge case.
					  var mobileRepeatBug = (current == 1 && transcript == event.results[0][0].transcript);

					  if(!mobileRepeatBug) {
					    noteTextarea.val(transcript);
					    set_voice_absen(transcript);
					  }
					};

					recognition.onstart = function() { 
					  instructions.text('Voice recognition activated. Try speaking into the microphone.');
					}

					recognition.onspeechend = function() {
					  instructions.text('You were quiet for a while so voice recognition turned itself off.');
					}

					recognition.onerror = function(event) {
					  if(event.error == 'no-speech') {
					    instructions.text('No speech was detected. Try again.');  
					  };
					}
				}

				function set_filter_absen(val){
					val.toLowerCase();
					if(val=='luar kota'){
						send_request(5);
					}

					if(val=='izin'){
						send_request(4);
					}

					if(val=='pulang'){
						send_request(2);
					}
				}

				function send_request(val){
					if(val==7 || val==9){
						$('#myModal').modal('hide');
					}

					data = [_request,val];
					data = "ajax=_request&object=absensi&data="+JSON.stringify(data);

					//pause slide to animation
					$('#multiSlider').multislider('pause');

					this.value = '';
					sobad_ajax('#absensi',data,set_absen,false);
				}

			// Play Auto Video 
				var myvid = document.getElementById('video-profile');

				myvid.addEventListener('ended', function(e) {
				  // get the active source and the next video source.
				  // I set it so if there's no next, it loops to the first one
				  var activesource = document.querySelector("#video-profile source.active");
				  var nextsource = document.querySelector("#video-profile source.active + source") || document.querySelector("#video-profile source:first-child");
				  
				  // deactivate current source, and activate next one
				  activesource.className = "";
				  nextsource.className = "active";
				  
				  // update the video source and play
				  myvid.src = nextsource.src;
				  myvid.play();
				});
			</script>
		<?php
	}
}