<?php

class employeeReport_absen extends _page{
	protected static $object = 'employeeReport_absen';

	protected static $table = 'sobad_user';

	// ----------------------------------------------------------
	// Layout category  -----------------------------------------
	// ----------------------------------------------------------

	protected function _array(){
		$args = employee_absen::_array();
		$args[] = '_resign_status';

		return $args;
	}

	private function head_title(){
		$args = array(
			'title'	=> 'Karyawan <small>report karyawan</small>',
			'link'	=> array(
				0	=> array(
					'func'	=> self::$object,
					'label'	=> 'karyawan'
				)
			),
			'date'	=> false
		); 
		
		return $args;
	}

	protected function get_box(){
		
		$box = array(
			'label'		=> 'Report Karyawan',
			'tool'		=> '',
			'action'	=> self::action(),
			'func'		=> '_layout',
			'object'	=> 'employeeReport_absen',
			'data'		=> ''
		);

		return $box;
	}

	protected function layout(){
		$box = self::get_box();
		
		$opt = array(
			'title'		=> self::head_title(),
			'style'		=> array('employeeReport_absen','_style'),
			'script'	=> array('employeeReport_absen','_script')
		);
		
		return portlet_admin($opt,$box);
	}

	protected function action(){
		ob_start();
		?> 
			<select class="form-control bs-select" data-live-search="true" data-size="6" data-style="blue" data-sobad="sobad__reportEmployee" data-load="report-employee" data-attribute="html" onchange="sobad_options(this)"> 
		<?php
			$user = sobad_user::get_employees(array('ID','no_induk','name'));
			foreach ($user as $key => $val) {
				?>
					<option value="<?php print($val['ID']) ;?>"><?php echo $val['no_induk'].' :: '.$val['name'] ;?></option>
				<?php
			}
		?>
			</select>
		<?php
		$opt = ob_get_clean();

		return $opt;
	}

	// ----------------------------------------------------------
	// Layout report --------------------------------------------
	// ----------------------------------------------------------

	public function _style(){
		?>
			<style type="text/css">
				/* general */
				div#report-employee {
    				background-color: #efefef;
    				padding: 30px;
    				border-radius: 20px !important;
    				font-size: 16px;
				}

				.bag-report {
				    background-color: #fff;
				    border-radius: 15px !important;
				    padding: 20px;
				    margin-top: 20px;
				}

				.title-report{
					color: #15499a;
					font-weight: 600;
				}

				h2.title-report {
				    margin-top: 0px;
				}

				.dashboard-stat.blue-report .visual > i,
				.dashboard-stat.green-report .visual > i,
				.dashboard-stat.yellow-report .visual > i,
				.dashboard-stat.red-report .visual > i {
				    color: #FFFFFF;
				    opacity: 0.2;
				    filter: alpha(opacity=10);
				}

				.blue-report,
				.dashboard-stat.blue-report .more{
					background-color:#15499a;
					color:#fff;
				}

				.blue-ocean,
				.dashboard-stat.blue-ocean .more{
					background-color:#9abef6;
					color:#fff;
				}

				.green-report,
				.dashboard-stat.green-report .more{
					background-color:#45c423;
					color:#fff;
				}

				.yellow-report,
				.dashboard-stat.yellow-report .more{
					background-color:#ffae00;
					color:#fff;
				}

				.red-report,
				.dashboard-stat.red-report .more{
					background-color:#ff0000;
					color:#fff;
				}

				/* Diagram circle */
				.diagram-circle4{
					width: 33.33333%;
					float: left;
					padding: 10px;
				}

				.c100.blue-ocean .bar, .c100.blue-ocean .fill {
				    border-color: #15499a;
				}

				.c100 > span.display-table {
				    width: 100% !important;
				    height: 100%;
				    line-height: unset;
				    display: table;
				}

				.c100 > span.display-table>.table-cell {
				    display: table-cell;
				    height: 100%;
				    width: 100%;
				    vertical-align: middle;
				    font-size: 150%;
				    color: #15499a;
				}

				.c100 > span.display-table>.table-cell>span {
				    display: block;
				    font-size: 10px;
				    line-height: 0;
				    color: #000;
				}

				/* Profile */
				div#absen-profile {
				    padding-bottom: 20px;
				}
				.box-profile,
				.box-profile>img {
				    border-radius: 15px !important;
				}
				.box-profile{
				    background-image: linear-gradient(#dfdfdf,#afafaf);
				}

				.no-induk {
				    padding: 5px 0px 10px;
				}

				.no-induk span {
				    padding: 5px;
				    font-size: 20px;
				    border-radius: 7px !important;
				}

				.name-employee {
				    color: #15499a;
				    font-size: 35px;
				    font-weight: 600;
				    line-height: 1;
				    margin: 10px 0px;
				}

				.name-employee>span {
				    font-size: 18px;
				    display: block;
				}

				.divisi-employee {
				    font-weight: 700;
				}

				.address-content {
				    margin-bottom: 10px;
				}

				.phone-content {
				    font-weight: 600;
				}

				/* Performance */
				.diagram-circle4>.c100 {
				    font-size: 90px;
				}

				.content-score {
				    width: 70%;
				    margin-left: 20px;
				    margin-top: 5px;
				}

				.content-score>.box-score {
				    padding: 15px 10px;
				    font-size: 50px;
				    text-align: center;
				    border-radius: 15px !important;
				}

				.history-label>label,
				.diagram-circle4>label,
				.content-score>label {
				    text-align: center;
				    font-size: 18px;
				    font-weight: 600;
				    line-height: 1;
				    margin-top: 12px;
				    width: 100%;
				}

				.history-label>label>span,
				.diagram-circle4>label>span,
				.content-score>label>span {
				    display: block;
				    font-size: 14px;
				    font-weight: 400;
				}

				.diagram-circle4>label>span {
				    font-weight: 700;
				}

				/* History Report */
				.history-report {
				    padding: 12px 20px 15px 20px;
				    text-align: center;
				}

				.history-title {
				    padding: 10px;
				}

				.history-label,
				.history-diagram {
				    width: 50%;
				    float: left;
				}

				.history-diagram {
				    height: 100%;
				    display: flex;
				}

				.history-diagram>.box-history {
				    margin: 30px auto 0;
				    height: inherit;
				}

				.history-diagram .c100 {
				    font-size: 150px;
				}

				.dashboard-stat {
				    border-radius: 15px !important;
				}

				.dashboard-stat .visual>svg {
				    width: 80%;
				    margin-top: -90%;
				}
			</style>
		<?php
	}

	public function _script(){
		?>
			<script type="text/javascript">
				
			</script>
		<?php
	}

