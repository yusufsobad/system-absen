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

		table.sobad-punishment thead tr{
			background-color:#a4a4ff;
		}

		table.sobad-punishment thead tr th{
			font-size:18px;
		}

		table.sobad-punishment tbody tr{
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

function get_rule_absen($first='00:00:00',$last='00:00:00'){
	$waktu = _conv_time($first,$last,2);
	//Jika Izin kurang dari 1 jam, Tidak ganti Jam
	if($waktu<60){
		return array(
			'time'		=> $waktu,
			'status'	=> 'Izin',
			'type'		=> 0
		);
	}

	//Jika Izin kurang dari setengah Hari, ganti Jam
	if($waktu>=60 && $waktu<210){
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
	$status = get_rule_absen($first,$last);

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