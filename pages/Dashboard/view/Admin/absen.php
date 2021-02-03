<?php

class report_absen extends _page{

	protected static $object = 'report_absen';

	protected static $table = 'sobad_user';

	// ----------------------------------------------------------
	// Layout category  -----------------------------------------
	// ----------------------------------------------------------

	protected function table($filter='',$date=''){
		$date = empty($date)?date('Y-m'):$date;
		$date = strtotime($date);

		$whr = "AND `abs-user`.status!='0' ";$width = '400px';
		if(!empty($filter)){
			$whr .= "AND `abs-user`.ID IN ($filter)";

			$count = explode(',', $filter);
			if(count($count)<3){
				$width = '200px';
			}
		}

		$object = self::$table;
		$users = $object::get_all(array('ID','no_induk','name','work_time'),$whr);
		
		$data['class'] = 'absensi';
		$data['table'] = array();

		$sum_days = sum_days(date('m'),date('Y'));

	// Title Table
		$data['table'][0]['tr'] = array('');
		$data['table'][0]['td'] = array(
			'Tanggal'		=> array(
				'left',
				$width,
				'Tanggal',
				true,
				1,
				2
			)
		);

		foreach($users as $key => $val){
			$data['table'][0]['td'][$val['no_induk']] = array(
				'center',
				'200px',
				$val['no_induk'],
				true
			);

			$data['table'][0]['td'][$val['name']] = array(
				'center',
				'200px;min-width:414px',
				$val['name'],
				true,
				3
			);

			$data['table'][1]['td']['Masuk_'.$val['ID']] = array(
				'center',
				'200px',
				'Masuk',
				true
			);

			$data['table'][1]['td']['Pulang_'.$val['ID']] = array(
				'center',
				'200px',
				'Pulang',
				true
			);

			$data['table'][1]['td']['Status_'.$val['ID']] = array(
				'left',
				'100px',
				'Status',
				true
			);

			$data['table'][1]['td']['Button_'.$val['ID']] = array(
				'center',
				'100px',
				'Button',
				false
			);
		}

		$default = date('Y',$date).'-'.date('m',$date).'-01';
		$default = strtotime($default);

		$before = strtotime('-1 days',$default);
		$before = date('d',$before);
		$before = 28 - intval($before);

		$_no = 1;
		for($i=$before;$i<28;$i++){
			$_no += 1;
			$now = date('Y-m-d',strtotime($i.' days',$default));
			$tanggal = format_date_id($now);

			$holiday =holiday_absen::_check_holiday($now);
			if($holiday){
				$tanggal = '<div style="color:red">'.format_date_id($now).'</div>';
			}

			$data['table'][$_no]['tr'] = array('');
			$data['table'][$_no]['td'] = array(
				'Tanggal'		=> array(
					'left',
					'400px',
					$tanggal,
					true
				)
			);

			foreach($users as $_ky => $_vl){
				$userid = $_vl['ID'];
				$id_date = date('Ymd',strtotime($now));
				$args = $object::get_logs(array('ID','type','time_in','time_out','_inserted'),"user='$userid' AND _inserted='$now'");
				$check = array_filter($args);

				$val = array(
					'time_in'	=> '',
					'time_out'	=> '',
					'status'	=> '',
				);

				$permit = array(
					'ID'	=> 'permit_'.$userid.'_'.$id_date,
					'func'	=> '_permit',
					'color'	=> 'green',
					'icon'	=> 'fa fa-recycle',
					'label'	=> 'Izin',
				);

				$button = _modal_button($permit);

				if(!empty($check)){
					$_permit = sobad_permit::get_all(array('type'),"AND user='$userid' AND (start_date<='$now' AND range_date>='$now')");
					$check = array_filter($_permit);
					if(!empty($check)){
						$args[0]['type'] = $_permit[0]['type'];
					}

					$status = permit_absen::_conv_type($args[0]['type']);
					if(empty($status)){
						if($args[0]['type']==0){
							$status = 'Alpha';
						}

						if($args[0]['type']==7){
							$status = 'Tidak Absen';
						}
					}

					$val = array(
						'time_in'	=> $args[0]['time_in'],
						'time_out'	=> $args[0]['time_out'],
						'status'	=> $status
					);		

					$button = '';		
				}else{
					//Check Permit
					$permit = sobad_permit::get_all(array('ID','type','note'),"AND user='$userid' AND type!='9' AND start_date<='$now' AND range_date>='$now' OR user='$userid' AND start_date<='$now' AND range_date='0000-00-00' AND num_day='0.0'");

					$check = array_filter($permit);
					if(!empty($check)){

						//Check Jam Kerja
						$shift = sobad_permit::get_all(array('note'),"AND user='$userid' AND type='9' AND start_date<='$now' AND range_date>='$now'");
							
						$check = array_filter($shift);
						if(!empty($check)){
							$worktime = $shift[0]['note'];
						}else{
							$worktime = $_vl['work_time'];
						}
					/*
						sobad_db::_insert_table('abs-user-log',array(
							'user' 		=> $userid,
							'type'		=> $permit[0]['type'],
							'shift'		=> $worktime,
							'_inserted'	=> $now,
							'note'		=> serialize(array('permit' => $permit[0]['note']))
						));
					*/
						$val = array(
							'time_in'	=> '00:00:00',
							'time_out'	=> '00:00:00',
							'status'	=> permit_absen::_conv_type($permit[0]['type'])
						);		
					}
				}

				if($holiday){
					$button = '';
				}	

				$data['table'][$_no]['td']['Masuk_'.$userid] = array(
					'center',
					'200px',
					$val['time_in'],
					true
				);

				$data['table'][$_no]['td']['Pulang_'.$userid] = array(
					'center',
					'200px',
					$val['time_out'],
					true
				);

				$data['table'][$_no]['td']['Status_'.$userid] = array(
					'left',
					'100px',
					$val['status'],
					true
				);

				$data['table'][$_no]['td']['Button_'.$userid] = array(
					'center',
					'100px',
					$button,
					false
				);		
			}
		}

		return $data;
	}

