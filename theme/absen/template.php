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

				$work[$grp][] = array(
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
					'group'	=> self::_get_group($val['divisi'])
				);
			}

			if($val['type']==4){
				$permit[$val['no_induk']] = array(
					'name'	=> empty($val['_nickname'])?'no name':$val['_nickname'],
					'image'	=> !empty($val['notes_pict'])?$val['notes_pict']:'no-profile.jpg',
					'group'	=> self::_get_group($val['divisi'])
				);
			}

			if($val['type']==5){
				$outcity[$val['no_induk']] = array(
					'name'	=> empty($val['_nickname'])?'no name':$val['_nickname'],
					'image'	=> !empty($val['notes_pict'])?$val['notes_pict']:'no-profile.jpg',
					'group'	=> self::_get_group($val['divisi'])
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
				function layout_user(id,arr){
					var _class = '';

					if("class" in arr){
						_class = arr['class'];
					}

					var args = ['div',[['class','absen-content '+_class]],''];
					var a = ceBefore(id,args);

					args = ['div',[['class','image-content']],'']
					var b = ceAppend(a,args);

					args = ['img',[['src','asset/img/user/'+arr['image']]],'']
					ceAppend(b,args);

					args = ['div',[['class','employee name-content']],arr['name']]
					ceAppend(a,args);

					if("time" in arr){
						args = ['div',[['class','employee time-content']],arr['time']]
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
					var args = '';var display = 'none';
					var idx = document.getElementById("employee-excity");

					if(outcity.length>0){
						display = 'block';
					}

					args = ['div',[['class','row absen-exwork'],['style','display:'+display]],'Luar Kota'];
					ceAppend(idx,args);

					for(var i in outcity){
						layout_user(idx,outcity[i]);
					}
				}

				load_absen();
			</script>
		<?php
	}

	private static function _animation(){
		?>
			<script type="text/javascript">
				
			</script>
		<?php
	}
}