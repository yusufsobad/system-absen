<?php

class internship_absen extends _page{
	protected static $object = 'internship_absen';

	protected static $table = 'sobad_user';

	protected static $post = 'internship';

	// ----------------------------------------------------------
	// Layout category  ------------------------------------------
	// ----------------------------------------------------------

	public function __construct(){
		parent::$type = 'intern_1';
	}

	protected function _array(){
		$args = array(
			'ID',
			'name',
			'no_induk',
			'_sex',
			'_address',
			'_email',
			'_province',
			'_city',
			'_subdistrict',
			'_postcode',
			'phone_no',
			'_education',
			'_university',
			'_study_program',
			'_faculty',
			'_semester',
			'_classes',
			'status',
			'work_time',
			'inserted'
		);

		return $args;
	}

	protected function table(){
		$data = array();
		$args = array('ID','name','no_induk','_address','phone_no','status','inserted');

		$start = intval(self::$page);
		$nLimit = intval(self::$limit);
		$type = parent::$type;

		if($type=='intern_1'){
			$where = "AND status='6'";
		}else{
			$where = "AND status='0' AND end_status='6'";
		}
		
		$kata = '';
		if(self::$search){
			$src = self::like_search($args,$where);
			$cari = $src[0];
			$where = $src[0];
			$kata = $src[1];
		}else{
			$cari=$where;
		}
		
		$limit = 'LIMIT '.intval(($start - 1) * $nLimit).','.$nLimit;
		$where .= $limit;

		$args = sobad_user::get_all($args,$where,self::$post);
		$sum_data = sobad_user::count("1=1 ".$cari);

		$data['data'] = array('data' => $kata, 'type' => $type);
		$data['search'] = array('Semua','nama','no induk','alamat');
		$data['class'] = '';
		$data['table'] = array();
		$data['page'] = array(
			'func'	=> '_pagination',
			'data'	=> array(
				'start'		=> $start,
				'qty'		=> $sum_data,
				'limit'		=> $nLimit,
				'type'		=> $type
			)
		);

		$no = ($start-1) * $nLimit;
		foreach($args as $key => $val){
			$no += 1;
			$edit = array(
				'ID'	=> 'edit_'.$val['ID'],
				'func'	=> '_edit',
				'color'	=> 'blue',
				'icon'	=> 'fa fa-edit',
				'label'	=> 'edit',
				'type'	=> $type
			);

			$color = 'yellow';$status = '';
			if($val['status']==0){
				$status = "disabled";
				$color = "red";
			}
			
			$btn_sts = array(
				'ID'	=> 'status_'.$val['ID'],
				'func'	=> '_status',
				'color'	=> $color,
				'icon'	=> 'fa fa-user',
				'label'	=> 'Non Aktif',
				'status'=> $status,
				'type'	=> $type
			);

			$status = "Non Aktif";
			$no_induk = self::_conv_no_induk($val['no_induk'],$val['inserted']);
			
			$data['table'][$key]['tr'] = array('');
			$data['table'][$key]['td'] = array(
				'no'		=> array(
					'center',
					'5%',
					$no,
					true
				),
				'NIK'		=> array(
					'left',
					'5%',
					$no_induk,
					true
				),
				'Nama'		=> array(
					'left',
					'auto',
					$val['name'],
					true
				),
				'Alamat'	=> array(
					'left',
					'30%',
					$val['_address'],
					true
				),
				'No HP'		=> array(
					'left',
					'10%',
					$val['phone_no'],
					true
				),
				'Status'	=> array(
					'left',
					'10%',
					$status,
					true
				),
				'Edit'		=> array(
					'center',
					'10%',
					edit_button($edit),
					false
				),
				'Change'	=> array(
					'center',
					'10%',
					_click_button($btn_sts),
					false
				)
				
			);
		}
		
		return $data;
	}

