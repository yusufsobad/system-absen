<?php
include 'pages/function.php';

abstract class _page{

	protected static $page = 1;

	protected static $search = false;

	protected static $type = ''; //Default

	protected static $data = array(); // data pencarian

	protected static $limit = 10;

	protected static $list_meta = '';

	// ----------------------------------------------------------
	// Layout Pages  --------------------------------------------
	// ----------------------------------------------------------
	public function _sidemenu(){
		return static::layout();
	}

	public function _tabs($type){
		self::$type = $type;
		$data = static::get_box();
		$metronic = new metronic_layout();
		
		ob_start();
		?>
			<div class="row">
				<?php $metronic->_content('_portlet',$data); ?>
			</div>
		<?php
		return ob_get_clean();
	}

	protected function like_search($args=array(),$whr=''){
		$kata = '';$where = '';
		$cari = self::$data;
		$search = isset($cari['search'])?$cari['search']:'';
		$src = array();

		$meta = array();
		if(property_exists(new static, 'table')){
			$post = '';
			if(property_exists(new static, 'post')){
				$post = static::$post;
			}

			$object = static::$table;
			$meta = $object::list_meta($post);

			$blueprint = $object::blueprint($post);
			if(isset($blueprint['detail'])){
				foreach ($blueprint['detail'] as $key => $val) {
					if(in_array($key, $args)){
						foreach ($val['column'] as $ky => $vl) {
							$args[] = '_'.$key.'.'.$vl;
						}
					}
				}
			}
		}
		
		if(!empty($cari['words'])){
			if($search==0){
				unset($args[0]);

				$search = implode(',',$args);
				$kata = $cari['words'];
				
				foreach($args as $key => $val){
					if(in_array($val, $meta)){
						$src[] = "(meta_key='$val' AND meta_value LIKE '%$kata%') ".$whr;
					}else{
						$src[] = "$val LIKE '%$kata%' ".$whr;
					}
				}
				
				$src = implode(" OR ",$src);
				$where = "AND (".$src.") ";
			}else{
				$search = $args[$search];
				$kata = $cari['words'];
				if(in_array($search, $meta)){
					$where = "AND (meta_key='$search' AND meta_value LIKE '%$kata%') ".$whr;
				}else{
					$where = "AND $search LIKE '%$kata%' ".$whr;
				}
			}
		}else{
			$where = $whr;
		}
		
		return array($where,$kata);
	}

	protected function action(){
		$add = array(
			'ID'	=> 'add_0',
			'func'	=> 'add_form',
			'color'	=> 'btn-default',
			'icon'	=> 'fa fa-plus',
			'label'	=> 'Tambah',
			'type'	=> self::$type
		);
		
		return edit_button($add);
	}

	// ----------------------------------------------------------
	// Function Form Select wilayah -----------------------------
	// ----------------------------------------------------------

		// -------------- get value select opt ----------------------
	public function get_cities($id=0){
		$kota = array();
		if($id!=0){
			$cities = sobad_wilayah::get_cities($id);
			foreach($cities as $key => $kab){
				$tipe = $kab['tipe'];
				if($tipe=='Kabupaten'){
					$tipe = 'Kab.';
				}
				
				$kota[$kab['id_kab']] = $tipe.' '.$kab['kabupaten'];
			}
		}
		
		return $kota;
	}

	public function get_subdistricts($id=0){
		$kec = array();
		if($id!=0){
			$kec = sobad_wilayah::get_subdistricts($id);
			$kec = convToOption($kec,'id_kec','kecamatan');
		}
		
		return $kec;
	}

	public function get_postcodes($prov=0,$kota=0,$kec=0){
		$pos = array();
		if($kec!=0){
			$pos = sobad_wilayah::get_postcode($prov,$kota,$kec);
			$pos = convToOption($pos,'kodepos','kodepos');
		}
		
		return $pos;
	}

	// -------------- option select onchange --------------------

	public function option_city($id=0){
		$data = self::get_cities($id);
		return self::_conv_option($data);
	}

	public function option_subdistrict($id=0){
		$data = self::get_subdistricts($id);
		return self::_conv_option($data);
	}

