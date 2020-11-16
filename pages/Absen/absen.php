<?php

class absensi{
	protected $object = 'absensi';

	public static function _status_group($data = array()){
		$group = array();
		if(isset($data)){
			if(in_array(1,$data)){
				$group['status'] = 1;
			}else{
				$group['status'] = 0;
			}

			if(in_array(2,$data)){
				$group['group'] = 1;
			}else{
				$group['group'] = 0;
			}

			if(in_array(3,$data)){
				$group['punish'] = 1;
			}else{
				$group['punish'] = 0;
			}
		}

		return $group;
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
		$_userid = 0;

		//get work
		$work = array();
		$users = sobad_user::get_all(array('ID','divisi','work_time'),$whr." AND status!='0'");

		$check = array_filter($users);
		if(!empty($check)){
			//Check Setting Auto Shift
			$_userid = $users[0]['ID'];
			$shift = sobad_permit::get_all(array('note'),"AND user='$_userid' AND type='9' AND start_date<='$date' AND range_date>='$date'");
			
			$check = array_filter($shift);
			if(!empty($check)){
				$worktime = $shift[0]['note'];
			}else{
				$worktime = $users[0]['work_time'];
			}

			$work = sobad_work::get_id($worktime,array('time_in','time_out'),"AND days='$day' AND status='1'");
			$group = sobad_module::_get_group($users[0]['divisi']);
		}

		$check = array_filter($work);
		if(empty($check)){
			$work = array(
				'time_in'	=> '08:00:00',
				'time_out'	=> '16:00:00'
			);

			$group = array(
				'ID'	=> 0,
				'name'	=> 'undefined',
				'data'	=> array(0),
				'group'	=> array(0)
			);
		}else{
			$work = $work[0];
		}


		//check kemarin
//		$yesterday = strtotime($date);
//		$yesterday = date('Y-m-d',strtotime('-1 days',$yesterday));
//		$user_y = sobad_user::get_absen(array('id_join','type','time_in','time_out'),$yesterday,$whr." AND `abs-user-log`.type='1'");

		//Check Permit
		$permit = sobad_permit::get_all(array('ID','user','type'),"AND user='$_userid' AND start_date<='$date' AND range_date>='$date' OR user='$_userid' AND start_date<='$date' AND range_date='0000-00-00' AND num_day='0.0'");

		$check = array_filter($permit);
		if(!empty($check)){
			sobad_db::_update_single($permit[0]['ID'],'abs-permit',array('range_date' => $date));
		}

		//check group
		$group['status'] = self::_status_group($group['status']);

		$punish = 0;
		if($times>=$work['time_in']){
			$punish = 1;
		}

		if($group['status']['punish']==0){
			$punish = 0;
		}

		//check log
		$user = sobad_user::get_absen(array('_nickname','id_join','type','time_in','time_out','history'),$date,$whr);

		$check = array_filter($user);
		if(empty($check)){
			if($times>=$work['time_out']){
				return array(
					'id' 		=> $id,
					'data' 		=> NULL,
					'status' 	=> 1,
					'msg' 		=> 'Sudah Jam Pulang!!!'
				);
			}

			foreach ($users as $key => $val) {
				sobad_db::_insert_table('abs-user-log',array(
						'user' 		=> $val['ID'],
						'type' 		=> 1,
						'shift'		=> $worktime,
						'_inserted' => $date,
						'time_in' 	=> $times,
						'time_out'	=> '00:00:00',
						'note'		=> serialize(array('pos_user' => $pos_user, 'pos_group' => $pos_group)),
						'punish'	=> $punish,
						'history'	=> serialize(array('logs' => array( 0 => array('type' => 1,'time' => $time))))
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
				if($time>=$work['time_in']){
					$time = '<span style="color:red;">'.$time.'</span>';
				}

				if($group['status']['punish']==0){
					$time = '';
				}

				$history = unserialize($user['history']);
				$history['logs'][] = array('type' => 1,'time' => $times);
				$history = unserialize($history['logs']);

				sobad_db::_update_single($user['id_join'],'abs-user-log',array('type' => 1,'punish' => $punish,'time_in' => $times,'history' => $history));
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
					$waktu = date_create($user['time_in']);
					date_add($waktu, date_interval_create_from_date_string('3 minutes'));
					$waktu = date_format($waktu,'H:i:s');

					if($time<=$waktu){
						return array(
							'id' 		=> $id,
							'data' 		=> NULL,
							'status' 	=> 1,
							'msg' 		=> 'Anda sudah scan masuk!!!'
						);	
					}

					return array(
						'id' 		=> $id,
						'data' 		=> true,
						'status' 	=> 0,
						'msg' 		=> '<div style="text-align:center;margin-bottom:20px;font-size:20px;">Mau Kemana \''.$user['_nickname'].'\'?</div>
										<div class="row" style="text-align:center;">
											<div class="col-md-4">
												<button style="width:80%;" type="button" class="btn btn-info" onclick="send_request(5)">Luar Kota</button>
											</div>
											<div class="col-md-4">
												<button style="width:80%;" type="button" class="btn btn-warning" onclick="send_request(4)">Izin</button>
											</div>
											<div class="col-md-4">
												<button style="width:80%;" type="button" class="btn btn-danger" onclick="send_request(2)">Pulang</button>
											</div>
										</div>',
						'modal'		=> true
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

			case 4:
			case 5:
				$to = 1;
				if($time>=$work['time_out']){
					$to = 2;
				}

				return array(
					'id' 		=> $id,
					'data' 		=> array(
							'type'	=> $user['type'],
							'date'	=> $time,
							'to'	=> $to
						),
					'status' 	=> 1,
					'msg' 		=> ''
				);

				break;
		}

		return array('id' => $id,'data' => NULL, 'status' => 1);
	}

	public function _request($args=array()){
		$date = date('Y-m-d');
		$time = date('H:i');

		$args = json_decode($args);
		$data = $args[0];
		$type = $args[1];

		$user = sobad_user::get_all(array('ID','id_join','history'),"AND no_induk='$data' AND `abs-user-log`._inserted='$date'");
		$idx = $user[0]['id_join'];

		$user = unserialize($user[0]['history']);
		$user['logs'][] = array('type' => $type, 'time' => $time);

		$_args = array('type' => $type,'history' => serialize($user));
		if($type==2){
			$_args['type'] = 4;
			$_args['time_out'] = $time;
		}

		sobad_db::_update_single($idx,'abs-user-log',$_args);

		return array(
					'id' 		=> $data,
					'data' 		=> array(
							'type'	=> $type,
							'date'	=> $time,
							'to'	=> $type
						),
					'status' 	=> 1,
					'msg' 		=> ''
				);
	}

	public static function _data_employee(){
		$date = date('Y-m-d');
		$whr = "AND `abs-user`.status!=0";
		$user = sobad_user::get_all(array('ID','divisi','_nickname','no_induk','picture','work_time','inserted','status'),$whr);
		$permit = sobad_permit::get_all(array('user','type'),"AND start_date<='$date' AND range_date>='$date' OR start_date<='$date' AND range_date='0000-00-00' AND num_day='0.0'");

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
		$video = array();
		$user = sobad_user::get_employees(array('no_induk','status'),"AND status!='0'");
		$intern = sobad_user::get_internships(array('no_induk','inserted','status'),"AND status!='0'");

		foreach ($user as $key => $val) {
			if($val['status']==0){
				continue;
			}

			$filename = 'asset/img/upload/user_'.$val['no_induk'].'.mp4';
			if(file_exists($filename)){
				$video[] = $filename;
			}
		}

		foreach ($intern as $key => $val) {
			if($val['status']==0){
				continue;
			}

			$no_induk = internship_absen::_conv_no_induk($val['no_induk'],$val['inserted']);
			$filename = 'asset/img/upload/user_'.$no_induk.'.mp4';
			if(file_exists($filename)){
				$video[] = $filename;
			}
		}

		$args = array(
			'total'		=> self::_employees(),
			'intern'	=> self::_internship(),
			'masuk'		=> self::_inWork(),
			'izin'		=> self::_permitWork(),
			'cuti'		=> self::_holidayWork(),
			'luar kota'	=> self::_outCity(),
			'video'		=> $video
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