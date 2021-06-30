<?php

class divisi_absen extends _page{

	protected static $object = 'divisi_absen';

	protected static $table = 'sobad_module';

	// ----------------------------------------------------------
	// Layout category  ------------------------------------------
	// ----------------------------------------------------------

	protected function _array(){
		$args = array(
			'ID',
			'meta_value',
			'meta_note',
			'meta_key'
		);

		return $args;
	}

	protected function table(){
		$data = array();
		$args = self::_array();

		$start = intval(parent::$page);
		$nLimit = intval(parent::$limit);
		
		$kata = '';$where = "AND meta_key='department'";
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

			$whr = "(divisi='$id' AND status!='7' AND end_status='0')";
			$qty = sobad_user::count($whr);

			$edit = array(
				'ID'	=> 'edit_'.$id,
				'func'	=> '_edit',
				'color'	=> 'blue',
				'icon'	=> 'fa fa-edit',
				'label'	=> 'edit'
			);

			$detail = array(
				'ID'	=> 'detail_'.$id,
				'func'	=> '_detail',
				'color'	=> '',
				'icon'	=> '',
				'label'	=> $qty
			);
			
			$hapus = array(
				'ID'	=> 'del_'.$id,
				'func'	=> '_delete',
				'color'	=> 'red',
				'icon'	=> 'fa fa-trash',
				'label'	=> 'hapus',
			);
			
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
					$val['meta_value'],
					true
				),
				'Status'	=> array(
					'left',
					'25%',
					empty($val['meta_note'])?'user':'admin',
					true
				),
				'Jumlah'	=> array(
					'center',
					'10%',
					_modal_button($detail),
					true
				),
				'Edit'			=> array(
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
			'title'	=> 'Jabatan <small>data jabatan</small>',
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
			'label'		=> 'Data Jabatan',
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

	// ----------------------------------------------------------
	// Form data category -----------------------------------
	// ----------------------------------------------------------
	public function add_form($func='',$load='sobad_portlet'){
		$vals = array(0,0,'','department');
		$vals = array_combine(self::_array(),$vals);

		if($func=='add_0'){
			$func = '_add_db';
		}
		
		$args = array(
			'title'		=> 'Tambah data jabatan',
			'button'	=> '_btn_modal_save',
			'status'	=> array(
				'link'		=> $func,
				'load'		=> $load
			)
		);
		
		return self::_data_form($args,$vals);
	}

	protected function edit_form($vals=array()){
		$check = array_filter($vals);
		if(empty($check)){
			return '';
		}
		
		$vals['meta_note'] = empty($vals['meta_note'])?0:1;
		
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

		$data = array(
			0 => array(
				'func'			=> 'opt_hidden',
				'type'			=> 'hidden',
				'key'			=> 'ID',
				'value'			=> $vals['ID']
			),
			array(
				'func'			=> 'opt_hidden',
				'type'			=> 'hidden',
				'key'			=> 'meta_key',
				'value'			=> $vals['meta_key']
			),
			array(
				'func'			=> 'opt_input',
				'type'			=> 'text',
				'key'			=> 'meta_value',
				'label'			=> 'Jabatan',
				'class'			=> 'input-circle',
				'value'			=> $vals['meta_value'],
				'data'			=> 'placeholder="Nama jabatan"'
			),
		);
		
		$args['func'] = array('sobad_form');
		$args['data'] = array($data);
		
		return modal_admin($args);
	}

	public function _detail($id=0){
		$id = str_replace('detail_', '', $id);
		intval($id);

		$whr = "AND (divisi='$id' AND status='0' AND end_status!='7') OR (divisi='$id' AND status!='7' AND end_status='0')";
		$args = sobad_user::get_all(array('picture','no_induk','name','status'),$whr);

		$data['class'] = '';
		$data['table'] = array();

		$no = 0;
		foreach ($args as $key => $val) {
			$no += 1;

			$image = empty($val['notes_pict'])?'no-profile.jpg':$val['notes_pict'];
			$status = employee_absen::_conv_status($val['status']);

			$data['table'][$key]['tr'] = array('');
			$data['table'][$key]['td'] = array(
				'no'		=> array(
					'center',
					'5%',
					$no,
					true
				),
				'Profile'	=> array(
					'left',
					'5%',
					'<img src="asset/img/user/'.$image.'" style="width:100%">',
					true
				),
				'NIK'		=> array(
					'left',
					'5%',
					$val['no_induk'],
					true
				),
				'Nama'		=> array(
					'left',
					'auto',
					$val['name'],
					true
				),
				'Status'	=> array(
					'left',
					'13%',
					$status,
					true
				),
			);
		}

		$args = array(
			'title'		=> 'Detail data',
			'button'	=> '_btn_modal_save',
			'status'	=> array(),
			'func'		=> array('sobad_table'),
			'data'		=> array($data)
		);
		
		return modal_admin($args);
	}
}