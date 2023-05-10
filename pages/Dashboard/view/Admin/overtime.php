<?php

class overtime_absen extends _page{

	protected static $object = 'overtime_absen';

	protected static $table = 'sobad_overtime';

	// ----------------------------------------------------------
	// Layout category  ------------------------------------------
	// ----------------------------------------------------------

	protected function _array(){
		$args = array(
			'ID',
			'title',
			'post_date',
			'approve',
			'accept',
			'note',
		);

		return $args;
	}

	protected function table($date=''){
		$data = array();
		$args = self::_array();

		$date = empty($date)?date('Y-m-d'):$date;
		$m = date('m',strtotime($date));$y = date('Y',strtotime($date));

		$range = report_absen::get_range($y.'-'.$m);
		$sdate = $range['start_date'];
		$fdate = $range['finish_date'];

		$user_id = get_id_user();
		$where = "AND `abs-overtime`.post_date BETWEEN '$sdate' AND '$fdate'";

		$object = self::$table;
		$args = $object::get_all($args,$where);

		$data['class'] = '';
		$data['table'] = array();

		$no = 0;
		foreach($args as $key => $val){
			$no += 1;
			$id = $val['ID'];

			$qty = 0;
			$lembur = sobad_overtime::get_details($id,array('status'));
			foreach ($lembur as $ky => $vl) {
				$qty += $vl['status']==2?0:1;
			}

			$detail = array(
				'ID'	=> 'detail_'.$id,
				'func'	=> '_detail',
				'color'	=> '',
				'icon'	=> '',
				'label'	=> $qty.' Orang'
			);

			// $acc_sts = $val['accept']>=1?'disabled':'';
			// $accept = array(
			// 	'ID'	=> 'accept_'.$id,
			// 	'func'	=> '_accept',
			// 	'color'	=> 'green',
			// 	'icon'	=> 'fa fa-check',
			// 	'label'	=> 'Terima',
			// 	'status'=> $acc_sts
			// );

			$tanggal = $val['post_date'];
			$tanggal = conv_day_id($tanggal).', '.format_date_id($tanggal);

			$color = '#cb5a5e';
			if($val['approve']>=1){
				$color = '#26a69a';
			}
			$ppic = '<i class="fa fa-circle" style="color:'.$color.'">';
			
			$data['table'][$key]['tr'] = array('');
			$data['table'][$key]['td'] = array(
				'no'		=> array(
					'center',
					'5%',
					$no,
					true
				),
				'Tanggal'		=> array(
					'left',
					'17%',
					$tanggal,
					true
				),
				'Keterangan'	=> array(
					'left',
					'auto',
					$val['note'],
					true
				),
				// 'PPIC'		=> array(
				// 	'center',
				// 	'7%',
				// 	$ppic,
				// 	true
				// ),
				'Jumlah'	=> array(
					'right',
					'10%',
					_modal_button($detail),
					true
				),
				// 'Accept'	=> array(
				// 	'center',
				// 	'10%',
				// 	_click_button($accept),
				// 	false
				// ),
			);
		}
		
		return $data;
	}

	private function head_title(){
		$args = array(
			'title'	=> 'Lembur <small>data lembur</small>',
			'link'	=> array(
				0	=> array(
					'func'	=> self::$object,
					'label'	=> 'lembur'
				)
			),
			'date'	=> false
		); 
		
		return $args;
	}

	protected function get_box(){
		$data = self::table();
		
		$box = array(
			'label'		=> 'Data Lembur',
			'tool'		=> '',
			'action'	=> self::action(),
			'func'		=> 'sobad_table',
			'data'		=> $data
		);

		return $box;
	}

	protected function layout(){
		$box = self::get_box();
		
		$opt = array(
			'title'		=> self::head_title(),
			'style'		=> array(''),
			'script'	=> array('')
		);
		
		return portlet_admin($opt,$box);
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

		$excel = array(
			'ID'	=> 'excel_0',
			'func'	=> '_export_excel',
			'color'	=> 'btn-default',
			'icon'	=> 'fa fa-file-excel-o',
			'label'	=> 'Export'
		);
		
		return $date.' '.print_button($excel);
	}

