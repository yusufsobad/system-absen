<?php

class punishment_absen extends _page{

	protected static $object = 'punishment_absen';

	protected static $table = 'sobad_user';

	// ----------------------------------------------------------
	// Layout category  ------------------------------------------
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

	protected function table_schedule($preview=false){
		$date = date('Y-m');
		$sum = sum_days(date('m'),date('Y'));

		$awal = $date.'-01';
		$akhir = $date.'-'.sprintf("%02d",$sum);

		$whr = "AND `abs-log-detail`.status IN ('0','2') OR (`abs-log-detail`.status='1' AND date_schedule BETWEEN '$awal' AND '$akhir')";

		$args = sobad_logDetail::get_punishments(array(),$whr);
		
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
				)				
			);

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
			)
		);
		
		$args['func'] = array('sobad_form');
		$args['data'] = array($data);
		
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
		$_total = ($sum - $sunday - $dayoff - date('d'));

		$object = self::$table;
		$args = $object::get_late('',"AND _inserted<'$_now'");

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
									'log_id'		=> $val['ID'],
									'date_schedule'	=> $_k,
									'times'			=> $val['punishment'],
									'log_history'	=> serialize(array('history' => array(
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
					'log_id'		=> $val['ID'],
					'date_schedule'	=> $_key,
					'times'			=> $val['punishment'],
					'log_history'	=> serialize(array('history' => array(
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
				$punish = sobad_logDetail::_check_log($vl['log_id']);
				$check = array_filter($punish);
				if(!empty($check)){
					continue;
				}

				$vl['type_log'] = 1;
				$q = sobad_db::_insert_table('abs-log-detail',$vl);
			}
		}

		if($q!==0){
			$table = self::table_schedule();
			return table_admin($table);
		}else{
			die(_error::_alert_db('Sudah Terjadwal!!!'));
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

		$whr = "AND `abs-log-detail`.status IN ('0','2') OR (`abs-log-detail`.status='1' AND date_schedule BETWEEN '$awal' AND '$akhir')";

		$args = sobad_logDetail::get_punishments(array(),$whr);

		?>
		<page backtop="10mm" backbottom="20mm" backleft="15mm" backright="15mm" pagegroup="new">	
			<div style="text-align:center;width:100%;">
				<h1 style="margin-bottom: 0px;"> JADWAL PUNISHMENT KETERLAMBATAN </h1>
				<h3 style="margin-top: 0px;">Bulan <u>Absensi</u>: <?php echo conv_month_id(date('m')).' '.date('Y') ;?></h3>
			</div><br>
			<table class="table-bordered sobad-punishment" style="width:100%;font-family:calibri;">
				<thead>
					<tr>
						<th rowspan="2" style="width:5%;font-family: calibriBold;">No</th>
						<th rowspan="2" style="width:20%;font-family: calibriBold;">Nama</th>
						<th colspan="2" style="width:15%;font-family: calibriBold;">Data</th>
						<th rowspan="2" style="width:8%;font-family: calibriBold;">Punishment</th>
						<th rowspan="2" style="width:5%;font-family: calibriBold;">Hari</th>
						<th rowspan="2" style="width:10%;font-family: calibriBold;">Tanggal</th>
						<th rowspan="2" style="width:27%;font-family: calibriBold;">Pekerjaan</th>
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
							$history = unserialize($val['log_history']);
							$period = '';

							if(count($history['history'])>1){
								$period = 'warning';

								if(count($history['history'])>2){
									$period = 'danger';
								}
							}


							?>
								<tr class="<?php echo $period ;?>">
									<td style="font-size: 18px;"> <?php print(($key + 1)) ;?> </td>
									<td style="font-size: 18px;text-align: left;"> <?php print($val['name_user']) ;?> </td>
									<td style="font-size: 18px;"> <?php print($val['_inserted_log_']) ;?> </td>
									<td style="font-size: 18px;"> <?php print($val['time_in_log_']) ;?> </td>
									<td style="font-size: 18px;"> <?php print($val['times']) ;?> Menit</td>
									<td style="font-size: 18px;"> <?php print(conv_day_id($val['date_schedule'])) ;?></td>
									<td style="font-size: 18px;"> <?php print($val['date_schedule']) ;?> </td>
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
									Punishment dilakukan <span style="font-family: calibriBold">sebelum</span> jam kerja berlangsung
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