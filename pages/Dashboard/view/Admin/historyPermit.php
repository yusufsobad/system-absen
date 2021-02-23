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

	protected function table($now=''){
		$data = array();
		$args = self::_array();

		$start = intval(parent::$page);
		$nLimit = intval(parent::$limit);

		$now = strtotime($now);
		$y = date('Y',$now);
		$m = date('m',$now);

		switch (parent::$type) {
			case 'history_3':
				$where = "AND type='3'";
				break;

			case 'history_4':
				$where = "AND (type='3' OR type>'10')";
				break;

			case 'history_5':
				$where = "AND type='5'";
				break;
			
			default:
				$where = "AND type NOT IN (9)";
				break;
		}

		$where .= " AND ((YEAR(start_date)='$y' AND MONTH(start_date)='$m') OR (YEAR(range_date)='$y' AND MONTH(range_date)='$m'))";
		
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
		$sum_data = $object::count("1=1 ".$cari,self::_array());
		
		$data['data'] = array('data' => $kata,'type' => parent::$type);
		$data['search'] = array('Semua','nama');
		$data['class'] = '';
		$data['table'] = array();

		$no = 0;
		foreach($args as $key => $val){
			$no += 1;
			$id = $val['ID'];

			$sts_day = 'hari';
			if($val['range_date']=='0000-00-00'){
				$val['range_date'] = date('Y-m-d');
			}

			$range = strtotime($val['range_date']) - strtotime($val['start_date']);
			$range = floor($range / (60 * 60 * 24));

			if($val['num_day']>0){
				$range = $val['num_day']-1;
				
				switch ($val['type_date']) {

					case 2:
						$sts_day = 'bulan';
						$_num = $range.' months';
						$val['range_date'] = _calc_date($val['start_date'],'+'.$range.' months');
						break;

					case 3:
						$sts_day = 'tahun';
						$_num = $range.' years';
						$val['range_date'] = _calc_date($val['start_date'],'+'.$range.' years');
						break;

					default:
						$_range = $range;
						if($val['num_day']==0.5){
							$_range = 0;
						}

						$sts_day = 'hari kerja';
						$val['range_date'] = _calc_date($val['start_date'],'+'.$_range.' days');

						$_num = $range.' days';
						break;
				}

				//$range_date = strtotime($val['start_date']);
				//$val['range_date'] = date('Y-m-d',strtotime('+'.$_num,$range_date));
			}

			if($val['type_date']<2){
				$_num = ceil($range);
				$_date = strtotime($val['start_date']);
				for($i=0;$i<$_num;$i++){
					$_date = strtotime("+".$i." days",$_date);
					$_check = holiday_absen::_check_holiday(date('Y-m-d',$_date));
					if($_check){
						$range -= 1;
					}
				}
			}
			
			$data['table'][$key]['tr'] = array('');
			$data['table'][$key]['td'] = array(
				'No'		=> array(
					'center',
					'5%',
					$no,
					true
				),
				'Name'		=> array(
					'left',
					'auto',
					$val['name_user'],
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
					'center',
					'15%',
					permit_absen::_conv_type($val['type']),
					true
				),
				'Lama'		=> array(
					'center',
					'10%',
					($range + 1).' '.$sts_day,
					true
				),
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
// Form Ganti Jam -----------------------------------------------
// --------------------------------------------------------------


// --------------------------------------------------------------
// Database -----------------------------------------------------
// --------------------------------------------------------------	
	public function _preview($args=array()){
		$_SESSION[_prefix.'development'] = 1;
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
		$data = self::table($now);

		$now = strtotime($now);
		$dateM = date('m',$now);
		$dateY = date('Y',$now);

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

		unset($data['data']);
		unset($data['search']);
		?>
			<page backtop="5mm" backbottom="5mm" backleft="5mm" backright="5mm" pagegroup="new">
				<div style="text-align:center;width:100%;">
					<h2 style="margin-bottom: 0px;"> <?php print($title) ;?> </h2>
					<h3 style="margin-top: 0px;">Bulan <u>Absensi</u>: <?php echo conv_month_id($dateM).' '.$dateY ;?></h3>
				</div><br>
			<?php
				metronic_layout::sobad_table($data);
			?>
			</page>
		<?php
	}
}