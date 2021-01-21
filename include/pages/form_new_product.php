<?php

abstract class _new_product extends form_product{

	protected static $table = 'sobad_post';

	// ----------------------------------------------------------
	// Form new product -----------------------------------------
	// ----------------------------------------------------------

	protected function _get_image_list_file($start=1,$search=false,$cari=array()){
		$args = array('ID','notes','var');

		$kata = '';$where = "ORDER BY inserted DESC ";
		if($search){
			$src = like_pencarian($args,$cari);
			$cari = $src[0];
			$where = $src[0]." ".$where;
			$kata = $src[1];
		}else{
			$cari=$where;
		}

		$limit = 'LIMIT '.intval(($start - 1) * 18).',18';
		$where .= $limit;
		
		$object = self::$table;
		$args = $object::get_images($args,$where);
		$sum_data = $object::get_images(array('ID'),$cari);

		$_list = array();
		
		$_list['data'] = array(
			'object'	=> static::$object,
			'func' 		=> '_search_list_file',
			'data' 		=> $kata,
			'load' 		=> 'inline_malika4',
			'name' 		=>'_file'
		);

		$_list['search'] = array('name');

		$_list['list'] = array();

		$_list['page'] = array(
			'start'		=> $start,
			'qty'		=> count($sum_data),
			'limit'		=> 18,
			'load'		=> 'inline_malika4',
			'func'		=> '_pagination_list_file',
			'object'	=> static::$object
		);

		$_list['script'] = array(
			'id'			=> 'inline_malika4',
			'func_remove'	=> '_remove_file_list'
		);

		foreach ($args as $key => $val) {
			$_list['list'][$key] = array(
				'id'		=> $val['ID'],
				'name'		=> $val['notes'],
				'url'		=> $val['notes'],
				'type'		=> $val['var'],
				'func'		=> 'get_image_new_product(this)'
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

		$data = array(
			'func'	=> '_list_file',
			'data'	=> self::_get_image_list_file(1,true,$args)
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

		$data = array(
			'func'	=> '_list_file',
			'data'	=> self::_get_image_list_file($start,$status,$args)
		);

		ob_start();
		metronic_layout::sobad_file_manager($data);
		return ob_get_clean();
	}

	public function _remove_file_list($idx=0){
		$asset = '../asset/img/upload/';

		$post = sobad_post::get_id($idx,array('notes'));

	// Hapus database
		sobad_db::_delete_single($idx,'sdn-post');

	// Hapus File
		$check = array_filter($post);
		if(!empty($check)){
			$target_file = $asset.$post[0]['notes'];
			if (file_exists($target_file)) {
				unlink($target_file);
			}
		}

		$data = array(
			'func'	=> '_list_file',
			'data'	=> self::_get_image_list_file(1)
		);

		ob_start();
		metronic_layout::sobad_file_manager($data);
		return ob_get_clean();
	}

	public function _get_table_packet_product($id=0){
		$data = array();
		$data['class'] = '';
		$data['table'] = array();

		$args = sobad_item::get_id($id,array('assembly'));

		$check = array_filter($args);
		if(empty($check)){
			return $data;
		}


		$packet = json_decode($args[0]['assy'],true);
		if(isset($packet['data'])){
			$packet = $packet['data'];
			foreach ($packet as $key => $val) {
				$product = $item->get_item($val['ID'],array('name','pcture','price'));
				$product = $product[0];

				$image = $product['picture'];
				$name = $product['name'];
				$harga = $product['price'];

				$hapus = array(
					'ID'	=> 'del_'.$val['ID'],
					'func'	=> 'hapus_packet_new_product',
					'load'	=> 'packet_product',
					'color'	=> 'red',
					'icon'	=> 'fa fa-trash',
					'label'	=> 'hapus',
					'status'=> $status
				);

				$data['table'][$key]['tr'] = array();
				$data['table'][$key]['td'] = array(
					'Image'		=> array(
						'center',
						'15%',
						'asset/img/'.empty($image)?'no-image.png':'upload/'.$image,
						true
					),
					'Name'		=> array(
						'left',
						'auto',
						$name.'<input type="hidden" name="id_item" value="'.$val['ID'].'">',
						true
					),
					'Qty'		=> array(
						'left',
						'15%',
						'<input class="money" style="width:100%;" type="text" name="qty_item" value="'.$val['qty'].'" onkeydown="mask_money(\'.money\')">',
						true
					),
					'price'		=> array(
						'left',
						'10%',
						$harga,
						true
					),
					'Hapus'		=> array(
						'center',
						'10%',
						hapus_button($hapus),
						false
					),
				);
			}
		}

		return $data;
	}

	public function add_form($idx=''){
		$idx = str_replace('new_', '', $idx);
		$vals = array(0,intval($idx),'','','',0,'',2);
		
		$args = array(
			'title'		=> 'Tambah barang baru',
			'button'	=> '_btn_modal_save',
			'status'	=> array(
				'link'		=> 'add_new_product',
				'load'		=> 'quotation_detail .portlet-body'
			)
		);
		
		return self::_item_form($args,$vals);
	}

	public function _item_form($args=array(),$vals=array()){
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
				'accept'	=> 'image/*',
				'load'		=> 'inline_malika4',
				'object'	=> static::$object
			)
		);

		$tab2 = array(
			'func'		=> '_list_file',
			'data'		=> $_list
		);

		$tab3 = array(
			'cols'	=> array(3,9),
			0 => array(
				'func'			=> 'opt_hidden',
				'type'			=> 'hidden',
				'key'			=> 'id_product',
				'value'			=> $vals[0]
			),
			1 => array(
				'func'			=> 'opt_hidden',
				'type'			=> 'hidden',
				'key'			=> 'reff_quotation',
				'value'			=> $vals[1]
			),
			2 => array(
				'id'			=> 'image-new-product1',
				'func'			=> 'opt_hidden',
				'type'			=> 'hidden',
				'key'			=> 'picture',
				'value'			=> $vals[2]
			),
			3 => array(
				'func'			=> 'opt_input',
				'type'			=> 'text',
				'key'			=> 'name',
				'label'			=> 'Name',
				'class'			=> 'input-circle',
				'value'			=> $vals[3],
				'data'			=> 'placeholder="Name"'
			),
			4 => array(
				'func'			=> 'opt_input',
				'type'			=> 'text',
				'key'			=> 'product_code',
				'label'			=> 'Product Code',
				'class'			=> 'input-circle',
				'value'			=> $vals[4],
				'data'			=> 'placeholder="Product Code"'
			),
			5 => array(
				'id'			=> 'price_new_product',
				'func'			=> 'opt_input',
				'type'			=> 'text',
				'key'			=> 'price',
				'label'			=> 'Harga',
				'class'			=> 'input-circle money',
				'value'			=> format_nominal($vals[5]),
				'data'			=> 'placeholder="Price" onkeydown="mask_money(\'.money\')"'
			),
			6 => array(
				'func'			=> 'opt_textarea',
				'key'			=> 'description',
				'label'			=> 'Diskripsi',
				'class'			=> 'input-circle',
				'value'			=> $vals[6],
				'rows'			=> 4,
				'data'			=> 'placeholder="No Invoice" readonly'
			),
			7 => array(
				'func'			=> 'opt_select',
				'data'			=> $type_var,
				'key'			=> 'var',
				'label'			=> 'Type',
				'class'			=> 'input-circle',
				'select'		=> $vals[7],
				'status'		=> ''
			),
		);

		$data = array(
			'menu'		=> array(
				3	=> array(
					'key'	=> '',
					'icon'	=> 'fa fa-upload',
					'label'	=> 'Upload File'
				),
				4	=> array(
					'key'	=> '',
					'icon'	=> 'fa fa-file',
					'label'	=> 'List File'
				),
				5	=> array(
					'key'	=> '',
					'icon'	=> 'fa fa-file',
					'label'	=> 'Diskripsi'
				)
			),
			'content'	=> array(
				3	=> array(
					'func'	=> 'sobad_file_manager',
					'data'	=> $tab1
				),
				4	=> array(
					'func'	=> 'sobad_file_manager',
					'data'	=> $tab2
				),
				5	=> array(
					'func'	=> '_layout_product',
					'object'=> static::$object,
					'data'	=> array(
						'form'	=> $tab3,
						'reff'	=> $vals[0]
					)
				)
			)
		);

		$args['func'] = array('_inline_menu');
		$args['data'] = array($data);
		
		return modal_admin($args);
	}

	public function _upload_product(){
		sobad_asset::handling_upload_file('file','../asset/img/upload');

		$args = array(
			'notes'			=> $_FILES['file']['name'],
			'var'			=> 'image',
		);

		sobad_db::_insert_table('sdn-post',$args);

		$data = array(
			'func'	=> '_list_file',
			'data'	=> self::_get_image_list_file(1)
		);

		ob_start();
		metronic_layout::sobad_file_manager($data);
		return ob_get_clean();
	}

	public function _layout_product($data=array()){
		$status = false;$image[0] = 'no-image.png';
		$args = $data['form'];

		$add = array(
			'ID'		=> 'product_'.$data['reff'],
			'func'		=> 'form_new_packet_product',
			'color'		=> 'btn-default',
			'icon'		=> 'fa fa-plus',
			'label'		=> 'Tambah'
		);

		if($data['reff']!=0){
			$paket = sobad_item::get_id($data['reff'],array('picture','detail'));

			$check = array_filter($paket);
			if(!empty($paket)){
				$image = sobad_item::get_image($paket[0]['picture']);

				$assy = json_decode($paket[0]['detail'],true);
				if(isset($assy['detail'])){
					$status = true;
					$paket = json_encode($assy['detail']['content']);
				}
			}
		}

		?>
			<style type="text/css">
				.col-md-3.box-image-show:hover > a.remove-image-show {
				    opacity: 1;
				}

				a.remove-image-show {
				    position: absolute;
				    right: 7px;
				    top: -7px;
				    opacity: 0;
				}

				a.remove-image-show:hover {
				    border: 1px solid #dfdfdf;
				    padding: 3px;
				}
			</style>

			<div class="row">
				<div class="col-md-3 box-image-show">
					<a class="remove-image-show" href="javascript:" onclick="remove_image_new_product()">
						<i style="font-size: 24px;color: #e0262c;" class="fa fa-trash"></i>
					</a>
					<img src="asset/img/<?php print($image[0]) ;?>" style="width:100%" id="img-new-product">
				</div>
				<div class="col-md-9">
					<?php metronic_layout::sobad_form($args) ;?>
				</div>
			</div>
			<hr>
			<div class="row">
				<div class="col-md-12">
					<div class="portlet gren">
						<div class="portlet-title">
							<div class="caption">Data Item dalam Produk</div>
							<div class="tools"></div>
							<div class="actions">
								<?php print(_modal_button($add,3)) ;?>
							</div>
						</div>
					</div>
					<div class="table_flexible">
						<form role="form" method="post" class="form-horizontal" enctype="multipart/form-data">
							<table id="packet_product" class="table table-striped table-bordered table-hover dataTable no-footer">
								<thead>
									<tr role="row">
										<th class="sorting" style="text-align:center;width:15%;">Image</th>
										<th class="sorting" style="text-align:center;width:auto;">Name</th>
										<th class="sorting" style="text-align:center;width:15%;">Qty</th>
										<th class="sorting" style="text-align:center;width:15%;">Harga</th>
										<th style="text-align:center;width:10%;">Hapus</th>				
									</tr>
								</thead>
								<tbody>
									<!-- This Is Data Item -->
								</tbody>
							</table>
						</form>
					</div>
				</div>
			</div>

			<?php
				if($status){
					echo '
						<script type="text/javascript">
							get_paket_new_product('.$paket.');
						</script>
					';
				}
			?>

			<script type="text/javascript">
				function get_paket_new_product(paket){
					for(i=0;i<paket.length;i++){
						var ajx = 'add_new_packet_product';
						var id = '#packet_product tbody';

						data = "ajax="+ajx+"&object="+object+"&data="+paket[i]['ID'];
						sobad_ajax(id,data,_create_table_packet,false);
					}
				}

				function remove_image_new_product(){
					$('#img-new-product').attr('src',"asset/img/no-image.png");
					$('#image-new-product1').val('');
					$(".imgList .row .box_file_list.selected").removeClass("selected");
					_select_file_list[0] = {};
				}

				function get_image_new_product(val){
					select_file_list(val,false);
					$('#img-new-product').attr('src',_select_file_list[0]['url']);
					$('#image-new-product1').val(_select_file_list[0]['id']);
				}

				function set_apply_packet(val){
					var ajx = 'add_new_packet_product';
					var id = '#packet_product tbody';
					var lbl = $(val).attr('id');
			
					data = "ajax="+ajx+"&object="+object+"&data="+lbl;
					sobad_ajax(id,data,_create_table_packet,false);
				}

				function _create_table_packet(data,idx){
					var image = 'asset/img/';
					var inp_id = '<input type="hidden" name="id_item" value="'+data["ID"]+'">';
					var inp_qty = '<input class="money" style="width:100%;" type="text" name="qty_item" value="1" onkeydown="mask_money(\'.money\')">';
					var btn_hapus = '<a id="del_'+data["ID"]+'" href="javascript:;" class="btn btn-xs red btn_data_malika" onclick="_del_item_packet(this)"><i class="fa fa-trash"></i> hapus</a>';

					image += data['picture'];

					var _html = '<tr><td style="text-align:center"><img src="'+image+'" style="width:100%;margin:auto;"></td><td style="text-align:left">'+data["name"]+inp_id+'</td><td style="text-align:left">'+inp_qty+'</td><td style="text-align:left">'+data["price"]+'</td><td style="text-align:left">'+btn_hapus+'</td></tr>';

					$(idx).append(_html);
				}

				function _del_item_packet(val){
					$(val).parent().parent().remove();
				}
			</script>
		<?php
	}

	public function form_new_packet_product($idx=0){
		$_SESSION['filter_product'] = array(
			'load'		=> 'here_modal3',
			'script'	=> 'set_apply_packet(this)',
			'modal'		=> 3,
			'search'	=> '_new_product'
		);

		$id = str_replace('product_','',$idx);
		intval($id);
		return parent::insert_to($id);
	}

	public function add_new_packet_product($idx=0){
		$idx = str_replace('apply_', '', $idx);
		intval($idx);

		$data = sobad_item::get_id($idx,array('ID','picture','name','price'));
		$data = $data[0];

		$image = sobad_item::get_image($data['picture']);
		$data['picture'] = $image[0];

		$check = array_filter($data);
		if(empty($check)){
			$err = new _error();
			die($err->_alert_db("Product Tidak Tersedia"));
		}

		return $data;
	}
}