// --------------------------------------------------
// Function -----------------------------------------
// --------------------------------------------------	
	
	public function _circle_number($text='',$value=0,$span=''){
		?>
			<div class="c100 <?php echo 'p'.$value ;?> blue-ocean">
				<span class="display-table">
					<div class="table-cell">
						<?php print($text) ;?>
						<span><?php echo $span;?></span>
					</div>
				</span>
				<div class="slice">
					<div class="bar"></div>
					<div class="fill"></div>
				</div>
			</div>
		<?php
	}

	public function _score_entryHours($idx=0){
		$score = 0;
		$user = sobad_user::get_id($idx,array('shift','time_in','_inserted'),"AND time_in!='00:00:00'");

		foreach ($user as $key => $val) {
			$date = strtotime($val['_inserted']);
			$day = date('w',$date);

			$work = sobad_work::get_id($val['shift'],array('time_in'),"AND days='$day'");
			$time = _conv_time($val['time_in'],$work[0]['time_in'],2);
			$time += 5;

			$score += ($time * 4);
		}

		if($score>100){
			$score = 100;
		}else if($score<0){
			$score = 0;
		}

		$score = round($score / count($user),0);

		if($score>80){
			$abjad = 'A';
		}else if($score<=80 && $score>60){
			$abjad = 'B';
		}else if($score<=60 && $score>40){
			$abjad = 'C';
		}else if($score<=40 && $score>20){
			$abjad = 'D';
		}else{
			$abjad = 'E';
		}

		return array(
			'nominal'	=> $score,
			'abjad'		=> $abjad
		);
	}

