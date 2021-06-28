<?php

class mutasi_absen extends _page{
	protected static $object = 'mutasi_absen';

	protected static $table = 'sobad_user';

	// ----------------------------------------------------------
	// Layout category  -----------------------------------------
	// ----------------------------------------------------------

	public function _array(){
		$args = array(
			'ID',
			'no_induk',
			'name',
			'divisi',
			'status',
			'picture',
		);

		return $args;
	}

	protected function table(){
		$data = array();
		$args = self::_array();

		$start = intval(self::$page);
		$nLimit = intval(self::$limit);

		$tab = parent::$type;
		$type = str_replace('employee_', '', $tab);

		$where = "AND `abs-user`.status NOT IN ('0','7') AND `abs-user`.end_status!='7'";
		
		$kata = '';$_args = array();
		if(self::$search){
			$_args = array('ID','no_induk','name','divisi');
			$src = self::like_search($_args,$where);
			$cari = $src[0];
			$where = $src[0];
			$kata = $src[1];
		}else{
			$cari=$where;
		}
		
		$limit = 'ORDER BY no_induk ASC,ID ASC LIMIT '.intval(($start - 1) * $nLimit).','.$nLimit;
		$where .= $limit;

		$args = sobad_user::get_employees($args,$where,true);
		$sum_data = sobad_user::count("1=1 ".$cari,$_args);

		$data['data'] = array('data' => $kata,'type' => $tab);
		$data['search'] = array('Semua','nik','nama','jabatan');
		$data['class'] = '';
		$data['table'] = array();
		$data['page'] = array(
			'func'	=> '_pagination',
			'data'	=> array(
				'start'		=> $start,
				'qty'		=> $sum_data,
				'limit'		=> $nLimit,
				'type'		=> $tab
			)
		);

		$no = ($start-1) * $nLimit;
		$now = time();
		foreach($args as $key => $val){
			$no += 1;
			
			$history = array(
				'ID'	=> 'history_'.$val['ID'],
				'func'	=> '_history',
				'color'	=> 'yellow',
				'icon'	=> 'fa fa-history',
				'label'	=> 'history',
				'type'	=> $tab
			);

			$edit = array(
				'ID'	=> 'edit_'.$val['ID'],
				'func'	=> '_edit',
				'color'	=> 'green',
				'icon'	=> 'fa fa-refresh',
				'label'	=> 'mutasi',
				'type'	=> $tab
			);

			$image = empty($val['notes_pict'])?'no-profile.jpg':$val['notes_pict'];
			
			$data['table'][$key]['tr'] = array('');
			$data['table'][$key]['td'] = array(
				'No'		=> array(
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
					'8%',
					$val['no_induk'],
					true
				),
				'Nama'		=> array(
					'left',
					'auto',
					$val['name'],
					true
				),
				'Jabatan'	=> array(
					'left',
					'20%',
					$val['meta_value_divi'],
					true
				),
				'History'		=> array(
					'center',
					'10%',
					edit_button($history),
					false
				),
				'Mutasi'		=> array(
					'center',
					'10%',
					edit_button($edit),
					false
				),
			);
		}
		
		return $data;
	}

	private function head_title(){
		$args = array(
			'title'	=> 'Karyawan <small>mutasi Karyawan</small>',
			'link'	=> array(
				0	=> array(
					'func'	=> self::$object,
					'label'	=> 'karyawan'
				)
			),
			'date'	=> false,
			'modal'	=> 3
		); 
		
		return $args;
	}

