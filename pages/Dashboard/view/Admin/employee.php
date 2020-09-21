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
			'picture'
		);

		return $args;
	}

	protected function table(){
		$data = array();
		$args = array('ID','no_induk','name','_address','phone_no','status','picture','end_status');

		$start = intval(self::$page);
		$nLimit = intval(self::$limit);

		$tab = parent::$type;
		$type = str_replace('employee_', '', $tab);

		if($type==0){
			$where = "AND `abs-user`.status NOT IN ('0','6') AND `abs-user`.end_status!='6'";
		}else if($type==9){
			$where = "AND `abs-user`.status='0' AND `abs-user`.end_status!='6'";
		}else{
			$where = "AND `abs-user`.status='$type'";
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
		
		$limit = 'ORDER BY no_induk ASC,ID ASC LIMIT '.intval(($start - 1) * $nLimit).','.$nLimit;
		$where .= $limit;

		$args = sobad_user::get_employees($args,$where,true);
		$sum_data = sobad_user::count("1=1 ".$cari);

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
				'type'	=> $tab
			);

			if($val['status']){
				$status = self::_conv_status($val['status']);
			}else{
				$status = self::_conv_status($val['end_status']);
			}

			$image = empty($val['notes_pict'])?'no-profile.jpg':$val['notes_pict'];
			
			$data['table'][$key]['tr'] = array('');
			$data['table'][$key]['td'] = array(
				'no'		=> array(
					'center',
					'5%',
					$no,
					true
				),
				'profile'	=> array(
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
			'action'	=> parent::action(),
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
			'label'	=> self::_conv_status(0),
			'qty'	=> sobad_user::count("status!='0'")
		);

		for($i=1;$i<6;$i++){
			$tabs[$i] = array(
				'key'	=> 'employee_'.$i,
				'label'	=> self::_conv_status($i),
				'qty'	=> sobad_user::count("status='$i'")
			);
		}

		$tabs[6] = array(
			'key'	=> 'employee_9',
			'label'	=> 'Berhenti',
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

	private function _conv_status($status=''){
		$types = array('Aktif','Training','Kontrak 1','Kontrak 2','Tetap','Pensiun');
		$label = isset($types[$status])?$types[$status]:'Berhenti';

		return $label;
	}

	// ----------------------------------------------------------
	// Form data category -----------------------------------
	// ----------------------------------------------------------
	public function add_form($func='',$load='sobad_portlet'){
		$no = sobad_user::get_maxNIK();
		$no = sprintf("%03d",$no+1);

		$vals = array(0,$no,1,'',0,1,'','','','','male',date('Y-m-d'),'',date('Y-m-d'),'',0,0,0,0,1,1,'',0);
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
				'data'			=> array('belum menikah','menikah','cerai mati'),
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

		$tab3 = array(
			0 => array(
				'func'			=> 'opt_select',
				'data'			=> array(1 => 'Training', 'Kontrak 1', 'Kontrak 2', 'Tetap', 'Pensiun'),
				'key'			=> 'status',
				'label'			=> 'Status',
				'class'			=> 'input-circle',
				'select'		=> $vals['status'],
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

	public function _status($id){
		$id = str_replace("status_", '', $id);
		$user = sobad_user::get_id($id,array('status'));
		$status = $user[0]['status'];

		$q = sobad_db::_update_single($id,'abs-user',array('ID' => $id,'status' => 0,'end_status' => $status));

		// Update user-meta
		$data2 = array('meta_id' => $id,'meta_key' => date('Y-m-d'));

		$dt_meta = sobad_user::check_meta($id,'resign_date');	
		$check = array_filter($dt_meta);
		if(empty($check)){
			$data2['meta_key'] = 'resign_date';
			$q = sobad_db::_insert_table('abs-user-meta',$data2);
		}else{
			$whr = "meta_id='$id' AND meta_key='resign_date'";
			$q = sobad_db::_update_multiple($whr,'abs-user-meta',$data2);
		}
		
		if($q!==0){
			$pg = isset($_POST['page'])?$_POST['page']:1;
			return self::_get_table($pg);
		}
	}
}