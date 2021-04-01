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
			<div class="select-employee">
				<select class="form-control bs-select" data-live-search="true" data-size="6" data-sobad="sobad__reportEmployee" data-load="report-employee" data-attribute="html" onchange="option_report(this)"> 
		<?php
			$user = sobad_user::get_employees(array('ID','no_induk','name'));
			foreach ($user as $key => $val) {
				?>
					<option value="<?php print($val['ID']) ;?>"><?php echo $val['no_induk'].' :: '.$val['name'] ;?></option>
				<?php
			}
		?>
				</select>
			</div>
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

				.select-employee>.form-control.bs-select {
				    width: 200px;
				    margin-right: 20px;
				}

				.select-employee span.filter-option.pull-left,
				.select-employee span.caret {
    				color: #333;
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

				.dashboard-stat .details{
					z-index: 2;
				}

				.dashboard-stat .visual>svg {
				    width: 80%;
				    margin-top: -90%;
				    fill: #fff;
				}
			</style>
		<?php
	}

	public function _script(){
		?>
			<script type="text/javascript">
				ComponentsDropdowns.init();

				function option_report(val){
					var ajx = $(val).attr("data-sobad");
					if(ajx){
						var lbl = val.value;
						var id = $(val).attr('data-load');
						var att = $(val).attr('data-attribute');

						sobad_load(id);
					
						data = "ajax="+ajx+"&object="+object+"&data="+lbl;
						sobad_ajax('#'+id,data,att,false);
					}
				}

				function filter_report(val){
					var id = $(val).attr('data-load');
					var ajx = $(val).attr('data-sobad');
					var tp = $(val).attr('data-type');
					var index = $(val).attr('data-index');
					var dt = $(val).val();

					sobad_load(id);
						
					filter = dt;
					var data = "ajax="+ajx+"&object="+object+"&data="+dt+'&type='+tp+'&index='+index;
					sobad_ajax('#'+id,data,callback_filter,false,'','');
				}

				function callback_filter(data,id){
					var func = '';
					$(id+' .blockUI').remove();


					for(var k in data){
						for(var l in data[k]){
							func = l;
							if(typeof func == 'function'){
								func(data[k][l],k);
							}else if(typeof window[func] == 'function'){
								window[func](data[k][l],k);
							}else{
								if(typeof $('#'+k)[func] == 'function'){					
									func = ('inner' in data)?data['inner']:func;
									$('#'+k)[func](data[k][l]);
								}
							}
						}
					}
				}

				function opt_monthAbsen(){
					var option = {
						tooltips	: {
							enabled		: true,
							mode		: 'single',
							callbacks	: {
								label 		: function(value, data) {
									var jam = Math.floor(value.yLabel);
									var menit = Math.floor((value.yLabel - jam) * 60);

									jam = jam<10?'0'+jam:jam;
									menit = menit<10?'0'+menit:menit;
									return jam+':'+menit;
								}
							}
						}
					}

					return option;
				}
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

	public function _score_entryHours($idx=0,$limit=''){
		$user = sobad_user::get_id($idx,array('shift','time_in','_inserted'),"AND time_in!='00:00:00' ".$limit);	
		return self::_get_score($user,'time_in');
	}

	public function _score_leftHours($idx=0,$limit=''){
		$user = sobad_user::get_id($idx,array('shift','time_out','_inserted'),"AND time_out!='00:00:00' ".$limit);
		return self::_get_score($user,'time_out');
	}

	public function _get_score($user=array(),$_time=''){
		$score = 0;
		foreach ($user as $key => $val) {
			$date = strtotime($val['_inserted']);
			$day = date('w',$date);

			$work = sobad_work::get_id($val['shift'],array($_time),"AND days='$day'");
			$time = _conv_time($val[$_time],$work[0][$_time],2);
			$time += 5;

			$score += ($time * 4);
		}

		if($score!=0 || count($user)!=0){
			$score = round($score / count($user),0);
		}else{
			$score = 0;
		}

		if($score>100){
			$score = 100;
		}else if($score<0){
			$score = 0;
		}

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

	public function _filter($date=''){
		$date = empty($date)?date('Y-m'):$date;
		$idx = isset($_POST['index'])?$_POST['index']:0;
		$type = isset($_POST['type'])?$_POST['type']:'month';

		$_POST['type'] = $idx;

		ob_start();
		$func = '_history_'.$type.'ly';
		self::{$func}($date,$idx);
		$history = ob_get_clean();

		ob_start();
		$func = '_dahs'.ucwords($type).'ly';
		self::{$func}($date,$idx);
		$permit = ob_get_clean();

		$absen = 'dash_absen'.ucwords($type).'ly';
		$over = 'dash_overtime'.ucwords($type).'ly';
		
		$args = array(
			'absen-'.$type.'ly'		=> array(
				'load_chart_dash'	=> self::{$absen}($date)
			),
			'overtime-'.$type.'ly'	=> array(
				'load_chart_dash'	=> self::{$over}($date)
			),
			'history-'.$type.'ly'	=> array(
				'html'				=> $history
			),
			'permit-'.$type.'ly'	=> array(
				'html'				=> $permit
			)
		);
		return $args;
	}

	public function _permitMonth($data=''){
		$idx = $_POST['type'];
		$date = empty($_POST['filter'])?date('Y-m'):$_POST['filter'];
		$type = str_replace('month_', '', $data);

		$date = report_absen::get_range($date);

		$start = $date['start_date'];
		$finish = $date['finish_date'];

		return self::_permitForm($idx,$type,$start,$finish);
	}

	public function _permitYear($data=''){
		$idx = $_POST['type'];
		$year = empty($_POST['filter'])?date('Y'):$_POST['filter'];
		$type = str_replace('year_', '', $data);

		$dateA = report_absen::get_range($year.'-01');
		$dateB = report_absen::get_range($year.'-12');

		$start = $dateA['start_date'];
		$finish = $dateB['finish_date'];

		return self::_permitForm($idx,$type,$start,$finish);
	}

	private static function _permitForm($idx=0,$type=0,$sdate='',$fdate='',$limit=''){
		//Alpha Form
		if($type==0){
			return self::_alphaForm($idx,$sdate,$fdate);
		}

		$where = "AND (start_date>='$sdate' AND range_date<='$fdate') AND user='$idx' ";
		if($type==4){
			$where .= "AND type='4' OR (type>'10' AND type!='48')";
		}else{
			$where .= "AND type='$type'";
		}

		$where .= $limit;
		$history = sobad_permit::get_all(array('user','start_date','range_date','num_day','type_date','type','note'),$where);

		$data['class'] = '';
		$data['table'] = array();

		$no = 0;
		foreach ($history as $key => $val) {
			$no += 1;

			$conv = permit_absen::_conv_dateRange($val);
			$val = $conv['data'];
			$sts_day = $conv['status'];
			$range = $conv['range'];

			$range = ($range + 1).' '.$sts_day;

			if($val['type']==4){
				$_user = $val['user'];
				$_now = $val['start_date'];
				$logs = sobad_logDetail::get_all(array('log_id','times'),"AND _log_id.user='$_user' AND _log_id._inserted='$_now'");
				$check = array_filter($logs);

				if(!empty($check)){
					$range = round($logs[0]['times'] / 60,2).' jam';
				}
			}

			$data['table'][$no-1]['tr'] = array('');
			$data['table'][$no-1]['td'] = array(
				'no'			=> array(
					'center',
					'5%',
					$no,
					true
				),
				'Mulai'		=> array(
					'center',
					'17%',
					conv_day_id($val['start_date']).', '.format_date_id($val['start_date']),
					true
				),
				'Sampai'	=> array(
					'center',
					'17%',
					conv_day_id($val['range_date']).', '.format_date_id($val['range_date']),
					true
				),
				'Jenis'		=> array(
					'left',
					'20%',
					permit_absen::_conv_type($val['type']),
					true
				),
				'Keterangan'	=> array(
					'left',
					'auto',
					$val['note'],
					true
				),
				'Lama'		=> array(
					'center',
					'10%',
					$range,
					true
				),
			);
		}

		$args = array(
			'title'		=> 'History',
			'button'	=> '_btn_modal_save',
			'status'	=> array(),
			'func'		=> array('sobad_table'),
			'data'		=> array($data)
		);
		
		return modal_admin($args);
	}

	private static function _alphaForm($idx=0,$sdate='',$fdate='',$limit=''){
		$where = "AND user='$idx' AND _inserted BETWEEN '$sdate' AND '$fdate'";
		$args = array('user','shift','type','_inserted');

		// Alpha
		$history = sobad_user::get_logs($args,"type='0' ".$where);

		// Tidak Absen Pulang
		$logs = sobad_user::get_logs($args,"type='1' ".$where);
		$history = array_merge($history,$logs);

		$data['class'] = '';
		$data['table'] = array();

		$no = 0;
		foreach ($history as $key => $val) {
			$no += 1;

			$user = sobad_user::get_id($val['user'],array('name'));
			$user = $user[0]['name'];

			$day = date('w',strtotime($val['_inserted']));
			$work = sobad_work::get_id($val['shift'],array('time_in','time_out'),"AND days='$day'");
			$work = $work[0];

			$data['table'][$no-1]['tr'] = array('');
			$data['table'][$no-1]['td'] = array(
				'No'			=> array(
					'center',
					'5%',
					$no,
					true
				),
				'Nama'			=> array(
					'left',
					'auto',
					$user,
					true
				),
				'Hari'			=> array(
					'left',
					'10%',
					conv_day_id($val['_inserted']),
					true
				),
				'Tanggal'		=> array(
					'left',
					'25%',
					format_date_id($val['_inserted']),
					true
				),
				'Jam Masuk'	=> array(
					'center',
					'10%',
					$work['time_in'],
					true
				),
				'Jam Pulang'	=> array(
					'center',
					'10%',
					$work['time_out'],
					true
				),
				'Keterangan'	=> array(
					'left',
					'15%',
					$val['type']==0?'Alpha':'Tidak Absen Pulang',
					true
				)
			);
		}

		$args = array(
			'title'		=> 'History',
			'button'	=> '_btn_modal_save',
			'status'	=> array(),
			'func'		=> array('sobad_table'),
			'data'		=> array($data)
		);
		
		return modal_admin($args);
	}

// --------------------------------------------------
// --------------------------------------------------
// --------------------------------------------------

	public function _layout(){
		?>
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
					<?php self::_yearlyReport($user['ID']) ;?>
				</div>
			</div>
			<script type="text/javascript">
				if(jQuery().datepicker) {
		            $(".reportpicker").datepicker( {
					    format: "yyyy-mm",
					    viewMode: "months", 
					    minViewMode: "months",
					    rtl: Metronic.isRTL(),
			            orientation: "right",
			            autoclose: true
					});

					$(".yearlypicker").datepicker( {
					    format: "yyyy",
					    viewMode: "years", 
					    minViewMode: "years",
					    rtl: Metronic.isRTL(),
			            orientation: "right",
			            autoclose: true
					});
		        };
			</script>
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
		$now = date('Y-m');
		?>
			<div id="monthly-report" class="bag-report">
				<div class="row">
					<div class="col-md-12">
						<div style="display: inline-flex;float: right;" class="input-group input-medium date date-picker" data-date-format="yyyy-mm" data-date-viewmode="months">
							<input type="text" class="form-control reportpicker" value="<?php print($now) ;?>" data-sobad="_filter" data-load="monthly-report" data-type="month" data-index="<?php print($idx) ;?>" name="filter_date" onchange="filter_report(this)">
						</div>
					</div>
				</div>
				<div class="row">
					<?php self::_graphMonthly($idx) ;?>
					<div class="col-md-6">
						<div id="history-monthly" class="history-report">
							<?php self::_history_monthly($now,$idx) ;?>
						</div>
					</div>
				</div>
				<div id="permit-monthly" class="row">
					<?php self::_dahsMonthly($now,$idx) ;?>
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

		//Get Data User
		$_user = sobad_user::get_id($idx,array('work_time','_entry_date'));

		$no = -1;
		$label = array();$data = array();
		for($i=$sDay;$i<$fDay;$i++){
			$no += 1;
			
			$work_in = 0;
			$work_out = 0;

			$pcolor = 'rgba(21,73,154,1)';
			$data[0]['data'][$no] = 0;
			$data[1]['data'][$no] = 0;
			
			$now = date('Y-m-d',strtotime($i.' days',$default));
			$user = sobad_user::get_id($idx,array('shift','time_in','time_out'),"AND _inserted='$now'");

			$_label = date('M-d',strtotime($i.' days',$default));
			$label[] = $_label;			
			
			$check = array_filter($user);
			if(!empty($check)){
				$time_in = _conv_time('00:00:00',$user[0]['time_in'],2);
				$time_out = _conv_time('00:00:00',$user[0]['time_out'],2);

				$time_in = round($time_in/60,2);
				$time_out = round($time_out/60,2);

				$data[0]['data'][$no] = $time_in;
				$data[1]['data'][$no] = $time_out;

				$work_in = 8;
				$pcolor = $time_in>=$work_in?'rgba(255,0,0,1)':$pcolor;
			}

			// Work Time
			$_shift = sobad_permit::get_all(array('start_date','range_date','note'),"AND user='$idx' AND start_date<='$now' AND range_date>='$now'");
			$check = array_filter($_shift);
			if(!empty($check)){
				$wTime = $_shift[0]['note'];
			}else{
				$wTime = $_user[0]['work_time'];
			}

			if($_user[0]['_entry_date']<=$now){
				$_day = date('w',strtotime($now));
				$_work = sobad_work::get_id($wTime,array('time_in','time_out'),"AND days='$_day'");

				$check = array_filter($_work);
				if(!empty($check)){
					$work_in = $_work[0]['time_in'];
					$work_out = $_work[0]['time_out'];

					$work_in = _conv_time('00:00:00',$work_in,2);
					$work_out = _conv_time('00:00:00',$work_out,2);
				}
			}

			if($work_in!=0){
				$work_in = round($work_in/60,2);
			}

			if($work_out!=0){
				$work_out = round($work_out/60,2);
			}

			$data[2]['data'][$no] = $work_in;
			$data[3]['data'][$no] = $work_out;

			$data[2]['pRadius'][$no] = 0;
			$data[3]['pRadius'][$no] = 0;

			$data[0]['pBgColor'][$no] = $pcolor;
		}

		$data[0]['label'] = 'Entry Hours';
		$data[1]['label'] = 'Left Hours';
		$data[2]['label'] = 'Entry Work';
		$data[3]['label'] = 'Left Work';

		$data[0]['type'] = 'line';
		$data[1]['type'] = 'line';
		$data[2]['type'] = 'line';
		$data[3]['type'] = 'line';

		$data[0]['bgColor'] = 'rgba(21,73,154,1)';
		$data[0]['brdColor'] = 'rgba(21,73,154,1)';

		$data[1]['bgColor'] = 'rgba(255,174,0,1)';
		$data[1]['brdColor'] = 'rgba(255,174,0,1)';

		$data[2]['bgColor'] = 'rgba(69,196,35,0.5)';
		$data[2]['brdColor'] = 'rgba(69,196,35,0.5)';

		$data[3]['bgColor'] = 'rgba(255,0,0,0.5)';
		$data[3]['brdColor'] = 'rgba(255,0,0,0.5)';

		$args = array(
			'type'		=> 'bar',
			'label'		=> $label,
			'data'		=> $data,
			'option'	=> 'opt_monthAbsen'
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
			$range = floor($range / (60 * 60 * 24)) + 1;

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
				'desc'		=> 'Leaving of<br>Absence',
				'column'	=> $column,
				'button'	=> button_toggle_block(array('ID' => 'month_3','func' => '_permitMonth','status' => 'data-type="'.$idx.'"'))
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
				'button'	=> button_toggle_block(array('ID' => 'month_4','func' => '_permitMonth','status' => 'data-type="'.$idx.'"'))
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
				'button'	=> button_toggle_block(array('ID' => 'month_48','func' => '_permitMonth','status' => 'data-type="'.$idx.'"'))
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
				'button'	=> button_toggle_block(array('ID' => 'month_5','func' => '_permitMonth','status' => 'data-type="'.$idx.'"'))
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
				'button'	=> button_toggle_block(array('ID' => 'month_0','func' => '_permitMonth','status' => 'data-type="'.$idx.'"'))
			)
		);

		return metronic_layout::sobad_dashboard($dash);
	}

	// Report Monthly -----------------------------------------
	public function _yearlyReport($idx=0){
		$now = date('Y');
		?>
			<div id="yearly-report" class="bag-report">
				<div class="row">
					<div class="col-md-12">
						<div style="display: inline-flex;float: right;" class="input-group input-medium date date-picker" data-date-format="yyyy" data-date-viewmode="years">
							<input type="text" class="form-control yearlypicker" value="<?php print($now) ;?>" data-sobad="_filter" data-load="yearly-report" data-type="year" data-index="<?php print($idx) ;?>" name="filter_date" onchange="filter_report(this)">
						</div>
					</div>
				</div>
				<div class="row">
					<?php self::_graphYearly($idx) ;?>
					<div class="col-md-6">
						<div id="history-yearly" class="history-report">
							<?php self::_history_yearly($now,$idx) ;?>
						</div>
					</div>
				</div>
				<div id="permit-yearly" class="row">
					<?php self::_dahsYearly($now,$idx) ;?>
				</div>
			</div>
		<?php
	}

	// Graphic Yearly Report ---------------------------------
	public function _graphYearly($idx=0){
		$chart[] = array(
			'func'	=> '_site_load',
			'data'	=> array(
				'id'		=> 'absen-yearly',
				'func'		=> 'dash_absenYearly',
				'status'	=> '',
				'col'		=> 12,
				'label'		=> '<h2 class="title-report">Yearly Report</h2>',
				'type'		=> $idx
			),
		);

		$chart[] = array(
			'func'	=> '_site_load',
			'data'	=> array(
				'id'		=> 'overtime-yearly',
				'func'		=> 'dash_overtimeYearly',
				'status'	=> '',
				'col'		=> 6,
				'label'		=> '<h2 class="title-report">Overtime Report</h2>',
				'type'		=> $idx
			),
		);
		
		return metronic_layout::sobad_chart($chart);
	}

	public function dash_absenYearly($year=''){
		$idx = isset($_POST['type'])?$_POST['type']:0;

		$year = empty($year)?date('Y'):$year;		

		$no = -1;
		$label = array();$data = array();
		for($i=1;$i<=12;$i++){
			$no += 1;
			$date = report_absen::get_range($year.'-'.$i);

			$start = $date['start_date'];
			$finish = $date['finish_date'];

			$scoreA = self::_score_entryHours($idx,"AND _inserted BETWEEN '$start' AND '$finish'");
			$scoreB = self::_score_leftHours($idx,"AND _inserted BETWEEN '$start' AND '$finish'");
		
			$label[] = conv_month_id($i);
			$data[0]['data'][$no] = $scoreA['nominal'];
			$data[1]['data'][$no] = $scoreB['nominal'];
		}

		$data[0]['label'] = 'Score Entry';
		$data[1]['label'] = 'Score Left';

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

	public function dash_overtimeYearly($year=''){
		$idx = isset($_POST['type'])?$_POST['type']:0;

		$year = empty($year)?date('Y'):$year;
		$label = array();$data = array();
		for($m=1;$m<=12;$m++){
			$date = report_absen::get_range($year.'-'.$m);

			$start = $date['start_date'];
			$finish = $date['finish_date'];

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
				
			$label[] = sprintf('%02d',$m);
			$data[0]['data'][$m-1] = $over;
			$data[1]['data'][$m-1] = $total;
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

	public function _history_yearly($year='',$idx=0){
		$idx = isset($_POST['type'])?$_POST['type']:$idx;

		$year = empty($year)?date('Y'):$year;
	
		$yearA = $year.'-01';
		$yearB = $year.'-12';

		$dateA = report_absen::get_range($yearA);
		$dateB = report_absen::get_range($yearB);

		$start = $dateA['start_date'];
		$finish = $dateB['finish_date'];

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
	public function _dahsYearly($date='',$idx=0){
		$idx = isset($_POST['type'])?$_POST['type']:$idx;

		$date = empty($date)?date('Y'):$date;
		$yearA = $date.'-01';
		$yearB = $date.'-12';

		$dateA = report_absen::get_range($yearA);
		$dateB = report_absen::get_range($yearB);

		$start = $dateA['start_date'];
		$finish = $dateB['finish_date'];

		$whr = "AND user='$idx' AND type NOT IN ('6','9') AND ((start_date BETWEEN '$start' AND '$finish') OR (range_date BETWEEN '$start' AND '$finish') OR range_date='0000-00-00')";	
		$logs = sobad_permit::get_all(array('start_date','range_date','num_day','type_date','type'),$whr);

		// Check Permit
		$dayoff = 0;$izin = 0;$sick = 0;$outcity = 0;
		foreach ($logs as $key => $val) {
			$val['start_date'] = $val['start_date']<$start?$start:$val['start_date'];

			$val['range_date'] = $val['range_date']=='0000-00-00'?date('Y-m-d'):$val['range_date'];
			$val['range_date'] = $val['range_date']>$finish?$finish:$val['range_date'];

			$range = strtotime($val['range_date']) - strtotime($val['start_date']);
			$range = floor($range / (60 * 60 * 24)) + 1;

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
		for($v=1;$v<=12;$v++) {
			$_date = report_absen::get_range($date.'-'.$v);

			$_start = $_date['start_date'];
			$_finish = $_date['finish_date'];

			$logs = sobad_user::get_logs(array('ID'),"user='$idx' AND type='1' AND _inserted BETWEEN '".$_start."' AND '".$_finish."'");
			$cnt = count($logs);
			$cnt = floor($cnt/3);

			$alpha += $cnt;
		}

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
				'desc'		=> 'Leaving of<br>Absence',
				'column'	=> $column,
				'button'	=> button_toggle_block(array('ID' => 'year_3','func' => '_permitYear','status' => 'data-type="'.$idx.'"'))
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
				'button'	=> button_toggle_block(array('ID' => 'year_4','func' => '_permitYear','status' => 'data-type="'.$idx.'"'))
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
				'button'	=> button_toggle_block(array('ID' => 'year_48','func' => '_permitYear','status' => 'data-type="'.$idx.'"'))
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
				'button'	=> button_toggle_block(array('ID' => 'year_5','func' => '_permitYear','status' => 'data-type="'.$idx.'"'))
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
				'button'	=> button_toggle_block(array('ID' => 'year_0','func' => '_permitYear','status' => 'data-type="'.$idx.'"'))
			)
		);

		return metronic_layout::sobad_dashboard($dash);
	}

// Report Icon -----------------------------------------	

	public static function _iconLeaving(){
		?>
			<!-- Creator: CorelDRAW 2020 (64-Bit) -->
			<svg xmlns="http://www.w3.org/2000/svg" xml:space="preserve" width="51.4565mm" height="51.3808mm" version="1.1" style="shape-rendering:geometricPrecision; text-rendering:geometricPrecision; image-rendering:optimizeQuality; fill-rule:evenodd; clip-rule:evenodd"
			viewBox="0 0 1492.33 1490.13"
			 xmlns:xlink="http://www.w3.org/1999/xlink"
			 xmlns:xodm="http://www.corel.com/coreldraw/odm/2003">
			 <g id="Layer_x0020_1">
			  <metadata id="CorelCorpID_0Corel-Layer"/>
			  <g id="_1922961280304">
			   <path class="fil0" d="M309.49 424.88l341.88 0 0 338.13 -362.34 0 0 -338.13 20.46 0zm300.95 40.93l-280.49 0 0 256.27 280.49 0 0 -256.27z"/>
			   <polygon class="fil0" points="734.37,508.54 1099.82,508.54 1099.82,549.46 734.37,549.46 "/>
			   <polygon class="fil0" points="734.37,662.64 1099.82,662.64 1099.82,703.57 734.37,703.57 "/>
			   <polygon class="fil0" points="272.06,821.14 1099.82,821.14 1099.82,862.07 272.06,862.07 "/>
			   <polygon class="fil0" points="272.06,979.65 1099.82,979.65 1099.82,1020.58 272.06,1020.58 "/>
			   <polygon class="fil0" points="272.06,1138.15 1099.82,1138.15 1099.82,1179.08 272.06,1179.08 "/>
			   <g>
			    <path class="fil0" d="M211.07 40.93l0 1224.91 971.75 0 0 -922.1 -302.81 -302.82 -668.93 0zm-40.93 1245.38l0 -1286.31 726.8 0c108.91,108.96 217.89,217.85 326.8,326.81l0 979.96 -1053.6 0 0 -20.46z"/>
			    <g>
			     <polygon class="fil0" points="859.2,329.76 859.2,20.46 900.13,20.46 900.13,329.76 "/>
			     <polygon class="fil0" points="859.2,309.3 1188.96,309.3 1188.96,350.23 859.2,350.23 "/>
			    </g>
			   </g>
			   <g>
			    <path class="fil0" d="M1351.18 1408.59l-88.5 -301.92 0 -660.52c0,-31.59 12.91,-60.31 33.71,-81.11 20.8,-20.8 49.52,-33.71 81.11,-33.71 31.59,0 60.31,12.91 81.11,33.71 20.8,20.8 33.72,49.52 33.72,81.11l0 660.52 -123.23 358.31 -17.92 -56.39zm-47.58 -301.92l147.8 0 0 -660.52c0,-20.3 -8.32,-38.78 -21.72,-52.18 -13.4,-13.4 -31.87,-21.72 -52.18,-21.72 -20.31,0 -38.78,8.32 -52.18,21.72 -13.4,13.4 -21.72,31.88 -21.72,52.18l0 660.52zm7.42 40.93l61.09 192.3 71.5 -192.3 -132.59 0z"/>
			    <polygon class="fil0" points="1283.14,538.25 1471.86,538.25 1471.86,579.18 1283.14,579.18 "/>
			   </g>
			   <polygon class="fil0" points="190.61,201.63 40.93,201.63 40.93,1449.21 1050.73,1449.21 1050.73,1286.31 1091.66,1286.31 1091.66,1490.13 -0,1490.13 -0,160.71 190.61,160.71 "/>
			   <path class="fil0" d="M309.49 115.58l447.55 0 0 230.25 -468.02 0 0 -230.25 20.46 0zm406.62 40.93l-386.16 0 0 148.4 386.16 0 0 -148.4z"/>
			   <path class="fil0" d="M351.74 605.88c-8.49,-11.26 -6.26,-27.27 5,-35.77 11.26,-8.49 27.27,-6.26 35.77,5l36.42 48.31 120.14 -118.26c10.04,-9.88 26.19,-9.74 36.07,0.3 9.88,10.04 9.74,26.19 -0.3,36.07l-139.94 137.75c-1.04,1.13 -2.19,2.18 -3.46,3.14 -11.26,8.49 -27.27,6.26 -35.77,-5l-53.93 -71.54z"/>
			  </g>
			 </g>
			</svg>
		<?php
	}

	public static function _iconPermit(){
		?>
			<!-- Creator: CorelDRAW 2020 (64-Bit) -->
			<svg xmlns="http://www.w3.org/2000/svg" xml:space="preserve" width="40.4952mm" height="49.349mm" version="1.1" style="shape-rendering:geometricPrecision; text-rendering:geometricPrecision; image-rendering:optimizeQuality; fill-rule:evenodd; clip-rule:evenodd"
			viewBox="0 0 13957.22 17008.8"
			 xmlns:xlink="http://www.w3.org/1999/xlink"
			 xmlns:xodm="http://www.corel.com/coreldraw/odm/2003">
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

	public static function _iconSick(){
		?>
			<!-- Creator: CorelDRAW 2020 (64-Bit) -->
			<svg xmlns="http://www.w3.org/2000/svg" xml:space="preserve" width="39.1017mm" height="52.608mm" version="1.1" style="shape-rendering:geometricPrecision; text-rendering:geometricPrecision; image-rendering:optimizeQuality; fill-rule:evenodd; clip-rule:evenodd"
			viewBox="0 0 1707.24 2296.94"
			 xmlns:xlink="http://www.w3.org/1999/xlink"
			 xmlns:xodm="http://www.corel.com/coreldraw/odm/2003">
			 <g id="Layer_x0020_1">
			  <metadata id="CorelCorpID_0Corel-Layer"/>
			  <g id="_1922954106368">
			   <path class="fil0" d="M264.12 2228.82l69.65 40.21c46.94,5.9 75.23,25.33 136.14,27.65 45.73,1.74 96.26,-5.52 130.57,-19 83.32,-32.74 163.04,-79.77 214.59,-193.13 22.47,-46.43 34.45,-92.62 36.84,-135.77 9.71,-175.81 -70.8,-8.66 140.59,-375.21l614.62 -1067.67c53.82,-94.03 105.11,-155.03 99.74,-251.6 -7.06,-126.86 -63.7,-148.88 -98.64,-201.2l-64.91 -37.47c-61.03,-3.22 -87.2,-25.73 -148.56,-10.22 -47.36,11.96 -89.27,24.88 -129.16,64.03 -62.33,61.15 -726.8,1238.2 -859.89,1468.23 -26.54,45.87 -18.03,32.79 -58.82,46.24 -36.86,12.16 -90.11,47.89 -125.07,81.39 -123.77,118.57 -135.24,311.87 -64.18,441.5 43.74,79.8 82.32,91.15 106.51,122.05zm1242.59 -2112.11c-136.93,-50.81 -187.02,45.85 -232.18,117.11 -161.17,254.24 -386.47,663.16 -541.69,932l-269.43 467.45c-19.61,29.45 -35.78,32.56 -59.06,40.16 -26.67,8.7 -34.16,11.25 -60.44,26.24 -193.38,110.26 -157.07,385.55 26.23,471.19 170.6,79.71 406.21,-56.32 375.76,-272.12 -8.52,-60.38 -23.44,-60.5 20.74,-135.39 31.3,-53.05 61.55,-106.84 92.13,-160.31l720.92 -1255.79c44.83,-78.29 21.94,-195.33 -72.98,-230.55z"/>
			   <path class="fil0" d="M517.02 1761.37c-29.25,22.46 -125.93,-10.06 -176.54,99.31 -82.02,177.23 159.77,297.04 257.27,169.83 29.28,-38.21 43.49,-72.81 40.56,-114.15 -3.62,-51.02 -12.15,-50.08 -35.68,-95.98 77.37,-131.13 448.73,-746.42 467,-835.89 -85.61,-78.68 -110.66,15.66 -158.59,97.5 -44.1,75.3 -88.15,150.63 -131.99,226.08 -87.66,150.88 -174.78,302.16 -262.03,453.29zm-29 112.97c-72.4,-7.37 -81.07,103.21 -6.68,107.81 58.54,3.62 81.5,-100.19 6.68,-107.81z"/>
			   <path class="fil0" d="M641.46 100.42c39.02,33.99 157.28,94.8 212.22,126.52 28.9,16.69 79.39,54.77 109.38,58.41 29.5,3.59 64.64,-27.92 50.82,-64.95 -14.98,-40.15 -156.75,-105.42 -204.21,-132.83 -76.9,-44.4 -165.71,-118.76 -168.21,12.84z"/>
			   <path class="fil0" d="M-0 1208.02c30.1,31.44 297.26,186.43 340.83,184.9 1.38,-0.31 96.47,-42.31 -17.8,-108.28 -84.46,-48.77 -168.93,-97.53 -253.38,-146.29 -48.25,-27.86 -68.47,31.03 -69.64,69.67z"/>
			   <path class="fil0" d="M692.41 759.07c-37.19,-21.47 -300.45,-184.53 -332.67,-178.3 -36.13,6.98 -56.38,56.1 -21.42,84.5 27.23,22.12 287.68,182.95 321.96,173.82 48.99,-13.04 31.2,-46.16 32.13,-80.02z"/>
			   <path class="fil0" d="M365.34 1180.56c91.34,52.74 124.91,31.81 113.71,-54.69 -40.98,-22.49 -119.19,-85.87 -159.84,-64.24 -74.65,39.73 0.76,92.73 46.13,118.93z"/>
			   <path class="fil0" d="M713.44 375.22c58.36,80.93 212.83,138.73 197.28,36.66 -5.05,-33.11 -67.85,-66.6 -105.94,-84.33 -53.65,-24.96 -73.33,-8.47 -91.34,47.67z"/>
			   <path class="fil0" d="M393 929.39c43.29,64.09 206.38,149.06 198.16,41.71 -2.69,-35.2 -61.79,-67.91 -100.03,-85.54 -60.7,-27.98 -76.02,-15.74 -98.13,43.83z"/>
			   <path class="fil0" d="M668.19 503.16c-45.72,-6.85 -69.97,50.68 -44.53,81.26 16.09,19.35 103.82,69.08 128.19,70.75 41.17,2.81 72.25,-48.94 40.97,-82.22 -18.38,-19.56 -101.14,-66.27 -124.63,-69.79z"/>
			  </g>
			 </g>
			</svg>
		<?php
	}

	public static function _iconAlpha(){
		?>
			<!-- Creator: CorelDRAW 2020 (64-Bit) -->
			<svg xmlns="http://www.w3.org/2000/svg" xml:space="preserve" width="48.3905mm" height="52.846mm" version="1.1" style="shape-rendering:geometricPrecision; text-rendering:geometricPrecision; image-rendering:optimizeQuality; fill-rule:evenodd; clip-rule:evenodd"
			viewBox="0 0 2619.39 2860.57"
			 xmlns:xlink="http://www.w3.org/1999/xlink"
			 xmlns:xodm="http://www.corel.com/coreldraw/odm/2003">
			 <g id="Layer_x0020_1">
			  <metadata id="CorelCorpID_0Corel-Layer"/>
			  <g id="_1922806597504">
			   <path class="fil0" d="M1434.25 907.34c-136.98,-26.42 -342.14,-199.11 -460.79,-215.74 -123.64,-17.33 -320.61,184.31 -407.6,262.35 -60.31,54.1 -131.01,89.67 -128.98,199.3 2.53,136.33 133.26,267.11 377.53,48.71 97.81,-87.45 154.87,-162.2 199.8,-106.57 28.47,35.24 -424.1,800.66 -470.24,830.37 -64.79,41.73 -312.92,22.33 -408.96,47.41 -118.3,30.89 -180.02,158.79 -96.62,270.82 72.7,97.65 221.42,64.41 364.93,52.97 150.42,-12 285.2,7.98 365.43,-90.38 67.28,-82.49 114.57,-217.53 188.18,-267.96 68.55,18.39 136.86,87.31 191.1,131.54 69.15,56.37 119.45,70.17 129.02,193.52 14.45,186.24 -28.32,498.53 231.49,441.37 195.4,-43 115.36,-339.62 105.19,-513.09 -15.97,-272.3 -119.48,-276.43 -261,-408.08l209.46 -397.9c195.94,66.43 192.43,144.84 381.6,-8.92l302.32 -251.94c104.63,-130.13 7.14,-303.41 -160.76,-280.29 -84.64,11.65 -253.03,182.29 -326.39,233.83l-324.71 -171.34zm-269.28 144.42c-65.56,177.14 -287.66,551.7 -392.77,719.65 -35.78,57.17 -68.81,109.49 -102.44,164.84 -89.39,147.15 -172.39,102.18 -432.22,125.63 -109.52,9.88 -140.29,26.04 -128.95,131.36 113.74,36.84 280.67,0.9 409.86,-7.43 202.87,-13.07 178.87,-54.25 265.5,-192.81 156.38,-250.13 217.92,-171.22 405.12,-21.29 218.72,175.17 168.18,116.97 197.84,493.62 16.78,213.13 163.32,201.32 144.18,-32.12 -8.46,-103.22 -3.19,-333.62 -55.08,-404.15 -29.08,-39.53 -208.92,-177.81 -256.34,-202.37 18.42,-84.55 260.49,-529.99 310.15,-577.13 347.14,146.39 137.57,203.22 533.1,-97.98 14.53,-11.06 72.57,-58.76 82.92,-70.01 46.13,-50.15 47.47,-27.68 27.51,-113.09 -73.29,-39.86 -91.4,-20.56 -150.51,28.34 -46.7,38.63 -81.96,70.08 -125.43,104.54 -142.36,112.87 -114.17,122.39 -296.57,19.68 -83.2,-46.85 -551.47,-303.09 -617.08,-321.02 -72.59,-19.84 -104.49,24.24 -148.59,60.28l-251.7 207.8c-46.47,38.76 -79.01,109.09 -1.95,139.81 72.38,28.85 221.61,-135.98 271.48,-177.05 130.5,-107.5 168.86,-58.5 312,20.89z"/>
			   <path class="fil0" d="M917.03 647.88l120.54 16.54 -0.11 -537.25 1459.29 0.87 0.99 2603.36 -1458.98 6.01 -1.41 -647.98c0,-87.49 -28.6,-94.1 -83.76,-115.1 -40.59,92.13 -39.58,60.8 -39.02,190.8l0.82 695.44 1704.01 -0.21 -0.18 -2860.36 -1703.83 0.64 1.65 647.24z"/>
			   <line class="fil0" x1="1434.25" y1="907.34" x2="1434.25" y2= "907.34" />
			   <path class="fil1" d="M1601.87 280.31c80.67,0 153.72,32.71 206.59,85.58 52.87,52.87 85.58,125.92 85.58,206.59 0,80.67 -32.71,153.72 -85.58,206.59 -52.87,52.87 -125.92,85.58 -206.59,85.58 -80.67,0 -153.72,-32.71 -206.59,-85.58 -52.87,-52.87 -85.58,-125.92 -85.58,-206.59 0,-80.67 32.71,-153.72 85.58,-206.59 52.87,-52.87 125.92,-85.58 206.59,-85.58zm125.58 166.59c-32.13,-32.13 -76.53,-52.01 -125.58,-52.01 -49.04,0 -93.45,19.88 -125.58,52.01 -32.13,32.13 -52.01,76.53 -52.01,125.58 0,49.04 19.88,93.45 52.01,125.58 32.13,32.13 76.53,52.01 125.58,52.01 49.04,0 93.45,-19.88 125.58,-52.01 32.13,-32.13 52.01,-76.53 52.01,-125.58 0,-49.04 -19.88,-93.45 -52.01,-125.58z"/>
			  </g>
			 </g>
			</svg>
		<?php
	}
}