	protected function get_box(){
		$data = self::table();
		
		$box = array(
			'label'		=> 'Mutasi Karyawan',
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

	// ----------------------------------------------------------
	// Form data category ---------------------------------------
	// ----------------------------------------------------------

	protected function edit_form($vals=array()){
		$check = array_filter($vals);
		if(empty($check)){
			return '';
		}

		$vals['_id'] = 0;
		$vals['date'] = date('Y-m-d');
		$vals['div_now'] = $vals['divisi'];
		
		$args = array(
			'title'		=> 'Edit Mutasi karyawan',
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

		$add_divisi = array(
			'ID'	=> 'add_0',
			'func'	=> '_form_divisi',
			'class'	=> '',
			'color'	=> 'green',
			'icon'	=> 'fa fa-plus',
			'label'	=> 'Add',
		);

		$divisi = sobad_module::_gets('department',array('ID','meta_value'));
		$divisi = convToOption($divisi,'ID','meta_value');

		$data = array(
			0	=> array(
				'func'			=> 'opt_hidden',
				'type'			=> 'hidden',
				'key'			=> '_IDX',
				'value'			=> $vals['ID']
			),
			array(
				'func'			=> 'opt_hidden',
				'type'			=> 'hidden',
				'key'			=> '_id',
				'value'			=> $vals['_id']
			),
			array(
				'func'			=> 'opt_hidden',
				'type'			=> 'hidden',
				'key'			=> 'last_divisi',
				'value'			=> $vals['divisi']
			),
			array(
				'func'			=> 'opt_input',
				'type'			=> 'text',
				'key'			=> 'no_induk',
				'label'			=> 'NIK',
				'class'			=> 'input-circle',
				'value'			=> $vals['no_induk'],
				'data'			=> 'disabled'
			),
			array(
				'func'			=> 'opt_input',
				'type'			=> 'text',
				'key'			=> 'name',
				'label'			=> 'Nama',
				'class'			=> 'input-circle',
				'value'			=> $vals['name'],
				'data'			=> 'placeholder="Nama Karyawan" disabled',
			),
			array(
				'func'			=> 'opt_datepicker',
				'key'			=> '_mutasi_date',
				'label'			=> 'Tanggal Mutasi',
				'class'			=> 'input-circle',
				'value'			=> $vals['date']
			),
			array(
				'id'			=> 'divisi',
				'func'			=> 'opt_select',
				'data'			=> $divisi,
				'key'			=> 'divisi',
				'button'		=> _modal_button($add_divisi,3),
				'label'			=> 'Jabatan',
				'class'			=> 'input-circle',
				'select'		=> $vals['div_now'],
				'searching'		=> true,
				'status'		=> ''
			),
		);	
		
		$args['func'] = array('sobad_form');
		$args['data'] = array($data);
		
		return modal_admin($args);
	}

	// ----------------------------------------------------------
	// Form2 data -----------------------------------------------
	// ----------------------------------------------------------

	public function _history($id=0){
		$id = str_replace('history_', '', $id);
		intval($id);

		$history = sobad_history::_gets($id,'_mutasi',array('ID','meta_value','meta_note','meta_date'));

		$data['class'] = '';
		$data['table'] = array();

		$no = 0;
		foreach ($history as $key => $val) {
			$no += 1;

			$_edit = array(
				'ID'	=> 'edit_'.$val['ID'],
				'func'	=> '_editMutasi',
				'color'	=> 'blue',
				'icon'	=> 'fa fa-edit',
				'label'	=> 'Edit',
				'type'	=> self::$type
			);

			$user = sobad_user::get_id($id,array('name'));
			$user = $user[0]['name'];

			$diva = sobad_module::get_id($val['meta_value'],array('meta_value'));
			$diva = $diva[0]['meta_value'];

			$divb = sobad_module::get_id($val['meta_note'],array('meta_value'));
			$divb = $divb[0]['meta_value'];

			$data['table'][$no-1]['tr'] = array('');
			$data['table'][$no-1]['td'] = array(
				'no'			=> array(
					'center',
					'5%',
					$no,
					true
				),
				'Name'		=> array(
					'left',
					'auto',
					$user,
					true
				),
				'Jabatan'		=> array(
					'left',
					'15%',
					$diva,
					true
				),
				'Mutasi'	=> array(
					'left',
					'15%',
					$divb,
					true
				),
				'Tanggal'	=> array(
					'left',
					'15%',
					format_date_id($val['meta_date']),
					true
				),
				'Edit'	=> array(
					'center',
					'10%',
					_modal_button($_edit,2),
					true
				),
			);
		}

		$args = array(
			'id'		=> 'historyMutasi',
			'title'		=> 'Mutasi',
			'button'	=> '_btn_modal_save',
			'status'	=> array(),
			'func'		=> array('sobad_table'),
			'data'		=> array($data)
		);
		
		return modal_admin($args);
	}

	public static function _editMutasi($id=0){
		$id = str_replace('edit_', '', $id);
		intval($id);

		$q = sobad_history::get_id($id,array('meta_id','meta_value','meta_note','meta_date'));
		$q = $q[0];

		$user = sobad_user::get_id($q['meta_id'],array('name','no_induk'));
		$user = $user[0];

		$vals = array(
			'ID'			=> $q['meta_id'],
			'_id'			=> $id,
			'divisi'		=> $q['meta_value'],
			'name'			=> $user['name'],
			'no_induk'		=> $user['no_induk'],
			'date'			=> $q['meta_date'],
			'div_now'		=> $q['meta_note']
		);

		$args = array(
			'title'		=> 'Edit Mutasi karyawan',
			'button'	=> '_btn_modal_save',
			'status'	=> array(
				'link'		=> '_updateMutasi',
				'load'		=> 'here_modal'
			)
		);
		
		return self::_data_form($args,$vals);
	}

	// ----------------------------------------------------------
	// Option Divisi --------------------------------------------
	// ----------------------------------------------------------

	public function _form_divisi(){
		return employee_absen::_form_divisi();
	}

	public function _add_divisi($args=array()){
		return employee_absen::_add_divisi();
	}

	public function _option_divisi(){
		return employee_absen::_option_divisi();
	}

	// ----------------------------------------------------------
	// Function category to database ----------------------------
	// ----------------------------------------------------------

	public function _updateMutasi($args=array()){
		$args = sobad_asset::ajax_conv_json($args);

		$data = array(
			'meta_note'		=> $args['divisi'],
			'meta_date'		=> $args['_mutasi_date']
		);

		$q = sobad_db::_update_single($args['_id'],'abs-history',$data);

		if($q!==0){
			return self::_history($args['_IDX']);
		}
	}

	protected function _callback($args=array()){
		$args['ID'] = $args['_IDX'];

		sobad_db::_insert_table('abs-history',array(
			'meta_id'		=> $args['ID'],
			'meta_key'		=> '_mutasi',
			'meta_value'	=> $args['last_divisi'],
			'meta_note'		=> $args['divisi'],
			'meta_date'		=> $args['_mutasi_date']." ".date('H:i:s'),
			'meta_var'		=> 'user'
		));

		return $args;
	}
}