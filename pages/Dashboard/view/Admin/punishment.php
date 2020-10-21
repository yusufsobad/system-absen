<?php

class punishment_absen extends _page{

	protected static $object = 'punishment_absen';

	protected static $table = 'sobad_user';

	// ----------------------------------------------------------
	// Layout category  ------------------------------------------
	// ----------------------------------------------------------

	protected function table(){
		$date = date('Y-m');

		$object = self::$table;
		$args = $object::get_late($date);
		
		$data['class'] = '';
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
		
		$box = array(
			'label'		=> 'Data punishment',
			'tool'		=> '',
			'action'	=> '',
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

		$log = sobad_user::get_logs(array('note'),"ID='$id'");
		if(empty($log[0]['note'])){
			$note = array('permit' => $args['note']);
			$note = serialize($note);
		}else{
			$note = unserialize($log[0]['note']);
			$note['permit'] = $args['note'];
			$note = serialize($note);
		}

		$data = array(
			'type'		=> 4,
			'note'		=> $note
		);

		$q = sobad_db::_update_single($id,'abs-user-log',$data);
		
		if($q!==0){
			$table = self::table();
			return table_admin($table);
		}
	}
}