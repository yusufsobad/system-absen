<?php

class employee_admin extends _page{
	protected static $object = 'employee_admin';

	protected static $table = 'sobad_user';

	// ----------------------------------------------------------
	// Layout category  -----------------------------------------
	// ----------------------------------------------------------

	protected function _array(){
		$args = array(
			'ID',
			'no_induk',
			'name',
			'divisi',
			'status',
			'username',
			'password',
			'dayOff',
			'picture',
			'divisi'
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

		if($type==0){
			$where = "AND `abs-user`.status NOT IN ('0','7') AND `abs-user`.end_status!='7'";
		}else if($type==9){
			$where = "AND `abs-user`.status='0' AND `abs-user`.end_status!='7'";
		}else{
			$where = "AND `abs-user`.status='$type'";
		}

		
		$kata = '';$_args = array();
		if(self::$search){
			$_args = array('ID','no_induk','name');
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
		$data['search'] = array('Semua','nik','nama');
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
			$edit = array(
				'ID'	=> 'edit_'.$val['ID'],
				'func'	=> '_edit',
				'color'	=> 'blue',
				'icon'	=> 'fa fa-edit',
				'label'	=> 'edit',
				'type'	=> $tab
			);

			$hapus = array(
				'ID'	=> 'del_'.$val['ID'],
				'func'	=> '_delete',
				'color'	=> 'red',
				'icon'	=> 'fa fa-trash',
				'label'	=> 'hapus',
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
					'10%',
					$val['no_induk'],
					true
				),
				'Nama'		=> array(
					'left',
					'auto',
					$val['name'],
					true
				),
				'Jabatan'		=> array(
					'left',
					'15%',
					$val['meta_value_divi'],
					true
				),
				'Cuti'		=> array(
					'right',
					'10%',
					$val['dayOff'].' hari',
					true
				),
				'Edit'		=> array(
					'center',
					'10%',
					edit_button($edit),
					false
				),
				'Hapus'		=> array(
					'center',
					'10%',
					hapus_button($hapus),
					false
				),
			);
		}
		
		return $data;
	}

	private function head_title(){
		$args = array(
			'title'	=> 'Karyawan <small>data Karyawan</small>',
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
			'label'		=> 'Data Karyawan',
			'tool'		=> '',
			'action'	=> self::action(),
			'func'		=> 'sobad_table',
			'data'		=> $data
		);

		return $box;
	}

	protected function layout(){
		parent::$type = 'employee_0';
		$box = self::get_box();

		$tabs = array();

		$tabs[0] = array(
			'key'	=> 'employee_0',
			'label'	=> 'Aktif',
			'qty'	=> sobad_user::count("status NOT IN ('0','7')")
		);

		for($i=1;$i<7;$i++){
			$tabs[$i] = array(
				'key'	=> 'employee_'.$i,
				'label'	=> self::_conv_status($i),
				'qty'	=> sobad_user::count("status='$i'")
			);
		}

		$tabs[7] = array(
			'key'	=> 'employee_9',
			'label'	=> 'Non Aktif',
			'qty'	=> sobad_user::count("status='0'")
		);

		$tabs = array(
			'tab'	=> $tabs,
			'func'	=> '_portlet',
			'data'	=> $box
		);

		$opt = array(
			'title'		=> self::head_title(),
			'style'		=> array(),
			'script'	=> array('')
		);
		
		return tabs_admin($opt,$tabs);
	}

	protected function action(){
		$import = array(
			'ID'	=> 'import_0',
			'func'	=> 'import_form',
			'color'	=> 'btn-default',
			'load'	=> 'here_modal2',
			'icon'	=> 'fa fa-file-excel-o',
			'label'	=> 'Import',
			'spin'	=> false
		);
		
		return apply_button($import);
	}

	public function _conv_status($status=''){
		$types = array('Non Aktif','Training','Kontrak 1','Kontrak 2','Tetap','Founder','Pensiun','Internship');
		$label = isset($types[$status])?$types[$status]:'Berhenti';

		return $label;
	}

	// ----------------------------------------------------------
	// Form data category ---------------------------------------
	// ----------------------------------------------------------

	public function import_form(){
		return employee_absen::import_form();
	}

