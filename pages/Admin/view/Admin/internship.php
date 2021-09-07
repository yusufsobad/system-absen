<?php

class internship_admin extends _page{
	protected static $object = 'internship_admin';

	protected static $table = 'sobad_user';

	protected static $post = 'internship';

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
			'picture',
			'divisi',
			'inserted'
		);

		return $args;
	}

	protected function table(){
		$data = array();
		$args = self::_array();

		$start = intval(self::$page);
		$nLimit = intval(self::$limit);

		$tab = parent::$type;
		$type = str_replace('intern_', '', $tab);

		if($type=='1'){
			$where = "AND status='7'";
		}else{
			$where = "AND status='0' AND end_status='7'";
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

		$args = sobad_user::get_all($args,$where,self::$post);
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

			$status = internship_absen::_conv_divisi($val['divisi']);
			$no_induk = internship_absen::_conv_no_induk($val['no_induk'],$val['inserted'],$val['divisi']);
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
					$no_induk,
					true
				),
				'Nama'		=> array(
					'left',
					'auto',
					$val['name'],
					true
				),
				'Status'		=> array(
					'left',
					'15%',
					$status,
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
			'title'	=> 'Karir <small>data karir</small>',
			'link'	=> array(
				0	=> array(
					'func'	=> self::$object,
					'label'	=> 'karir'
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
			'label'		=> 'Data Karir',
			'tool'		=> '',
			'action'	=> self::action(),
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
					'qty'	=> sobad_user::count("status='7'")
				),
				array(
					'key'	=> 'intern_0',
					'label'	=> 'Non Aktif',
					'qty'	=> sobad_user::count("status='0' AND end_status='7'")
				)
			),
			'func'	=> '_portlet',
			'data'	=> $box
		);

		$opt = array(
			'title'		=> self::head_title(),
			'style'		=> array(),
			'script'	=> array(self::$object,'_script')
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
			)
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
	// Function category to database -----------------------------
	// ----------------------------------------------------------

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