	private function head_title(){
		$args = array(
			'title'	=> 'Absensi <small>data absen</small>',
			'link'	=> array(
				0	=> array(
					'func'	=> self::$object,
					'label'	=> 'absen'
				)
			),
			'date'	=> false
		); 
		
		return $args;
	}

	protected function get_box(){
		//$data = self::table();
		
		$box = array(
			'label'		=> 'Data Absen '.conv_month_id(date('m')).' '.date('Y'),
			'tool'		=> '',
			'action'	=> self::action(),
			'object'	=> self::$object,
			'func'		=> 'display_absen',
			'data'		=> ''
		);

		return $box;
	}

	protected function layout(){
		$box = self::get_box();
		
		$opt = array(
			'title'		=> self::head_title(),
			'style'		=> array(self::$object,'_style'),
			'script'	=> array('')
		);
		
		return portlet_admin($opt,$box);
	}

	protected function action(){
		$import = array(
			'ID'	=> 'import_0',
			'func'	=> 'import_form',
			'color'	=> 'btn-default',
			'load'	=> 'here_modal2',
			'icon'	=> 'fa fa-file-excel-o',
			'label'	=> 'Import Data Absen',
			'spin'	=> false
		);

		$excel = array(
			'ID'	=> 'excel_0',
			'func'	=> '_export_excel',
			'color'	=> 'btn-default',
			'icon'	=> 'fa fa-file-excel-o',
			'label'	=> 'Export'
		);
		
		return apply_button($import).' '.print_button($excel);
	}

	public function display_absen(){
		$user = sobad_user::get_all(array('ID','name'));
		$user = convToOption($user,'ID','name');

		$button = array(
			'ID'	=> 'filter_0',
			'func'	=> '_filter',
			'class'	=> '',
			'color'	=> 'green',
			'icon'	=> 'fa fa-filter',
			'label'	=> 'Filter',
			'load'	=> 'table_absensi'
		);

		$form = array(
			'cols'	=> array(2,9),
			0 => array(
				'id'			=> 'monthpicker',
				'func'			=> 'opt_input',
				'type'			=> 'text',
				'key'			=> 'date',
				'label'			=> 'Tanggal',
				'class'			=> 'input-circle',
				'value'			=> date('Y-m'),
				'data'			=> ''
			),
			array(
				'func'			=> 'opt_select_tags',
				'data'			=> $user,
				'key'			=> 'user',
				'label'			=> 'Karyawan',
				'class'			=> 'input-circle',
				'select'		=> array(),
				'button'		=> _click_button($button)
			)
		);

		$data = self::table();

		echo '<div class="row">';
			metronic_layout::sobad_form($form);

			echo '<div id="table_absensi" class="col-md-12">';
			metronic_layout::sobad_table($data);
			self::_script();
			echo '</div>';
		echo '</div>';
	}

	public function _filter(){
		$args = $_POST['args'];
		$args = sobad_asset::ajax_conv_json($args);

		$data = self::table($args['user'],$args['date']);
		ob_start();
		metronic_layout::sobad_table($data);
		self::_script();
		return ob_get_clean();
	}

	public function _style(){
		?>
			<style type="text/css">
				#table_absensi .table_flexible {
				  height:350px; 
				  width:100%;
				  overflow: hidden;
				}

