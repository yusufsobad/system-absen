<?php

class employee_absen extends _file_manager{
	protected static $object = 'employee_absen';

	protected static $table = 'sobad_user';

	protected static $file_type = 'profile';

	protected static $url = '../asset/img/user';

	// ----------------------------------------------------------
	// Layout category  -----------------------------------------
	// ----------------------------------------------------------

	protected function _array(){
		$args = array(
			'ID',
			'no_induk',
			'divisi',
			'name',
			'work_time',
			'status',
			'end_status',
			'phone_no',
			'username',
			'_address',
			'_email',
			'_sex',
			'_entry_date',
			'_place_date',
			'_birth_date',
			'_resign_date',
			'_province',
			'_city',
			'_subdistrict',
			'_postcode',
			'_marital',
			'_religion',
			'_nickname',
			'picture',
			'dayOff'
		);

		return $args;
	}

	protected function table(){
		$data = array();
		$args = array('ID','no_induk','name','_address','phone_no','status','picture','end_status','_birth_date','_entry_date','_resign_date','_resign_status','dayOff','_province','_city','_subdistrict','_postcode');

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
			$_args = array('ID','no_induk','name','_address');
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
		$data['search'] = array('Semua','nik','nama','alamat');
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

			$color = 'yellow';$status = '';

			$btn_next = array(
				'ID'	=> 'next_'.$val['ID'],
				'func'	=> '_next',
				'icon'	=> 'fa fa-user',
				'color'	=> '',
				'label'	=> 'Lanjut',
				'status'=> '',
				'type'	=> $tab
			);
			
			$btn_sts = array(
				'ID'	=> 'status_'.$val['ID'],
				'func'	=> '_resign',
				'icon'	=> 'fa fa-power-off',
				'color'	=> '',
				'label'	=> 'Resign',
				'status'=> '',
				'type'	=> $tab
			);

			$btn_miss = array(
				'ID'	=> 'status_'.$val['ID'],
				'func'	=> '_dismissed',
				'icon'	=> 'fa fa-power-off',
				'color'	=> '',
				'label'	=> 'Di Berhentikan',
				'status'=> '',
				'type'	=> $tab
			);

			$drop = array(
				'label'		=> 'Change',
				'color'		=> 'default',
				'button'	=> array(
					_click_button($btn_next),
					_click_button($btn_sts),
					_click_button($btn_miss)
				)
			);

			$change = dropdown_button($drop);
			if($val['status']==0){
				$change = '';
			}

			$image = empty($val['notes_pict'])?'no-profile.jpg':$val['notes_pict'];
			
			$umur = date($val['_birth_date']);
			$umur = strtotime($umur);
			$umur = $now - $umur;
			$umur = floor($umur / (60 * 60 * 24 * 365))." Tahun";

			// Check masa status
			$life = self::_check_lifetime($val['status'],$val['_entry_date']);
			$masa = empty($life['masa'])?'':$life['masa'].' Hari';
			$end_date = $life['end_date'];

			if($val['status']){
				$status = self::_conv_status($val['status']);
			}else{
				$status = self::_conv_status($val['end_status']);
				$masa = '';
				$end_date = ' - ';

				if(!empty($val['_resign_date'])){
					$masa = $val['_resign_status']==2?'<br>- Di Berhentikan':'<br>- Resign';
					$end_date = format_date_id($val['_resign_date']);
				}
			}

			if($val['status']>3){
				$status .= ': <br> Masa Bakti ';

				$bakti = date($val['_entry_date']);
				$bakti = strtotime($bakti);
				$bakti = $now - $bakti;
				$bTahun = floor($bakti / (60 * 60 * 24 * 365));
				
				$bBulan = floor($bakti / (60 * 60 * 24 * 30.416667));
				$bBulan -= ($bTahun * 12);
				$end_date = $bTahun . ' Tahun ' . $bBulan .' Bulan';
			}

			$_address = sobad_wilayah::_conv_address($val['_address'],array(
				'province'		=> $val['_province'],
				'city'			=> $val['_city'],
				'subdistrict'	=> $val['_subdistrict'],
				'postcode'		=> $val['_postcode'],
			));
			$_address = $_address['result'];
			
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
				'Alamat'	=> array(
					'left',
					'20%',
					$_address,
					true
				),
				'No HP'		=> array(
					'left',
					'10%',
					$val['phone_no'],
					true
				),
				'Umur'	=> array(
					'left',
					'8%',
					$umur,
					true
				),
				'Status'	=> array(
					'left',
					'14%',
					$status.' : '.$masa.'<br><strong>'.$end_date.'</strong>',
					true
				),
				'Cuti'		=> array(
					'right',
					'7%',
					$val['dayOff'].' hari',
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
					$change,
					false
				)
				
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
		
		$import = apply_button($import);

		$add = array(
			'ID'	=> 'add_0',
			'func'	=> 'add_form',
			'color'	=> 'btn-default',
			'icon'	=> 'fa fa-plus',
			'label'	=> 'Tambah',
			'type'	=> self::$type
		);
		
		$add = edit_button($add);

		return $import.$add;
	}

