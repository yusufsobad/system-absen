<?php

class prodi_absen extends _page{

	protected static $object = 'prodi_absen';

	protected static $table = 'sobad_module';

	// ----------------------------------------------------------
	// Layout category  ------------------------------------------
	// ----------------------------------------------------------

	protected function _array(){
		$args = array(
			'ID',
			'meta_value',
			'meta_note'
		);

		return $args;
	}

	protected function table(){
		$data = array();
		$args = self::_array();

		$start = intval(parent::$page);
		$nLimit = intval(parent::$limit);
		
		$kata = '';$where = "AND meta_key='study_program'";
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

			$qty = sobad_user::count("(status='7' OR end_status='7') AND (meta_key='_study_program' AND meta_value='$id') ");
			
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
					$val['meta_value'],
					true
				),
				'note'	=> array(
					'left',
					'25%',
					$val['meta_note'],
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
			'title'	=> 'Prodi <small>data prodi</small>',
			'link'	=> array(
				0	=> array(
					'func'	=> self::$object,
					'label'	=> 'prodi'
				)
			),
			'date'	=> false
		); 
		
		return $args;
	}

	protected function get_box(){
		$data = self::table();
		
		$box = array(
			'label'		=> 'Data Prodi',
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
		$vals = array(0,'','');

		if($func=='add_0'){
			$func = '_add_db';
		}
		
		$args = array(
			'title'		=> 'Tambah data prodi',
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
		
		$vals = array(
			$vals['ID'],
			$vals['meta_value'],
			$vals['meta_note']
		);
		
		$args = array(
			'title'		=> 'Edit data prodi',
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
				'value'			=> $vals[0]
			),
			1 => array(
				'func'			=> 'opt_input',
				'type'			=> 'text',
				'key'			=> 'meta_value',
				'label'			=> 'Nama Prodi',
				'class'			=> 'input-circle',
				'value'			=> $vals[1],
				'data'			=> 'placeholder="ex: Tehnik Mesin"'
			),
			2 => array(
				'func'			=> 'opt_input',
				'type'			=> 'text',
				'key'			=> 'meta_note',
				'label'			=> 'Catatan',
				'class'			=> 'input-circle',
				'value'			=> $vals[2],
				'data'			=> 'placeholder="note"'
			),
		);
		
		$args['func'] = array('sobad_form');
		$args['data'] = array($data);
		
		return modal_admin($args);
	}

	// ----------------------------------------------------------
	// Function category to database -----------------------------
	// ----------------------------------------------------------

	public function _update_db($args=array()){
		$lang = get_locale();
		$args = sobad_asset::ajax_conv_json($args);
		$id = $args['ID'];
		unset($args['ID']);
		
		if(isset($args['search'])){
			$src = array(
				'search'	=> $args['search'],
				'words'		=> $args['words']
			);

			unset($args['search']);
			unset($args['words']);
		}

		$data = array(
			'meta_value'	=> $args['meta_value'],
			'meta_note'		=> $args['meta_note']
		);
		
		$q = sobad_db::_update_single($id,'abs-module',$data);
		
		if($q===1){
			$pg = isset($_POST['page'])?$_POST['page']:1;
			return parent::_get_table($pg,$src);
		}
	}

	public function _add_db($args=array(),$menu='contact',$obj=''){
		$lang = get_locale();
		$args = sobad_asset::ajax_conv_json($args);
		$id = $args['ID'];
		unset($args['ID']);
		
		if(isset($args['search'])){
			$src = array(
				'search'	=> $args['search'],
				'words'		=> $args['words']
			);

			unset($args['search']);
			unset($args['words']);
		}
		
		$data = array(
			'meta_key'		=> 'study_program',
			'meta_value'	=> $args['meta_value'],
			'meta_note'		=> $args['meta_note']
		);
		
		$q = sobad_db::_insert_table('abs-module',$data);
		
		if($q!==0){
			if($menu=='contact'){
				$pg = isset($_POST['page'])?$_POST['page']:1;
				return parent::_get_table($pg,$src);
			}else{
				if(is_callable(array($obj,$menu))){
					return $obj::{$menu}();
				}else{
					die(_error::_alert_db("Object Not Found!!!"));
				}
			}
		}
	}
}