	public function _filter($date=''){
		ob_start();
		self::$type = '';
		$table = self::table($date);
		metronic_layout::sobad_table($table);
		return ob_get_clean();
	}

	// ----------------------------------------------------------
	// Database Lembur ------------------------------------------
	// ----------------------------------------------------------

	public static function _export_excel(){
		$date = !empty($_GET['filter']) ? $_GET['filter'] : date('Y-m');
		$_date = strtotime($date);

		$month = conv_month_id(date('m',$_date));
		$year = date('Y',$_date);
		$_date = $month.' '.$year;

		ob_start();
		header("Content-type: application/vnd-ms-excel");
		header("Content-Disposition: attachment; filename=Data Lembur ".$_date.".xls");

		$data = self::data_overtime($date);
		self::table_overtime($data, $_date);
		return ob_get_clean();
	}

	public static function data_overtime($date=''){
		$data = array();

		$tgl = report_absen::get_range($date);
		$startD = $tgl['start_day'] - 1;
		$finishD = $tgl['finish_day'];

		$sdate = $tgl['start_date'];
		$fdate = $tgl['finish_date'];

		$_date = strtotime($date);
		$default = date('Y',$_date).'-'.date('m',$_date).'-01';
		$default = strtotime($default);

		$before = strtotime('-1 days',$default);
		$before = date('d',$before);
		$before = $startD - intval($before);

		$data['number_day'] = $tgl['number_day'];
		$data['date'] = $data['user'] = array();

		$where = "AND `abs-overtime`.post_date BETWEEN '$sdate' AND '$fdate'";

		$object = self::$table;
		$args = $object::get_all(array('ID','post_date'),$where);

		$overtime = $temp_user = array(0 => 0);
		foreach ($args as $key => $val) {
			$code = strtotime($val['post_date']);
			$holiday = holiday_absen::_check_holiday($val['post_date']);

			$lembur = sobad_overtime::get_details($val['ID'],array('ID','user_id','start_time','finish_time','status'),"AND `abs-overtime-detail`.status='1'");

			foreach ($lembur as $ky => $vl) {
				if($vl['status']==1){
					if(!isset($overtime[$code])){
						$overtime[$code] = array();
					}

					$idu = $vl['user_id'];
					$otime = _conv_time($vl['start_time'],$vl['finish_time'],3);

					if($otime<=2){
						$ctime = $otime * 1.5;
					}else{
						$ctime = ($otime * 2) - 1;
					}

					if($holiday){
						$ctime = ($otime * 2);
					}

					$id = $vl['ID'];
					$logs = sobad_logDetail::get_all(array('time_over'),"AND id_over_detail='$id'");
					foreach ($logs as $ky => $vl) {
						$otime -= $vl['time_over'];
					}

					$overtime[$code][$idu] = array(
						'overtime'		=> $otime,
						'calculation'	=> $ctime
					);

					if(!isset($temp_user[$idu])){
						$temp_user[$idu] = $code;
					}
				}
			}
		}

		$users = array_keys($temp_user);
		$users = implode(',', $users);
		$users = sobad_user::get_all(array('ID','name','no_induk','status','end_status','divisi','inserted','_nickname'),"AND `abs-user`.ID IN ($users)");

		for($i=$before;$i<$finishD;$i++){
			$now = date('Y-m-d',strtotime($i.' days',$default));
			$tanggal = format_date_id($now);

			$holiday = holiday_absen::_check_holiday($now);

			$data['date'][] = array(
				'tanggal'		=> $tanggal,
				'status'		=> $holiday ? 1 : 0
			);

			$code = strtotime($now);
			foreach ($users as $key => $val) {
				$idu = $val['ID'];
				if(!isset($data['user'][$idu])){
					$data['user'][$idu] = $val;
					$data['user'][$idu]['date'] = array();
				}

				$otime = $ctime = 0;
				if( isset($overtime[$code]) ){
					if( isset($overtime[$code][$idu]) ){
						$otime = $overtime[$code][$idu]['overtime'];
						$ctime = $overtime[$code][$idu]['calculation'];
					}
				}

				$data['user'][$idu]['date'][$code] = array(
					'overtime'		=> $otime,
					'calculation'	=> $ctime
				);
			}
		}

		return $data;
	}