	protected function edit_form($vals=array()){
		$check = array_filter($vals);
		if(empty($check)){
			return '';
		}
		
		$args = array(
			'title'		=> 'Edit data karyawan',
			'button'	=> '_btn_modal_save',
			'status'	=> array(
				'link'		=> '_update_db',
				'load'		=> 'sobad_portlet',
				'type'		=> $_POST['type']
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
			'label'	=> 'Add'
		);

		$divisi = sobad_module::_gets('department',array('ID','meta_value'));
		$divisi = convToOption($divisi,'ID','meta_value');

		$data = array(
			'cols'	=> array(3,8),
			0	=> array(
				'func'			=> 'opt_hidden',
				'type'			=> 'hidden',
				'key'			=> '_IDX',
				'value'			=> $vals['ID']
			),
			array(
				'func'			=> 'opt_input',
				'type'			=> 'text',
				'key'			=> 'name',
				'label'			=> 'Nama',
				'class'			=> 'input-circle',
				'value'			=> $vals['name'],
				'data'			=> 'placeholder="Nama Karyawan" readonly'
			),
			array(
				'func'			=> 'opt_input',
				'type'			=> 'text',
				'key'			=> 'username',
				'label'			=> 'Username',
				'class'			=> 'input-circle',
				'value'			=> $vals['username'],
				'data'			=> ''
			),
			array(
				'func'			=> 'opt_input',
				'type'			=> 'text',
				'key'			=> 'password',
				'label'			=> 'Reset Password',
				'class'			=> 'input-circle',
				'value'			=> '',
				'data'			=> 'placeholder="Reset"'
			),
			array(
				'id'			=> 'divisi',
				'func'			=> 'opt_select',
				'data'			=> $divisi,
				'key'			=> 'divisi',
				'button'		=> _modal_button($add_divisi,3),
				'label'			=> 'Jabatan',
				'searching'		=> true,
				'class'			=> 'input-circle',
				'select'		=> $vals['divisi'],
				'status'		=> ''
			),
			array(
				'func'			=> 'opt_input',
				'type'			=> 'decimal',
				'key'			=> 'dayOff',
				'label'			=> 'Sisa Cuti',
				'class'			=> 'input-circle',
				'value'			=> number_format($vals['dayOff'],1,',','.'),
				'data'			=> ''
			),
		);	

		$data = array($data,$vals['picture']);
		
		$args['func'] = array('_layout_form');
		$args['object'] = array(self::$object);
		$args['data'] = array($data);
		
		return modal_admin($args);
	}

	public static function _layout_form($args=array()){
		$picture = $args[1];
		$args = $args[0];

		$image = 'no-profile.jpg';
		if($picture!=0){
			$image = sobad_post::get_id($picture,array('notes'));
			$image = $image[0]['notes'];
		}

		?>
			<style type="text/css">
				.box-image-show{
					cursor:default;
					padding-left: 50px;
				}

				.box-image-show>img {
				    border-radius: 20px !important;
				}
			</style>

			<div class="row" style="padding-right: 20px;">
				<div class="col-md-3 box-image-show">
					<img src="asset/img/user/<?php print($image) ;?>" style="width:100%" id="profile-employee">
				</div>
				<div class="col-md-9">
					<?php metronic_layout::sobad_form($args) ;?>
				</div>
			</div>
		<?php
	}

	// ----------------------------------------------------------
	// Option Divisi --------------------------------------------
	// ----------------------------------------------------------

	public function _form_divisi(){
		return employee_absen::_form_divisi();
	}

	public function _add_divisi($args=array()){
		return employee_absen::_add_divisi($args);
	}

	public function _option_divisi(){
		return employee_absen::_option_divisi();
	}

	// ----------------------------------------------------------
	// Function category to database -----------------------------
	// ----------------------------------------------------------

	public function _conv_import($files=array()){
		return employee_absen::_conv_import($files);
	}

	protected function _callback($args=array(),$_args=array()){
		$username = strtolower($args['username']);
		$username = preg_replace('/\s+/', '_', $username);

		$args['ID'] = $args['_IDX'];
		$args['username'] = $username;

		if(empty($args['password'])){
			unset($args['password']);
		}else{
			$args['password'] = md5($args['password']);
		}

		return $args;
	}
}