	public function option_postcode($id=0){
		$ids = sobad_wilayah::get_id_by_subdistrict($id);

		$prov = $ids[0]['id_prov'];
		$kab = $ids[0]['id_kab'];
		$kec = $ids[0]['id_kec'];
		
		$data = self::get_postcodes($prov,$kab,$kec);	
		return self::_conv_option($data);	
	}

	protected function _conv_option($args=array()){
		$check = array_filter($args);
		if(empty($check)){
			return '';
		}
		
		$opt = '';
		foreach($args as $key => $val){
			$opt .= '<option value="'.$key.'">'.$val.'</option>';
		}
		
		return $opt;
	}

	// ----------------------------------------------------------
	// Function Pages to database -------------------------------
	// ----------------------------------------------------------
	protected function _get_table($idx,$args=array()){
		if($idx==0){
			$idx = 1;
		}

		self::$page = $idx;
		self::$search = true;
		self::$data = isset($_POST['args'])?sobad_asset::ajax_conv_json($_POST['args']):$args;
		self::$type = isset($_POST['type'])?$_POST['type']:'';

		$table = static::table();
		return table_admin($table);
	}

	public function _pagination($idx){
		return self::_get_table($idx);
	}

	public function _search($args=array()){
		$args = sobad_asset::ajax_conv_json($args);
		return self::_get_table(1,$args);
	}

	public function _trash($id=0){
		$id = str_replace('trash_','',$id);
		intval($id);

		$object = static::$table;
		$table = $object::$table;

		$q = sobad_db::_update_single($id,$table,array('ID' => $id, 'trash' => 1));

		if($q===1){
			$pg = isset($_POST['page'])?$_POST['page']:1;
			return self::_get_table($pg);
		}
	}

	public function _recovery($id=0){
		$id = str_replace('recovery_','',$id);
		intval($id);

		$object = static::$table;
		$table = $object::$table;

		$q = sobad_db::_update_single($id,$table,array('ID' => $id, 'trash' => 0));

		if($q===1){
			$pg = isset($_POST['page'])?$_POST['page']:1;
			return self::_get_table($pg);
		}
	}

	public function _delete($id=0){
		$id = str_replace('del_','',$id);
		intval($id);

		$object = static::$table;
		$table = $object::$table;

		if(property_exists($object, 'tbl_meta')){
			$q = sobad_db::_delete_multiple("meta_id='$id'",$object::$tbl_meta);
		}

		if(property_exists($object, 'tbl_join')){
			$q = sobad_db::_delete_multiple("reff='$id'",$object::$tbl_join);
		}

		$q = sobad_db::_delete_single($id,$table);

		if($q===1){
			$pg = isset($_POST['page'])?$_POST['page']:1;
			return self::_get_table($pg);
		}
	}

	public function _edit($id=0){
		$id = str_replace('edit_','',$id);
		intval($id);
		
		$args = static::_array();
		self::$type = isset($_POST['type'])?$_POST['type']:'';

		$post = '';
		if(property_exists(new static, 'post')){
			$post = static::$post;
		}

		$object = static::$table;
		$q = $object::get_id($id,$args,'',$post);
		
		if($q===0){
			return '';
		}
		
		return static::edit_form($q[0]);
	}

	public function _import(){
		$fileName = $_FILES["data"]["tmp_name"];
		
		if ($_FILES["data"]["size"] > 0) {
	        $delimiter = _detectDelimiter($fileName);
	        $file = fopen($fileName, "r");
	        
	        $status = true;$_colm = array();$files = array();
	        while (($column = fgetcsv($file, 10000, $delimiter)) !== FALSE) {
	        	foreach ($column as $key => $val) {
	        		if($status){
	        			$_colm[$key] = strtolower($val);
	        		}else{
	        			$files[$_colm[$key]] = $val;
	        		}
	        	}

	        	if(!$status){
		        	//Check data
		        	$data = array();
		        	$check = static::_check_import($files);
		        	
		        	foreach ($check['data'] as $key => $val) {
		        		$data[] = array('name' => $key, 'value' => $val);
		        	}

		        	if($check['status']){
		        		$q = self::_schema(json_encode($data),false); //Update data
		        	}else{
		        		if($check['insert']){
		        			$q = self::_schema(json_encode($data),true); // Add data
		        		}
		        	}
		        }

		        $status = false;
	        }
			
			$pg = isset($_POST['page'])?$_POST['page']:1;
			return self::_get_table($pg);
	    }
	}