	public static function table_overtime($data='',$date=''){
		$range = $data['number_day'] + 1;

		$style_head = 'border:1px solid #666;color:#000;text-align:center;';
		$style_body = 'border:1px solid #999;color:#0070C0;';
		$style_body_over = 'border:1px solid #999;color:#000;';

		$bag_def = 'background: #B4C6E7;';
		$bag_red = 'background: #FF0000;';
		$bag_tot = 'background: #FFC000;';

		?>
			<table>
				<thead>
					<tr>
						<th colspan="4" style="text-align: left;">Rekap Lembur Karyawan</th>
						<th colspan="<?= $range ;?>"></th>
					</tr>
					<tr>
						<th colspan="4" style="text-align: left;">Periode : <?= $date ;?></th>
						<th colspan="<?= $range ;?>"></th>
					</tr>
					<tr>
						<th colspan="<?= $range + 4 ;?>"></th>
					</tr>
					<tr>
						<th style="width:30px;<?= $style_head . $bag_def ;?>">No</th>
						<th style="width:50px;<?= $style_head . $bag_def ;?>text-align: left;">NIP</th>
						<th style="width:200px;<?= $style_head . $bag_def ;?>">Nama</th>
						<th style="width:100px;<?= $style_head . $bag_def ;?>">-</th>

						<?php
							// Looping Range Date
							foreach ($data['date'] as $key => $val) {
								$bag_col = $val['status'] == 1 ? $bag_red : $bag_def;
								echo '<th style="' . $style_head . $bag_col . '">'.$val['tanggal'].'</th>';
							}
						?>
						<th style="width:70px;<?= $style_head . $bag_tot ;?>">Total</th>
					</tr>
				</thead>
				<tbody>
					<?php
						$no = 0;
						foreach ($data['user'] as $key => $val) {
							$no += 1;
							$ototal = $ctotal = 0;

							$name = empty($val['_nickname']) ? $val['name'] : $val['_nickname'];

							$no_induk = $val['status'] == 7 ||  $val['end_status'] == 7 ? internship_absen::_conv_no_induk($val['no_induk'],$val['inserted'],$val['divisi']) : $val['no_induk'];

							// Overtime

							echo '<tr>';
							echo '<td style="'.$style_body.'text-align:center;">'.$no.'</td>';
							echo '<td style="'.$style_body.'">'.$no_induk.'</td>';
							echo '<td style="'.$style_body.'">'.$name.'</td>';
							echo '<td style="'.$style_body.'">Overtime</td>';

							foreach ($val['date'] as $ky => $vl) {
								$ototal += $vl['overtime'];
								$over = empty($vl['overtime']) ? '' : $vl['overtime'];

								echo '<td style="'.$style_body_over.'text-align:center;">'.$over.'</td>';
							}

							echo '<td style="'.$style_body_over.'text-align:center;">'.$ototal.'</td>';
							echo '</tr>';

							// Calculation Overtime

							echo '<tr>';
							echo '<td style="'.$style_body.'text-align:center;"> </td>';
							echo '<td style="'.$style_body.'"> </td>';
							echo '<td style="'.$style_body.'"> </td>';
							echo '<td style="'.$style_body.'">Calculation </td>';
							
							foreach ($val['date'] as $ky => $vl) {
								$ctotal += $vl['calculation'];
								$calc = empty($vl['calculation']) ? '' : $vl['calculation'];

								echo '<td style="'.$style_body.'text-align:center;">'.$calc.'</td>';
							}

							echo '<td style="'.$style_body_over.'text-align:center;background:#B4C6E7;">'.$ctotal.'</td>';
							echo '</tr>';
						}
					?>
				</tbody>
			</table>
		<?php
	}

	// ----------------------------------------------------------
	// Database Lembur ------------------------------------------
	// ----------------------------------------------------------

	public static function _detail($id=0){
		return lembur_supervisor::_detail($id);
	}

	public static function _accept($id=0){
		$id = str_replace('accept_', '', $id);
		intval($id);
		
		$q = sobad_db::_update_single($id,'abs-overtime',array(
			'accept'	=> get_id_user(),
		));

		if($q!==0){
			$table = self::table();
			return table_admin($table);
		}
	}
}