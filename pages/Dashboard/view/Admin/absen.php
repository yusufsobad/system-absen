<?php

class report_absen extends _page{

	protected static $object = 'report_absen';

	protected static $table = 'sobad_user';

	// ----------------------------------------------------------
	// Layout category  -----------------------------------------
	// ----------------------------------------------------------

	protected function table(){
		if(parent::$type=='punishment_1'){
			return self::table_schedule();
		}

		$date = date('Y-m');

		$object = self::$table;
		$args = $object::get_late($date);
		
		$data['class'] = 'punishment';
		$data['table'] = array();

		$no = 0;
		foreach($args as $key => $val){
			$no += 1;

			$permit = array(
				'ID'	=> 'permit_'.$val['ID'],
				'func'	=> '_permit',
				'color'	=> 'green',
				'icon'	=> 'fa fa-recycle',
				'label'	=> 'Izin',
			);
			
			$name = $object::get_id($val['user'],array('name','no_induk'));
			$name = $name[0]['name'];

			$data['table'][$key]['tr'] = array('');
			$data['table'][$key]['td'] = array(
				'No'			=> array(
					'center',
					'5%',
					$no,
					true
				),
				'Name'			=> array(
					'left',
					'auto',
					$name,
					true
				),
				'Tanggal'		=> array(
					'left',
					'25%',
					format_date_id($val['_inserted']),
					true
				),
				'Waktu'			=> array(
					'center',
					'10%',
					$val['time_in'],
					true
				),
				'Punishment'	=> array(
					'left',
					'10%',
					$val['punishment'].' menit',
					true
				),
				'Button'		=> array(
					'center',
					'10%',
					_modal_button($permit),
					false
				)
			);
		}

		return $data;
	}

	protected function table_schedule(){
		$date = date('Y-m');
		$sum = sum_days(date('m'),date('Y'));

		$awal = $date.'-01';
		$akhir = $date.'-'.sprintf("%02d",$sum);

		$whr = "AND `abs-punishment`.status IN ('0','2') OR (`abs-punishment`.status='1' AND date_punish BETWEEN '$awal' AND '$akhir')";

		$object = self::$table;
		$args = sobad_logDetail::get_all(array(),$whr);
		
		$data['class'] = 'schedule';
		$data['table'] = array();

		$no = 0;
		foreach($args as $key => $val){
			$no += 1;

			$data['table'][$key]['tr'] = array('');
			$data['table'][$key]['td'] = array(
				'No'			=> array(
					'center',
					'5%',
					$no,
					true
				),
				'Name'			=> array(
					'left',
					'auto',
					$val['name_user'],
					true
				),
				'Tanggal'		=> array(
					'left',
					'25%',
					format_date_id($val['date_punish']),
					true
				),
				'Waktu'			=> array(
					'center',
					'10%',
					$val['punish'].' menit',
					true
				)
			);
		}

		return $data;
	}

	private function head_title(){
		$args = array(
			'title'	=> 'Punishment <small>data punishment</small>',
			'link'	=> array(
				0	=> array(
					'func'	=> self::$object,
					'label'	=> 'punishment'
				)
			),
			'date'	=> false
		); 
		
		return $args;
	}

	protected function get_box(){
		$data = self::table();

		$label = 'Data punishment';
		$action = '';

		if(parent::$type=='punishment_1'){
			$label = 'Schedule Punishment';
			$action = self::action();
		}
		
		$box = array(
			'label'		=> $label,
			'tool'		=> '',
			'action'	=> $action,
			'func'		=> 'sobad_table',
			'data'		=> $data
		);

		return $box;
	}

