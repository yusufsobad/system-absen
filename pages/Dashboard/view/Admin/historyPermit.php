<?php

class historyPermit_absen extends _page{

	protected static $object = 'historyPermit_absen';

	protected static $table = 'sobad_permit';

	// ----------------------------------------------------------
	// Layout category  ------------------------------------------
	// ----------------------------------------------------------

	protected static function _array(){
		$args = array(
			'ID',
			'user',
			'start_date',
			'range_date',
			'num_day',
			'type',
			'note',
			'type_date'
		);

		return $args;
	}

	protected function _where($now=''){
		$conv = report_absen::get_range($now);
		$sdate = $conv['start_year'].'-'.$conv['start_month'].'-'.$conv['start_day'];
		$fdate = $conv['finish_year'].'-'.$conv['finish_month'].'-'.$conv['finish_day'];

		switch (parent::$type) {
			case 'history_3':
				$where = "AND type='3'";
				break;

			case 'history_4':
				$where = "AND (type='4' OR type>'10')";
				break;

			case 'history_5':
				$where = "AND type='5'";
				break;
			
			default:
				$where = "AND type NOT IN (9)";
				break;
		}

		$where .= " AND (start_date>='$sdate' AND range_date<='$fdate')";
		
		return $where;
	}

	protected function table($now=''){
		$data = array();
		$args = self::_array();

		$start = intval(parent::$page);
		$nLimit = intval(parent::$limit);

		$where = self::_where($now);
		
		$kata = '';
		if(parent::$search){
			$_args = array(
				'ID',
				'user'
			);

			$src = parent::like_search($_args,$where);	
			$cari = $src[0];
			$where = $src[0];
			$kata = $src[1];
		}else{
			$cari=$where;
		}
	
		$limit = ' ORDER BY start_date DESC ';
		$where .= $limit;

		$object = self::$table;
		$args = $object::get_all($args,$where);
		//$sum_data = $object::count("1=1 ".$cari,self::_array());
		
		$data['data'] = array('data' => $kata,'type' => parent::$type);
		$data['search'] = array('Semua','nama');
		$data['class'] = '';
		$data['table'] = array();

		$users = array();
		foreach($args as $key => $val){
			$idx = $val['user'];
			if(isset($users[$idx])){
				$user[$idx] = array();
			}

			$users[$idx][] = $val;
		}

		//Hitung Jumlah Cuti
		$sisa = 0;
		if(parent::$type=='history_3'){
			$_date = strtotime($now);
			$_y = date('Y',$_date);
			$_m = date('m',$_date);

			$_option = sobad_module::get_all(array('meta_value'),"AND meta_key='opt_dayoff' AND meta_reff='$_y'");
			$_option = $_option[0]['meta_value'];

			$_dayoff = array();
			$q = sobad_permit::get_all(array('user','num_day'),"AND type='3' AND YEAR(start_date)='$_y' AND MONTH(start_date)<='$_m'");
			foreach ($q as $key => $val) {
				if(!isset($_dayoff[$val['user']])){
					$_dayoff[$val['user']] = $_option;
				}

				$_dayoff[$val['user']] -= $val['num_day'];
			}
		}

		$no = 0;
		foreach($users as $key => $val){
			$no += 1;
			$id = $key;

			$lama = 0;
			$count = 0;
			foreach ($val as $_key => $_val) {
				$conv = permit_absen::_conv_dateRange($_val);
				$range = $conv['range'];

				if($_val['type']==4){
					$_now = $_val['start_date'];
					$logs = sobad_logDetail::get_all(array('log_id','times'),"AND _log_id.user='$key' AND _log_id._inserted='$_now'");
					$check = array_filter($logs);

					if(!empty($check)){
						$range = round($logs[0]['times'] / 60 / 24,1);
					}
				}else{
					$count += 1;
				}

				$lama += $range;
			}

			$lama += $count;

			$_history = array(
				'ID'	=> 'history_'.$id,
				'func'	=> '_history',
				'color'	=> 'yellow',
				'icon'	=> 'fa fa-eye',
				'label'	=> 'History',
				'type'	=> self::$type.'#'.$now
			);

			if(parent::$type=='history_3'){
				$sisa = isset($_dayoff[$key])?$_dayoff[$key]:0;
			}
			
			$data['table'][$no-1]['tr'] = array('');
			$data['table'][$no-1]['td'] = array(
				'No'		=> array(
					'center',
					'5%',
					$no,
					true
				),
				'Name'		=> array(
					'left',
					'auto',
					$val[0]['name_user'],
					true
				),
				'Banyak (X)'		=> array(
					'center',
					'15%',
					count($val),
					true
				),
				'Lama'		=> array(
					'center',
					'10%',
					$lama.' hari',
					true
				),
				'Sisa'		=> array(
					'center',
					'10%',
					$sisa.' hari',
					true
				),		
				'History'		=> array(
					'center',
					'10%',
					_modal_button($_history),
					true
				),
			);

			if(parent::$type!='history_3'){
				unset($data['table'][$no-1]['td']['Sisa']);
			}
		}
		
		return $data;
	}

