<?php

class lembur_user extends _page{

	protected static $object = 'lembur_user';

	protected static $table = 'sobad_overtime';

	// ----------------------------------------------------------
	// Layout category  ------------------------------------------
	// ----------------------------------------------------------

	protected function _array(){
		$args = array(
			'ID',
			'title',
			'post_date',
			'note',
			'id_join',
			'user_id',
			'start_time',
			'finish_time',
			'status'
		);

		return $args;
	}

	protected function table(){
		$data = array();
		$args = self::_array();

		$now = date('Y-m-d');
		$user_id = get_id_user();

		if($user_id>0){
			$where = "AND `abs-overtime-detail`.user_id='$user_id'";
		}else{
			$where = "AND `abs-overtime`.post_date>='$now'";
		}

		$object = self::$table;
		$args = $object::get_all($args,$where);

		$data['class'] = '';
		$data['table'] = array();

		$no = 0;
		foreach($args as $key => $val){
			$no += 1;
			$idx = $val['id_join'];

			$status = $val['status']>0?'disabled':'';
			$accept = array(
				'ID'	=> 'acpt_'.$idx,
				'func'	=> '_accept_form',
				'color'	=> 'blue',
				'icon'	=> 'fa fa-check',
				'label'	=> 'Sedia',
				'status'=> $status,
				'type'	=> $val['ID']
			);

			$tanggal = $val['post_date'];
			$tanggal = conv_day_id($tanggal).', '.format_date_id($tanggal);

			$color = '#666';
			if($val['status']==1){
				$color = '#26a69a';
			}else if($val['status']==2){
				$color = '#cb5a5e';
			}
			$status = '<i class="fa fa-circle" style="color:'.$color.'">';

			$name = sobad_user::get_id($val['user_id'],array('name'));
			$name = $name[0]['name'];			
			
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
					'15%',
					$tanggal,
					true
				),
				'Nama'		=> array(
					'left',
					'15%',
					$name,
					true
				),
				'Mulai'			=> array(
					'left',
					'10%',
					format_time_id($val['start_time']),
					true
				),
				'Selesai'		=> array(
					'left',
					'10%',
					format_time_id($val['finish_time']),
					true
				),
				'Keterangan'	=> array(
					'left',
					'auto',
					$val['note'],
					true
				),
				'Status'		=> array(
					'center',
					'8%',
					$status,
					true
				),
				'Form'		=> array(
					'center',
					'10%',
					_modal_button($accept),
					false
				),
			);

			if($user_id>0){
				unset($data['table'][$key]['td']['Nama']);
			}
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

	// ----------------------------------------------------------
	// Form data Lembur -----------------------------------------
	// ----------------------------------------------------------
	
	public function _accept_form($id=0){
		$data = self::_data_form($id);
		
		$args = array(
			'title'		=> 'Tambah data lembur',
			'button'	=> '_btn_modal_save',
			'status'	=> array(
				'link'		=> '_update_lembur',
				'load'		=> 'sobad_portlet'
			),
			'func'		=> array('sobad_form'),
			'data'		=> array($data)
		);
		
		return modal_admin($args);
	}

	private function _data_form($id=0){
		$id = str_replace('acpt_', '', $id);
		intval($id);

		$args = sobad_overtime::get_detail($id,array('user_id','status','notes'));
		$args = $args[0];

		$data = array(
			0 => array(
				'func'			=> 'opt_hidden',
				'type'			=> 'hidden',
				'key'			=> 'ID',
				'value'			=> $id
			),
			array(
				'func'			=> 'opt_hidden',
				'type'			=> 'hidden',
				'key'			=> 'reff',
				'value'			=> $_POST['type']
			),
			array(
				'func'			=> 'opt_hidden',
				'type'			=> 'hidden',
				'key'			=> 'user_id',
				'value'			=> $args['user_id']
			),
			array(
				'func'			=> 'opt_box',
				'type'			=> 'radio',
				'key'			=> 'status',
				'label'			=> 'Pilihan',
				'inline'		=> true,
				'value'			=> $args['status'],
				'data'			=> array(
					0	=> array(
						'title'		=> 'Terima',
						'value'		=> 1
					),
					1	=> array(
						'title'		=> 'Tolak',
						'value'		=> 2
					),
				)
			),
			array(
				'func'			=> 'opt_textarea',
				'key'			=> 'notes',
				'label'			=> 'Keterangan Menolak',
				'class'			=> 'input-circle',
				'value'			=> $args['notes'],
				'data'			=> 'placeholder="Keterangan Tolak"',
				'rows'			=> 4
			),
		);
		
		return $data;
	}

	// ----------------------------------------------------------
	// Database Lembur ------------------------------------------
	// ----------------------------------------------------------

	public static function _update_lembur($args=array()){
		$args = sobad_asset::ajax_conv_json($args);

		// Buat Log Lembur
		if($args['status']==1){
			$user_id = $args['user_id'];
			$where = "AND `abs-overtime-detail`.user_id='$user_id'";

			$object = self::$table;
			$log = $object::get_id($args['reff'],array('post_date','note','start_time','finish_time'),$where);
			$log = $log[0];

			$logs = array(
				'user'	=> $user_id,
				'note'	=> $log['note']
			);

			$time = _conv_time($log['start_time'],$log['finish_time'],3);

			$q = sobad_db::_insert_table('abs-log-detail',array(
				'log_id'		=> $user_id,
				'times'			=> $time,
				'date_schedule'	=> $log['post_date'],
				'log_history'	=> serialize($logs),
				'type_log'		=> 4
			));
		}

		$q = sobad_db::_update_single($args['ID'],'abs-overtime-detail',array(
			'status'	=> $args['status'],
			'notes'		=> $args['notes']
		));

		if($q!==0){
			$table = self::table();
			return table_admin($table);
		}
	}	
}