<?php

function style_punishment(){
	?>
		table.sobad-punishment{
			padding-left:25px;
			padding-right:25px;
		}

		table.sobad-punishment thead tr{
			text-align:center;
		}

		table.sobad-punishment thead tr.default{
			background-color:#a4a4ff;
		}

		table.sobad-punishment thead tr th{
			font-size:18px;
		}

		table.sobad-punishment tbody tr.default{
			background-color:#c2c2fb;
		}

		table.sobad-punishment tbody tr.danger{
			background-color:#ff1b1b;
		}

		table.sobad-punishment tbody tr.warning{
			background-color:#ffc310;
		}

		table.sobad-punishment thead tr th, table.sobad-punishment tbody tr td {
    		padding: 3px;
    		text-align:center;
		}

		ol li{
			list-style-type: decimal;
		}

		ul li{
			list-style-type: lower-alpha;
			padding-left:5px;
		}
	<?php
}

function get_rule_absen($first='00:00:00',$last='00:00:00',$worktime=0,$day=0){
	$waktu = _conv_time($first,$last,2);

	$restTime = 0;
	if(!empty($worktime)){
		$work = sobad_work::get_id($worktime,array('note'),"AND days='$day'");
		$check = array_filter($work);
		if(!empty($check)){
			$rests = array();
			$pointA = array(); $pointB = array();

			$work = explode(',', $work[0]['note']);
			$rests[0] = array('00:00:00','00:00:00');
			foreach ($work as $key => $val) {
				$rest = explode('-',$val);

			//Range Waktu
				$check = array_filter($pointA);
				if(empty($check)){
					if($rest[0]>$first){
						$pointA = array(1,$key);
					}else if($rest[0]<=$first && $rest[1]>=$first){
						$rest[0] = $first;
						$pointA = array(2,$key+1);
					}else if($rest[1]<$first){
						$pointA = array(3,$key+1);
					}
				}

				$check = array_filter($pointB);
				if(empty($check)){
					if($rest[0]>$last){
						$pointB = array(1,$key);
					}else if($rest[0]<=$last && $rest[1]>=$last){
						$rest[1] = $last;
						$pointB = array(2,$key+1);
					}else if($rest[1]<$first){
						$pointB = array(3,$key+1);
					}
				}

				$rests[] = $rest;
			}

			//Calculation jam istirahat
			if($pointB[1]>$pointA[1]){
				for ($i=$pointA[1]; $i<=$pointB[1] ; $i++) { 
					$restTime += _conv_time($rests[$i][0],$rests[$i][1],2);
				}
			}else{
				if($pointB[0]==2 && $pointA[0]==2){
					$waktu = 0;
				}
			}
		}
	}

	$waktu -= $restTime;

	if($waktu>=0 && $waktu<=10){
		//Jika Izin kurang dari sama dengan 10 menit, Tidak ganti Jam
		return array(
			'time'		=> $waktu,
			'status'	=> 'Izin',
			'type'		=> 0
		);
	}

	//Jika Izin kurang dari setengah Hari, ganti Jam
	if($waktu>10 && $waktu<210){
		$_check = $waktu % 30;
		if($_check<=10){
			$waktu -= $_check;
		}else{
			$waktu += (30 - $_check);
		}

		return array(
			'time'		=> $waktu,
			'status'	=> 'Ganti Jam',
			'type'		=> 2
		);
	}

	//Jika Izin setengah hari, Ambil Cuti setengah
	if($waktu>=210 && $waktu<300){
		return array(
			'time'		=> $waktu,
			'hour'		=> $waktu%60,
			'value'		=> 0.5,
			'status'	=> 'Cuti',
			'type'		=> 3
		);
	}

	//Jika Izin Full, Cuti 1 hari
	if($waktu>=300){
		return array(
			'time'		=> $waktu,
			'hour'		=> $waktu%60,
			'value'		=> 1,
			'status'	=> 'Cuti',
			'type'		=> 3
		);
	}
}

function set_rule_absen($first='00:00:00',$last='00:00:00',$args=array()){
	$status = get_rule_absen($first,$last,$args['work'],$args['day']);

	if($status['type']==3){
		$user = sobad_user::get_id($args['user'],array('dayOff'));
		$user = $user[0];

		if($user['dayOff']<$status['value']){
			$cuti = $user['dayOff'] - $status['value'];
			set_rule_cuti($status['value'],$cuti,$args);
		}else{
			$status['status'] = 'Ganti Jam';
			$status['type'] = 2;
		}
	}

	if($status['type']==2){
		sobad_db::_insert_table('abs-log-detail',array(
			'log_id'		=> $args['id'],
			'date_schedule'	=> $args['date'],
			'times'			=> $status['time'],
			'type_log'		=> 2
		));
	}

	return $status;
}

function set_rule_cuti($num_day=0,$cuti=0,$args=array()){
	sobad_db::_update_single($args['user'],'abs-user',array('ID' => $args['user'], 'dayOff' => $cuti));

	//Set Permit
	sobad_db::_insert_table('abs-permit',array(
		'user'			=> $args['user'],
		'start_date'	=> $args['date'],
		'range_date'	=> $args['date'],
		'num_day'		=> $num_day,
		'type_date'		=> 1,
		'type'			=> 3,
		'note'			=> $args['note']
	));
}