	public function _conv_status($status=''){
		$types = array('Non Aktif','Training','Kontrak 1','Kontrak 2','Tetap','Founder','Pensiun','Internship');
		$label = isset($types[$status])?$types[$status]:'Berhenti';

		return $label;
	}

	public function _check_lifetime($status=0,$entry=''){
		$now = time();
		switch ($status) {
			case 1:
				$masa = date($entry);
				$masa = strtotime($masa);
				$masa = strtotime("+3 month",$masa);

				$end_date = date("Y-m-d",$masa);
				$end_date = format_date_id($end_date);

				$masa -= $now;
				$masa = (floor($masa / (60 * 60 * 24) + 1) * -1);
				break;

			case 2:
				$masa = date($entry);
				$masa = strtotime($masa);
				$masa = strtotime("+1 year",$masa);

				$end_date = date("Y-m-d",$masa);
				$end_date = format_date_id($end_date);

				$masa -= $now;
				$masa = (floor($masa / (60 * 60 * 24) + 1) * -1);
				break;

			case 3:
				$masa = date($entry);
				$masa = strtotime($masa);
				$masa = strtotime("+2 year",$masa);

				$end_date = date("Y-m-d",$masa);
				$end_date = format_date_id($end_date);

				$masa -= $now;
				$masa = (floor($masa / (60 * 60 * 24) + 1) * -1);
				break;

			case 4:
				$end_date = '';
				$masa = '';
				break;
				
			default:
				$end_date = '';
				$masa = '';
				break;
		}

		return array('masa' => $masa,'end_date' => $end_date);
	}

	// ----------------------------------------------------------
	// Form data category ---------------------------------------
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

