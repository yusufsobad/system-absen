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

			$masuk = 'Masuk';$pulang = 'Pulang';
			if(self::$type=='history_2'){
				$masuk = 'Keluar';$pulang = 'Kembali';
				$history = unserialize($val['history_log_']);

				$val['time_in_log_'] = '-';
				$val['time_out_log_'] = '-';

				if(isset($history['logs'])){
					foreach ($history['logs'] as $ky => $vl) {
						if($vl['type']=='4'){
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
				'Waktu'	=> array(
					'left',
					'10%',
					$val['times'] .' '. $note,
					true
				)
			);
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


		$box = array(
			'label'		=> 'History '.$label,
			'tool'		=> '',
			'action'	=> $action = $type==1?self::action():'',
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

	public function _filter($date=''){
		ob_start();
		self::$type = $_POST['type'];
		$table = self::table($date);
		metronic_layout::sobad_table($table);
		return ob_get_clean();
	}
}