	// ----------------------------------------------------------
	// Function Update to database ------------------------------
	// ----------------------------------------------------------

	protected function _schema($_args=array(),$add=false){
		$args = sobad_asset::ajax_conv_json($_args);
		if(is_callable(array(new static(), '_callback'))){
			$args = static::_callback($args,$_args);
		}
		
		$id = $args['ID'];
		unset($args['ID']);
	
		$src = array();
		if(isset($args['search'])){
			$src = array(
				'search'	=> $args['search'],
				'words'		=> $args['words']
			);

			unset($args['search']);
			unset($args['words']);
		}

		$post = '';
		if(property_exists(new static, 'post')){
			$post = static::$post;
		}

		$object = static::$table;
		$schema = $object::blueprint($post);

		self::$list_meta = $object::list_meta($post);

		$data = array();
		$list = $object::_list();
		foreach ($list as $key => $val) {
			if(isset($args[$val])){
				$data[$val] = $args[$val];
			}
		}

		if($add){
			
			$idx = sobad_db::_insert_table($schema['table'],$data);
			$q = self::_add_meta_db($idx,$args,$schema);

			$id = $idx;
		}else{

			$q = sobad_db::_update_single($id,$schema['table'],$data);
			$q = self::_update_meta_db($id,$args,$schema);
		}

		return array('index' => $id, 'data' => $q,'search' => $src);
	}	

	public function _update_db($args=array()){
		$args = self::_schema($args,false);
		$q = $args['data'];
		$src = $args['search'];

		if($q!==0){
			$pg = isset($_POST['page'])?$_POST['page']:1;
			return self::_get_table($pg,$src);
		}
	}

	protected function _update_meta_db($idx=0,$args=array(),$schema=array()){
		$q = $idx;
		$object = static::$table;
		// Meta Table
		if(isset($schema['meta'])){
			$table = $schema['meta']['table'];

			// Insert Data Meta
			$_meta_key = $schema['meta']['key'];
			$list = self::$list_meta;	
			foreach ($list as $key => $val) {
				if(!isset($args[$val])){
					continue;
				}

				$_data = array(
					$_meta_key		=> $idx,
					'meta_key'		=> $val,
					'meta_value'	=> $args[$val]
				);

				$meta = $object::check_meta($idx,$val);
				$check = array_filter($meta); 
				if(empty($check)){
					$q = sobad_db::_insert_table($table,$_data);
				}else{
					$q = sobad_db::_update_single($meta[0]['ID'],$table,$_data);
				}
			}
		}

		return $q;
	}

	// ----------------------------------------------------------
	// Function Add to database -------------------------------
	// ----------------------------------------------------------	

	public function _add_db($args=array(),$menu='default',$obj=''){
		$args = self::_schema($args,true);
		$q = $args['data'];
		$src = $args['search'];
		
		if($q!==0){
			if($menu=='default'){
				$pg = isset($_POST['page'])?$_POST['page']:1;
				return self::_get_table($pg,$src);
			}else{
				if(is_callable(array($obj,$menu))){
					return $obj::{$menu}();
				}else{
					die(_error::_alert_db("Object Not Found!!!"));
				}
			}
		}
	}

	protected function _add_meta_db($idx=0,$args=array(),$schema=array()){
		$q = $idx;
		// Meta Table
		if(isset($schema['meta'])){
			$table = $schema['meta']['table'];

			// Insert Data Meta
			$_meta_key = $schema['meta']['key'];
			$list = self::$list_meta;
			foreach ($list as $key => $val) {
				if(!isset($args[$val])){
					continue;
				}

				$_data = array(
					$_meta_key		=> $idx,
					'meta_key'		=> $val,
					'meta_value'	=> $args[$val]
				);

				$q = sobad_db::_insert_table($table,$_data);
			}
		}

		return $q;
	}

}