	public function add_form($func='',$load='sobad_portlet'){
		$no = sobad_user::get_maxNIK();
		$no = sprintf("%03d",$no+1);

		$vals = array(0,$no,1,'',0,1,0,'','','','','male',date('Y-m-d'),'',date('Y-m-d'),'',0,0,0,0,1,1,'',0,7);
		$vals = array_combine(self::_array(), $vals);

		if($func=='add_0'){
			$func = '_add_db';
		}
		
		$args = array(
			'title'		=> 'Tambah data karyawan',
			'button'	=> '_btn_modal_save',
			'status'	=> array(
				'link'		=> $func,
				'load'		=> $load,
				'type'		=> $_POST['type']
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

		$work = sobad_work::get_works(array('ID','name'));
		$work = convToOption($work,'ID','name');

		$place = array();
		$places = sobad_wilayah::get_all(array('id_kab','kabupaten','tipe'));
		foreach($places as $key => $val){
			$place[$val['id_kab']] = $val['tipe'].' '.$val['kabupaten'];
		}

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
				'id'			=> 'picture-employee',
				'func'			=> 'opt_hidden',
				'type'			=> 'hidden',
				'key'			=> 'picture',
				'value'			=> $vals['picture']
			),
			array(
				'func'			=> 'opt_input',
				'type'			=> 'text',
				'key'			=> 'no_induk',
				'label'			=> 'NIK',
				'class'			=> 'input-circle',
				'value'			=> $vals['no_induk'],
				'data'			=> ''
			),
			array(
				'func'			=> 'opt_input',
				'type'			=> 'text',
				'key'			=> 'name',
				'label'			=> 'Nama',
				'class'			=> 'input-circle',
				'value'			=> $vals['name'],
				'data'			=> 'placeholder="Nama Karyawan"'
			),
			array(
				'func'			=> 'opt_input',
				'type'			=> 'text',
				'key'			=> '_nickname',
				'label'			=> 'Panggilan',
				'class'			=> 'input-circle',
				'value'			=> $vals['_nickname'],
				'data'			=> 'placeholder="Nama Panggilan"'
			),
			array(
				'func'			=> 'opt_select',
				'data'			=> array('male' => 'Laki - Laki','female' => 'Perempuan'),
				'key'			=> '_sex',
				'label'			=> 'Jenis Kelamin',
				'class'			=> 'input-circle',
				'select'		=> $vals['_sex'],
				'status'		=> ''
			),
			array(
				'func'			=> 'opt_select',
				'data'			=> array(1 => 'Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu', 'Kepercayaan'),
				'key'			=> '_religion',
				'label'			=> 'Agama',
				'class'			=> 'input-circle',
				'select'		=> $vals['_religion'],
				'status'		=> ''
			),
			array(
				'func'			=> 'opt_select',
				'data'			=> $place,
				'key'			=> '_place_date',
				'label'			=> 'Tempat Lahir',
				'class'			=> 'input-circle',
				'searching'		=> true,
				'select'		=> $vals['_place_date'],
				'status'		=> ''
			),
			array(
				'func'			=> 'opt_datepicker',
				'key'			=> '_birth_date',
				'label'			=> 'Tanggal Lahir',
				'class'			=> 'input-circle',
				'value'			=> $vals['_birth_date']
			),
			array(
				'func'			=> 'opt_select',
				'data'			=> array('belum menikah','menikah','cerai mati','cerai hidup'),
				'key'			=> '_marital',
				'label'			=> 'Status Perkawinan',
				'class'			=> 'input-circle',
				'select'		=> $vals['_marital'],
				'status'		=> ''
			)
		);

		$tab2 = array(
			0 => array(
				'func'			=> 'opt_textarea',
				'type'			=> 'text',
				'key'			=> '_address',
				'label'			=> 'Alamat',
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

		$_key = 'status';
		$_label = 'status';
		if($vals[$_key]==0){
			$_key = 'end_status';
			$_label = 'status terakhir';
		}

		$tab3 = array(
			0 => array(
				'func'			=> 'opt_select',
				'data'			=> array(1 => 'Training', 'Kontrak 1', 'Kontrak 2', 'Tetap', 'Founder', 'Pensiun'),
				'key'			=> $_key,
				'label'			=> $_label,
				'class'			=> 'input-circle',
				'select'		=> $vals[$_key],
				'status'		=> ''
			),
			array(
				'id'			=> 'divisi',
				'func'			=> 'opt_select',
				'data'			=> $divisi,
				'key'			=> 'divisi',
				'button'		=> _modal_button($add_divisi,3),
				'label'			=> 'Jabatan',
				'class'			=> 'input-circle',
				'select'		=> $vals['divisi'],
				'status'		=> ''
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
				'func'			=> 'opt_datepicker',
				'key'			=> '_entry_date',
				'label'			=> 'Tanggal Masuk',
				'class'			=> 'input-circle',
				'value'			=> $vals['_entry_date']
			),
			array(
				'func'			=> 'opt_input',
				'type'			=> 'decimal',
				'key'			=> 'dayOff',
				'label'			=> 'Sisa Cuti',
				'class'			=> 'input-circle',
				'value'			=> number_format($vals['dayOff'],1,',','.'),
				'data'			=> ''
			)
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
					'icon'	=> 'fa fa-building',
					'label'	=> 'Company'
				),
			),
			'content'	=> array(
				0	=> array(
					'func'	=> '_layout_form',
					'object'=> self::$object,
					'data'	=> array($tab1,$vals['picture'])
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
				.col-md-3.box-image-show:hover > a.remove-image-show {
				    opacity: 1;
				}

				.col-md-3.box-image-show:hover > a.change-image-show {
				    opacity: 1;
				}

				a.change-image-show {
				    position: absolute;
				    opacity: 0;
				    top: 50%;
				    left: 40%;
				}

				a.change-image-show>i {
				    font-size: 50px;
				    color: #333;
				}

				a.remove-image-show {
				    position: absolute;
				    right: 7px;
				    top: -7px;
				    opacity: 0;
				}

				a.change-image-show:hover > i {
				    opacity: 0.8;
				}

				a.remove-image-show:hover {
				    border: 1px solid #dfdfdf;
				    padding: 3px;
				}

				.box-image-show{
					cursor:default;
				}

				.box-image-show>img {
				    border-radius: 20px !important;
				}
			</style>

			<div class="row" style="padding-right: 20px;">
				<div class="col-md-3 box-image-show">
					<a class="remove-image-show" href="javascript:" onclick="remove_image_profile()">
						<i style="font-size: 24px;color: #e0262c;" class="fa fa-trash"></i>
					</a>

					<a data-toggle="modal" data-sobad="_form_upload" data-load="here_modal2" data-type="" data-alert="" href="#myModal2" class="change-image-show" onclick="sobad_button(this,0)">
						<i class="fa fa-upload"></i>
					</a>

					<img src="asset/img/user/<?php print($image) ;?>" style="width:100%" id="profile-employee">
				</div>
				<div class="col-md-9">
					<?php metronic_layout::sobad_form($args) ;?>
				</div>
			</div>

			<script type="text/javascript">
				function remove_image_profile(){
					$('#profile-employee').attr('src',"asset/img/user/no-profile.jpg");
					$('#picture-employee').val(0);
				}

				function set_file_list(val){
					select_file_list(val,false);
					$("#myModal2").modal('hide');

					$('#profile-employee').attr('src',_select_file_list[0]['url']);
					$('#picture-employee').val(_select_file_list[0]['id']);
				}
			</script>
		<?php
	}

	public function _form_upload(){

		$args = array(
			'title'		=> 'Select Photo Profile',
			'button'	=> '',
			'status'	=> array(
				'link'		=> '',
				'load'		=> ''
			)
		);

		return parent::_item_form($args);
	}

	// ----------------------------------------------------------
	// Option Divisi --------------------------------------------
	// ----------------------------------------------------------

	public function _form_divisi(){
		return divisi_absen::add_form('_add_divisi','divisi');
	}

	public function _add_divisi($args=array()){
		return divisi_absen::_add_db($args,'_option_divisi',self::$object);
	}

	public function _option_divisi(){
		$opt = '';
		$divisi = sobad_module::_gets('department',array('ID','meta_value'));
		foreach ($divisi as $key => $val) {
			$opt .= '<option value="'.$val['ID'].'"> '.$val['meta_value'].' </option>';
		}

		return $opt;
	}

	// ----------------------------------------------------------
	// Function category to database -----------------------------
	// ----------------------------------------------------------

	public function _next($id){
		$id = str_replace("next_", '', $id);
		$user = sobad_user::get_id($id,array('status','_entry_date'));

		$life = self::_check_lifetime($user[0]['status'],$user[0]['_entry_date']);
		if($life['masa']<0){
			die(_error::_alert_db('Karyawan belum Habis Masa!!!'));
		}

		$status = $user[0]['status'];

		$status += 1;
		if($status==5){
			$status = 6;
		}

		$q = sobad_db::_update_single($id,'abs-user',array('ID' => $id,'status' => $status));
		
		if($q!==0){
			$pg = isset($_POST['page'])?$_POST['page']:1;
			return self::_get_table($pg);
		}
	}

	public function _resign($id){
		return self::_status($id,1);
	}

	public function _dismissed($id){
		return self::_status($id,2);
	}

	private function _status($id,$type=0){
		$id = str_replace("status_", '', $id);
		$user = sobad_user::get_id($id,array('status'));
		$status = $user[0]['status'];

		$q = sobad_db::_update_single($id,'abs-user',array('ID' => $id,'status' => 0,'end_status' => $status));

		// Insert Status Berhenti
		sobad_db::_insert_table('abs-user-meta',array(
				'meta_id' 		=> $id,
				'meta_key' 		=> '_resign_status',
				'meta_value' 	=> $type
			)
		);

		// Update user-meta
		$data2 = array('meta_id' => $id,'meta_value' => date('Y-m-d'));

		$dt_meta = sobad_user::check_meta($id,'_resign_date');	
		$check = array_filter($dt_meta);
		if(empty($check)){
			$data2['meta_key'] = '_resign_date';
			$q = sobad_db::_insert_table('abs-user-meta',$data2);
		}else{
			$whr = "meta_id='$id' AND meta_key='_resign_date'";
			$q = sobad_db::_update_multiple($whr,'abs-user-meta',$data2);
		}
		
		if($q!==0){
			$pg = isset($_POST['page'])?$_POST['page']:1;
			return self::_get_table($pg);
		}
	}

	public function _check_noInduk($id=0){
		//Check user ---> employee atau internship
		$whr = "AND no_induk='$id'";
		if(preg_match("/^M[0-9]{4}/", $id)){
			$_id = preg_replace("/^M/", "", $id);

			$year = preg_replace("/[0-9]{2}\z/","", $_id);
			$year = "20".$year;

			$_id = preg_replace("/^[0-9]{2}/","", $_id);
			intval($_id);			

			$whr = "AND YEAR(`abs-user`.inserted)='$year' AND no_induk='$_id'";
		}else{
			$_id = $id;
		}

		return array(
			'id'	=> $_id,
			'where'	=> $whr
		);
	}

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

		if(!isset($files['status'])){
			$files['status'] = 'berhenti';
		}

		return self::_conv_import($files);
	}

	public function _conv_import($files=array()){

		if(isset($files['no_induk']) && !empty($files['no_induk'])){
			$check = self::_check_noInduk($files['no_induk']);
			$files['ID'] = $check['id'];
			$where = $check['where'];
			$status = false;

			$user = sobad_user::get_all(array('ID'),$where);
			$check = array_filter($user);
			if(!empty($check)){
				$status = true;
			}

			return array(
				'status'	=> $status,
				'data'		=> $files
			);
		}else{
			$status = false;
			$name = $files['name'];
			$user = sobad_user::get_all(array('ID'),"AND name='$name'");
			
			$check = array_filter($user);
			if(!empty($check)){
				$files['ID'] = $user[0]['ID'];
				$status = true;
			}

			return array(
				'status'	=> $status,
				'data'		=> $files
			);
		}
	}

	private function _convert_column($files=array()){
		$data = array();

		$args = array(
			'no_induk'		=> array(
				'data'			=> array('nik','no induk','induk karyawan','no induk karyawan'),
				'type'			=> 'number'
			),
			'name'			=> array(
				'data'			=> array('nama','nama lengkap','nama karyawan'),
				'type'			=> 'text'
			),
			'_nickname'		=> array(
				'data'			=> array('nama pendek','nama panggilan','panggilan','nickname'),
				'type'			=> 'text'
			),
			'_sex'			=> array(
				'data'			=> array('sex','kelamin','jenis kelamin'),
				'type'			=> 'number'
			),
			'_religion'		=> array(
				'data'			=> array('agama','religion'),
				'type'			=> 'number'
			),
			'_place_date'	=> array(
				'data'			=> array('tempat lahir'),
				'type'			=> 'number'
			),
			'_birth_date'	=> array(
				'data'			=> array('tanggal lahir'),
				'type'			=> 'date'
			),
			'_marital'		=> array(
				'data'			=> array('marital','status perkawinan','status pernikahan'),
				'type'			=> 'number'
			),
			'_address'		=> array(
				'data'			=> array('alamat','alamat lengkap','address','alamat sesuai ktp'),
				'type'			=> 'text'
			),
			'phone_no'		=> array(
				'data'			=> array('no. hp','no hp','no. handphone','no handphone','no telp','no. telp'),
				'type'			=> 'text'
			),
			'_entry_date'	=> array(
				'data'			=> array('tanggal masuk','masuk tanggal'),
				'type'			=> 'date'
			),
			'divisi'		=> array(
				'data'			=> array('jabatan','departemen','divisi'),
				'type'			=> 'number'
			),
			'status'		=> array(
				'data'			=> array('status','status karyawan'),
				'type'			=> 'number'
			)
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
		$data = array();
		switch ($key) {
			case '_sex':
				$_data = strtolower($_data);
				$_data = preg_replace('/\s+/', '', $_data);
				if($_data=='laki-laki'){
					$_data = 'male';
				}else if($_data=='perempuan'){
					$_data = 'female';
				}else{
					$_data = '';
				}

				break;

			case '_religion':
				$args = array('islam' => 1, 'kristen' => 2, 'katolik' => 3, 'hindu' => 4, 'buddha' => 5, 'konghucu' => 6, 'kepercayaan' => 7);
				$_data = strtolower($_data);
				if(isset($args[$_data])){
					$_data = $args[$_data];
				}else{
					$_data = 0;
				}
				
				break;

			case '_place_date':
				$city = sobad_wilayah::get_all(array('id_kab'),"kabupaten LIKE '%$_data%' GROUP BY id_kab");
				
				$check = array_filter($city);
				if(!empty($check)){
					$_data = $city[0]['id_kab'];
				}else{
					$_data = 0;
				}

				break;

			case '_marital':
				$args = array('belum menikah' => 0,'menikah' => 1,'cerai mati' => 2);
				$_data = strtolower($_data);
				if(isset($args[$_data])){
					$_data = $args[$_data];
				}else{
					$_data = 0;
				}
				
				break;

			case '_address':
				$data = array(
					'_address' 		=> '',
					'_province'		=> 0,
					'_city'			=> 0,
					'_subdistrict'	=> 0,
					'_postcode'		=> 0
				);

				$_data = explode(',',$_data);
				$_count = count($_data);
				$_pos = explode('.',$_data[$_count-1]);

				$_data[$_count-1] = $_pos[0];
				$_pos = preg_replace('/\s+/', '', isset($_pos[1])?$_pos[1]:'');

				for($i = ($_count - 1); $i>=0; $i--){
					
					// search provinsi
					if(empty($data['_province'])){
						$prov = $_data[$i];
						$prov = trim($prov);
						$prov = sobad_wilayah::get_all(array('id_prov'),"provinsi LIKE '%".$prov."%' GROUP BY id_prov");
						
						$check = array_filter($prov);
						if(!empty($check)){
							$data['_province'] = $prov[0]['id_prov'];
							unset($_data[$i]);

							continue;
						}
					}

					// search Kabupaten
					if(empty($data['_city'])){
						$kab = str_replace('kota', '', $_data[$i]);
						$kab = str_replace('kab', '', $kab);
						$kab = str_replace('.', '', $kab);
						$kab = str_replace('kabupaten', '', $kab);
						$kab = trim($kab);

						if(empty($data['_province'])){
							$kab = sobad_wilayah::get_all(array('id_prov','id_kab'),"kabupaten LIKE '%".$kab."%' GROUP BY id_kab");
						}else{
							$prov = $data['_province'];
							$kab = sobad_wilayah::get_all(array('id_prov','id_kab'),"id_prov='$prov' AND kabupaten LIKE '%".$kab."%' GROUP BY id_kab");
						}

						$check = array_filter($kab);
						if(!empty($check)){
							$data['_province'] = $kab[0]['id_prov'];
							$data['_city'] = $kab[0]['id_kab'];
							unset($_data[$i]);

							continue;
						}
					}

					// search kecamatan
					if(empty($data['_subdistrict'])){
						$kec = str_replace('kec', '', $_data[$i]);
						$kec = str_replace('.', '', $kec);
						$kec = str_replace('kecamatan', '', $kec);
						$kec = trim($kec);


						if(empty($data['_province']) && empty($data['_city'])){
							//$data['_address'] = implode(', ', $_data);

							break;
						}else{
							$prov = $data['_province'];
							$kab = $data['_city'];
							$kec = sobad_wilayah::get_all(array('id_kec','kodepos'),"id_prov='$prov' AND id_kab='$kab' AND kecamatan LIKE '%".$kec."%' GROUP BY id_kec");
						}

						$check = array_filter($kec);
						if(!empty($check)){
							$data['_subdistrict'] = $kec[0]['id_kec'];

							if(empty($_pos)){
								$data['_postcode'] = $kec[0]['kodepos'];
							}else{
								$data['_postcode'] = $_pos;
							}

							unset($_data[$i]);

							continue;
						}

						break;
					}
				}
				
				$_data = implode(', ', $_data);
				$_data .= empty($_pos)?'':empty($data['_subdistrict'])?'. '.$_pos:'';
				break;

			case 'status':
				$args = array('berhenti' => 0, 'resign' => 0, 'training' => 1, 'masa percobaan' => 1, 'kontrak1' => 2, 'kontrak2' => 3, 'tetap' => 4, 'founder' => 5, 'pensiun' => 6, 'internship' => 7);
				
				$_data = strtolower($_data);
				$_data = preg_replace('/\s+/', '', $_data);

				if(isset($args[$_data])){
					$_data = $args[$_data];
				}else{
					$_data = 1;
				}

				break;

			case 'divisi':
				$args = sobad_module::_gets('department',array('ID'),"AND meta_value='$_data'");
				
				$check = array_filter($args);
				if(empty($check)){
					$_data = sobad_db::_insert_table('abs-module',array('meta_key' => 'department','meta_value' => ucwords($_data) ));
				}else{
					$_data = $args[0]['ID'];
				}
				
				break;
			
			default:
				// default
				break;
		}

		if($type=='date'){
			$args = conv_month_id();
			foreach ($args as $ky => $vl) {
				$_data = str_replace($vl, sprintf("%02d",$ky), $_data);
				$_data = preg_replace('/\s+/', '-', $_data);
			}
		}

		$data[$key] = formatting::sanitize($_data,$type);

		return $data;
	}
}