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

	public function _send($id){
		$datetime = date('Y-m-d H:i:s');
		$date = date('Y-m-d');
		$times = date('H:i:s');
		$time = date('H:i');
		$day = date('w');

		//get work
		$work = array();
		//$work = sobad_user::get_id($id,array('shift1'));
		//$work = json_decode($work[0]['shift1'],true);

		if(!isset($work['shift'])){
			$work = array(
				'work'	=> '08:00:00',
				'home'	=> '16:00:00'
			);
		}else{
			$work = $work['shift'];
		}

		//check log
		$user = sobad_user::get_absen(array('id_join','type','time_in','time_out'),$date,"AND no_induk='$id'");

		$check = array_filter($user);
		if(empty($check)){
			$users = sobad_user::get_all(array('ID','work_time'),"AND no_induk='$id' AND status!='0'");
			foreach ($users as $key => $val) {
				sobad_db::_insert_table('abs-user-log',array(
						'user' 		=> $val['ID'],
						'type' 		=> 1,
						'shift'		=> $val['work_time'],
						'inserted' 	=> $date,
						'time_in' 	=> $times,
						'time_out'	=> '00:00:00'
					)
				);
			}

			return array(
					'id' 		=> $id,
					'data' 		=> array(
						'type' => 1,
						'date' => $time
					),
					'status' 	=> 1,
					'msg' 		=> ''
				);

		}else{
			$user = $user[0];
		}

		switch ($user['type']) {
			case 0:
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
				if($time>=$work['home']){
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

		return array('id' => $id,'data' => NULL, 'status' => 0);
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
		$user = sobad_user::get_all(array('ID','divisi','_nickname','no_induk','picture','type','status','time_in','time_out'),"AND `abs-user`.status!=0");

		$group = sobad_module::_gets('group',array('ID','meta_value','meta_note'));

		return array('user' => $user, 'group' => $group);
	}

	public static function _status(){
		$args = array(
			'total'		=> self::_employees(),
			'masuk'		=> self::_inWork(),
			'izin'		=> self::_permitWork(),
			'cuti'		=> self::_holidayWork(),
			'luar kota'	=> self::_outCity()
		);

		return $args;
	}

	public static function _employees(){
		$work = sobad_user::count("status!=0");
		return $work;
	}

	public static function _inWork(){
		$date = date('Y-m-d');
		$work = sobad_user::go_work(array('id_join'),"AND `abs-user-log`.inserted='$date'");
		return count($work);
	}

	public static function _permitWork(){
		$date = date('Y-m-d');
		$work = sobad_user::go_permit(array('id_join'),"AND `abs-user-log`.inserted='$date'");
		return count($work);
	}

	public static function _holidayWork(){
		$date = date('Y-m-d');
		$work = sobad_user::go_holiday(array('id_join'),"AND `abs-user-log`.inserted='$date'");
		return count($work);
	}

	public static function _outCity(){
		$date = date('Y-m-d');
		$work = sobad_user::go_outCity(array('id_join'),"AND `abs-user-log`.inserted='$date'");
		return count($work);
	}
}