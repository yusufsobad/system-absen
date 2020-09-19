<?php

abstract class _file_manager extends _page{

	protected static $table = 'sobad_post';

	// ----------------------------------------------------------
	// Form new product -----------------------------------------
	// ----------------------------------------------------------

	protected function _get_image_list_file($start=1){
		$args = array('ID','notes','var');

		$kata = '';$where = "ORDER BY inserted DESC ";
		if(parent::$search){
			$src = parent::like_search($args);
			$cari = $src[0];
			$where = $src[0]." ".$where;
			$kata = $src[1];
		}else{
			$cari=$where;
		}

		$limit = 'LIMIT '.intval(($start - 1) * 18).',18';
		$where .= $limit;
		
		$object = self::$table;
		$func = static::$file_type;
		$func = 'get_'.$func.'s';

		$args = $object::{$func}($args,$where);
		$sum_data = $object::{$func}(array('ID'),$cari);

		$_list = array();
		
		$_list['data'] = array(
			'object'	=> static::$object,
			'func' 		=> '_search_list_file',
			'data' 		=> $kata,
			'load' 		=> 'inline_malika81',
			'name' 		=>'_file'
		);

		$_list['search'] = array('name');

		$_list['list'] = array();

		$_list['page'] = array(
			'start'		=> $start,
			'qty'		=> count($sum_data),
			'limit'		=> 18,
			'load'		=> 'inline_malika81',
			'func'		=> '_pagination_list_file',
			'object'	=> static::$object
		);

		$_list['script'] = array(
			'id'			=> 'inline_malika81',
			'func_remove'	=> '_remove_file_list'
		);

		foreach ($args as $key => $val) {
			$_list['list'][$key] = array(
				'id'		=> $val['ID'],
				'name'		=> $val['notes'],
				'url'		=> $val['notes'],
				'type'		=> $val['var'],
				'func'		=> 'set_file_list(this)'
			);
		}

		return $_list;
	}

	public function _search_list_file($args=array()){
		$args = sobad_asset::ajax_conv_json($args);
		$args = array(
			'words'	=> $args['words_file'],
			'search'=> $args['search_file']
		);

		parent::$search = true;
		parent::$data = $args;

		$data = array(
			'func'	=> '_list_file',
			'data'	=> self::_get_image_list_file(1)
		);

		ob_start();
		metronic_layout::sobad_file_manager($data);
		return ob_get_clean();
	}

	public function _pagination_list_file($start=1){
		$status = is_array($_POST['args'])?true:false;
		$args = sobad_asset::ajax_conv_array_json($_POST['args']);
		$args = array(
			'words'	=> $args['words'][0],
			'search'=> $args['search'][0]
		);

		parent::$search = $status;
		parent::$data = $args;

		$data = array(
			'func'	=> '_list_file',
			'data'	=> self::_get_image_list_file($start)
		);

		ob_start();
		metronic_layout::sobad_file_manager($data);
		return ob_get_clean();
	}

	public function _remove_file_list($idx=0){
		$asset = static::$url;

		$post = sobad_post::get_id($idx,array('notes'));

	// Hapus File
		$check = array_filter($post);
		if(!empty($check)){
			$target_file = $asset.'/'.$post[0]['notes'];	
			if (file_exists($target_file)) {
				unlink($target_file);
			}else{
				die(_error::_alert_db("Gagal Menghapus File"));
			}
		}

		// Hapus database
		$object = self::$table;
		$table = $object::$table;
		sobad_db::_delete_single($idx,$table);

		$data = array(
			'func'	=> '_list_file',
			'data'	=> self::_get_image_list_file(1)
		);

		ob_start();
		metronic_layout::sobad_file_manager($data);
		return ob_get_clean();
	}

	public function _item_form($args=array()){
		$_list = self::_get_image_list_file(1);
		$_list['object'] = static::$object;

		$type_var = array(
			0		=> 'Undefined',
			1		=> 'Std. Part',
			2		=> 'Part',
			3		=> 'Assy',
			4		=> 'Product',
			5		=> 'Paket'
		);

		$tab1 = array(
			'func'		=> '_upload_file',
			'data'		=> array(
				'id'		=> 'upload_item',
				'func'		=> '_upload_product',
				'object'	=> static::$object,
				'accept'	=> 'image/*',
				'load'		=> 'inline_malika81',
				'object'	=> static::$object
			)
		);

		$tab2 = array(
			'func'		=> '_list_file',
			'data'		=> $_list
		);

		$data = array(
			'menu'		=> array(
				80	=> array(
					'key'	=> '',
					'icon'	=> 'fa fa-upload',
					'label'	=> 'Upload File'
				),
				81	=> array(
					'key'	=> '',
					'icon'	=> 'fa fa-file',
					'label'	=> 'List File'
				)
			),
			'content'	=> array(
				80	=> array(
					'func'	=> 'sobad_file_manager',
					'data'	=> $tab1
				),
				81	=> array(
					'func'	=> 'sobad_file_manager',
					'data'	=> $tab2
				)
			)
		);

		$args['func'] = array('_inline_menu');
		$args['data'] = array($data);
		
		return modal_admin($args);
	}

	public function _upload_product(){
		$name_file = sobad_asset::handling_upload_file('file',static::$url);

		$args = array(
			'notes'			=> $name_file,
			'var'			=> static::$file_type,
		);

		$object = self::$table;
		$table = $object::$table;
		sobad_db::_insert_table($table,$args);

		$data = array(
			'func'	=> '_list_file',
			'data'	=> self::_get_image_list_file(1)
		);

		ob_start();
		metronic_layout::sobad_file_manager($data);
		return ob_get_clean();
	}
}