<?php

class report_absen extends _page{

	protected static $object = 'report_absen';

	protected static $table = 'sobad_user';

	// ----------------------------------------------------------
	// Layout category  -----------------------------------------
	// ----------------------------------------------------------

	protected function table(){
		$date = date('Y-m');

		$object = self::$table;
		$args = $object::get_all(array('ID','no_induk','_nickname','time_in','time_out','_inserted'),"AND `abs-user`.ID='7'");
		
		$data['class'] = 'Absensi';
		$data['table'] = array();

		foreach($args as $key => $val){
			$permit = array(
				'ID'	=> 'permit_'.$val['ID'],
				'func'	=> '_permit',
				'color'	=> 'green',
				'icon'	=> 'fa fa-recycle',
				'label'	=> 'Izin',
			);

			$data['table'][$key]['tr'] = array('');
			$data['table'][$key]['td'] = array(
				'Tanggal'		=> array(
					'left',
					'15%',
					$val['_inserted'],
					true
				),
				'NIK'			=> array(
					'left',
					'5%',
					$val['no_induk'],
					true
				),
				'Nama'			=> array(
					'left',
					'auto',
					$val['_nickname'],
					true
				),
				'Masuk'			=> array(
					'center',
					'10%',
					$val['time_in'],
					true
				),
				'Pulang'		=> array(
					'center',
					'10%',
					$val['time_out'],
					true
				),
				'Status'		=> array(
					'left',
					'10%',
					'',
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

	private function head_title(){
		$args = array(
			'title'	=> 'Absensi <small>data absen</small>',
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
		
		$box = array(
			'label'		=> 'Data Absen '.conv_month_id(date('m')).' '.date('Y'),
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
			'style'		=> array(),
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
		
		return '';//apply_button($import);
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
		$id = str_replace('permit_', '', $id);
		$vals = array($id,'');
		
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

		$files = self::_convert_column($files);	
		$data = employee_absen::_conv_import($files);
		return $data['insert'] = true;
	}

	private function _convert_column($files=array()){
		$data = array();

		$args = array(
			'_inserted'		=> array(
				'data'			=> array('tanggal'),
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

					unset($files[$ky]);
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