	protected function layout(){
		$box = self::get_box();

		$tabs = array(
			'tab'	=> array(
				0	=> array(
					'key'	=> 'punishment_0',
					'label'	=> 'User',
					'qty'	=> ''
				),
				1	=> array(
					'key'	=> 'punishment_1',
					'label'	=> 'Jadwal',
					'qty'	=> ''
				)
			),
			'func'	=> '_portlet',
			'data'	=> $box
		);
		
		$opt = array(
			'title'		=> self::head_title(),
			'style'		=> array(),
			'script'	=> array('')
		);
		
		return tabs_admin($opt,$tabs);
	}

	protected function action(){
		$add = array(
			'ID'	=> 'schedule_0',
			'func'	=> '_schedule',
			'color'	=> 'btn-default',
			'icon'	=> 'fa fa-refresh',
			'label'	=> 'Schedule',
			'type'	=> parent::$type
		);
		
		return _click_button($add);
	}

	public static function _permit($id=0){
		$id = str_replace('permit_', '', $id);
		$vals = array($id,'');
		
		$args = array(
			'title'		=> 'Alasan Terlambat',
			'button'	=> '_btn_modal_save',
			'status'	=> array(
				'link'		=> '_add_permit',
				'load'		=> 'sobad_portlet'
			)
		);
		
		return self::_data_form($args,$vals);
	}

	private function _data_form($args=array(),$vals=array()){
		$check = array_filter($args);
		if(empty($check)){
			return '';
		}

		$data = array(
			0 => array(
				'func'			=> 'opt_hidden',
				'type'			=> 'hidden',
				'key'			=> 'ID',
				'value'			=> $vals[0]
			),
			1 => array(
				'func'			=> 'opt_input',
				'type'			=> 'text',
				'key'			=> 'note',
				'label'			=> 'Alasan',
				'class'			=> 'input-circle',
				'value'			=> $vals[1],
				'data'			=> 'placeholder="Alasan"'
			)
		);
		
		$args['func'] = array('sobad_form');
		$args['data'] = array($data);
		
		return modal_admin($args);
	}

	protected static function _check_holiday($date='',$dayoff=array()){

		$date = date($date);
		$_date = strtotime($date);

		$year = date('Y',$_date);
		$month = date('m',$_date);
		$day = date('d',$_date);
		$sum = sum_days($month,$year);

		for($i=$day;$i<=$sum;$i++){
			$date = $year.'-'.$month.'-'.sprintf("%02d",$i);

			$date = date($date);
			$_date = strtotime($date);

			if(date('w',$_date)==0){
				continue;
			}

			if(in_array($date,$dayoff)){
				continue;
			}

			return $date;
		}
	}

