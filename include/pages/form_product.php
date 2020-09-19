<?php

abstract class form_product extends _page{
	protected static $object = 'form_product';

	protected function _array(){
		return array('ID','name','product_code','price','picture');
	}

	private function table($sort='name ASC'){	
		$data = array();
		$filter=isset($_SESSION['filter_product'])?$_SESSION['filter_product']:array();
		$args = self::_array();

		$start = intval(parent::$page);
		$nLimit = intval(parent::$limit);
		
		$kata = '';$where = isset($filter['where'])?$filter['where']:'';
		if(parent::$search){
			$src = parent::like_search($args,$where);
			$cari = $src[0];
			$where = $src[0];
			$kata = $src[1];
		}else{
			$cari=$where;
		}

		$limit = ' LIMIT '.intval(($start - 1) * $nLimit).','.$nLimit;
		$order_by = " ORDER BY ".$sort;
		$where .= $order_by.$limit;

		$sum_data = sobad_item::count("1=1 ".$cari);
		$args = sobad_item::get_products($args,$where);

		$load = isset($filter['load'])?$filter['load']:'here_modal2';
		$script = isset($filter['script'])?$filter['script']:'set_apply_product(this)';
		$search = isset($filter['search'])?$filter['search']:'_product';

		$data['data'] = array(
			'data'		=>$kata,
			'object'	=>self::$object,
			'func'		=> '_search_product',
			'load'		=>$load,
			'name'		=>$search
		);

		$data['search'] = array('Semua','name','product_code');
		$data['class'] = '';
		$data['table'] = array();
		$data['page'] = array(
			'func'	=> '_pagination',
			'data'	=> array(
				'start'		=> $start,
				'qty'		=> $sum_data,
				'load'		=> $load,
				'limit'		=> $nLimit,
				'func'		=> '_pagination_product'
			)
		);

		$no = ($start -1) * $nLimit;
		$modal = isset($filter['modal'])?$filter['modal']:2;
		$asset = 'asset/img/';
		foreach($args as $key => $val){
			$no += 1;
			$color = '';
			$apply = array(
				'ID'		=> 'apply_'.$val['ID'],
				'func'		=> '',
				'color'		=> 'green',
				'icon'		=> 'fa fa-edit',
				'label'		=> 'apply',
				'load'		=> '',
				'script' 	=> $script,
				'object'	=> static::$object
			);
			
			$lokasi = sobad_item::get_image($val['picture']);
			$lokasi = $asset.$lokasi[0];
			
			$data['table'][$key]['tr'] = array();
			$data['table'][$key]['td'] = array(
				'No.'	=> array(
					'left',
					'5%',
					$no,
					true
				),
				'Gambar'	=> array(
					'left',
					'10%',
					'<img src="'.$lokasi.'" style="width:100%;margin:auto;">',
					true
				),
				'SKU'		=> array(
					'left',
					'15%',
					$val['product_code'],
					true
				),
				'Name'		=> array(
					'left',
					'auto%',
					$val['name'],
					true
				),
				'Harga'		=> array(
					'left',
					'15%',
					'Rp. '.format_nominal($val['price']),
					true
				),
				'Apply'			=> array(
					'center',
					'10%',
					_modal_button($apply,$modal),
					false
				)
				
			);
		}

		return $data;
	}

	public function insert_to($id){
		$table = self::table();		
		return self::modal_layout($table);
	}

	private function modal_layout($table){
		$data = array(
			'label'		=> 'Daftar Product',
			'table'		=> $table
		);

		ob_start();
		?>
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
				<h4 class="modal-title"><?php print($data['label']) ;?></h4>
			</div>
			<div class="modal-body">
			<?php
				print(table_admin($data['table']));
			?>
			</div>
		</div>
		<script>
			
		</script>
		<?php
		return ob_get_clean();
	}

	// ----------------------------------------------------------
	// Function selling to database -----------------------------
	// ----------------------------------------------------------
	private function _get_table_product($idx,$args=array()){
		if($idx==0){
			$idx = 1;
		}

		$filter = isset($_SESSION['filter_product'])?$_SESSION['filter_product']:array();
		$search = isset($filter['search'])?$filter['search']:'_product';
		$args = isset($_POST['args'])?sobad_asset::ajax_conv_json($_POST['args']):$args;

		$data = array(
			'words'		=> $args['words'.$search],
			'search'	=> $args['search'.$search]
		);

		parent::$page = $idx;
		parent::$search = true;
		parent::$data = $data;

		$table = self::table();
		return self::modal_layout($table);
	}

	public function _pagination_product($idx){
		return self::_get_table_product($idx);
	}

	public function _search_product($args=array()){
		$args = sobad_asset::ajax_conv_json($args);
		return self::_get_table_product(1,$args);
	}
}