				#table_absensi table {
				  position: relative;
				  table-layout: fixed;
				  display: flex;
				  flex-direction: column;
				  height: 100%;
				  width: 100%;
				}


				/*thead*/
				#table_absensi thead {
				  position: relative;
				  display: block; /*seperates the header from the body allowing it to be positioned*/
				}

				#table_absensi thead th {
				  min-width: 120px;
				}

				#table_absensi thead th:nth-child(1) {/*first cell in the header*/
				  position: relative;
				  background-color: #fff;
				  z-index: 10;
				}


				/*tbody*/
				#table_absensi tbody {
				  flex: 1;
				  position: relative;
				  display: block; /*seperates the tbody from the header*/
				  overflow: auto;
				}

				#table_absensi tbody td {
				  min-width: 130px;
				}

				#table_absensi tbody tr:nth-child(2) td:nth-child(1){
					position: unset !important;
				}

				#table_absensi tbody tr td:nth-child(1) {  /*the first cell in each tr*/
				  position: relative;
				  background-color: #fff;
				  z-index: 10;
				}
			</style>
		<?php
	}

	public function _script(){
		?>
			<script type="text/javascript">
				$(document).ready(function() {
				  $('#table_absensi tbody').scroll(function(e) { //detect a scroll event on the tbody
				  	/*
				    Setting the thead left value to the negative valule of tbody.scrollLeft will make it track the movement
				    of the tbody element. Setting an elements left value to that of the tbody.scrollLeft left makes it maintain 			it's relative position at the left of the table.    
				    */
				    $('#table_absensi thead').css("left", -$("#table_absensi tbody").scrollLeft()); //fix the thead relative to the body scrolling
				    $('#table_absensi thead th:nth-child(1)').css("left", $("#table_absensi tbody").scrollLeft()); //fix the first cell of the header
				    $('#table_absensi tbody tr:not(#table_absensi tbody tr:nth-child(2)) td:nth-child(1)').css("left", $("#table_absensi tbody").scrollLeft()); //fix the first column of tdbody
				  });
				});

				if(jQuery().datepicker) {
		            $("#monthpicker").datepicker( {
					    format: "yyyy-mm",
					    viewMode: "months", 
					    minViewMode: "months",
					    rtl: Metronic.isRTL(),
			            orientation: "left",
			            autoclose: true
					});
		        };
			</script>
		<?php
	}

	// ----------------------------------------------------------
	// Form data absen ------------------------------------------
	// ----------------------------------------------------------

	public function import_form(){
		$data = array(
			'id'	=> 'importForm',
			'cols'	=> array(3,8),
			0 => array(
				'func'			=> 'opt_hidden',
				'type'			=> 'hidden',
				'key'			=> 'ajax',
				'value'			=> '_import'
			),
			array(
				'func'			=> 'opt_hidden',
				'type'			=> 'hidden',
				'key'			=> 'object',
				'value'			=> self::$object
			),
			array(
				'id'			=> 'file_import',
				'func'			=> 'opt_file',
				'type'			=> 'file',
				'key'			=> 'data',
				'label'			=> 'Filename',
				'accept'		=> '.csv',
				'data'			=> ''
			)
		);
		
		$args = array(
			'title'		=> 'Import Karyawan',
			'button'	=> '_btn_modal_import',
			'status'	=> array(
				'id'		=> 'importForm',
				'link'		=> 'import_file',
				'load'		=> 'sobad_portlet',
				'type'		=> $_POST['type']
			)
		);
		
		$args['func'] = array('sobad_form');
		$args['data'] = array($data);
		
		return modal_admin($args);
	}

	public static function _permit($id=0){
		$data = str_replace('permit_', '', $id);
		$data = explode('_', $data);

		$vals = array($data[0],$data[1],'');
		
		$args = array(
			'title'		=> 'Alasan Tidak Absen',
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

		$user = sobad_user::get_id($vals[0],array('name'));

		$data = array(
			0 => array(
				'func'			=> 'opt_hidden',
				'type'			=> 'hidden',
				'key'			=> 'ID',
				'value'			=> $vals[0]
			),
			array(
				'func'			=> 'opt_hidden',
				'type'			=> 'hidden',
				'key'			=> '_inserted',
				'value'			=> $vals[1]
			),
			array(
				'func'			=> 'opt_input',
				'type'			=> 'text',
				'key'			=> 'name',
				'label'			=> 'Nama',
				'class'			=> 'input-circle',
				'value'			=> $user[0]['name'],
				'data'			=> 'readonly'
			),
			array(
				'func'			=> 'opt_input',
				'type'			=> 'text',
				'key'			=> 'date',
				'label'			=> 'Alasan',
				'class'			=> 'input-circle',
				'value'			=> format_date_id($vals[1]),
				'data'			=> 'readonly'
			),
			array(
				'func'			=> 'opt_select',
				'data'			=> array(0 => 'Alpha',7 => 'Tidak Absen', 3 => 'Cuti', 'Izin', 'Luar Kota','Libur'),
				'key'			=> 'type',
				'label'			=> 'Status',
				'class'			=> 'input-circle',
				'select'		=> 0,
				'status'		=> ''
			),
			array(
				'func'			=> 'opt_input',
				'type'			=> 'text',
				'key'			=> 'note',
				'label'			=> 'Alasan',
				'class'			=> 'input-circle',
				'value'			=> '',
				'data'			=> 'placeholder="Alasan"'
			)
		);
		
		$args['func'] = array('sobad_form');
		$args['data'] = array($data);
		
		return modal_admin($args);
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
		
		//Check Jam Kerja
		$date = date('Y-m-d',strtotime($args['_inserted']));
		
		$users = sobad_user::get_id($id,array('work_time','dayOff'));
		$shift = sobad_permit::get_all(array('note'),"AND user='$id' AND type='9' AND start_date<='$date' AND range_date>='$date'");
			
		$check = array_filter($shift);
		if(!empty($check)){
			$worktime = $shift[0]['note'];
		}else{
			$worktime = $users[0]['work_time'];
		}

		if($args['type']==3){
			$dayoff = $users[0]['dayOff'] - 1;
			if($dayoff<0){
				$args['type'] = 4;

				$args['note'] = $args['note'].' \r\n ::Sisa Cuti Tidak Cukup';
			}else{
				sobad_db::_update_single($id,'abas-user',array('ID' => $id,'dayOff' => $dayoff));
			}
		}

		$note = array('absen' => $args['note']);
		$note = serialize($note);

		$data = array(
			'user'		=> $id,
			'shift'		=> $worktime,
			'type'		=> $args['type'],
			'note'		=> $note,
			'_inserted'	=> $date
		);

		$q = sobad_db::_insert_table('abs-user-log',$data);

		if($q!==0){
			$table = self::table();
			return table_admin($table);
		}
	}

	public function _export_excel($data=array()){
		$args = sobad_asset::ajax_conv_json($data);
		$date = $args['date'];
		$_date = strtotime($date);

		$month = conv_month_id(date('m',$_date));
		$year = date('Y',$_date);
		$_date = $month.' '.$year;

		ob_start();
		header("Content-type: application/vnd-ms-excel");
		header("Content-Disposition: attachment; filename=Data Absen ".$_date.".xls");

		metronic_layout::sobad_table(self::table('',$date));
		return ob_get_clean();
	}

	// ----------------------------------------------------------
	// Function absen to database -------------------------------
	// ----------------------------------------------------------

	protected function _check_import($files=array()){
		$check = array_filter($files);
		if(empty($check)){
			return array(
				'status'	=> false,
				'data'		=> $files,
				'insert'	=> false
			);
		}

		if(!empty($files['scan masuk'])){
			$files = self::_convert_column($files);
			$induk = employee_absen::_check_noInduk($files['no_induk']);
			$user = sobad_user::get_all(array('ID','work_time'),$induk['where']);

			$check = array_filter($user);
			if(!empty($check)){
				// Check user absen
				$user = $user[0];
				$_idx = $user['ID'];
				$_date = $files['_inserted'];

				$_log = sobad_user::get_logs(array('ID','time_in'),"user='$_idx' AND _inserted='$_date'");
				$check = array_filter($_log);

				if(empty($check)){
					$args = array(
						'user'		=> $user['ID'],
						'shift'		=> $user['work_time'],
						'type'		=> 1,
						'_inserted'	=> $files['_inserted'],
						'time_in'	=> $files['time_in'],
						'note'		=> serialize(array('note' => 'import absen')),
						'history'	=> serialize(array(0 => array('type' => 1,'time' => $files['time_in'])))
					);

					sobad_db::_insert_table('abs-user-log',$args);
				}
			}
		}
		
		return array('data' => array(),'status' => false,'insert' => false);
	}

	private function _convert_column($files=array()){
		$data = array();

		$args = array(
			'_inserted'		=> array(
				'data'			=> array('scan masuk'),
				'type'			=> 'date'
			),
			'time_in'		=> array(
				'data'			=> array('scan masuk'),
				'type'			=> 'time'
			),
			'time_out'			=> array(
				'data'			=> array('scan pulang'),
				'type'			=> 'time'
			),
			'no_induk'		=> array(
				'data'			=> array('nip'),
				'type'			=> 'text'
			),
			'name'		=> array(
				'data'			=> array('nama'),
				'type'			=> 'text'
			),		
		);

		foreach ($args as $key => $val) {
			foreach ($files as $ky => $vl) {
				$_data = '';
				if(in_array($ky, $val['data'])){
					$_data = self::_filter_column($key,$vl,$val['type']);
					$data = array_merge($data,$_data);

					//unset($files[$ky]);
					break;
				}
			}
		}

		return $data;
	}

	private function _filter_column($key='',$_data='',$type=''){
		$data[$key] = formatting::sanitize($_data,$type);
		return $data;
	}
}