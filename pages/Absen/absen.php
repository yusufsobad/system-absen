<?php

class absensi{
	protected $object = 'absensi';

	public function layout(){
		self::divisi();
	}

	private function divisi($id=0){
		$where = '';

		$group = sobad_group::get_all(array('name','data'),"AND status='1'");
		$group = sobad_group::_conv_absensi($group);

		sobad_absen::_divisi($group);
	}

	public function _send($args=array()){
		$datetime = date('Y-m-d H:i:s');
		$date = date('Y-m-d');
		$times = date('H:i:s');
		$time = date('H:i');
		$day = date('w');

		//Convert data
		$args = json_decode($args);
		$id = $args[0];
		$pos_user = $args[1];
		$pos_group = $args[2];
 
		//Check user ---> employee atau internship
		$check = employee_absen::_check_noInduk($id);
		$_id = $check['id'];
		$whr = $check['where'];

		//get work
		$work = array();
		$users = sobad_user::get_all(array('ID','work_time'),$whr." AND status!='0'");

		$check = array_filter($users);
		if(!empty($check)){
			$work = sobad_work::get_id($users[0]['work_time'],array('time_in','time_out'),"AND days='$day' AND status='1'");
		}

		$check = array_filter($work);
		if(empty($check)){
			$work = array(
				'time_in'	=> '08:00:00',
				'time_out'	=> '16:00:00'
			);
		}else{
			$work = $work[0];
		}

		//check log
		$user = sobad_user::get_absen(array('id_join','type','time_in','time_out'),$date,$whr);

		$check = array_filter($user);
		if(empty($check)){
			foreach ($users as $key => $val) {
				sobad_db::_insert_table('abs-user-log',array(
						'user' 		=> $val['ID'],
						'type' 		=> 1,
						'shift'		=> $val['work_time'],
						'_inserted' => $date,
						'time_in' 	=> $times,
						'time_out'	=> '00:00:00',
						'note'		=> serialize(array('pos_user' => $pos_user, 'pos_group' => $pos_group))
					)
				);
			}

			$waktu = $time;
			if($pos_user==1){
	//			$waktu = '<span style="color:green;">'.$time.'</span>';
			}

			if($times>=$work['time_in']){
				$waktu = '<span style="color:red;">'.$time.'</span>';
			}

			return array(
					'id' 		=> $id,
					'data' 		=> array(
						'type' => 1,
						'date' => $waktu
					),
					'status' 	=> 1,
					'msg' 		=> ''
				);

		}else{
			$user = $user[0];
		}

		switch ($user['type']) {
			case 0:
				if($pos_user==1){
					$time = '<span style="color:green;">'.$time.'</span>';
				}

				if($time>=$work['time_in']){
					$time = '<span style="color:red;">'.$time.'</span>';
				}

				sobad_db::_update_single($user['id_join'],'abs-user-log',array('type' => 1,'time_in' => $times));
				return array(
					'id' 		=> $id,
					'data' 		=> array(
						'type' => 1,
						'date' => $time
					),
					'status' 	=> 1,
					'msg' 		=> ''
				);

				break;

			case 1:
				if($time>=$work['time_out']){
					sobad_db::_update_single($user['id_join'],'abs-user-log',array('type' => 2,'time_out' => $times));
					return array(
						'id' 		=> $id,
						'data' 		=> array(
							'type' => 2,
							'date' => $time
						),
						'status' 	=> 1,
						'msg' 		=> ''
					);
				}else{
					return array(
						'id' 		=> $id,
						'data' 		=> NULL,
						'status' 	=> 1,
						'msg' 		=> 'Anda sudah scan masuk!!!'
					);
				}

			case 2:
				return array(
					'id' 		=> $id,
					'data' 		=> NULL,
					'status' 	=> 1,
					'msg' 		=> 'Anda sudah scan pulang!!!'
				);

				break;
		}

		return array('id' => $id,'data' => NULL, 'status' => 1);
	}

