<?php

class punishment_absen extends _page{

	protected static $object = 'punishment_absen';

	protected static $table = 'sobad_user';

	// ----------------------------------------------------------
	// Layout category  ------------------------------------------
	// ----------------------------------------------------------

	protected function table(){
		if(parent::$type!='punishment_0'){
			return self::table_schedule();
		}

		$date = date('Y-m');

		$object = self::$table;
		$args = $object::get_late();
		
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

	protected function table_schedule($preview=false){
		$date = date('Y-m');
		$sum = sum_days(date('m'),date('Y'));

		$start = intval(parent::$page);
		$nLimit = intval(parent::$limit);

		$awal = $date.'-01';
		$akhir = $date.'-'.sprintf("%02d",$sum);

		if(parent::$type=='punishment_2'){
			$limit = 'LIMIT '.intval(($start - 1) * $nLimit).','.$nLimit;
			$whr = "ORDER BY `abs-log-detail`.date_schedule DESC ".$limit;
		}else{
			$whr = "AND `abs-log-detail`.status IN ('0','2') OR (`abs-log-detail`.status='1' AND date_schedule BETWEEN '$awal' AND '$akhir') ORDER BY `abs-log-detail`.date_schedule ASC";
		}

		$args = sobad_logDetail::get_punishments(array(),$whr);
		$sum_data = sobad_logDetail::count("type_log='1'");
		
		$data['class'] = 'schedule';
		$data['table'] = array();

		if(parent::$type=='punishment_2'){
			$data['page'] = array(
				'func'	=> '_pagination',
				'data'	=> array(
					'start'		=> $start,
					'qty'		=> $sum_data,
					'limit'		=> $nLimit,
					'type'		=> parent::$type
				)
			);

			$no = ($start-1) * $nLimit;
		}else{
			$no = 0;
		}

		foreach($args as $key => $val){
			$no += 1;

			$history = array(
				'ID'	=> 'history_'.$val['ID'],
				'func'	=> '_history',
				'color'	=> 'yellow',
				'icon'	=> 'fa fa-eye',
				'label'	=> 'History',
			);

			$status = '';
			switch ($val['status']) {
				case 0:
					$status = '#666;';
					break;

				case 1:
					$status = '#26a69a;';
					break;

				case 2:
					$status = '#f5b724;';
					break;
				
				default:
					$status = '#fff;';
					break;
			}

			$status = '<i class="fa fa-circle" style="color:'.$status.'"></i>';

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
				'Terlambat'		=> array(
					'left',
					'15%',
					format_date_id($val['_inserted_log_']),
					true
				),
				'Pukul'		=> array(
					'left',
					'10%',
					$val['time_in_log_'],
					true
				),
				'Waktu'			=> array(
					'center',
					'10%',
					$val['times'].' menit',
					true
				),
				'Tanggal'		=> array(
					'left',
					'15%',
					format_date_id($val['date_schedule']),
					true
				),
				'Status'		=> array(
					'center',
					'10%',
					$status,
					true
				),
				'History'		=> array(
					'center',
					'10%',
					_modal_button($history),
					true
				),	
			);

			if(parent::$type!='punishment_2'){
				unset($data['table'][$key]['td']['Status']);
				unset($data['table'][$key]['td']['History']);
			}

			if($preview){
				$data['table'][$key]['td']['Pekerjaan'] = array(
					'left','30%','',false
				);

				$data['table'][$key]['td']['K.Div'] = array(
					'left','10%','',false
				);

				$data['table'][$key]['td']['HRD'] = array(
					'left','10%','',false
				);
			}
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
		parent::$type = 'punishment_0';
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
				),
				2	=> array(
					'key'	=> 'punishment_2',
					'label'	=> 'History',
					'qty'	=> ''
				)
			),
			'func'	=> '_portlet',
			'data'	=> $box,
		);
		
		$opt = array(
			'title'		=> self::head_title(),
			'style'		=> array(),
			'script'	=> array('')
		);
		
		return tabs_admin($opt,$tabs);
	}

	protected function action(){
		$print = array(
			'ID'	=> 'preview_0',
			'func'	=> '_preview',
			'color'	=> 'btn-default',
			'icon'	=> 'fa fa-print',
			'label'	=> 'Preview',
			'type'	=> parent::$type
		);

		$add = array(
			'ID'	=> 'schedule_0',
			'func'	=> '_schedule',
			'color'	=> 'btn-default',
			'icon'	=> 'fa fa-refresh',
			'label'	=> 'Schedule',
			'alert'	=> true,
			'type'	=> parent::$type
		);
		
		return print_button($print).' '._click_button($add);
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
			),
			array(
				'func'			=> 'opt_box',
				'type'			=> 'checkbox',
				'key'			=> 'status',
				'label'			=> 'Status',
				'inline'		=> true,
				'value'			=> '',
				'data'			=> array(
					0	=> array(
						'title'		=> 'Ganti Jam',
						'value'		=> '1'
					)
				)
			),
		);
		
		$args['func'] = array('sobad_form');
		$args['data'] = array($data);
		
		return modal_admin($args);
	}

	public function _history($id=0){
		$id = str_replace('history_', '', $id);
		intval($id);

		$args = sobad_logDetail::get_id($id,array('date_actual','log_history'));
		$history = unserialize($args[0]['log_history']);
		$history = $history['history'];

		$data['class'] = '';
		$data['table'] = array();

		$no = 0;
		foreach ($history as $ky => $vl) {

			if(isset($vl['punishment'])){
				$type = true;
				$_args = $vl['punishment'];
			}else{
				$type = false;
				$_args = explode(',', $args[0]['date_actual']);
			}

			$check = array_filter($_args);
			if(empty($check)){
				continue;
			}

			foreach ($_args as $key => $val) {
				$no += 1;

				$_date = ($type)?$key:$val;
				$note = ($type)?'<input type="text" value="'.$val.'">':'Telah melaksanakan punishment';

				$data['table'][$key]['tr'] = array('');
				$data['table'][$key]['td'] = array(
					'no'			=> array(
						'center',
						'5%',
						$no,
						true
					),
					'Jadwal'		=> array(
						'left',
						'15%',
						format_date_id($vl['date']),
						true
					),
					'Actual'		=> array(
						'left',
						'15%',
						format_date_id($_date),
						true
					),
					'Keterangan'	=> array(
						'left',
						'auto',
						$note,
						true
					)
				);
			}
		}

		$args = array(
			'title'		=> 'Detail data',
			'button'	=> '_btn_modal_save',
			'status'	=> array(),
			'func'		=> array('sobad_table'),
			'data'		=> array($data)
		);
		
		return modal_admin($args);
	}

	public static function _check_holiday($date='',$dayoff=array()){

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
		$_now = date('Y').'-'.date('m').'-01';

		$sunday = floor(($sum - $day - date('d')) / 7) + 1;

		$awal = date('Y-m-d');
		$akhir = date('Y-m').'-'.sprintf("%02d",$sum);
		$holidays = sobad_holiday::get_all(array('ID','holiday'),"AND holiday BETWEEN '$awal' AND '$akhir'");
		$dayoff = count($holidays);
		$_total = ($sum - $sunday - $dayoff - date('d')); // Jumlah hari kerja

	// Insert Data Punish	
		$object = self::$table;
		$args = $object::get_late('',"AND _inserted<'$_now'");

		foreach ($args as $key => $val) {
			$_detail = array(
				'log_id'		=> $val['ID'],
				'times'			=> $val['punishment'],
				'type_log'		=> 1
			);

			sobad_db::_update_single($val['ID'],'abs-user-log',array('ID' => $val['ID'], 'punish' => 0));
			$q = sobad_db::_insert_table('abs-log-detail',$_detail);
		}

	//Get data punishment
		$args = sobad_logDetail::get_punishments(array('ID','date_schedule','log_id','status','log_history','times','user'),"AND `abs-log-detail`.status IN ('0','2')");

		// Check 
		$check = array_filter($args);
		if(empty($check)){
			die(_error::_alert_db('Tidak ada jadwal punishment!!!'));
		}

		$check = strtotime($args[0]['date_schedule']);	
		$_cy = date('Y',$check);
		$_cm = date('m',$check);
		$_cnom = $_cy * 12 + $_cm;

		$_dy = date('Y',$date);
		$_dm = date('m',$date);
		$_dnom = $_dy * 12 + $_dm;

		if($_cnom == $_dnom){
			die(_error::_alert_db('Sudah Terjadwal!!!'));
		}

		// Calculate jadwal harian
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

	// Insert Data Punishment
		$_users = array();
		foreach ($args as $key => $val) {
			if(empty($val['date_schedule'])){
				continue;
			}

			if(array_key_exists($val['user_log_'], $_users)){
				$_users[$val['user_log_']] = array();
			}

			$_users[$val['user_log_']][] = array(
				'index'		=> $key,
				'status'	=> $val['status'],
				'times'		=> $val['times']
			);
		}

		$_count = count($args);
		foreach ($_users as $key => $val) {
			$_status = true;
			foreach ($val as $ky => $vl) {
				if($vl['times']<=30){
					$_status = false;
					$args[$vl['index']]['times'] = 60;
					break;
				}

				if($vl['times']>=60){
					$_status = false;
					if($vl['status']==2){
						$args[$vl['index']]['status'] = 0;
						break;
					}
				}
			}

			if($_status){
				$args[$_count] = array(
					'ID'				=> 0,
					'log_id'			=> $val['log_id'],
					'date_schedule'		=> '',
					'times'				=> 30,
					'log_history'		=> '',
					'status'			=> 0
				);

				$_count += 1;
			}
		}

	// Create Schedule Punishment
		$_key = date('Y-m-d',strtotime("+1 days",$date));
		$_key = self::_check_holiday($_key,$holiday);
		$date = strtotime($_key);

		$_jadwal = array();
		foreach ($args as $key => $val) {
			$_user = $val['user_log_'];
			$_args = array(
				'ID'				=> $val['ID'],
				'log_id'			=> $val['log_id'],
				'date_schedule'		=> '',
				'times'				=> $val['times'],
				'log_history'		=> unserialize($val['log_history']),
				'status'			=> $val['status']
			);

			if(!array_key_exists($_key, $_jadwal)){
				$_jadwal[$_key] = array();
			}

			if(array_key_exists($_user, $_jadwal[$_key])){
				$_sts = true;
				foreach ($_jadwal as $ky => $vl) {
					if(count($vl)<$j){
						if(!array_key_exists($_user, $vl)){
							$_sts = false;
							$_dt = strtotime($ky);

							$_args['date_schedule'] = $ky;
							$_jadwal[$ky][$_user] = $_args;
							break;
						}
					}

					$_dt = strtotime($ky);
				}

				if($_sts){
					$_dt = date('Y-m-d',strtotime("+1 days",$_dt));
					$_dt = self::_check_holiday($_dt,$holiday);

					$_args['date_schedule'] = $_dt;
					$_jadwal[$_dt][$_user] = $_args;
				}

				continue;
			}else{
				if(count($_jadwal[$_key])<$j){
					$_args['date_schedule'] = $_key;
					$_jadwal[$_key][$_user] = $_args;
				}else{
					$_key = date('Y-m-d',strtotime("+1 days",$date));
					$_key = self::_check_holiday($_key,$holiday);
					$date = strtotime($_key);

					$_args['date_schedule'] = $_key;
					$_jadwal[$_key][$_user] = $_args;
				}
			}
		}

		// Update Jadwal Punishment
		foreach ($_jadwal as $_date => $_user) {
			foreach ($_user as $key => $val) {
				$_history = $val['log_history'];
				if(isset($_history['history'])){
					$_cnt = count($_history['history']);
					$_history['history'][$_cnt-1] = array(
						'date'		=> $_date,
						'periode'	=> $_cnt
					);
				}else{
					$_history = array(
						'history' => array(
							0			=> array(
								'date'		=> $_key,
								'periode'	=> 1
							)
						)
					);
				}

				$val['log_history'] = serialize($_history);
				$val['type_log'] = 1;
				if(!empty($val['ID'])){
					$q = sobad_db::_update_single($val['ID'],'abs-log-detail',$val);
				}else{
					$q = sobad_db::_insert_table('abs-log-detail',$val);
				}
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

		$log = sobad_user::get_logs(array('user','shift','_inserted','note','history','time_in'),"ID='$id'");
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

		//Check ganti Jam
		if(isset($args['status']) && $args['status']==1){
			$data = array(
				'id'	=> $id,
				'date'	=> $log[0]['_inserted'],
				'user'	=> $log[0]['user'],
				'note'	=> $args['note']
			);

			set_rule_absen($work[0]['time_in'],$log[0]['time_in'],$data);
		}

		if($q!==0){
			$table = self::table();
			return table_admin($table);
		}
	}

	// ----------------------------------------------------------
	// Print data punishmnet ------------------------------------
	// ----------------------------------------------------------

	public function _preview($args=array()){
		$_SESSION[_prefix.'development'] = 0;

		$args = array(
			'data'		=> '',
			'style'		=> array('style_type2','style_punishment'),
			'object'	=> self::$object,
			'html'		=> '_html',
			'setting'	=> array(
				'posisi'	=> 'landscape',
				'layout'	=> 'A4',
			),
			'name save'	=> 'Punishment '.conv_month_id(date('m')).' '.date('Y')
		);

		return sobad_convToPdf($args);
	}

	public function _html(){
		$date = date('Y-m');
		$sum = sum_days(date('m'),date('Y'));

		$awal = $date.'-01';
		$akhir = $date.'-'.sprintf("%02d",$sum);

		$whr = "AND `abs-log-detail`.status IN ('0','2') OR (`abs-log-detail`.status='1' AND date_schedule BETWEEN '$awal' AND '$akhir') ORDER BY `abs-log-detail`.date_schedule ASC";

		$args = sobad_logDetail::get_punishments(array(),$whr);

		?>
		<page backtop="5mm" backbottom="5mm" backleft="5mm" backright="5mm" pagegroup="new">	
			<div style="text-align:center;width:100%;">
				<h2 style="margin-bottom: 0px;"> JADWAL PUNISHMENT KETERLAMBATAN </h2>
				<h3 style="margin-top: 0px;">Bulan <u>Absensi</u>: <?php echo conv_month_id(date('m')).' '.date('Y') ;?></h3>
			</div><br>
			<table class="table-bordered sobad-punishment" style="width:100%;font-family:calibri;">
				<thead>
					<tr>
						<th rowspan="2" style="width:5%;font-family: calibriBold;">No</th>
						<th rowspan="2" style="width:10%;font-family: calibriBold;">Nama</th>
						<th colspan="2" style="width:15%;font-family: calibriBold;">Data</th>
						<th rowspan="2" style="width:8%;font-family: calibriBold;">Punishment</th>
						<th rowspan="2" style="width:5%;font-family: calibriBold;">Hari</th>
						<th rowspan="2" style="width:10%;font-family: calibriBold;">Tanggal</th>
						<th rowspan="2" style="width:37%;font-family: calibriBold;">Pekerjaan</th>
						<th colspan="2" style="width:10%;font-family: calibriBold;">TTD.</th>
					</tr>
					<tr>
						<th style="width:10%;font-family: calibriBold;">Tanggal</th>
						<th style="width:5%;font-family: calibriBold;">Pukul</th>
						<th style="font-family: calibriBold;">K.Div</th>
						<th style="font-family: calibriBold;">HRD</th>
					</tr>
				</thead>
				<tbody>
					<?php
						foreach ($args as $key => $val) {
							$_user = sobad_user::get_id($val['user_log_'],array('_nickname'));
							$history = unserialize($val['log_history']);
							$period = '';

							$_date = strtotime($val['_inserted_log_']);
							$_date = date('d/m/Y',$_date);

							$_date2 = strtotime($val['date_schedule']);
							$_date2 = date('d/m/Y',$_date2);

							if(count($history['history'])>1){
								$period = 'warning';

								if(count($history['history'])>2){
									$period = 'danger';
								}
							}

							?>
								<tr class="<?php echo $period ;?>">
									<td style="font-size: 15px;"> <?php print(($key + 1)) ;?> </td>
									<td style="font-size: 15px;text-align: left;"> <?php print($_user[0]['_nickname']) ;?> </td>
									<td style="font-size: 15px;"> <?php print($_date) ;?> </td>
									<td style="font-size: 15px;"> <?php print($val['time_in_log_']) ;?> </td>
									<td style="font-size: 15px;"> <?php print($val['times']) ;?> Menit</td>
									<td style="font-size: 15px;"> <?php print(conv_day_id($val['date_schedule'])) ;?></td>
									<td style="font-size: 15px;"> <?php print($_date2) ;?> </td>
									<td> </td>
									<td> </td>
									<td> </td>
								</tr>
							<?php
						}
					?>
					<tr style="background-color: #fff; ">
						<td colspan="10" style="border:none;padding-top: 50px;text-align: left;">
							<label>KETERANGAN</label><br>
							<ol>
								<li>
									<span style="font-family: calibriBold;">
									Pekerjaan Punishment di tentukan dan di isikan oleh masing-masing Kepala Divisi
									</span>
								</li>
								<li>
									Punishment dilakukan <span style="font-family: calibriBold">sebelum</span> jam kerja berlangsung dengan ketentuan:<br>
									<i style="padding-left:10px;"></i>a. Untuk Punishment 30 menit : maksimal absen sebelum jam 07.20 WIB <br>
									<i style="padding-left:10px;"></i>b. Untuk Punishment 60 menit : maksimal absen sebelum jam 06.50 WIB <br>
									<i style="padding-left:10px;"></i>c. Jika absen punishment di atas jam tersebut, maka secara sistem tidak akan terekam sebagai pelaksanaan punishment <br>
									<i style="padding-left:10px;"></i>d. Jika absen setelah jam 06.50 WIB dan sebelum jam 07.20 maka akan di hitung telah melaksanakan punishment 30 menit <br>
									<i style="padding-left:10px;"></i>e. Untuk punishment 60 menit dapat dilakukan 2 x 30 menit (2 hari) <br>
								</li>
								<li>
									Setelah selesai menjalankan Punishment akan dicek dan di tanda tangani Kepala Divisi dan HRD
								</li>
								<li>
									Bagi yang tidak menjalankan Punishment akan di jadwalkan ulang pada bulan berikutnya di tambah <span style="font-family: calibriBold">30 menit</span>
								</li>
								<li>
									Ijin terlambat untuk suatu kepentingan bisa menghubungi bagian HRD
								</li>
							</ol>
						</td>
					</tr>
					<tr style="background-color: #fff;">
						<td colspan="7" style="border:none;">
							&nbsp;
						</td>
						<td style="border:none;text-align: center;">
							<span style="font-family: calibriBold;">
								Tertanda<br>HRD
							</span>
						</td>
						<td colspan="2" style="border:none;">
							&nbsp;
						</td>
					</tr>
				</tbody>
			</table>
		</page>
		<?php
	}
}