	private function head_title(){
		$args = array(
			'title'	=> 'History <small>data history</small>',
			'link'	=> array(
				0	=> array(
					'func'	=> self::$object,
					'label'	=> 'history'
				)
			),
			'date'	=> false,
		); 
		
		return $args;
	}

	protected function get_box(){
		$data = self::table(date('Y-m'));
		
		$type = str_replace('history_', '', parent::$type);
		$label = permit_absen::_conv_type($type);
		$box = array(
			'label'		=> 'History '.$label,
			'tool'		=> '',
			'action'	=> self::action(),
			'func'		=> 'sobad_table',
			'data'		=> $data
		);

		return $box;
	}

	protected function layout(){
		self::$type = 'history_3';
		$box = self::get_box();

		$tabs = array(
			'tab'	=> array(
				0	=> array(
					'key'	=> 'history_3',
					'label'	=> 'Cuti',
					'qty'	=> ''
				),
				1	=> array(
					'key'	=> 'history_4',
					'label'	=> 'Izin',
					'qty'	=> ''
				),
				2	=> array(
					'key'	=> 'history_5',
					'label'	=> 'Luar Kota',
					'qty'	=> ''
				),
			),
			'func'	=> '_portlet',
			'data'	=> $box
		);
		
		$opt = array(
			'title'		=> self::head_title(),
			'style'		=> array(''),
			'script'	=> array('')
		);

		return tabs_admin($opt,$tabs);
	}

	protected function action(){
		$type = self::$type;
		$date = date('Y-m');
		ob_start();
		?>
			<div style="display: inline-flex;" class="input-group input-medium date date-picker" data-date-format="yyyy-mm" data-date-viewmode="months">
				<input id="monthpicker" type="text" class="form-control" value="<?php print($date); ?>" data-sobad="_filter" data-load="sobad_portlet" data-type="<?php print($type) ;?>" name="filter_date" onchange="sobad_filtering(this)">
			</div>
			<script type="text/javascript">
				if(jQuery().datepicker) {
		            $("#monthpicker").datepicker( {
					    format: "yyyy-mm",
					    viewMode: "months", 
					    minViewMode: "months",
					    rtl: Metronic.isRTL(),
			            orientation: "right",
			            autoclose: true
					});
		        };
			</script>
		<?php
		$date = ob_get_clean();	

		$print = array(
			'ID'	=> 'preview_0',
			'func'	=> '_preview',
			'color'	=> 'btn-default',
			'icon'	=> 'fa fa-print',
			'label'	=> 'Print',
			'type'	=> parent::$type
		);	

		return $date.' '.print_button($print);
	}

	public function _filter($date=''){
		ob_start();
		self::$type = $_POST['type'];
		$table = self::table($date);
		metronic_layout::sobad_table($table);
		return ob_get_clean();
	}

// --------------------------------------------------------------
// History ------------------------------------------------------
// --------------------------------------------------------------

	public function _history($id=0){
		$id = str_replace('history_', '', $id);
		intval($id);

		$data = $_POST['type'];
		$data = explode('#', $data);
		
		self::$type = $data[0];
		$date = $data[1];

		$where = self::_where($date);
		$history = sobad_permit::get_all(array('user','start_date','range_date','num_day','type_date','type','note'),$where." AND user='$id'");

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
			'title'		=> 'History '.$history[0]['name_user'],
			'button'	=> '_btn_modal_save',
			'status'	=> array(),
			'func'		=> array('sobad_table'),
			'data'		=> array($data)
		);
		
		return modal_admin($args);
	}


