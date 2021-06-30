<?php

class university_absen extends _page{

	protected static $object = 'university_absen';

	protected static $table = 'sobad_university';

	// ----------------------------------------------------------
	// Layout category  ------------------------------------------
	// ----------------------------------------------------------

	protected function _array(){
		$args = array(
			'ID',
			'name',
			'address',
			'phone_no',
			'email',
			'province',
			'city',
			'subdistrict',
			'post_code'
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
		$data['search'] = array('Semua','nama','alamat');
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

			$qty = sobad_user::count("(status='7' OR end_status='7') AND (meta_key='_university' AND meta_value='$id') ");

			$provinsi = sobad_wilayah::get_province($val['province']);
			$kota = sobad_wilayah::get_city($val['city']);
			$kecamatan = sobad_wilayah::get_subdistrict($val['subdistrict']);
			$address = $val['address'].', '.$kecamatan[0]['kecamatan'].', '.$kota[0]['kabupaten'].'<br>'.$provinsi[0]['provinsi'].' - '.$val['post_code'];
			
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
					$val['name'],
					true
				),
				'phone no'	=> array(
					'left',
					'10%',
					$val['phone_no'],
					true
				),
				'email'		=> array(
					'left',
					'10%',
					$val['email'],
					true
				),
				'address'	=> array(
					'left',
					'30%',
					$address,
					true
				),
				'jumlah'	=> array(
					'center',
					'10%',
					$qty,
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
			'title'	=> 'Universitas / Sekolah <small>data universitas / sekolah</small>',
			'link'	=> array(
				0	=> array(
					'func'	=> self::$object,
					'label'	=> 'universitas / sekolah'
				)
			),
			'date'	=> false
		); 
		
		return $args;
	}

	protected function get_box(){
		$data = self::table();
		
		$box = array(
			'label'		=> 'Data Universitas / Sekolah',
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
		$vals = array(0,'','','','',0,0,0,0);
		$vals = array_combine(self::_array(), $vals);

		if($func=='add_0'){
			$func = '_add_db';
		}
		
		$args = array(
			'title'		=> 'Tambah data universitas / sekolah',
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
			'title'		=> 'Edit data universitas / sekolah',
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

		$provinces = sobad_wilayah::get_provinces();
		$provinces = convToOption($provinces,'id_prov','provinsi');

		$cities = self::get_cities($vals['province']);

		$subdistricts = self::get_subdistricts($vals['city']);

		$postcodes = self::get_postcodes($vals['province'],$vals['city'],$vals['subdistrict']);

		$data = array(
			0	=> array(
				'func'			=> 'opt_hidden',
				'type'			=> 'hidden',
				'key'			=> 'ID',
				'value'			=> $vals['ID']
			),
			array(
				'func'			=> 'opt_input',
				'type'			=> 'text',
				'key'			=> 'name',
				'label'			=> 'Nama',
				'class'			=> 'input-circle',
				'value'			=> $vals['name'],
				'data'			=> 'placeholder="Nama Universitas atau Sekolah"'
			),
			array(
				'func'			=> 'opt_textarea',
				'type'			=> 'text',
				'key'			=> 'address',
				'label'			=> 'Address',
				'class'			=> 'input-circle',
				'value'			=> $vals['address'],
				'data'			=> 'placeholder="address"',
				'rows'			=> 4
			),
			array(
				'func'			=> 'opt_select',
				'data'			=> $provinces,
				'key'			=> 'province',
				'label'			=> 'Provinsi',
				'class'			=> 'input-circle',
				'searching'		=> true,
				'select'		=> $vals['province'],
				'status'		=> 'data-sobad="option_city" data-load="city_cust" data-attribute="sobad_option_search" '
			),
			array(
				'id'			=> 'city_cust',
				'func'			=> 'opt_select',
				'data'			=> $cities,
				'key'			=> 'city',
				'label'			=> 'Kota/Kabupaten',
				'class'			=> 'input-circle',
				'searching'		=> true,
				'select'		=> $vals['city'],
				'status'		=> 'data-sobad="option_subdistrict" data-load="subdistrict_cust" data-attribute="sobad_option_search" '
			),
			array(
				'id'			=> 'subdistrict_cust',
				'func'			=> 'opt_select',
				'data'			=> $subdistricts,
				'key'			=> 'subdistrict',
				'label'			=> 'Kecamatan',
				'class'			=> 'input-circle',
				'searching'		=> true,
				'select'		=> $vals['subdistrict'],
				'status'		=> 'data-sobad="option_postcode" data-load="post_code_cust" data-attribute="sobad_option_search" '
			),
			array(
				'func'			=> 'opt_input',
				'type'			=> 'text',
				'key'			=> 'email',
				'label'			=> 'Email',
				'class'			=> 'input-circle',
				'value'			=> $vals['email'],
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
			array(
				'id'			=> 'post_code_cust',
				'func'			=> 'opt_select',
				'data'			=> $postcodes,
				'key'			=> 'post_code',
				'label'			=> 'Kode Pos',
				'class'			=> 'input-circle',
				'select'		=> $vals['post_code'],
				'status'		=> ''
			)
		);
		
		$args['func'] = array('sobad_form');
		$args['data'] = array($data);
		
		return modal_admin($args);
	}

}