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

	public static function _check_punishment($user=0,$worktime=''){
		$punish = 0;
		$times = date('H:i:s');

		$worktime = _calc_time($worktime,'-40 minutes'); // 10 menit adalah waktu briefing

		if($times<$worktime){
			$punish = 30;
		}

		$worktime = _calc_time($worktime,'-30 minutes');

		if($times<$worktime){
			$punish = 60;
		}

		return self::_set_punishment($user,$punish);
	}

	public static function _set_punishment($user=0,$punish=0,$date=''){
		$waktu = date('H:i');
		$date = empty($date)?date('Y-m-d'):$date;
		$strdate = strtotime($date);

		$where = "AND _log_id.user='$user' AND `abs-log-detail`.status!='1'";
		$punishment = sobad_logDetail::get_punishments(array('ID','log_id','times','status','date_actual','log_history'),$where);

		$_name = '';$_nik = '';
		$total = $punish * -1;
		$status = false;$_data = array();
		foreach ($punishment as $key => $val) {
			if($val['status']==2){
				$val['times'] -= 30;
			}else{
				if($val['times']>=60){
					if($punish==30){
						$_data[] = $val;
						$status = true;
					}
				}
			}

			if($val['times']<=$punish){
				$_data[] = $val;
				$status = true;
			}

			$_name = sobad_user::get_id($val['user_log_'],array('_nickname'));

			$check = array_filter($_name);
			$_name = empty($check)?$val['name_user']:$_name[0]['_nickname'];

			$_nik = $val['no_induk_user'];
			$total += $val['times'];
		}

		$modal = array(
			'id' 		=> $_nik,
			'data' 		=> array(
				'type' => 1,
				'date' => $waktu,
				'note' => 'punishment'
			),
			'status' 	=> 0,
			'msg' 		=> '<div style="text-align:center;margin-bottom:20px;font-size:20px;">Anda punishment yha, \''.$_name.'\'?</div>
					<div class="row" style="text-align:center;">
						<div class="col-md-12">
							Sisa punishment anda : <H1>'.$total.'</H1> menit
						</div>
					</div>
				</div>',
			'absen'		=> true,
			'modal'		=> true,
			'timeout'	=> 10 * 1000
		);

		if($status){
			//Update Punishment
			$_actual = explode(',', $_data[0]['date_actual']);
			$check = array_filter($_actual);

			if(empty($check)){
				$_actual = array($date);
			}else{
				$_actual[] = $date;
			}

			$_index = date('Ymd',$strdate);
			$_history = unserialize($_data[0]['log_history']);
			$_history = $_history['history'];
			
			$_cnt = count($_history);
			if(!isset($_history[$_cnt-1]['punishment'])){
				$_history[$_cnt-1]['punishment'] = array();
			}
			
			$_history[$_cnt-1]['punishment'][$_index] = 'Telah Melakukan Punishment';
			$_log = array();
			$_log['history'] = $_history;

			if(($_data[0]['times'] - $punish)<=0){
				sobad_db::_update_single($_data[0]['ID'],'abs-log-detail',array(
					'status'		=> 1,
					'date_actual'	=> implode(',', $_actual),
					'log_history'	=> serialize($_log)
				));
			}else{
				sobad_db::_update_single($_data[0]['ID'],'abs-log-detail',array(
					'status'		=> 2,
					'date_actual'	=> implode(',', $_actual),
					'log_history'	=> serialize($_log)
				));
			}
		}

		return array(
			'status'	=> $status,
			'total'		=> $total,
			'modal'		=> $modal
		);
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

			$work = sobad_work::get_id($worktime,array('time_in','time_out','status'),"AND days='$day'");
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
		$permit = sobad_permit::get_all(array('ID','user','type'),"AND user='$_userid' AND type!='9' AND start_date<='$date' AND range_date>='$date' OR user='$_userid' AND start_date<='$date' AND range_date='0000-00-00' AND num_day='0.0'");

		$check = array_filter($permit);
		if(!empty($check)){
			$pDate = strtotime($date);
			$pDate = date('Y-m-d',strtotime('-1 days',$pDate));
			sobad_db::_update_single($permit[0]['ID'],'abs-permit',array('range_date' => $pDate));
		}

		//check group
		$group['status'] = self::_status_group($group['status']);

		$punish = 0;
		if($work['status']){
			if($times>=$work['time_in']){
				$punish = 1;
			}
		}

		if($group['status']['punish']==0){
			$punish = 0;
		}

		//check log
		$user = sobad_user::get_absen(array('_nickname','id_join','type','time_in','time_out','history'),$date,$whr);

		$check = array_filter($user);
		if(empty($check)){
			if($work['status']){
				if($times>=$work['time_out']){
					return array(
						'id' 		=> $id,
						'data' 		=> NULL,
						'status' 	=> 1,
						'msg' 		=> 'Sudah Jam Pulang!!!'
					);
				}
			}

			foreach ($users as $key => $val) {
				$q = sobad_db::_insert_table('abs-user-log',array(
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

				if($work['status']==0){
					// Update Lembur
					sobad_db::_insert_table('abs-log-detail',array(
						'log_id'		=> $q,
						'date_schedule'	=> $date,
						'type_log'		=> 3,
						'status'		=> 2
					));
				}
			}

			//Check punishment
			if($work['status']){
				$check = self::_check_punishment($_userid,$work['time_in']);
				if($check['status']){
					return $check['modal'];
				}
			}

			$waktu = $time;
			if($pos_user==1){
	//			$waktu = '<span style="color:green;">'.$time.'</span>';
			}

			if($work['status']){
				if($times>=$work['time_in']){
					if($punish){
						$waktu = '<span style="color:red;">'.$time.'</span>';
					}
				}
			}

			return array(
					'id' 		=> $id,
					'data' 		=> array(
						'type' 		=> 1,
						'date' 		=> $waktu,
					),
					'status' 	=> 1,
					'msg' 		=> '',
					'absen'		=> true
				);

		}else{
			$user = $user[0];
		}

		switch ($user['type']) {
			case 0:
				if($work['status']){
					if($time>=$work['time_in']){
						if($punish){
							$time = '<span style="color:red;">'.$time.'</span>';
						}
					}
				}

				if($group['status']['punish']==0){
					$time = '';
				}

				$history = unserialize($user['history']);
				$history['logs'][] = array('type' => 1,'time' => $times);
				$history = serialize($history['logs']);

				sobad_db::_update_single($user['id_join'],'abs-user-log',array('type' => 1,'punish' => $punish,'time_in' => $times,'history' => $history));

				if($work['status']==0){
					// Update Lembur
					sobad_db::_insert_table('abs-log-detail',array(
						'log_id'		=> $user['id_join'],
						'date_schedule'	=> $date,
						'type_log'		=> 3,
						'status'		=> 2
					));
				}

				return array(
					'id' 		=> $id,
					'data' 		=> array(
						'type'		=> 1,
						'date'		=> $time
					),
					'status' 	=> 1,
					'msg' 		=> '',
					'absen'		=> true
				);

				break;

			case 1:
				if(empty($work['status'])){
					// Update Lembur Pulang
					$_logid = $user['id_join'];
					$_logs = sobad_logDetail::get_all(array('ID'),"AND log_id='$_logid' AND type_log='3'");
					$check = array_filter($_logs);

					if(!empty($check)){
						$_logid = $_logs[0]['ID'];
						$_waktu = _conv_time($user['time_in'],$times,3);
						sobad_db::_update_single($_logid,'abs-log-detail',array('times' => $_waktu, 'status' => 1));

						$history = unserialize($user['history']);
						$history['logs'][] = array('type' => 2,'time' => $times);
						$history = serialize($history['logs']);

						sobad_db::_update_single($_logid,'abs-user-log',array('type' => 2,'time_out' => $times, 'history' => $history));

						return array(
							'id' 		=> $id,
							'data' 		=> array(
								'type' 		=> 2,
								'date' 		=> $time
							),
							'status' 	=> 1,
							'msg' 		=> '',
							'absen'		=> true
						);
					}
				}

				if($time>=$work['time_out']){
					$history = unserialize($user['history']);
					$history['logs'][] = array('type' => 2,'time' => $times);
					$history = serialize($history['logs']);

					sobad_db::_update_single($user['id_join'],'abs-user-log',array('type' => 2,'time_out' => $times, 'history' => $history));

					$_out = _calc_time($work['time_out'],'1 hours');
					// Jika lebih dari 1 jam ---> modal box
					if($time>=$_out){
						$_log = sobad_logDetail::get_all(array('ID','log_id','times'),"AND _log_id.user='$_userid' AND type_log='2'");
						$check = array_filter($_log);

						// Jika tidak ada ganti jam
						if(empty($check)){
							$label = 'Lembur';
							$index = 9;
						}else{
							$label = 'Ganti Jam';
							$index = 7;
						}

						return array(
							'id' 		=> $id,
							'data' 		=> array(
								'type' => 2,
								'date' => $time
							),
							'status' 	=> 0,
							'msg' 		=> '<div style="text-align:center;margin-bottom:20px;font-size:20px;">'.$label.' ya, \''.$user['_nickname'].'\'?</div>
											<div class="row" style="text-align:center;">
												<div class="col-md-12">
													<button style="width:30%;" type="button" class="btn btn-info" onclick="send_request('.$index.')">Ya</button>
												</div>
											</div>',
							'modal'		=> true,
							'absen'		=> true
						);
					}

					return array(
						'id' 		=> $id,
						'data' 		=> array(
							'type' => 2,
							'date' => $time
						),
						'status' 	=> 1,
						'msg' 		=> '',
						'absen'		=> true
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
						'msg' 		=> '<div style="text-align:center;margin-bottom:20px;font-size:20px;">Mau Kemana \''.$user['_nickname'].'\'? <span id="note-textarea"> </span></div>
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
											<div class="col-md-12">
												<p style="margin-top: 20px;" id="recording-instructions">Press the <strong>Start Recognition</strong> button and allow access.</p>
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

			case 3:
			case 4:
				$timeB = $times;
				if($work['status']){
					if($time>=$work['time_out']){
						$timeB = $work['time_out'];
					}
				}

				$timeA = unserialize($user['history']);

				$cnt = count($timeA);
				$timeA = $timeA['logs'][$cnt-1]['time'];

				$ganti = get_rule_absen($timeA,$timeB);
				sobad_db::_insert_table('abs-log-detail',array(
					'log_id'		=> $user['id_join'],
					'date_schedule'	=> date('Y-m-d'),
					'times'			=> $ganti['time'],
					'type_log'		=> 2
				));

			case 5:
				$type = 1;
				$_label = 'time_in';
				if($work['status']){
					if($time>=$work['time_out']){
						$type = 2;
						$_label = 'time_out';
					}
				}

				$history = unserialize($user['history']);
				$history['logs'][] = array('type' => $type,'time' => $times);
				$history = serialize($history['logs']);

				sobad_db::_update_single($user['id_join'],'abs-user-log',array('type' => $type, $_label => $times, 'history' => $history));

				return array(
					'id' 		=> $id,
					'data' 		=> array(
							'type'	=> $type,
							'date'	=> $time,
							'from'	=> $user['type']
						),
					'status' 	=> 1,
					'msg' 		=> '',
					'absen'		=> true
				);

				break;
		}

		return array('id' => $id,'data' => NULL, 'status' => 1);
	}

	public function _request($args=array()){
		$date = date('Y-m-d');
		$times = date('H:i:s');
		$time = date('H:i');
		$day = date('w');

		$args = json_decode($args);
		$data = $args[0];
		$type = $args[1];

		$user = sobad_user::get_all(array('ID','work_time','dayOff','_nickname','id_join','history'),"AND no_induk='$data' AND `abs-user-log`._inserted='$date'");

		$_id = $user[0]['ID'];
		$_worktime = $user[0]['work_time'];
		$_dayOff = $user[0]['dayOff'];
		$_nickname = $user[0]['_nickname'];
		$idx = $user[0]['id_join'];

		$user = unserialize($user[0]['history']);
		$user['logs'][] = array('type' => $type, 'time' => $times);

		$_args = array('type' => $type,'time_out' => $times,'history' => serialize($user));
		if($type==2){
			
			// Jika Pilih Pulang
			return array(
				'id' 		=> $data,
				'data' 		=> true,
				'status' 	=> 0,
				'msg' 		=> '<div style="text-align:center;margin-bottom:20px;font-size:20px;">Mau pilih yang mana, \''.$_nickname.'\'?</div>
									<div class="row" style="text-align:center;">
										<div class="col-md-4">
											<button style="width:60%;" type="button" class="btn btn-info" onclick="send_request(3)">Cuti</button>
										</div>
										<div class="col-md-4">
											<button style="width:60%;" type="button" class="btn btn-warning" onclick="send_request(8)">Ganti Jam</button>
										</div>
										<div class="col-md-4">
											<button style="width:60%;" type="button" class="btn btn-warning" onclick="send_request(8)">Izin Sakit</button>
										</div>
								</div>',
				'modal'		=> true
			);
		}

		// Check Izin, Luar Kota, Pulang (Ganti Jam atau Cuti )
		// 3 : Cuti ( type == 2 ) | 4 : Izin | 5 : Luar Kota | 8 : Ganti Jam ( type == 2 )

		$work = sobad_work::get_id($_worktime,array('time_out'),"AND days='$day'");
		$work = $work[0]['time_out'];
		
		if($type==3){
			if($_dayOff<=0){
				$type = 8;
			}
		}

		switch ($type) {
			case 3:
				$_args['type'] = 2;
				$_data = array(
					'id'	=> $idx,
					'date'	=> $date,
					'user'	=> $_id,
					'note'	=> 'Pulang Cepat'
				);

				$waktu = _conv_time($times,$work,2);
				if($waktu<=270){
					$num_day = 0.5;
				}else{
					$num_day = 1;
				}

				$cuti = $_dayOff - $num_day;
				if($_dayOff<$num_day){
					set_rule_absen($times,$work,$data);
				}
				set_rule_cuti($num_day,$cuti,$data);	
				break;

			case 8:
				$_args['type'] = 2;
				$type = 2;

				$ganti = get_rule_absen($times,$work);
				sobad_db::_insert_table('abs-log-detail',array(
					'log_id'		=> $idx,
					'date_schedule'	=> date('Y-m-d'),
					'times'			=> $ganti['time'],
					'type_log'		=> 2
				));
				break;

			case 4:

				break;

			case 5:
				// next
				break;

			case 7: // Pulang telat --> Ganti Jam
				$ganti = _conv_time($times, $work, 2);
				history_absen::_calc_gantiJam($_id,$ganti);

				return array('id' => $data,'data' => NULL, 'status' => 0);
			case 9: // Pulang telat --> Lembur
				$lembur = _conv_time($times, $work, 3);
				sobad_db::_insert_table('abs-log-detail',array(
					'log_id'		=> $idx,
					'date_schedule'	=> date('Y-m-d'),
					'times'			=> $lembur,
					'status'		=> 1,
					'type_log'		=> 3
				));

				return array('id' => $data,'data' => NULL, 'status' => 0);
				break;
			
			default:
				return array('id' => $data,'data' => NULL, 'status' => 1, 'msg' => 'Tidak ada Pilihan!!!');
				break;
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
					'msg' 		=> '',
					'absen'		=> true
				);
	}

	public static function _data_employee(){
		$date = date('Y-m-d');
		$whr = "AND `abs-user`.status!=0";
		$user = sobad_user::get_all(array('ID','divisi','_nickname','no_induk','picture','work_time','inserted','status','_resign_date'),$whr);
		$permit = sobad_permit::get_all(array('user','type'),"AND type!='9' AND start_date<='$date' AND range_date>='$date' OR start_date<='$date' AND range_date='0000-00-00' AND num_day='0.0'");

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
			if(!in_array($val['type'],array(3,5,6))){
				$val['type'] = 4;
			}

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

				if(isset($val['_resign_date'])){
					if($date>$val['_resign_date']){
						sobad_db::_update_single($val['ID'],'abs-user',array('ID' => $val['ID'],'status' => 0,'end_status' => 7));
						unset($user[$key]);
						continue;
					}
				}
			}

			$idx = $val['ID'];
			$log = sobad_user::get_all(array('type','id_join','shift','time_in','time_out','note'),"AND `abs-user`.ID='$idx' AND `abs-user-log`._inserted='$date'");

			$_log = true;
			$check = array_filter($log);
			if(empty($check)){
				$log[0] = array(
					'type'		=> NULL,
					'shift'		=> 0,
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
				$_libur = holiday_absen::_check_holiday();
				if(!$_libur){
					$log[0]['type'] = $_permit[$idx];
				}

				if($_log){
					sobad_db::_update_single($log[0]['id_join'],'abs-user-log',array(
							'user' 		=> $idx,
							'type'		=> $_permit[$idx],
						)
					);
				}else{
					if(!$_libur){
						sobad_db::_insert_table('abs-user-log',array(
								'user' 		=> $idx,
								'shift' 	=> $val['work_time'],
								'type'		=> $_permit[$idx],
								'_inserted'	=> $date,
							)
						);
					}
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