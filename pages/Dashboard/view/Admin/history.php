<?php

class history_absen extends _page{

	protected static $object = 'history_absen';

	protected static $table = 'sobad_logDetail';

	// ----------------------------------------------------------
	// Layout category  ------------------------------------------
	// ----------------------------------------------------------

	protected function table($now=''){
		$now = empty($now)?date('Y-m'):$now;

		$data = array();
		$args = array();

		$start = intval(parent::$page);
		$nLimit = intval(parent::$limit);
		
		$status = str_replace('history_', '', parent::$type);
		intval($status);

		$status = $status==0?1:$status;
		$whr = '';
		if($status==1){
			$whr = "AND (`abs-log-detail`.log_history LIKE '%$now%') ";
		}

		$kata = '';$where = "AND `abs-log-detail`.type_log='$status' $whr";
		if(parent::$search){
			$src = parent::like_search($args,$where);	
			$cari = $src[0];
			$where = $src[0];
			$kata = $src[1];
		}else{
			$cari=$where;
		}
	
		$limit = 'ORDER BY date_schedule DESC ';//'LIMIT '.intval(($start - 1) * $nLimit).','.$nLimit;
		$where .= $limit;

		$object = self::$table;
		$args = $object::get_all($args,$where);
		//$sum_data = $object::count("1=1 ".$cari);
		
		$data['data'] = array('data' => $kata, 'type' => parent::$type);
		$data['search'] = array('Semua','nama');
		$data['class'] = '';
		$data['table'] = array();
	/*	
		$data['page'] = array(
			'func'	=> '_pagination',
			'data'	=> array(
				'start'		=> $start,
				'qty'		=> $sum_data,
				'limit'		=> $nLimit,
				'type'		=> parent::$type
			)
		);
	*/

		$no = ($start-1) * $nLimit;
		foreach($args as $key => $val){
			$no += 1;

			$note = ($val['type_log']==3)?'Jam':'Menit';

			$date = date($val['_inserted_log_']);
			$date = strtotime($date);
			$days = date('w',$date);

			$work = sobad_work::get_workTime($val['ID_shif'],"AND `abs-work-normal`.days='$days'");
			$worktime = format_time_id($work[0]['time_in']).' - '.format_time_id($work[0]['time_out']);

			$masuk = 'Masuk';$pulang = 'Pulang';$extime = $val['times'].' menit';
			if(self::$type=='history_2'){
				//Check kekurangan
				$history = unserialize($val['log_history']);
				if(isset($history['extime'])){
					$extime = $history['extime'].' menit';
				}

				//Check history time
				$masuk = 'Keluar';$pulang = 'Kembali';
				$history = unserialize($val['history_log_']);

				$val['time_in_log_'] = '-';
				$val['time_out_log_'] = '-';

				if(isset($history['logs'])){
					foreach ($history['logs'] as $ky => $vl) {
						if(in_array($vl['type'],array('4','8'))){
							$val['time_in_log_'] = $vl['time'];
							$_idx = $ky;
							break;
						}
					}

					if(isset($history['logs'][$_idx + 1])){
						$val['time_out_log_'] = $history['logs'][$_idx + 1]['time'];
					}
				}
			}

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

			$_history = array(
				'ID'	=> 'history_'.$val['ID'],
				'func'	=> '_history',
				'color'	=> 'yellow',
				'icon'	=> 'fa fa-eye',
				'label'	=> 'History',
				'type'	=> self::$type
			);

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
					'15%',
					format_date_id($val['date_schedule']),
					true
				),
				'Jam Kerja'		=> array(
					'center',
					'15%',
					$worktime,
					true
				),
				$masuk			=> array(
					'center',
					'10%',
					$val['time_in_log_'],
					true
				),
				$pulang			=> array(
					'center',
					'10%',
					$val['time_out_log_'],
					true
				),
				'Total'	=> array(
					'left',
					'10%',
					$val['times'] .' '. $note,
					true
				),
				'Waktu'	=> array(
					'left',
					'8%',
					$extime,
					true
				),
				'Status'		=> array(
					'center',
					'7%',
					$status,
					true
				),
				'History'		=> array(
					'center',
					'10%',
					_modal_button($_history),
					true
				),
			);

			if(self::$type=='history_3'){
				unset($data['table'][$key]['td']['Status']);
				unset($data['table'][$key]['td']['History']);
			}

			if(self::$type!='history_2'){
				unset($data['table'][$key]['td']['Waktu']);
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
			'date'	=> false
		); 
		