	public static function _set_logs(){
		$log = sobad_user::get_log(array('id_log'),date('Y-m-d'));
		if(count($log)>0){
			return '&nbsp;';
		}

		$users = sobad_user::get_all(array('ID'),"AND status='1'");
		foreach ($users as $key => $val) {
			$data = array(
				'ID'	=> $val['ID'],
				'shift'	=> 1
			);

			sobad_db::_insert_table('abs-user-log',$data);
		}
	}

	public static function _data_employee(){
		$date = date('Y-m-d');
		$whr = "AND `abs-user`.status!=0";
		$user = sobad_user::get_all(array('ID','divisi','_nickname','no_induk','picture','work_time','inserted','status'),$whr);
		$permit = sobad_permit::get_all(array('user','type'),"AND start_date<='$date' AND range_date>='$date'");

		$group = sobad_module::_gets('group',array('ID','meta_value','meta_note'));

		$_group = array();
		foreach ($group as $key => $val) {
			$data = unserialize($val['meta_note']);
			$group[$key]['meta_note'] = $data;

			if(isset($data['data'])){
				foreach ($data['data'] as $ky => $vl) {
					array_push($_group,$vl);
				}
			}
		}

		$_permit = array(0 => 0);
		foreach ($permit as $key => $val) {
			$_permit[$val['user']] = $val['type'];
		}

		foreach ($user as $key => $val) {
			if($val['status']!=7){
				if(!in_array($val['divisi'],$_group)){
					unset($user[$key]);
					continue;
				}
			}else{
				$_date = date($val['inserted']);
				$user[$key]['no_induk'] = internship_absen::_conv_no_induk($val['no_induk'],$val['inserted']);
			}

			$idx = $val['ID'];
			$log = sobad_user::get_all(array('type','id_join','time_in','time_out','note'),"AND `abs-user`.ID='$idx' AND `abs-user-log`._inserted='$date'");

			$_log = true;
			$check = array_filter($log);
			if(empty($check)){
				$log[0] = array(
					'type'		=> NULL,
					'time_in'	=> NULL,
					'time_out'	=> NULL,
					'note'		=> array(
						'pos_user'	=> 1,
						'pos_group'	=> 1
					)
				);

				$_log = false;
			}else{
				$log[0]['note'] = unserialize($log[0]['note']);
			}

			if(array_key_exists($idx, $_permit)){
				$log[0]['type'] = $_permit[$idx];

				if($_log){
					sobad_db::_update_single($log[0]['id_join'],'abs-user-log',array(
							'user' 		=> $idx,
							'type'		=> $_permit[$idx],
						)
					);
				}else{
					sobad_db::_insert_table('abs-user-log',array(
							'user' 		=> $idx,
							'shift' 	=> $val['work_time'],
							'type'		=> $_permit[$idx],
							'_inserted'	=> $date
						)
					);
				}
			}

			$user[$key] = array_merge($user[$key],$log[0]);
		}

		return array('user' => $user, 'group' => $group);
	}

	public static function _status(){
		$args = array(
			'total'		=> self::_employees(),
			'intern'	=> self::_internship(),
			'masuk'		=> self::_inWork(),
			'izin'		=> self::_permitWork(),
			'cuti'		=> self::_holidayWork(),
			'luar kota'	=> self::_outCity()
		);

		return $args;
	}

	public static function _employees(){
		$work = sobad_user::count("status NOT IN ('0','7')");
		return $work;
	}

	public static function _internship(){
		$work = sobad_user::count("status IN ('7')");
		return $work;
	}

	public static function _inWork(){
		$date = date('Y-m-d');
		$work = sobad_user::go_work(array('id_join'),"AND `abs-user-log`._inserted='$date'");
		return count($work);
	}

	public static function _permitWork(){
		$date = date('Y-m-d');
		$work = sobad_user::go_permit(array('id_join'),"AND `abs-user-log`._inserted='$date'");
		return count($work);
	}

	public static function _holidayWork(){
		$date = date('Y-m-d');
		$work = sobad_user::go_holiday(array('id_join'),"AND `abs-user-log`._inserted='$date'");
		return count($work);
	}

	public static function _outCity(){
		$date = date('Y-m-d');
		$work = sobad_user::go_outCity(array('id_join'),"AND `abs-user-log`._inserted='$date'");
		return count($work);
	}
}