	public function _schedule(){
		$date = date('Y-m-d');
		$date = strtotime($date);

		$day = date('w');
		$sum = sum_days(date('m'),date('Y'));

		$sunday = floor(($sum - $day - date('d')) / 7) + 1;

		$awal = date('Y-m-d');
		$akhir = date('Y-m').'-'.sprintf("%02d",$sum);
		$holidays = sobad_holiday::get_all(array('ID','holiday'),"AND holiday BETWEEN '$awal' AND '$akhir'");
		$dayoff = count($holidays);
		$_total = ($sum - $sunday - $dayoff - date('d'));

		$object = self::$table;
		$args = $object::get_late(date('Y-m',$date));

		$j = 2;
		if(count($args)>=($_total*2)){
			$j = ceil(count($args) / $_total);
		}else{
			$_total = ceil(count($args) / 2);
		}

		$z = ($_total * $j) - count($args);
		$_a = $_total - $z;

		$holiday = array();
		foreach ($holidays as $key => $val) {
			$holiday[] = $val['holiday'];
		}

		$_cols = array();
		$cols = array();

		$ky = -1;
		for($h = 0;$h < $_total;$h++){
			for($i = 0;$i < $j;$i++) {
				if(($i + 1) == $j){
					if(($h + 1) > $_a){
						$_key = date('Y-m-d',strtotime("+1 days",$date));
						$date = strtotime($_key);
						continue;
					}
				}

				$ky += 1;
			
				$val = $args[$ky];
				$_key = date('Y-m-d',$date);

				if(isset($cols[$_key])){
					if(count($cols[$_key])==$j){
						$_key = date('Y-m-d',strtotime("+1 days",$date));
						$date = strtotime($_key);
					}
				}

				if(!isset($cols[$_key])){
					$_key = self::_check_holiday($_key,$holiday);
					$cols[$_key] = array();

					$date = strtotime($_key);
				}

				if(isset($cols[$_key][$i])){
					continue;
				}

				if($i>0){
					// Check user dalam satu baris
					// Jika Ada
					if(in_array($val['user'],$cols[$_key])){

						// lakukan pencarian baris yang belum di isi oleh user X
						for($k = ($key+1);$k < $sum;$k++){

							$_k = date('Y-m').'-'.sprintf("%02d",$k);
							if(!isset($cols[$_k])){
								$_key = self::_check_holiday($_k,$holiday);
								$cols[$_k] = array();
							}

							if(in_array($val['user'],$cols[$_k])){
								continue;
							}else{

								// Jika baris sudah terisi penuh
								if(count($cols[$_k])==$j){
									continue;
								}

								// Pengisian terhadap kolom yang belum di isi user X
								$_l = count($cols[$_k]) - 1;
								$cols[$_k][$_l] = $val['user'];

								$_cols[$_k][$_l] = array(
									'user_log'		=> $val['ID'],
									'date_punish'	=> $_k,
									'punish'		=> $val['punishment'],
									'punish_history'=> serialize(array('history' => array(
											0			=> array(
												'date'		=> $_k,
												'periode'	=> 1
											)
										))
									)
								);

								break;
							}
						}
					}
				}

				$cols[$_key][$i] = $val['user'];

				$_cols[$_key][$i] = array(
					'user_log'		=> $val['ID'],
					'date_punish'	=> $_key,
					'punish'		=> $val['punishment'],
					'punish_history'=> serialize(array('history' => array(
							0			=> array(
								'date'		=> $_key,
								'periode'	=> 1
							)
						))
					)
				);
			}
		}	

		$q = 0;
		foreach ($_cols as $key => $val) {
			//check log punishment
			foreach($val as $ky => $vl){
				$punish = sobad_logDetail::_check_log($vl['user_log']);
				$check = array_filter($punish);
				if(!empty($check)){
					continue;
				}

				$q = sobad_db::_insert_table('abs-punishment',$vl);
			}
		}

		if($q!==0){
			$table = self::table_schedule();
			return table_admin($table);
		}
	}

	public function _add_permit($args=array()){
		$args = sobad_asset::ajax_conv_json($args);
		$src = array();

		$id = $args['ID'];
		unset($args['ID']);
		
		if(isset($args['search'][0])){
			$src = array(
				'search'	=> $args['search'],
				'words'		=> $args['words']
			);

			unset($args['search']);
			unset($args['words']);
		}

		$log = sobad_user::get_logs(array('shift','_inserted','note','history'),"ID='$id'");
		if(empty($log[0]['note'])){
			$note = array('permit' => $args['note']);
			$note = serialize($note);
		}else{
			$note = unserialize($log[0]['note']);
			$note['permit'] = $args['note'];
			$note = serialize($note);
		}

		$day = date($log[0]['_inserted']);
		$day = strtotime($day);
		$day = date('w',$day);

		$work = sobad_work::get_id($log[0]['shift'],array('time_in'),"AND days='$day' AND status='1'");

		$history = unserialize($log[0]['history']);
		$history['logs'][] = array('type' => 4,'time' => $work[0]['time_in']);

		$data = array(
			'note'		=> $note,
			'punish'	=> 0,
			'history'	=> serialize($history)
		);

		$q = sobad_db::_update_single($id,'abs-user-log',$data);

		if($q!==0){
			$table = self::table();
			return table_admin($table);
		}
	}
}