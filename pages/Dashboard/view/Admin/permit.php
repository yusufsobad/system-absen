<?php

class permit_absen extends _page{

	protected static $object = 'permit_absen';

	protected static $table = 'sobad_permit';

	// ----------------------------------------------------------
	// Layout category  ------------------------------------------
	// ----------------------------------------------------------

	protected function _array(){
		$args = array(
			'ID',
			'user',
			'start_date',
			'range_date',
			'type',
			'note'
		);

		return $args;
	}

	protected function table(){
		$data = array();
		$args = self::_array();

		$start = intval(parent::$page);
		$nLimit = intval(parent::$limit);
		
		$kata = '';$where = "";
		if(parent::$search){
			$src = parent::like_search($args,$where);	
			$cari = $src[0];
			$where = $src[0];
			$kata = $src[1];
		}else{
			$cari=$where;
		}
	
		$limit = 'LIMIT '.intval(($start - 1) * $nLimit).','.$nLimit;
		$where .= $limit;

		$object = self::$table;
		$args = $object::get_all($args,$where);
		$sum_data = $object::count("1=1 ".$cari);
		
		$data['data'] = array('data' => $kata);
		$data['search'] = array('Semua','nama');
		$data['class'] = '';
		$data['table'] = array();
		$data['page'] = array(
			'func'	=> '_pagination',
			'data'	=> array(
				'start'		=> $start,
				'qty'		=> $sum_data,
				'limit'		=> $nLimit
			)
		);

		$no = ($start-1) * $nLimit;
		foreach($args as $key => $val){
			$no += 1;
			$id = $val['ID'];

			$edit = array(
				'ID'	=> 'edit_'.$id,
				'func'	=> '_edit',
				'color'	=> 'blue',
				'icon'	=> 'fa fa-edit',
				'label'	=> 'edit'
			);
			
			$hapus = array(
				'ID'	=> 'del_'.$id,
				'func'	=> '_delete',
				'color'	=> 'red',
				'icon'	=> 'fa fa-trash',
				'label'	=> 'hapus',
			);

			$range = strtotime($val['range_date']) - strtotime($val['start_date']);
			$range = floor($range / (60 * 60 * 24));
			
			$data['table'][$key]['tr'] = array('');
			$data['table'][$key]['td'] = array(
				'no'		=> array(
					'center',
					'5%',
					$no,
					true
				),
				'name'		=> array(
					'left',
					'auto',
					$val['name_user'],
					true
				),
				'mulai'		=> array(
					'center',
					'18%',
					conv_day_id($val['start_date']).', '.format_date_id($val['start_date']),
					true
				),
				'sampai'	=> array(
					'center',
					'18%',
					conv_day_id($val['range_date']).', '.format_date_id($val['range_date']),
					true
				),
				'jenis'		=> array(
					'center',
					'10%',
					self::_conv_type($val['type']),
					true
				),
				'Lama'		=> array(
					'center',
					'10%',
					($range + 1).' hari',
					true
				),
				'Edit'		=> array(
					'center',
					'10%',
					edit_button($edit),
					false
				),
				'Hapus'			=> array(
					'center',
					'10%',
					hapus_button($hapus),
					false
				)
				
			);
		}
		
		return $data;
	}

	private function head_title(){
		$args = array(
			'title'	=> 'Izin <small>data izin</small>',
			'link'	=> array(
				0	=> array(
					'func'	=> self::$object,
					'label'	=> 'jabatan'
				)
			),
			'date'	=> false
		); 
		
		return $args;
	}

	protected function get_box(){
		$data = self::table();
		
		$box = array(
			'label'		=> 'Data Izin',
			'tool'		=> '',
			'action'	=> parent::action(),
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

	protected function _conv_type($id=0){
		$args = array(3 => 'Cuti', 'Izin', 'Luar Kota', 'Libur');
		return isset($args[$id])?$args[$id]:'';
	}

	// ----------------------------------------------------------
	// Form data category -----------------------------------
	// ----------------------------------------------------------
	public function add_form(){
		$vals = array(0,array(),date('d-m-Y'),date('d-m-Y'),1,'');
		$vals = array_combine(self::_array(),$vals);
		
		$args = array(
			'title'		=> 'Tambah data jabatan',
			'button'	=> '_btn_modal_save',
			'status'	=> array(
				'link'		=> '_add_db',
				'load'		=> 'sobad_portlet'
			)
		);
		
		return self::_data_form($args,$vals);
	}

	protected function edit_form($vals=array()){
		$check = array_filter($vals);
		if(empty($check)){
			return '';
		}
		
		$args = array(
			'title'		=> 'Edit data jabatan',
			'button'	=> '_btn_modal_save',
			'status'	=> array(
				'link'		=> '_update_db',
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

		$user = sobad_user::get_employees(array('ID','name'));
		$user = convToOption($user,'ID','name');

		$intern = sobad_user::get_internships(array('ID','name'));
		$intern = convToOption($intern,'ID','name');

		$group = array_merge($user,$intern);

		$data = array(
			0 => array(
				'func'			=> 'opt_hidden',
				'type'			=> 'hidden',
				'key'			=> 'ID',
				'value'			=> $vals['ID']
			),
			array(
				'func'			=> 'opt_select_tags',
				'data'			=> $group,
				'key'			=> 'user',
				'label'			=> 'Nama',
				'class'			=> 'input-circle',
				'select'		=> $vals['user']
			),
			array(
				'func'			=> 'opt_datepicker',
				'key'			=> 'start_date',
				'label'			=> 'Tanggal',
				'class'			=> 'input-circle',
				'value'			=> $vals['start_date']
			),
			array(
				'func'			=> 'opt_select',
				'data'			=> array(3 => 'Cuti', 'Izin', 'Luar Kota', 'Libur'),
				'key'			=> 'type',
				'label'			=> 'Jenis',
				'class'			=> 'input-circle',
				'select'		=> $vals['type'],
				'status'		=> ''
			),
			array(
				'func'			=> 'opt_textarea',
				'key'			=> 'note',
				'label'			=> 'Catatan',
				'class'			=> 'input-circle',
				'value'			=> $vals['note'],
				'data'			=> 'placeholder="Catatan"',
				'rows'			=> 4
			),
		);
		
		$args['func'] = array('sobad_form');
		$args['data'] = array($data);
		
		return modal_admin($args);
	}

	protected function _callback($args=array()){
		$users = explode(',', $args['user']);

		foreach ($users as $key => $val) {
			
		}
	}
}