// --------------------------------------------------------------
// Database -----------------------------------------------------
// --------------------------------------------------------------	
	public function _preview($args=array()){
		$_SESSION[_prefix.'development'] = 0;
		parent::$type = $_GET['type'];

		switch (parent::$type) {
			case 'history_3':
				$title = 'Cuti';
				break;

			case 'history_4':
				$title = 'Izin';
				break;

			case 'history_5':
				$title = 'Luar Kota';
				break;
			
			default:
				$title = 'Undefined';
				break;
		}

		$args = array(
			'data'		=> '',
			'style'		=> array('style_type2','style_history'),
			'object'	=> self::$object,
			'html'		=> '_html',
			'setting'	=> array(
				'posisi'	=> 'landscape',
				'layout'	=> 'A4',
			),
			'name save'	=> $title.' '.conv_month_id(date('m')).' '.date('Y')
		);

		return sobad_convToPdf($args);
	}

	public function _html(){
		parent::$type = $_GET['type'];
		$now = isset($_GET['filter']) && !empty($_GET['filter'])?$_GET['filter']:date('Y-m');

		$where = self::_where($now);
		$args = self::_array();

		$object = self::$table;
		$args = $object::get_all($args,$where);

		$now = strtotime($now);
		$dateM = date('m',$now);
		$dateY = date('Y',$now);

		$users = array();
		foreach($args as $key => $val){
			$idx = $val['user'];
			if(isset($users[$idx])){
				$user[$idx] = array();
			}

			$users[$idx][] = $val;
		}

		switch (parent::$type) {
			case 'history_3':
				$title = 'CUTI';
				break;

			case 'history_4':
				$title = 'IZIN';
				break;

			case 'history_5':
				$title = 'LUAR KOTA';
				break;
			
			default:
				$title = 'Undefined';
				break;
		}

		?>
			<page backtop="5mm" backbottom="5mm" backleft="5mm" backright="5mm" pagegroup="new">
				<div style="text-align:center;width:100%;">
					<h2 style="margin-bottom: 0px;"> <?php print($title) ;?> </h2>
					<h3 style="margin-top: 0px;">Bulan <u>Absensi</u>: <?php echo conv_month_id($dateM).' '.$dateY ;?></h3>
				</div><br>
				<table style="width:100%;font-family:calibri;">
					<thead>
						<tr>
							<th style="width:45%;font-family: calibriBold;"></th>
							<th style="width:20%;font-family: calibriBold;"></th>
							<th style="width:35%;font-family: calibriBold;"></th>
						</tr>
					</thead>
					<tbody>
						<?php
							$no = 0;
							foreach ($users as $key => $val) {
								$lama = 0;$no += 1;
								foreach ($val as $_key => $_val) {
									$conv = permit_absen::_conv_dateRange($_val);
									$range = $conv['range'];

									$lama += $range;
								}

								$lama += count($val);

								?>
									<tr>
										<td style="padding-top: 30px;font-family: calibriBold;"><?php print($val[0]['name_user']) ;?></td>
										<td style="padding-top: 30px;">Banyak : <?php print(count($val).' kali'); ?></td>
										<td style="text-align:right;padding-right: 20px;padding-top: 30px;">Total : <?php print($lama.' hari'); ?></td>
									</tr>
									<tr>
										<td colspan="3">
											<table class="table-bordered sobad-punishment" style="width:100%;font-family:calibri;">
												<thead>
													<tr>
														<th style="text-align:center;width:5%;font-family: calibriBold;">No</th>
														<th style="text-align:center;width:20%;font-family: calibriBold;">Mulai</th>
														<th style="text-align:center;width:20%;font-family: calibriBold;">Sampai</th>
														<th style="text-align:center;width:15%;font-family: calibriBold;">Jenis</th>
														<th style="text-align:center;width:25%;font-family: calibriBold;">Keterangan</th>
														<th style="text-align:center;width:15%;font-family: calibriBold;">Lama</th>
													</tr>
												</thead>
												<tbody>
													<?php
														$_no = 0;
														foreach ($val as $_key => $_val) {
															$_no += 1;

															$conv = permit_absen::_conv_dateRange($_val);
															$_val = $conv['data'];
															$sts_day = $conv['status'];
															$range = $conv['range'];

															$mulai = conv_day_id($_val['start_date']).', '.format_date_id($_val['start_date']);
															$sampai = conv_day_id($_val['range_date']).', '.format_date_id($_val['range_date']);
															?>
																<tr>
																	<td><?php print($_no) ;?></td>
																	<td><?php print($mulai) ;?></td>
																	<td><?php print($sampai) ;?></td>
																	<td><?php print(permit_absen::_conv_type($_val['type'])) ;?></td>
																	<td><?php print($_val['note']) ;?></td>
																	<td><?php print(($range + 1).' '.$sts_day) ;?></td>
																</tr>
															<?php
														}
													?>
												</tbody>
											</table>
										</td>
									</tr>
								<?php
							}
						?>
					</tbody>
				</table>
			</page>
		<?php
	}
}