	private function head_title(){
		$args = array(
			'title'	=> 'Internship <small>data internship</small>',
			'link'	=> array(
				0	=> array(
					'func'	=> self::$object,
					'label'	=> 'internship'
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
			'label'		=> 'Data Internship',
			'tool'		=> '',
			'action'	=> parent::action(),
			'func'		=> 'sobad_table',
			'data'		=> $data
		);

		return $box;
	}

	protected function layout(){
		$box = self::get_box();

		$tabs = array(
			'tab'	=> array(
				array(
					'key'	=> 'intern_1',
					'label'	=> 'Aktif',
					'qty'	=> sobad_internship::count("status='6'")
				),
				array(
					'key'	=> 'intern_0',
					'label'	=> 'Non Aktif',
					'qty'	=> sobad_internship::count("status='0' AND end_status='6'")
				)
			),
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

	private function _conv_no_induk($no=0,$date=''){
		$date = date($date);
		$date = strtotime($date);
		$date = date('y',$date);

		return 'M'.$date.sprintf("%02d",$no);
	}

	// ----------------------------------------------------------
	// Form data category -----------------------------------
	// ----------------------------------------------------------
	public function add_form($func='',$load='sobad_portlet'){
		$vals = array(0,'',1,'male','','',0,0,0,0,'',0,0,0,0,0,0,6,0,date('Y-m-d'));
		$vals = array_combine(self::_array(), $vals);

		if($func=='add_0'){
			$func = '_add_db';
		}
		
		$args = array(
			'title'		=> 'Tambah data internship',
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

		$args = array(
			'title'		=> 'Edit data internship',
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

		$no_induk = sobad_user::get_all(array('ID','name'),"AND divisi='17' AND status='0'");
		$no_induk = convToOption($no_induk,'ID','name');

		$work = sobad_work::get_works(array('ID','name'));
		$work = convToOption($work,'ID','name');

		$provinces = sobad_wilayah::get_provinces();
		$provinces = convToOption($provinces,'id_prov','provinsi');

		$cities = self::get_cities($vals['_province']);

		$subdistricts = self::get_subdistricts($vals['_city']);

		$postcodes = self::get_postcodes($vals['_province'],$vals['_city'],$vals['_subdistrict']);

		$tab1 = array(
			0	=> array(
				'func'			=> 'opt_hidden',
				'type'			=> 'hidden',
				'key'			=> 'ID',
				'value'			=> $vals['ID']
			),
			array(
				'func'			=> 'opt_hidden',
				'type'			=> 'hidden',
				'key'			=> 'status',
				'value'			=> $vals['status']
			),
			array(
				'func'			=> 'opt_hidden',
				'type'			=> 'hidden',
				'key'			=> 'no_induk',
				'value'			=> $vals['no_induk']
			),
			array(
				'func'			=> 'opt_input',
				'type'			=> 'text',
				'key'			=> 'nim',
				'label'			=> 'No Induk',
				'class'			=> 'input-circle',
				'value'			=> self::_conv_no_induk($vals['no_induk'],$vals['inserted']),
				'data'			=> 'placeholder="No Induk Magang" disabled',
			),
			array(
				'func'			=> 'opt_select',
				'data'			=> $work,
				'key'			=> 'work_time',
				'label'			=> 'Jam Kerja',
				'class'			=> 'input-circle',
				'select'		=> $vals['work_time'],
				'status'		=> ''
			),
			array(
				'func'			=> 'opt_input',
				'type'			=> 'text',
				'key'			=> 'name',
				'label'			=> 'Nama',
				'class'			=> 'input-circle',
				'value'			=> $vals['name'],
				'data'			=> 'placeholder="Nama"'
			),
			array(
				'func'			=> 'opt_box',
				'type'			=> 'radio',
				'key'			=> '_sex',
				'label'			=> 'Jenis Kelamin',
				'inline'		=> true,
				'value'			=> $vals['_sex'],
				'data'			=> array(
					0	=> array(
						'title'		=> 'Laki - Laki',
						'value'		=> 'male'
					),
					1	=> array(
						'title'		=> 'Perempuan',
						'value'		=> 'female'
					)
				)
			),
			array(
				'func'			=> 'opt_input',
				'type'			=> 'text',
				'key'			=> '_email',
				'label'			=> 'Email',
				'class'			=> 'input-circle',
				'value'			=> $vals['_email'],
				'data'			=> 'placeholder="Email"'
			),
			array(
				'func'			=> 'opt_input',
				'type'			=> 'text',
				'key'			=> 'phone_no',
				'label'			=> 'Phone No.',
				'class'			=> 'input-circle',
				'value'			=> $vals['phone_no'],
				'data'			=> 'placeholder="Phone Number"'
			),
		);

		$tab2 = array(
			0 => array(
				'func'			=> 'opt_textarea',
				'type'			=> 'text',
				'key'			=> 'address',
				'label'			=> 'Address',
				'class'			=> 'input-circle',
				'value'			=> $vals['_address'],
				'data'			=> 'placeholder="address"',
				'rows'			=> 4
			),
			array(
				'func'			=> 'opt_select',
				'data'			=> $provinces,
				'key'			=> '_province',
				'label'			=> 'Provinsi',
				'class'			=> 'input-circle',
				'searching'		=> true,
				'select'		=> $vals['_province'],
				'status'		=> 'data-sobad="option_city" data-load="city_cust" data-attribute="sobad_option_search" '
			),
			array(
				'id'			=> 'city_cust',
				'func'			=> 'opt_select',
				'data'			=> $cities,
				'key'			=> '_city',
				'label'			=> 'Kota/Kabupaten',
				'class'			=> 'input-circle',
				'searching'		=> true,
				'select'		=> $vals['_city'],
				'status'		=> 'data-sobad="option_subdistrict" data-load="subdistrict_cust" data-attribute="sobad_option_search" '
			),
			array(
				'id'			=> 'subdistrict_cust',
				'func'			=> 'opt_select',
				'data'			=> $subdistricts,
				'key'			=> '_subdistrict',
				'label'			=> 'Kecamatan',
				'class'			=> 'input-circle',
				'searching'		=> true,
				'select'		=> $vals['_subdistrict'],
				'status'		=> 'data-sobad="option_postcode" data-load="post_code_cust" data-attribute="sobad_option_search" '
			),
			array(
				'id'			=> 'post_code_cust',
				'func'			=> 'opt_select',
				'data'			=> $postcodes,
				'key'			=> '_postcode',
				'label'			=> 'Kode Pos',
				'class'			=> 'input-circle',
				'select'		=> $vals['_postcode'],
				'status'		=> ''
			)
		);

		$add_univ = array(
			'ID'	=> 'add_0',
			'func'	=> '_form_university',
			'class'	=> '',
			'color'	=> 'green',
			'icon'	=> 'fa fa-plus',
			'label'	=> 'Add'
		);

		$add_fakt = array(
			'ID'	=> 'add_0',
			'func'	=> '_form_faculty',
			'class'	=> '',
			'color'	=> 'green',
			'icon'	=> 'fa fa-plus',
			'label'	=> 'Add'
		);

		$add_prodi = array(
			'ID'	=> 'add_0',
			'func'	=> '_form_prodi',
			'class'	=> '',
			'color'	=> 'green',
			'icon'	=> 'fa fa-plus',
			'label'	=> 'Add'
		);

		$univ = sobad_university::get_all(array('ID','name'));
		$univ = convToOption($univ,'ID','name');

		$fakultas = sobad_module::_gets('faculty',array('ID','meta_value'));
		$fakultas = convToOption($fakultas,'ID','meta_value');

		$major = sobad_module::_gets('study_program',array('ID','meta_value'));
		$major = convToOption($major,'ID','meta_value');

		$tab3 = array(
			0 => array(
				'func'			=> 'opt_box',
				'type'			=> 'radio',
				'key'			=> '_education',
				'label'			=> 'Pendidikan',
				'inline'		=> true,
				'value'			=> $vals['_education'],
				'data'			=> array(
					0	=> array(
						'title'		=> 'S1',
						'value'		=> 'S1'
					),
					1	=> array(
						'title'		=> 'D3',
						'value'		=> 'D3'
					),
					2	=> array(
						'title'		=> 'SMK',
						'value'		=> 'SMK'
					)
				)
			),
			array(
				'id'			=> 'university',
				'func'			=> 'opt_select',
				'data'			=> $univ,
				'key'			=> '_university',
				'label'			=> 'Universitas',
				'class'			=> 'input-circle',
				'button'		=> _modal_button($add_univ,3),
				'searching'		=> true,
				'select'		=> $vals['_university'],
				'status'		=> ''
			),
			array(
				'id'			=> 'faculty',
				'func'			=> 'opt_select',
				'data'			=> $fakultas,
				'key'			=> '_faculty',
				'label'			=> 'Fakultas',
				'class'			=> 'input-circle',
				'button'		=> _modal_button($add_fakt,3),
				'searching'		=> true,
				'select'		=> $vals['_faculty'],
				'status'		=> ''
			),
			array(
				'id'			=> 'prodi',
				'func'			=> 'opt_select',
				'data'			=> $major,
				'key'			=> '_study_program',
				'label'			=> 'Jurusan',
				'class'			=> 'input-circle',
				'button'		=> _modal_button($add_prodi,3),
				'searching'		=> true,
				'select'		=> $vals['_study_program'],
				'status'		=> ''
			),
		);
		
		$data = array(
			'menu'		=> array(
				0	=> array(
					'key'	=> '',
					'icon'	=> 'fa fa-bars',
					'label'	=> 'General'
				),
				1	=> array(
					'key'	=> '',
					'icon'	=> 'fa fa-home',
					'label'	=> 'Address'
				),
				2	=> array(
					'key'	=> '',
					'icon'	=> 'fa fa-university',
					'label'	=> 'Unviversity'
				),
			),
			'content'	=> array(
				0	=> array(
					'func'	=> 'sobad_form',
					'data'	=> $tab1
				),
				1	=> array(
					'func'	=> 'sobad_form',
					'data'	=> $tab2
				),
				2	=> array(
					'func'	=> 'sobad_form',
					'data'	=> $tab3
				),
			)
		);
		
		$args['func'] = array('_inline_menu');
		$args['data'] = array($data);

		return modal_admin($args);
	}

	// ----------------------------------------------------------
	// Option Universitas ---------------------------------------
	// ----------------------------------------------------------

	public function _form_university(){
		return university_absen::add_form('_add_university','university');
	}

	public function _add_university($args=array()){
		return university_absen::_add_db($args,'_option_university');
	}

	public function _option_university(){
		$opt = '';
		$divisi = sobad_university::get_all(array('ID','name'));
		foreach ($divisi as $key => $val) {
			$opt .= '<option value="'.$val['ID'].'"> '.$val['name'].' </option>';
		}

		return $opt;
	}

		// ----------------------------------------------------------
	// Option Prodi --------------------------------------------
	// ----------------------------------------------------------

	public function _form_faculty(){
		return faculty_absen::add_form('_add_faculty','faculty');
	}

	public function _add_faculty($args=array()){
		return faculty_absen::_add_db($args,'_option_faculty');
	}

	public function _option_faculty(){
		$opt = '';
		$divisi = sobad_module::_gets('faculty',array('ID','meta_value'));
		foreach ($divisi as $key => $val) {
			$opt .= '<option value="'.$val['ID'].'"> '.$val['meta_value'].' </option>';
		}

		return $opt;
	}

	// ----------------------------------------------------------
	// Option Prodi --------------------------------------------
	// ----------------------------------------------------------

	public function _form_prodi(){
		return prodi_absen::add_form('_add_prodi','prodi');
	}

	public function _add_prodi($args=array()){
		return prodi_absen::_add_db($args,'_option_prodi');
	}

	public function _option_prodi(){
		$opt = '';
		$divisi = sobad_module::_gets('study_program',array('ID','meta_value'));
		foreach ($divisi as $key => $val) {
			$opt .= '<option value="'.$val['ID'].'"> '.$val['meta_value'].' </option>';
		}

		return $opt;
	}
}