// --------------------------------------------------
// --------------------------------------------------
// --------------------------------------------------

	public function _layout(){
		?>
			<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">

			<div id="report-employee">
				
			</div>
		<?php
	}

	public function _reportEmployee($idx=0){
		$args = self::_array();
		$user = sobad_user::get_id($idx,$args,"",'employee');
		$user = $user[0];

		ob_start();
		?>
			<div class="row">
				<div class="col-md-6">
					<?php self::_realtime($user); ?>
				</div>
				<div class="col-md-6">
					<?php self::_performance($user['ID']); ?>
				</div>
				<div class="col-md-6">
					<?php self::_dailyActivity($user['ID']); ?>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<?php self::_monthlyReport($user['ID']);?>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					
				</div>
			</div>
		<?php

		dash_absensi::dash_script();
		return ob_get_clean();
	}

	// Report Realtime ------------------------------------------
	public function _realtime($args=array()){
		self::_profile($args);
		self::_address($args);
		self::_company($args);
	}

	public function _profile($args=array()){
		$date = date('Y-m-d');
		$now = strtotime($date);
		$image = empty($args['notes_pict'])?'no-profile.jpg':$args['notes_pict'];

		$umur = date($args['_birth_date']);
		$umur = strtotime($umur);
		$umur = $now - $umur;
		$umur = floor($umur / (60 * 60 * 24 * 365))." Tahun";

		$nameA = '';$nameB = array();
		$names = explode(' ', $args['name']);
		foreach ($names as $key => $val) {
			if($key>0){
				$nameB[] = $val;
			}else{
				$nameA = $val;
			}
		}

		$nameB = implode(' ', $nameB);
		$name = $nameA.'<span>'.$nameB.'</span>';
		?>
			<div id="absen-profile">
				<div class="row">
					<div class="col-md-4">
						<div class="box-profile">
							<img style="width:100%;" src="asset/img/user/<?php print($image) ;?>">
						</div>
					</div>
					<div class="col-md-7">
						<div class="text-content">
							<div class="no-induk">
								<span class="blue-report">
									<?php print($args['no_induk']) ;?>
								</span>
							</div>
							<div class="name-employee">
								<?php print($name) ;?>
							</div>
							<div class="divisi-employee">
								<?php print($args['meta_value_divi']) ;?>
							</div>
							<span class="age-employee">
								<?php print($umur) ;?>
							</span>
						</div>
					</div>
				</div>
			</div>
		<?php
	}

	public function _address($args=array()){
		$data = array(
			'subdistrict'	=> $args['_subdistrict'],
			'city'			=> $args['_city'],
			'province'		=> $args['_province'],
			'postcode'		=> $args['_postcode']
		);

		$address = sobad_wilayah::_conv_address($args['_address'],$data);
		?>
			<div class="bag-report">
				<div class="address-title">
					<h2 class="title-report"> Alamat (Saat ini) </h2>
				</div>
				<div class="address-content">
					<?php 
						echo $address['address'].', '.$address['subdistrict'].', '.$address['city'].'<br>'.$address['province'].' - '.$address['postcode'];
					?>
				</div>
				<div class="phone-content">
					<?php echo $args['phone_no'] ;?>
				</div>
			</div>
		<?php
	}

	public function _company($args=array()){
		$date = date('Y-m-d');
		$now = strtotime($date);

		// Masa Kontrak
		$life = employee_absen::_check_lifetime($args['status'],$args['_entry_date']);
		$masa = empty($life['masa'])?'':$life['masa'].' Hari';
		$kontrak = $life['end_date'];

		// Masa Bakti (Tetap)
		$bakti = date($args['_entry_date']);
		$bakti = strtotime($bakti);
		$bakti = $now - $bakti;
		$bTahun = floor($bakti / (60 * 60 * 24 * 365));
				
		$bBulan = floor($bakti / (60 * 60 * 24 * 30.416667));
		$bBulan -= ($bTahun * 12);
		$masa_bakti = $bTahun . ' Years ' . $bBulan .' Months';

		$table = array(
			'Status'			=> employee_absen::_conv_status($args['status']),
			'End Status'		=> employee_absen::_conv_status($args['end_status']),
			'Position'			=> $args['meta_value_divi'],
			'Date of Entry'		=> format_date_id($args['_entry_date']),
			'Date of Resign'	=> format_date_id($args['_resign_date']),
			'Work Period'		=> $masa_bakti,
			'Contract Period'	=> $kontrak,
			'Remaining Day Off'	=> $args['dayOff'].' Days'
		);

		if($args['status']==0){
			unset($table['Work Period']);
			unset($table['Contract Period']);
			unset($table['Remaining Day Off']);
		}else if($args['status']<4){
			unset($table['End Status']);
			unset($table['Work Period']);
			unset($table['Date of Resign']);
			unset($table['Remaining Day Off']);
		}else{
			unset($table['End Status']);
			unset($table['Contract Period']);
			unset($table['Date of Resign']);
		}

		?>
			<div class="bag-report">
				<div>
					<table style="width:100%;">
						<tbody>
							<?php
								foreach ($table as $key => $val) {
									echo '
										<tr>
											<td style="width:35%;font-weight:600;">'.$key.'</td>
											<td style="width:5%;">:</td>
											<td class="title-report" style="width:auto;">'.$val.'</td>
										</tr>
									';
								}
							?>
						</tbody>
					</table>
				</div>
			</div>
		<?php
	}

	// Report Performance --------------------------------------
	public function _performance($idx=0){
		$date = date('Y-m');

		// Entry Hours
		$score = self::_score_entryHours($idx);

		// log Detail
		$punish = 0;$switch = 0;
		$logs = sobad_logDetail::get_all(array('log_id','status','times','log_history','type_log'),"AND _log_id.user='$idx' AND `abs-log-detail`.status!='1' AND `abs-log-detail`.type_log IN ('1','2')");
		foreach ($logs as $key => $val) {
			// Punishment
			if($val['type_log']==1){
				if($val['status']==2){
					$val['times'] -= 30;
				}

				$punish += $val['times'];
			}

			// Ganti Jam
			if($val['type_log']==2){
				if($val['status']==2){
					$hist = unserialize($val['log_history']);
					$val['times'] = isset($hist['extime'])?$hist['extime']:0;
				}

				$switch += $val['times'];
			}
		}

		$punish = $punish==0?'-':(round($punish/60,1)).'H';
		$switch = $switch==0?'-':(round($switch/60,1)).'H';
		?>
			<div class="absen-content">
				<div class="absen-title">
					<h2 class="title-report">Latest Performance</h2>
				</div>
				<div class="bag-report">
					<div class="row">
						<div class="col-md-4">
							<div class="content-score">
								<div class="box-score blue-report">
									<span>85</span>
								</div>
								<label>Overall
									<span>Performance</span>
								</label>
							</div>
						</div>
						<div class="col-md-8">
							<div class="diagram-circle4">
								<?php self::_circle_number($score['abjad'],$score['nominal'],$score['nominal'].'%') ;?>
								<label><span>Entry Hours</span></label>
							</div>
							<div class="diagram-circle4">
								<?php self::_circle_number($punish,0) ;?>
								<label><span>Punishment</span></label>
							</div>
							<div class="diagram-circle4">
								<?php self::_circle_number($switch,0) ;?>
								<label><span>Switch Hours</span></label>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php
	}

	// Report Daily Activity ----------------------------------
	public function _dailyActivity($idx=0){

	}

	// Report Monthly -----------------------------------------
	public function _monthlyReport($idx=0){
		?>
			<div id="monthly-report" class="bag-report">
				<div class="row">
					<div class="col-md-12">
						
					</div>
				</div>
				<div class="row">
					<?php self::_graphMonthly($idx) ;?>
					<div class="col-md-6">
						<div id="history-monthly" class="history-report">
							<?php self::_history_monthly(date('Y-m'),$idx) ;?>
						</div>
					</div>
				</div>
				<div class="row">
					<?php self::_dahsMonthly(date('Y-m'),$idx) ;?>
				</div>
			</div>
		<?php
	}

	// Graphic monthly Report ---------------------------------
	public function _graphMonthly($idx=0){
		$chart[] = array(
			'func'	=> '_site_load',
			'data'	=> array(
				'id'		=> 'absen-monthly',
				'func'		=> 'dash_absenMonthly',
				'status'	=> '',
				'col'		=> 12,
				'label'		=> '<h2 class="title-report">Monthly Report</h2>',
				'type'		=> $idx
			),
		);

		$chart[] = array(
			'func'	=> '_site_load',
			'data'	=> array(
				'id'		=> 'overtime-monthly',
				'func'		=> 'dash_overtimeMonthly',
				'status'	=> '',
				'col'		=> 6,
				'label'		=> '<h2 class="title-report">Overtime Report</h2>',
				'type'		=> $idx
			),
		);
		
		return metronic_layout::sobad_chart($chart);
	}

	public function dash_absenMonthly($date=''){
		$idx = isset($_POST['type'])?$_POST['type']:0;

		$date = empty($date)?date('Y-m'):$date;
		$date = report_absen::get_range($date);

		$default = $date['finish_year'].'-'.$date['finish_month'].'-01';
		$default = strtotime($default);

		$sDay = $date['number_day'];
		$fDay = $date['finish_day'];

		$no = -1;
		$label = array();$data = array();
		for($i=$sDay;$i<$fDay;$i++){
			$no += 1;
			$label[] = date('M-d',strtotime($i.' days',$default));
			$data[0]['data'][$no] = 0;
			$data[1]['data'][$no] = 0;
			
			$now = date('Y-m-d',strtotime($i.' days',$default));
			$user = sobad_user::get_id($idx,array('shift','time_in','time_out'),"AND _inserted='$now'");
			
			$check = array_filter($user);
			if(!empty($check)){
				$time_in = _conv_time('00:00:00',$user[0]['time_in'],2);
				$time_out = _conv_time('00:00:00',$user[0]['time_out'],2);

				$time_in = round($time_in/60,2);
				$time_out = round($time_out/60,2);

				$data[0]['data'][$no] = $time_in;
				$data[1]['data'][$no] = $time_out;
			}
		}

		$data[0]['label'] = 'Entry Hours';
		$data[1]['label'] = 'Left Hours';

		$data[0]['type'] = 'line';
		$data[1]['type'] = 'line';

		$data[0]['bgColor'] = 'rgba(21,73,154,1)';
		$data[0]['brdColor'] = 'rgba(21,73,154,1)';

		$data[1]['bgColor'] = 'rgba(255,174,0,1)';
		$data[1]['brdColor'] = 'rgba(255,174,0,1)';

		$args = array(
			'type'		=> 'bar',
			'label'		=> $label,
			'data'		=> $data,
			'option'	=> ''
		);
		
		return $args;
	}

	public function dash_overtimeMonthly($date=''){
		$idx = isset($_POST['type'])?$_POST['type']:0;

		$date = empty($date)?date('Y-m'):$date;
		$date = report_absen::get_range($date);

		$default = $date['finish_year'].'-'.$date['finish_month'].'-01';
		$default = strtotime($default);

		$sDay = $date['number_day'];
		$fDay = $date['finish_day'];
		$week = ceil(($fDay - $sDay) / 7);

		$label = array();$data = array();
		for($i=0;$i<$week;$i++){

			$start = $sDay + ($i * 7);
			$finish = $start + 6;
			$finish = $finish>$fDay?$fDay:$finish;

			$start = date('Y-m-d',strtotime($start.' days',$default));
			$finish = date('Y-m-d',strtotime($finish.' days',$default));

			$whr = "AND _log_id.user='$idx' AND type_log='3' AND date_schedule BETWEEN '$start' AND '$finish'";
			$logs = sobad_logDetail::get_all(array('log_id','times'),$whr);
			
			$over = 0;$total = 0;
			foreach ($logs as $key => $val) {
				$over += $val['times'];
				if($val['times']<2){
					$total += ($val['times'] + 0.5);
				}else{
					$total += (($val['times'] * 2) - 1);
				}
			}
			
			$label[] = 'week '.($i + 1);
			$data[0]['data'][$i] = $over;
			$data[1]['data'][$i] = $total;
		}

		$data[0]['label'] = 'Overtime';
		$data[1]['label'] = 'Total';

		$data[0]['type'] = 'bar';
		$data[1]['type'] = 'bar';

		$data[0]['bgColor'] = 'rgba(21,73,154,1)';
		$data[0]['brdColor'] = 'rgba(21,73,154,1)';

		$data[1]['bgColor'] = 'rgba(255,174,0,1)';
		$data[1]['brdColor'] = 'rgba(255,174,0,1)';

		$args = array(
			'type'		=> 'bar',
			'label'		=> $label,
			'data'		=> $data,
			'option'	=> ''
		);
		
		return $args;
	}

	public function _history_monthly($date='',$idx=0){
		$idx = isset($_POST['type'])?$_POST['type']:$idx;

		$date = empty($date)?date('Y-m'):$date;
		$date = report_absen::get_range($date);

		$start = $date['start_date'];
		$finish = $date['finish_date'];

		$whr = "AND _log_id.user='$idx' AND type_log IN ('1','2') AND date_schedule BETWEEN '$start' AND '$finish'";
		$logs = sobad_logDetail::get_all(array('log_id','times','type_log'),$whr);
		
		$switch = 0;$punish = 0;
		foreach ($logs as $key => $val) {
			if($val['type_log']==1){
				$punish += $val['times'];
			}

			if($val['type_log']==2){
				$switch += $val['times'];
			}
		}

		$punish = $punish==0?'-':round($punish/60,1).'H';
		$switch = $switch==0?'-':round($switch/60,1).'H';

		?>
			<div class="history-title">
				<h2 class="title-report">History Report</h2>
			</div>
			<div class="history-row">
				<div class="history-diagram">
					<div class="box-history">
						<?php self::_circle_number($switch,100) ;?>
					</div>
				</div>
				<div class="history-diagram">
					<div class="box-history">
						<?php self::_circle_number($punish,100) ;?>
					</div>
				</div>
			</div>
			<div class="history-row">
				<div class="history-label">
					<label>Switch Hours</label>
				</div>
				<div class="history-label">
					<label>Punishment</label>
				</div>
			</div>
		<?php
	}

	// Dashboard monthly Report ------------------------------
	public function _dahsMonthly($date='',$idx=0){
		$idx = isset($_POST['type'])?$_POST['type']:$idx;

		$date = empty($date)?date('Y-m'):$date;
		$date = report_absen::get_range($date);

		$start = $date['start_date'];
		$finish = $date['finish_date'];

		$whr = "AND user='$idx' AND type NOT IN ('6','9') AND ((start_date BETWEEN '$start' AND '$finish') OR (range_date BETWEEN '$start' AND '$finish') OR range_date='0000-00-00')";
		$logs = sobad_permit::get_all(array('start_date','range_date','num_day','type_date','type'),$whr);

		// Check Permit
		$dayoff = 0;$izin = 0;$sick = 0;$outcity = 0;
		foreach ($logs as $key => $val) {
			$val['start_date'] = $val['start_date']<$start?$start:$val['start_date'];

			$val['range_date'] = $val['range_date']=='0000-00-00'?date('Y-m-d'):$val['range_date'];
			$val['range_date'] = $val['range_date']>$finish?$finish:$val['range_date'];

			$range = strtotime($val['range_date']) - strtotime($val['start_date']);
			$range = floor($range / (60 * 60 * 24));

			if($val['type']==3){
				$dayoff += $range;
			}else if($val['type']==4 || ($val['type']>10 && $val['type']!=48)){
				$izin += $range;
			}else if($val['type']==5){
				$outcity += $range;
			}else if($val['type']==48){
				$sick += $range;
			}
		}

		// Check Alpha (Tidak Absen)
		$logs = sobad_user::get_logs(array('ID'),"user='$idx' AND type='0' AND _inserted BETWEEN '".$start."' AND '".$finish."'");
		$alpha = count($logs);

		// Check Tidak Absen Pulang
		$logs = sobad_user::get_logs(array('ID'),"user='$idx' AND type='1' AND _inserted BETWEEN '".$start."' AND '".$finish."'");
		$cnt = count($logs);
		$cnt = floor($cnt/3);

		$alpha += $cnt;

		// Get Icon
		ob_start();
		self::_iconLeaving();
		$icoLeave = ob_get_clean();

		ob_start();
		self::_iconPermit();
		$icoPermit = ob_get_clean();

		ob_start();
		self::_iconSick();
		$icoSick = ob_get_clean();

		ob_start();
		self::_iconAlpha();
		$icoAlpha = ob_get_clean();

		$column = array('lg' => 20, 'md' => 20);
		$dash[] = array(
			'func'	=> '_block_info',
			'data'	=> array(
				'icon'		=> $icoLeave,
				'color'		=> 'blue-report',
				'qty'		=> $dayoff,
				'desc'		=> 'Leaving of Absence',
				'column'	=> $column,
				'button'	=> button_toggle_block(array('ID' => 'absen_3','func' => '_view_block'))
			)
		);
		
		$dash[] = array(
			'func'	=> '_block_info',
			'data'	=> array(
				'icon'		=> $icoPermit,
				'color'		=> 'green-report',
				'qty'		=> $izin,
				'desc'		=> 'Permission',
				'column'	=> $column,
				'button'	=> button_toggle_block(array('ID' => 'absen_4','func' => '_view_block'))
			)
		);
		
		$dash[] = array(
			'func'	=> '_block_info',
			'data'	=> array(
				'icon'		=> $icoSick,
				'color'		=> 'yellow-report',
				'qty'		=> $sick,
				'desc'		=> 'Sick',
				'column'	=> $column,
				'button'	=> button_toggle_block(array('ID' => 'absen_48','func' => '_view_block'))
			)
		);
		
		$dash[] = array(
			'func'	=> '_block_info',
			'data'	=> array(
				'icon'		=> '',
				'color'		=> 'purple-plum',
				'qty'		=> $outcity,
				'desc'		=> 'Luar Kota',
				'column'	=> $column,
				'button'	=> button_toggle_block(array('ID' => 'absen_5','func' => '_view_block'))
			)
		);
		
		$dash[] = array(
			'func'	=> '_block_info',
			'data'	=> array(
				'icon'		=> $icoAlpha,
				'color'		=> 'red-report',
				'qty'		=> $alpha,
				'desc'		=> 'Off',
				'column'	=> $column,
				'button'	=> button_toggle_block(array('ID' => 'absen_0','func' => '_view_block'))
			)
		);

		return metronic_layout::sobad_dashboard($dash);
	}

	public static function _iconLeaving(){
		?>
			<!-- Creator: CorelDRAW 2020 (64-Bit) -->
			<svg xmlns="http://www.w3.org/2000/svg" xml:space="preserve" width="40.4952mm" height="49.349mm" version="1.1" style="shape-rendering:geometricPrecision; text-rendering:geometricPrecision; image-rendering:optimizeQuality; fill-rule:evenodd; clip-rule:evenodd"
			viewBox="0 0 13957.22 17008.8"
			 xmlns:xlink="http://www.w3.org/1999/xlink"
			 xmlns:xodm="http://www.corel.com/coreldraw/odm/2003">
			 <defs>
			  <style type="text/css">
			   <![CDATA[
			    .fil0 {fill:#1D1819}
			   ]]>
			  </style>
			 </defs>
			 <g id="Layer_x0020_1">
			  <metadata id="CorelCorpID_0Corel-Layer"/>
			  <path class="fil0" d="M13173.73 16209.49c943.34,-137.73 806.24,-1217.39 726.76,-2188.96 -76.17,-930.35 -1325.23,-686.5 -2232.73,-680.57 -5.41,-1153.18 -242.47,-941.28 -607.95,-1115.23 207.8,-1787.22 941.24,-2023.76 1207.36,-2827.58 266.49,-805 116.91,-1622.5 -380.41,-2280.95 -314.26,-416.11 -430.31,-317.06 -474.95,-615.12 0,-881.51 91.61,-5544.05 -67.9,-6000.01 -247.78,-708.25 -1774.33,-458.26 -2602.14,-458.26l-6081.35 0c-841.94,0 -2329.27,-249.71 -2584.56,478.39 -133.56,380.92 -48.15,13102.52 -57.15,15103.88 -6.76,1502.87 208.31,1380.41 1657.93,1377.45l10648.21 6.27c715.73,-9 745.96,-155.51 848.87,-799.31zm-5900.02 129.11c131.39,258.74 -151.27,23.02 148,144.38 109.5,44.39 203.35,42.63 326.57,45.74 843.6,21.37 4657.58,72.1 4896.63,-100.68 -53.25,-348.66 41.01,-223.03 -1130.67,-221.96l-3936.61 -5.48c-564.59,72.28 -24.02,9.03 -303.92,138zm2160.52 -3666.74l-481.46 137.52c-21.82,5.69 -54.15,14.51 -71.52,19.03 -17.06,4.41 -48.43,8.41 -70.35,18.82l-75.65 966.85c-2173.28,-42.39 -2256.62,-269.25 -2258.34,913.94 -2,1356.97 9.03,996.42 3427.51,996.42 697.01,0 2946.84,114.6 3414.72,-73.24l66.11 -108.91c99.85,-281.49 117.81,-1518.79 -48.77,-1655.52 -572.69,-222.82 -1468.85,-33.6 -2135.88,-96.4l-47.46 -994.35 -664.68 -21.4c214.83,-2483.92 928.01,-2576.43 1268.6,-3432.61 558.63,-1404.09 -467.98,-2661.56 -1762.95,-2686.27 -1332.71,-25.37 -2361.25,1227 -1878.31,2626.09 346.39,1003.49 998.56,634.73 1318.44,3390.04zm-8878.39 -12111.85c-160.2,1242.51 -56.73,13158.63 -56.21,15420.86 -0.21,734.37 497.69,540.67 1176.82,537.3 564.66,-2.86 4779.52,103.47 5120.63,-90.13 -214.04,-786.14 -797.69,507.83 -797.69,-2056.95 0,-1375.07 1074.25,-1037.92 2271.85,-1031.13 -4.41,-1108.2 229.82,-897.47 606.19,-1115.23 -234.34,-2214.77 -1650.25,-2227.35 -1336.29,-4046.35 139.18,-806.17 613.09,-1335.5 1133.63,-1658.8 721.97,-448.44 1402.68,-370.44 2255.44,-188.05l-0.59 -4749.7c6.79,-705.32 180.88,-1047.36 -578.07,-1047.36 -2589.28,0 -7469.37,-94.54 -9795.71,25.54z"/>
			  <path class="fil0" d="M5427 15703.25l-4.72 -557.18c-2430.15,973.78 -3055.48,-1082.28 -2685,-2043.51 235.09,-609.95 701.53,-1091.14 1537.72,-1133.15 1069.46,-53.73 1247.27,516.82 1738.21,1012.93l383.99 -194.22c-93.58,-233.92 -187.88,-394.12 -293.52,-518.41 -1114.92,-1311.31 -3154.95,-963.61 -3803.43,584.38 -565.97,1351.01 423.97,3767.59 3126.75,2849.16z"/>
			  <path class="fil0" d="M2164.8 4492.58c1075.18,92.16 4164.95,19.96 5593.23,20.13 564.52,0.03 1566.36,219.9 1482.54,-374.72 -18.44,-11.62 -48.67,-47.87 -58.45,-29.19l-270.91 -70.83c-97.06,-9.58 -324.81,-0.14 -433.9,1.1l-5910.53 -7.93c-472.77,16.85 -440.34,6.69 -401.98,461.44z"/>
			  <path class="fil0" d="M2660.42 2393.89l6081.35 0c421.18,0 486.77,20.82 527.3,-379.68 -232.82,-155.86 -168.13,-107.98 -527.3,-107.98 -2027.1,0 -4054.21,0 -6081.35,0 -785.94,0 -655.65,487.66 0,487.66z"/>
			  <path class="fil0" d="M8120.2 6156.28l-4833.91 4.45c-284.52,-0.86 -1502.18,-130.21 -1096,355.45 103.61,123.87 -90.3,59.32 274.01,124.7 49.73,8.89 388.71,6.41 461.71,5.34l4029.15 -0.97c710.56,8.75 859.38,-128.63 1165.03,-488.97z"/>
			  <path class="fil0" d="M2660.42 10898.4l4851.69 0 376.75 8.03 -316.33 -474.46 -351.04 -28.26 -4695.21 1.86c-568.11,0 -546.22,492.83 134.14,492.83z"/>
			  <path class="fil0" d="M2153.8 8629.24c383.47,330.77 1683.92,140.35 2295.25,140.38 767.67,0.03 1747.55,52.7 2488.85,-23.44l14.68 -470.74 -3846.13 1.9c-481.36,-5.45 -937.45,-139.9 -952.65,351.9z"/>
			  <path class="fil0" d="M4091.33 13859.61c-406.19,-243.13 -216.93,-430.9 -709.42,-291.83 -28.64,432.83 391.3,746.47 709.42,919.39 310.89,-170.47 537.12,-444.31 783.76,-673.2 264.15,-245.12 630.94,-422.42 453.37,-855.35 -431.55,-14.79 -837.84,666.13 -1237.14,900.98z"/>
			 </g>
			</svg>
		<?php
	}

	public static function _iconPermit(){
		?>
			<!-- Creator: CorelDRAW 2020 (64-Bit) -->
			<svg xmlns="http://www.w3.org/2000/svg" xml:space="preserve" width="51.8039mm" height="51.3808mm" version="1.1" style="shape-rendering:geometricPrecision; text-rendering:geometricPrecision; image-rendering:optimizeQuality; fill-rule:evenodd; clip-rule:evenodd"
			viewBox="0 0 2889.75 2866.15"
			 xmlns:xlink="http://www.w3.org/1999/xlink"
			 xmlns:xodm="http://www.corel.com/coreldraw/odm/2003">
			 <defs>
			  <style type="text/css">
			   <![CDATA[
			    .str0 {stroke:#1D1819;stroke-width:78.71;stroke-miterlimit:22.9256}
			    .str1 {stroke:#1D1819;stroke-width:98.39;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:22.9256}
			    .fil0 {fill:none}
			   ]]>
			  </style>
			 </defs>
			 <g id="Layer_x0020_1">
			  <metadata id="CorelCorpID_0Corel-Layer"/>
			  <polygon class="fil0 str0" points="366.62,39.36 366.62,2474.1 2314.42,2474.1 2314.42,644.87 1708.91,39.36 "/>
			  <rect class="fil0 str0" x="595.28" y="856.59" width="618.21" height="571.63"/>
			  <line class="fil0 str0" x1="1412.5" y1="1017.49" x2="2115.4" y2= "1017.49" />
			  <line class="fil0 str0" x1="1412.5" y1="1313.89" x2="2115.4" y2= "1313.89" />
			  <line class="fil0 str0" x1="523.28" y1="1618.76" x2="2115.4" y2= "1618.76" />
			  <line class="fil0 str0" x1="523.28" y1="1923.63" x2="2115.4" y2= "1923.63" />
			  <line class="fil0 str0" x1="523.28" y1="2228.5" x2="2115.4" y2= "2228.5" />
			  <line class="fil0 str0" x1="1691.96" y1="634.27" x2="1691.96" y2= "39.36" />
			  <line class="fil0 str0" x1="1691.96" y1="634.27" x2="2286.87" y2= "634.27" />
			  <polygon class="fil0 str0" points="2636.23,2697.46 2468,2167.94 2833.12,2167.94 "/>
			  <path class="fil0 str0" d="M2468 2167.94l363 0 0 -1309.82c0,-99.83 -81.67,-181.5 -181.5,-181.5l-0.01 0c-99.83,0 -181.5,81.67 -181.5,181.5l0 1309.82z"/>
			  <line class="fil0 str0" x1="2468" y1="1074.65" x2="2831.01" y2= "1074.65" />
			  <polyline class="fil0 str0" points="366.62,348.47 39.36,348.47 39.36,2826.78 2060.36,2826.78 2060.36,2474.1 "/>
			  <rect class="fil0 str0" x="595.28" y="261.66" width="821.47" height="364.15"/>
			  <polyline class="fil0 str1" points="715.74,1135.77 819.48,1273.38 1090.48,1006.62 "/>
			 </g>
			</svg>
		<?php
	}

	public static function _iconSick(){
		?>
			<!-- Creator: CorelDRAW 2020 (64-Bit) -->
			<svg xmlns="http://www.w3.org/2000/svg" xml:space="preserve" width="39.1016mm" height="52.6079mm" version="1.1" style="shape-rendering:geometricPrecision; text-rendering:geometricPrecision; image-rendering:optimizeQuality; fill-rule:evenodd; clip-rule:evenodd"
			viewBox="0 0 1604.42 2158.61"
			 xmlns:xlink="http://www.w3.org/1999/xlink"
			 xmlns:xodm="http://www.corel.com/coreldraw/odm/2003">
			 <defs>
			  <style type="text/css">
			   <![CDATA[
			    .fil0 {fill:black}
			   ]]>
			  </style>
			 </defs>
			 <g id="Layer_x0020_1">
			  <metadata id="CorelCorpID_0Corel-Layer"/>
			  <g id="_2218150820608">
			   <path class="fil0" d="M248.21 2094.59l65.45 37.79c44.11,5.55 70.7,23.81 127.94,25.98 42.98,1.63 90.46,-5.19 122.71,-17.86 78.31,-30.77 153.23,-74.96 201.66,-181.5 21.12,-43.63 32.38,-87.04 34.62,-127.6 9.12,-165.22 -66.53,-8.14 132.12,-352.61l577.6 -1003.38c50.58,-88.37 98.78,-145.69 93.73,-236.45 -6.64,-119.22 -59.86,-139.91 -92.7,-189.08l-61 -35.22c-57.35,-3.03 -81.95,-24.18 -139.62,-9.61 -44.51,11.24 -83.89,23.38 -121.38,60.17 -58.57,57.47 -683.03,1163.63 -808.1,1379.81 -24.94,43.11 -16.95,30.82 -55.27,43.45 -34.64,11.42 -84.68,45.01 -117.54,76.49 -116.32,111.43 -127.09,293.09 -60.32,414.91 41.11,75 77.37,85.66 100.09,114.7zm1167.76 -1984.91c-128.68,-47.75 -175.75,43.09 -218.2,110.06 -151.46,238.93 -363.2,623.22 -509.06,875.87l-253.2 439.3c-18.43,27.67 -33.62,30.6 -55.5,37.75 -25.06,8.18 -32.1,10.57 -56.8,24.66 -181.73,103.62 -147.61,362.33 24.65,442.82 160.33,74.91 381.75,-52.93 353.13,-255.73 -8.01,-56.75 -22.03,-56.85 19.49,-127.23 29.42,-49.86 57.84,-100.41 86.58,-150.66l677.5 -1180.16c42.13,-73.57 20.62,-183.56 -68.59,-216.67z"/>
			   <path class="fil0" d="M485.88 1655.29c-27.49,21.1 -118.35,-9.45 -165.91,93.33 -77.08,166.56 150.14,279.15 241.77,159.6 27.52,-35.91 40.87,-68.42 38.11,-107.28 -3.4,-47.95 -11.42,-47.06 -33.53,-90.2 72.71,-123.24 421.7,-701.47 438.88,-785.55 -80.45,-73.94 -104,14.72 -149.04,91.63 -41.44,70.77 -82.84,141.56 -124.04,212.47 -82.38,141.79 -164.25,283.97 -246.25,425.99zm-27.26 106.17c-68.04,-6.93 -76.18,96.99 -6.28,101.32 55.02,3.41 76.59,-94.16 6.28,-101.32z"/>
			   <path class="fil0" d="M602.82 94.37c36.67,31.94 147.81,89.09 199.44,118.9 27.16,15.68 74.6,51.47 102.79,54.9 27.72,3.37 60.74,-26.24 47.76,-61.04 -14.07,-37.73 -147.31,-99.08 -191.91,-124.83 -72.27,-41.73 -155.73,-111.61 -158.08,12.06z"/>
			   <path class="fil0" d="M-0 1135.27c28.28,29.55 279.36,175.21 320.3,173.76 1.3,-0.29 90.66,-39.76 -16.72,-101.76 -79.38,-45.83 -158.75,-91.66 -238.13,-137.48 -45.34,-26.18 -64.35,29.16 -65.45,65.48z"/>
			   <path class="fil0" d="M650.71 713.35c-34.95,-20.18 -282.36,-173.41 -312.64,-167.57 -33.95,6.56 -52.98,52.72 -20.13,79.41 25.59,20.79 270.36,171.93 302.57,163.35 46.04,-12.25 29.32,-43.38 30.2,-75.2z"/>
			   <path class="fil0" d="M343.33 1109.46c85.84,49.56 117.39,29.9 106.86,-51.39 -38.51,-21.14 -112.01,-80.7 -150.21,-60.37 -70.16,37.34 0.72,87.15 43.35,111.76z"/>
			   <path class="fil0" d="M670.47 352.62c54.84,76.05 200.01,130.38 185.4,34.45 -4.74,-31.11 -63.76,-62.59 -99.56,-79.25 -50.42,-23.45 -68.91,-7.96 -85.84,44.8z"/>
			   <path class="fil0" d="M369.32 873.42c40.68,60.23 193.95,140.08 186.23,39.2 -2.53,-33.08 -58.07,-63.82 -94,-80.39 -57.05,-26.3 -71.44,-14.79 -92.22,41.19z"/>
			   <path class="fil0" d="M627.94 472.85c-42.96,-6.44 -65.75,47.63 -41.84,76.37 15.12,18.18 97.57,64.92 120.47,66.49 38.69,2.64 67.9,-45.99 38.5,-77.27 -17.27,-18.39 -95.05,-62.28 -117.13,-65.59z"/>
			  </g>
			 </g>
			</svg>
		<?php
	}

	public static function _iconAlpha(){
		?>
			<!-- Creator: CorelDRAW 2020 (64-Bit) -->
			<svg xmlns="http://www.w3.org/2000/svg" xml:space="preserve" width="48.3905mm" height="52.846mm" version="1.1" style="shape-rendering:geometricPrecision; text-rendering:geometricPrecision; image-rendering:optimizeQuality; fill-rule:evenodd; clip-rule:evenodd"
			viewBox="0 0 1841.81 2011.4"
			 xmlns:xlink="http://www.w3.org/1999/xlink"
			 xmlns:xodm="http://www.corel.com/coreldraw/odm/2003">
			 <defs>
			  <style type="text/css">
			   <![CDATA[
			    .fil0 {fill:black}
			    .fil1 {fill:white}
			    .fil2 {fill:#1D1819;fill-rule:nonzero}
			   ]]>
			  </style>
			 </defs>
			 <g id="Layer_x0020_1">
			  <metadata id="CorelCorpID_0Corel-Layer"/>
			  <path class="fil0" d="M1008.49 637.99c-96.32,-18.57 -240.57,-140 -324,-151.7 -86.94,-12.19 -225.44,129.6 -286.6,184.47 -42.4,38.04 -92.12,63.05 -90.69,140.14 1.78,95.86 93.7,187.81 265.46,34.25 68.77,-61.49 108.9,-114.05 140.49,-74.93 20.02,24.78 -298.2,562.98 -330.65,583.87 -45.56,29.35 -220.03,15.7 -287.56,33.34 -83.18,21.72 -126.58,111.65 -67.94,190.43 51.12,68.66 155.69,45.29 256.6,37.24 105.77,-8.44 200.53,5.61 256.95,-63.55 47.31,-58 80.56,-152.95 132.32,-188.41 48.2,12.93 96.23,61.39 134.37,92.49 48.62,39.64 83.99,49.34 90.72,136.07 10.16,130.95 -19.91,350.54 162.77,310.35 137.4,-30.23 81.12,-238.8 73.96,-360.78 -11.23,-191.47 -84.01,-194.37 -183.52,-286.94l147.28 -279.78c137.77,46.71 135.3,101.84 268.32,-6.27l212.58 -177.15c73.57,-91.5 5.02,-213.34 -113.04,-197.08 -59.52,8.19 -177.92,128.18 -229.5,164.42l-228.32 -120.48zm-189.34 101.55c-46.1,124.56 -202.27,387.92 -276.18,506.02 -25.16,40.2 -48.38,76.99 -72.03,115.91 -62.85,103.47 -121.22,71.84 -303.92,88.34 -77.01,6.95 -98.64,18.31 -90.67,92.37 79.97,25.9 197.35,0.63 288.19,-5.22 142.65,-9.19 125.77,-38.15 186.68,-135.57 109.96,-175.87 153.23,-120.39 284.86,-14.97 153.79,123.17 118.26,82.25 139.11,347.09 11.8,149.86 114.84,141.55 101.38,-22.58 -5.95,-72.58 -2.25,-234.58 -38.73,-284.18 -20.45,-27.79 -146.9,-125.03 -180.25,-142.29 12.95,-59.45 183.16,-372.66 218.08,-405.8 244.09,102.93 96.73,142.89 374.84,-68.89 10.22,-7.78 51.03,-41.32 58.3,-49.22 32.44,-35.26 33.38,-19.46 19.35,-79.52 -51.53,-28.03 -64.27,-14.46 -105.83,19.93 -32.84,27.16 -57.63,49.27 -88.2,73.51 -100.1,79.36 -80.28,86.06 -208.53,13.84 -58.5,-32.94 -387.77,-213.11 -433.9,-225.73 -51.04,-13.95 -73.47,17.05 -104.48,42.39l-176.98 146.11c-32.67,27.26 -55.55,76.71 -1.37,98.31 50.89,20.29 155.83,-95.61 190.89,-124.5 91.76,-75.59 118.73,-41.14 219.38,14.69z"/>
			  <path class="fil0" d="M644.8 455.55l84.76 11.63 -0.08 -377.77 1026.09 0.61 0.69 1830.54 -1025.88 4.23 -0.99 -455.62c0,-61.52 -20.11,-66.17 -58.9,-80.93 -28.54,64.78 -27.83,42.75 -27.43,134.16l0.58 488.99 1198.17 -0.15 -0.13 -2011.25 -1198.04 0.45 1.16 455.1z"/>
			  <path class="fil1" d="M1120.1 366.31c-181.02,42.46 -100.59,272.79 52.53,233.36 147.29,-37.93 89.78,-266.75 -52.53,-233.36z"/>
			  <line class="fil0" x1="1008.49" y1="637.99" x2="1008.49" y2= "637.99" />
			  <path class="fil2" d="M1126.35 197.1c56.72,0 108.09,23 145.26,60.18 37.17,37.17 60.18,88.54 60.18,145.26 0,56.72 -23,108.09 -60.18,145.26 -37.17,37.17 -88.54,60.18 -145.26,60.18 -56.72,0 -108.09,-23 -145.26,-60.18 -37.17,-37.17 -60.18,-88.54 -60.18,-145.26 0,-56.72 23,-108.09 60.18,-145.26 37.17,-37.17 88.54,-60.18 145.26,-60.18zm88.3 117.14c-22.59,-22.59 -53.82,-36.57 -88.3,-36.57 -34.48,0 -65.71,13.98 -88.3,36.57 -22.59,22.59 -36.57,53.82 -36.57,88.3 0,34.48 13.98,65.71 36.57,88.3 22.59,22.59 53.82,36.57 88.3,36.57 34.48,0 65.71,-13.98 88.3,-36.57 22.59,-22.59 36.57,-53.82 36.57,-88.3 0,-34.48 -13.98,-65.71 -36.57,-88.3z"/>
			 </g>
			</svg>
		<?php
	}
}