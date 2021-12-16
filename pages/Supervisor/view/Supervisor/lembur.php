<?php

class lembur_supervisor extends _page{

	protected static $object = 'lembur_supervisor';

	protected static $table = 'sobad_overtime';

	// ----------------------------------------------------------
	// Layout category  ------------------------------------------
	// ----------------------------------------------------------

	protected function _array(){
		$args = array(
			'ID',
			'title',
			'post_date',
			'approve',
			'accept',
			'note',
		);

		return $args;
	}

	protected function table(){
		$data = array();
		$args = self::_array();

		$m = date('m');$y = date('Y');

		$user_id = get_id_user();
		$where = "AND `abs-overtime`.user='$user_id' AND YEAR(`abs-overtime`.post_date)='$y' AND MONTH(`abs-overtime`.post_date)='$m'";

		$object = self::$table;
		$args = $object::get_all($args,$where);

		$data['class'] = '';
		$data['table'] = array();

		$no = 0;
		foreach($args as $key => $val){
			$no += 1;
			$id = $val['ID'];

			$status = $val['approve'] > 0 || $val['accept'] > 0 ? 'disabled':'';

			$detail = array(
				'ID'	=> 'detail_'.$id,
				'func'	=> '_detail',
				'color'	=> 'yellow',
				'icon'	=> 'fa fa-eye',
				'label'	=> 'detail'
			);

			$edit = array(
				'ID'	=> 'edit_'.$id,
				'func'	=> '_edit',
				'color'	=> 'blue',
				'icon'	=> 'fa fa-edit',
				'label'	=> 'edit',
				'status'=> $status
			);

			$hapus = array(
				'ID'	=> 'del_'.$id,
				'func'	=> '_delete_lembur',
				'color'	=> 'red',
				'icon'	=> 'fa fa-trash',
				'label'	=> 'hapus',
				'status'=> $status
			);

			$drop = array(
				'label'		=> 'Change',
				'color'		=> 'default',
				'button'	=> array(
					_modal_button($detail),
					edit_button($edit),
					hapus_button($hapus)
				)
			);

			$tanggal = $val['post_date'];
			$tanggal = conv_day_id($tanggal).', '.format_date_id($tanggal);

			$qty = 0;
			$lembur = sobad_overtime::get_details($id,array('status'));
			foreach ($lembur as $ky => $vl) {
				$qty += $vl['status']==2?0:1;
			}

			$color = '#cb5a5e';
			if($val['approve']>=1){
				$color = '#26a69a';
			}
			$ppic = '<i class="fa fa-circle" style="color:'.$color.'">';

			$color = '#cb5a5e';
			if($val['accept']>=1){
				$color = '#26a69a';
			}
			$hrd = '<i class="fa fa-circle" style="color:'.$color.'">';
			
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
					'17%',
					$tanggal,
					true
				),
				'Keterangan'	=> array(
					'left',
					'auto',
					$val['note'],
					true
				),
				'PPIC'			=> array(
					'center',
					'7%',
					$ppic,
					true
				),
				'HRD'		=> array(
					'center',
					'7%',
					$hrd,
					true
				),
				'Jumlah'	=> array(
					'right',
					'10%',
					$qty.' Orang',
					true
				),
				'Button'	=> array(
					'center',
					'10%',
					dropdown_button($drop),
					false
				),
			);
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
			'style'		=> array(self::$object,'_style'),
			'script'	=> array('')
		);
		
		return portlet_admin($opt,$box);
	}

	public static function _style(){
		?>
			<style type="text/css">
				.table_flexible button.dropdown-toggle{
					margin-bottom: 5px;
				}

				#sobad_portlet td>.btn-group.open>.dropdown-menu {
    				display: contents;
				}

				#sobad_portlet .dropdown-menu .btn{
    				margin-top: 5px;
    				padding: 3px 8px;
				}

				#sobad_portlet .dropdown-menu li > a > [class^="fa-"], #sobad_portlet .dropdown-menu li > a > [class*=" fa-"]{
					color : #fff;
				}
			</style>
		<?php
	}

	protected static function _conv_tree_user($id=0){
		$idx = array();

		// Get Divisi
		$div = sobad_user::get_id($id,array('divisi'));
		$div = $div[0]['divisi'];

		$idx[] = $div;
		$data = sobad_module::_gets_tree_division($div);
		foreach ($data as $key => $val) {
			$idx[] = $val['ID'];
		}

		$idx = implode(',', $idx);

		$user = sobad_user::get_employees(array('ID','name'),"AND divisi IN ($idx) AND status!='0'");
		$user = convToOption($user,'ID','name');

		return $user;
	}

	// ----------------------------------------------------------
	// Form data Lembur -----------------------------------------
	// ----------------------------------------------------------
	
	public function add_form(){
		$vals = array(0,0,date('Y-m-d'),0,0,'');
		$vals = array_combine(self::_array(),$vals);
		
		$args = array(
			'title'		=> 'Tambah data lembur',
			'button'	=> '_btn_modal_save',
			'status'	=> array(
				'link'		=> '_add_data_lembur',
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
			'title'		=> 'Edit data lembur',
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
				'key'			=> 'user',
				'value'			=> get_id_user()
			),
			array(
				'func'			=> 'opt_datepicker',
				'key'			=> 'post_date',
				'label'			=> 'Tanggal Lembur',
				'class'			=> 'input-circle',
				'value'			=> $vals['post_date'],
			),
			array(
				'func'			=> 'opt_textarea',
				'key'			=> 'note',
				'label'			=> 'Keterangan',
				'class'			=> 'input-circle',
				'value'			=> $vals['note'],
				'data'			=> 'placeholder="Keterangan Lembur"',
				'rows'			=> 4
			),
		);
		
		$args['func'] = array('sobad_form','_portlet_layout');
		$args['data'] = array($data, $vals['ID']);
		
		return modal_admin($args);
	}

	public static function _portlet_layout($id=0){
		$data = self::_table_detail($id);
	
		$box = array(
			'ID'		=> 'portlet_user',
			'label'		=> 'Data Karyawan',
			'tool'		=> '',
			'action'	=> self::_portlet_action($id),
			'func'		=> 'sobad_table',
			'data'		=> $data
		);
		
		metronic_layout::_portlet($box);
	}

	private static function _portlet_action($id=1){
		$add = array(
			'ID'	=> 'add_' . $id,
			'func'	=> '_form_add',
			'color'	=> 'btn-default',
			'icon'	=> 'fa fa-plus',
			'label'	=> 'Tambah'
		);
		
		return apply_button($add);
	}

	// ----------------------------------------------------------
	// Form data Lembur -----------------------------------------
	// ----------------------------------------------------------
	
	public function _form_add($id=0){
		$id = str_replace('add_', '', $id);

		$vals = array(0,array(),date('H:i'),date('H:i'),0,'',$id);
		$vals = array_combine(self::_array_detail(),$vals);
		
		$args = array(
			'title'		=> 'Tambah data Karyawan',
			'button'	=> '_btn_modal_save',
			'status'	=> array(
				'link'		=> '_add_detail',
				'load'		=> 'portlet_user'
			)
		);
		
		return self::_detail_form($args,$vals);
	}

	protected function _form_edit($vals=array()){
		$check = array_filter($vals);
		if(empty($check)){
			return '';
		}
		
		$args = array(
			'title'		=> 'Edit data Karyawan',
			'button'	=> '_btn_modal_save',
			'status'	=> array(
				'link'		=> '_update_detail',
				'load'		=> 'portlet_user'
			)
		);
		
		return self::_detail_form($args,$vals);
	}

	private function _detail_form($args=array(),$vals=array()){
		$check = array_filter($args);
		if(empty($check)){
			return '';
		}

		$id = get_id_user();
		$user = self::_conv_tree_user($id);

		$data = array(
			0 => array(
				'func'			=> 'opt_hidden',
				'type'			=> 'hidden',
				'key'			=> 'ID_reff',
				'value'			=> $vals['ID']
			),
			array(
				'func'			=> 'opt_hidden',
				'type'			=> 'hidden',
				'key'			=> 'over_id',
				'value'			=> $vals['over_id']
			),
			array(
				'func'			=> 'opt_select_tags',
				'data'			=> $user,
				'key'			=> 'user_id',
				'label'			=> 'Karyawan',
				'class'			=> 'input-circle',
				'select'		=> $vals['user_id']
			),
			array(
				'func'			=> 'opt_input',
				'type'			=> 'clock',
				'key'			=> 'start_time',
				'label'			=> 'Mulai',
				'class'			=> 'input-circle',
				'value'			=> $vals['start_time'],
				'data'			=> '',
			),
			array(
				'func'			=> 'opt_input',
				'type'			=> 'clock',
				'key'			=> 'finish_time',
				'label'			=> 'Selesai',
				'class'			=> 'input-circle',
				'value'			=> $vals['finish_time'],
				'data'			=> '',
			),
		);

		if($vals['ID']>=1){
			$data[2]['func'] = 'opt_select';
			$data[2]['searching'] = true;
			$data[2]['status'] = '';
		}
		
		$args['func'] = array('sobad_form');
		$args['data'] = array($data);
		
		return modal_admin($args);
	}

	// ----------------------------------------------------------
	// Detail data Lembur ---------------------------------------
	// ----------------------------------------------------------	

	protected function _array_detail(){
		$args = array(
			'ID',
			'user_id',
			'start_time',
			'finish_time',
			'status',
			'notes',
			'over_id'
		);

		return $args;
	}

	public static function _detail($id=0){
		$id = str_replace('detail_', '', $id);
		intval($id);

		$data = self::_table_detail($id,true);

		$args = array(
			'title'		=> 'Detail data',
			'button'	=> '_btn_modal_save',
			'status'	=> array(),
			'func'		=> array('sobad_table'),
			'data'		=> array($data)
		);
		
		return modal_admin($args);
	}

	public static function _table_detail($id=0, $view=false){
		$args = sobad_overtime::get_details($id,self::_array_detail());

		$data['class'] = '';
		$data['table'] = array();

		$no = 0;
		foreach ($args as $key => $val) {
			$no += 1;
			$id = $val['ID'];

			$status = $val['status']>0?'disabled':'';

			$edit = array(
				'ID'	=> 'edit_'.$id,
				'func'	=> '_editDetail',
				'color'	=> 'blue',
				'icon'	=> 'fa fa-edit',
				'label'	=> 'edit',
				'status'=> $status
			);

			$hapus = array(
				'ID'	=> 'del_'.$id,
				'func'	=> '_deleteDetail',
				'color'	=> 'red',
				'icon'	=> 'fa fa-trash',
				'label'	=> 'hapus',
				'status'=> $status
			);

			$color = '#666';
			if($val['status']==1){
				$color = '#26a69a';
			}else if($val['status']==2){
				$color = '#cb5a5e';
			}
			$status = '<i class="fa fa-circle" style="color:'.$color.'">';

			$data['table'][$key]['tr'] = array('');
			$data['table'][$key]['td'] = array(
				'No'		=> array(
					'center',
					'5%',
					$no,
					true
				),
				'NIK'		=> array(
					'left',
					'5%',
					$val['no_induk_user'],
					true
				),
				'Nama'		=> array(
					'left',
					'auto',
					$val['name_user'],
					true
				),
				'Mulai'	=> array(
					'left',
					'8%',
					format_time_id($val['start_time']),
					true
				),
				'Selesai'	=> array(
					'left',
					'8%',
					format_time_id($val['finish_time']),
					true
				),
				'Status'	=> array(
					'center',
					'7%',
					$status,
					true
				),
				'Keterangan'	=> array(
					'left',
					'20%',
					$val['notes'],
					true
				),
				'Edit'	=> array(
					'center',
					'10%',
					_modal_button($edit,2),
					false
				),
				'Hapus'	=> array(
					'center',
					'10%',
					hapus_button($hapus),
					false
				),
			);

			if($view){
				unset($data['table'][$key]['td']['Edit']);
				unset($data['table'][$key]['td']['Hapus']);
			}else{
				unset($data['table'][$key]['td']['Status']);
				unset($data['table'][$key]['td']['Keterangan']);
			}
		}

		return $data;
	}	

	// ----------------------------------------------------------
	// Database detail Lembur -----------------------------------
	// ----------------------------------------------------------	

	public static function _editDetail($id=0){
		$id = str_replace('edit_', '', $id);
		intval($id);

		$q = sobad_overtime::get_detail($id,self::_array_detail());
		return self::_form_edit($q[0]);
	}

	public static function _deleteDetail($id=0){
		$id = str_replace('del_', '', $id);
		intval($id);

		// Get referensi 
		$reff = sobad_overtime::get_detail($id,array('over_id'));
		$reff = $reff[0]['over_id'];

		// Hapus Karyawan
		sobad_db::_delete_single($id,'abs-overtime-detail');

		$table = self::_table_detail($reff);
		return table_admin($table);
	}

	public static function _add_detail($args=array()){
		$args = sobad_asset::ajax_conv_json($args);
		$reff = $args['over_id'];

		// Tambah detail Karyawan
		$user = explode(',', $args['user_id']);
		foreach ($user as $key => $val) {
			$q = sobad_db::_insert_table('abs-overtime-detail',array(
				'user_id'		=> $val,
				'over_id'		=> $reff,
				'start_time'	=> $args['start_time'],
				'finish_time'	=> $args['finish_time']
			));
		}

		if($q!==0){
			$table = self::_table_detail($reff);
			return table_admin($table);
		}
	}

	public static function _update_detail($args=array()){
		$args = sobad_asset::ajax_conv_json($args);
		$reff = $args['over_id'];

		// Tambah detail Karyawan
		$q = sobad_db::_update_single($args['ID_reff'],'abs-overtime-detail',array(
				'user_id'		=> $args['user_id'],
				'over_id'		=> $reff,
				'start_time'	=> $args['start_time'],
				'finish_time'	=> $args['finish_time']
			));

		if($q!==0){
			$table = self::_table_detail($reff);
			return table_admin($table);
		}
	}

	// ----------------------------------------------------------
	// Database Lembur ------------------------------------------
	// ----------------------------------------------------------	

	public static function _add_data_lembur($args=array()){
		return self::_add_db($args,'_addDetail',self::$object);
	}

	public static function _addDetail($args=array()){
		$q = sobad_db::_update_multiple("over_id='0'",'abs-overtime-detail',array(
			'over_id'	=> $args['index']
		));

		if($q!==0){
			$table = self::table();
			return table_admin($table);
		}
	}

	public static function _delete_lembur($id=0){
		$id = str_replace('del_', '', $id);
		intval($id);

		$q = sobad_db::_delete_multiple("over_id='$id'",'abs-overtime-detail');
		return self::_delete($id);
	}
}