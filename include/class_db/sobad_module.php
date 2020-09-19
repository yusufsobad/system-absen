<?php

class sobad_module extends _class{
	public static $table = 'abs-module';

	public function blueprint(){
		$args = array(
			'type'		=> 'module',
			'table'		=> self::$table
		);

		return $args;
	}

	private function _check_type($type=''){
		if(!empty($type)){
			$args = array(
				'department',
				'faculty',
				'study_program',
				'group'
			);

			if(in_array($type, $args)){
				return true;
			}
		}

		return false;
	}
	
	public function _gets($type='',$args=array(),$limit=''){
		if(self::_check_type($type)){
			$where = "WHERE meta_key='$type' $limit";
			return self::_check_join($where,$args,$type);
		}

		return array();
	}

	public function _conv_absensi($args=array()){
		$check = array_filter($args);
		if(empty($check)){
			return array();
		}

		$data = array();
		foreach ($args as $key => $val) {
			$dts = json_decode($val['data'],true);
			$dts = implode(',',$dts['data']);
			$dts = empty($dts)?0:$dts;

			$whr = "AND `abs-user`.divisi IN ($dts)";
			$users = sobad_user::get_all(array('ID','no_induk'),$whr);
			$absen = sobad_user::get_absen(array('user','type','time_in','time_out'),date('Y-m-d'));
			
			$check = array();
			foreach ($absen as $ky => $vl) {
				$check[$vl['user']] = $vl;
			}

			foreach ($users as $ky => $vl) {
				if(isset($check[$vl['ID']])){
					$abs = $check[$vl['ID']];

					$date = $abs['type']==0?'':$abs['type']==1?$abs['time_in']:$abs['time_out'];
					$type = $abs['type'];
				}else{
					$date = '';
					$type = 0;
				}

				$users[$ky]['no_induk'] = $vl['no_induk'];
				$users[$ky]['image'] = $vl['no_induk'];
				$users[$ky]['date'] = $date;
				$users[$ky]['type'] = $type;
			}

			$title = $val['name'];
			$data[$key] = array(
				'key'		=> str_replace(' ', '_', $title),
				'title'		=> $title,
				'data'		=> $users
			);		
		}

		return $data;
	}

	public function _conv_divisi($data=''){
		$data = unserialize($data);
		if(isset($data['data'])){
			$idx = implode(',', $data['data']);
			$data = sobad_module::_gets('department',array('ID','meta_value'),"AND ID IN($idx)");
			$data = convToGroup($data,array('ID','meta_value'));

			return $data;
		}

		return array();
	}
}