		return $args;
	}

	protected function get_box(){
		$data = self::table();
		
		$type = str_replace("history_", '', parent::$type);
		switch ($type) {
			case 1:
				$label = 'Punishment';
				break;

			case 2:
				$label = 'Ganti Jam';
				break;

			case 1:
				$label = 'Lembur';
				break;
			
			default:
				$label = 'Punishment';
				break;
		}

		$action = $type==1?self::action():'';
		$action = $type==2?self::action2():$action;

		$box = array(
			'label'		=> 'History '.$label,
			'tool'		=> '',
			'action'	=> $action,
			'func'		=> 'sobad_table',
			'data'		=> $data
		);

		return $box;
	}

	protected function layout(){
		self::$type = 'history_1';
		$box = self::get_box();

		$tabs = array(
			'tab'	=> array(
				0	=> array(
					'key'	=> 'history_1',
					'label'	=> 'Punishment',
					'qty'	=> ''
				),
				1	=> array(
					'key'	=> 'history_2',
					'label'	=> 'Ganti Jam',
					'qty'	=> ''
				),
				2	=> array(
					'key'	=> 'history_3',
					'label'	=> 'Lembur',
					'qty'	=> ''
				)
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
			<div class="input-group input-medium date date-picker" data-date-format="yyyy-mm" data-date-viewmode="months">
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
		
		return $date;
	}

	protected function action2(){
		$manual = array(
			'ID'	=> 'manual_0',
			'func'	=> '_manual',
			'color'	=> 'btn-default',
			'icon'	=> 'fa fa-gear',
			'label'	=> 'Manual',
			'type'	=> parent::$type
		);

		return _modal_button($manual);
	}

	public function _filter($date=''){
		ob_start();
		self::$type = $_POST['type'];
		$table = self::table($date);
		metronic_layout::sobad_table($table);
		return ob_get_clean();
	}

	public function _manual($id=0){
		$id = str_replace('manual_', '', $id);
		$vals = array($id,'');
		
		$args = array(
			'title'		=> 'Tambah aktifitas ganti jam (Manual)',
			'button'	=> '_btn_modal_save',
			'status'	=> array(
				'link'		=> '_add_manual',
				'load'		=> 'sobad_portlet'
			)
		);
		
		return punishment_absen::_manual_form($args,$vals);
	}

// --------------------------------------------------------------
// Database -----------------------------------------------------
// --------------------------------------------------------------	

	public function _history($id=0){
		$id = str_replace('history_', '', $id);
		intval($id);

		$type = $_POST['type'];
		if($type=='history_1'){
			return punishment_absen::_history($id);
		}

		//View Ganti Jam
		$args = sobad_logDetail::get_id($id,array('times','log_history'));
		$history = unserialize($args[0]['log_history']);

		if(isset($history['history'])){
			$history = $history['history'];
		}else{
			$history = array();
		}

		$data['class'] = '';
		$data['table'] = array();

		$no = 0;
		foreach ($history as $key => $val) {
			$no += 1;

			$_date = $val['date'];
			$note = isset($val['note']) || !empty($val['note'])?$val['note']:'Telah mengganti jam';

			$data['table'][$no-1]['tr'] = array('');
			$data['table'][$no-1]['td'] = array(
				'no'			=> array(
					'center',
					'5%',
					$no,
					true
				),
				'Actual'		=> array(
					'left',
					'15%',
					format_date_id($_date),
					true
				),
				'Waktu'		=> array(
					'left',
					'15%',
					$val['time'].' Menit',
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

		$extime = $args[0]['times'];
		if(isset($history['extime'])){
			$extime = $history['extime'];
		}

		$args = array(
			'title'		=> 'History (masih : '.$extime.' Menit )',
			'button'	=> '_btn_modal_save',
			'status'	=> array(),
			'func'		=> array('sobad_table'),
			'data'		=> array($data)
		);
		
		return modal_admin($args);
	}

	public function _add_manual($args=array()){
		$args = sobad_asset::ajax_conv_json($args);
		$users = explode(',', $args['user']);

		$waktu = date('H:i');
		$date = $args['date'];
		$strdate = strtotime($date);

		self::$type = 'history_2';
		$punish = $args['time'];
		foreach ($users as $ky => $vl) {
			self::_calc_gantiJam($vl,$punish,$args['note']);
		}

		$table = self::table();
		return table_admin($table);
	}

	public static function _calc_gantiJam($_id=0,$ganti=0,$note='Telah mengganti Jam'){
		$_logs = sobad_logDetail::get_all(array('ID','log_id','times','status','date_actual','log_history'),"AND _log_id.user='$_id' AND `abs-log-detail`.type_log='2' AND `abs-log-detail`.status!='1'");

		$_check = $ganti % 30;
		if($_check<=20){
			$ganti -= $_check;
		}else{
			$ganti += (30 - $_check);
		}

		foreach ($_logs as $key => $val) {
			if($ganti<=0){
				break;
			}

			// Tambah data history
			$history = unserialize($val['log_history']);
			if(!isset($history['history'])){
				$history = array();
				$history['history'] = array();
			}

			if(isset($history['extime'])){
				$val['times'] = $history['extime'];
			}

			$_status = 1;
			$_times = $val['times'];

		// Tambah data actual
			$_actual = '';
			$_actual = explode(',', $val['date_actual']);

			if(empty($_actual)){
				$_actual = array();
			}

			$_actual[] = date('Y-m-d');
			$_actual = implode(',', $_actual);

		// Check jam
			$ganti -= $val['times'];

			if($ganti<=0){
				$_status = 2;
				$_times = $val['times'];
				$history['extime'] = $ganti * -1;
			}else{
				$history['extime'] = 0;
			}

			$history['history'][] = array(
				'date'		=> date('Y-m-d'),
				'time'		=> $_times,
				'note'		=> $note
			);

			sobad_db::_update_single($val['ID'],'abs-log-detail',array(
				'date_actual'	=> $_actual,
				'log_history'	=> serialize($history),
				'status'		=> $_status
			));
		}
	}	
}