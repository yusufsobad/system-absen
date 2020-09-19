<?php

abstract class _list_product extends _new_product{

	// ----------------------------------------------------------
	// Layout List Product --------------------------------------
	// ----------------------------------------------------------
	
	public function _layout_list($args=array()){
		$id = isset($args['ID'])?$args['ID']:0;
		$status = isset($args['status'])?$args['status']:0;
		
		$args = self::_detail_product($id);
		$id_sell = $args['_id'];
		$data = $args['data'];
		$func = '_add_product';
		
		$add = array();$new = array();
		if($status==0){
			$new = array(
				'ID'		=> 'new_'.$id_sell,
				'func'		=> '_form_newProduct',
				'color'		=> 'btn-default',
				'icon'		=> 'fa fa-plus',
				'label'		=> 'New',
				'toggle'	=> 'modal',
				'load'	 	=> 'here_modal2',
				'href' 		=> '#myModal2',
				'spin'		=> false
			);

			$add = array(
				'ID'		=> 'quotation_'.$id_sell,
				'func'		=> '_form_product',
				'color'		=> 'btn-default',
				'icon'		=> 'fa fa-plus',
				'label'		=> 'product',
				'toggle'	=> 'modal',
				'load'	 	=> 'here_modal2',
				'href' 		=> '#myModal2',
				'spin'		=> false
			);
		}
		
		?>
			<div id="quotation_detail" style="margin-left:1px;">
				<div class="portlet gren">
					<div class="portlet-title">
						<div class="caption">Data Produk</div>
						<div class="tools"></div>
						<div class="actions">
							<?php print(buat_button($new)) ;?>
							<?php print(buat_button($add)) ;?>
						</div>
					</div>
					<div class="portlet-body">
						<form id="quotation_product" class="form-horizontal">
						<?php 
							metronic_layout::sobad_table($data) ;
							self::_get_ongkir();
						?>
						</form>
					</div>
				</div>
			</div>
		<?php

			self::_script_layout($id,$id_sell,$func,$status);
	}

	private function _get_ongkir($data=array()){
		$check = array_filter($data);
		if(empty($check)){
			$data = array(
				'disc'		=> 0,
				'ongkir'	=> 0,
				'total'		=> 0
			);
		}

		$disc = format_nominal($data['disc']);
		$ongkir = format_nominal($data['ongkir']);
		$total = format_nominal($data['total']);

		?>
			<div>
				<hr>
			</div>
			<div class="form-group">
				<label class="col-md-4 control-label">Discount (Percent)</label>
				<div class="col-md-7">
					<input type="text" class="form-control input-circle money" name="discount" value="<?php print($disc) ;?>" placeholder="Diskon" onkeydown="mask_money('.money')">
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-4 control-label">Shipping Price</label>
				<div class="col-md-7">
					<input type="text" class="form-control input-circle money" name="shiping_price" value="<?php print($ongkir) ;?>" placeholder="Ongkir" onkeydown="mask_money('.money')">
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-4 control-label">Total</label>
				<div class="col-md-7">
					<input type="text" class="form-control input-circle" value="<?php print($total) ;?>" placeholder="Total" disabled>
				</div>
			</div>
		<?php
	}

	private function _script_layout($id=0,$id_sell=0,$func='',$status=0){
		if($status != 0){
			echo '<script type="text/javascript">';
			echo "	$('#quotation_detail table a').attr('disabled','disabled');";
			echo "	$('#quotation_detail table input').attr('disabled','disabled');";
			echo '</script>';
		}

		?>

			<script type="text/javascript">
				$('.money').on('keydown',function(){
					mask_money('.money');
				});

				$('.number').on('keydown',function(){
					mask_quantity('.number');
				});

				function _del_product(val){
					var ajx = $(val).attr('data-sobad');
					var id = '#quotation_detail .portlet-body';
						
					args = [
						{
							'name':'id',
							'value':$(val).attr('id')
						},
						{
							'name':'reff',
							'value':'<?php print($id) ;?>'
						}
					];
					lbl = JSON.stringify(args);
			
					data = "ajax="+ajx+"&object="+object+"&data="+lbl;
					sobad_ajax(id,data,'html',false);
				}
				
				function set_apply_product(val){
					var ajx = '<?php print($func) ;?>';
					var id = '#quotation_detail .portlet-body';
					
					args = [
						{
							'name':'barang',
							'value':$(val).attr('id')
						},
						{
							'name':'reff',
							'value':'<?php print($id_sell) ;?>'
						}
					];
					lbl = JSON.stringify(args);
			
					data = "ajax="+ajx+"&object="+object+"&data="+lbl;
					sobad_ajax(id,data,'html',false);
				}
			</script>
		<?php
	}

	// ---------------------------------------------------------
	// Function List Product -----------------------------------
	// ---------------------------------------------------------

	private function _detail_product($id=0){
		intval($id);
		
		$args = array('ID','barang','qty','unit','price','discount');

		if($id!=0){
			$args = array_push($args, 'id_join');
			$args = array_push($args, '_shipping_price');
			$args = array_push($args, '_discount');

			$q = sobad_post::_gets(static::$post,$id,$args);
		}else{
			$q = sobad_transaksi::get_reff(0,$args);
		}

		$data = self::_get_product($q);
		$data['_id'] = $id==0?0:$data['id'];

		return $data;
	}

	private function _get_product($q){
		$data = array();
		$asset = 'asset/img/upload/';
		
		$data['class'] = '';
		$data['table'] = array();
		
		$_id = 0;$subTotal = 0;$disc = 0;$ongkir = 0;$total = 0;

		$_ky = -1;
		foreach($q as $key => $val){
			$_id = $val['ID'];
			$disc = isset($val['_discount'])?$val['_discount']:0;
			$ongkir = isset($val['_shiping_price'])?$val['_shiping_price']:0;

			$idx = isset($val['id_join'])?$val['id_join']:$val['ID'];
			
			$arr_sku = array(
				'type'		=> 'hidden',
				'key'		=> 'product_code',
				'value'		=> $val['barang'],
				'status'	=> ''
			);

			$arr_price = array(
				'type'		=> 'hidden',
				'key'		=> 'product_price',
				'value'		=> $val['price'],
				'status'	=> 'min="1"'
			);
			
			$arr_qty = array(
				'type'		=> 'price',
				'key'		=> 'product_qty',
				'value'		=> intval($val['qty']),
				'status'	=> 'min="1"'
			);

			$arr_disc = array(
				'type'		=> 'number',
				'key'		=> 'product_discount',
				'value'		=> $val['discount'],
				'status'	=> 'min="1"'
			);
			
			$subTotal = $val['price'] * $val['qty'] - $val['discount'];
			$total += $subTotal;

			$edit_sku = editable_value($arr_sku);
			$edit_price = editable_value($arr_price);
			$edit_qty = editable_value($arr_qty);
			$edit_disc = editable_value($arr_disc);
			
			if(empty($val['image'])){
				$lokasi = 'asset/img/no-image.png';
			}else{
				$lokasi = $asset.$val['image'];
			}
			
			$hapus = array(
				'ID'		=> 'del_'.$idx,
				'func'		=> '_delete_product',
				'color'		=> 'red',
				'icon'		=> 'fa fa-trash',
				'label'		=> 'hapus',
				'script'	=> '_del_product(this)'
			);
			
			$_ky += 1;
			$data['table'][$_ky]['tr'] = array();
			$data['table'][$_ky]['td'] = array(
				'Image'	=> array(
					'left',
					'10%',
					'<img src="'.$lokasi.'" style="width:100%;margin:auto;">',
					true
				),
				'Product Code'		=> array(
					'left',
					'auto',
					$val['product_code_bara'].$edit_sku,
					true
				),
				'Name'		=> array(
					'left',
					'15%',
					$val['name_bara'],
					true
				),
				'Harga'		=> array(
					'right',
					'15%',
					format_nominal($val['price']).$edit_price,
					true
				),
				'Qty'		=> array(
					'left',
					'12%',
					$edit_qty,
					true
				),
				'Diskon %'	=> array(
					'left',
					'12%',
					$edit_disc,
					true
				),
				'Subtotal'		=> array(
					'right',
					'15%',
					format_nominal($subTotal),
					true
				),
				'Hapus'			=> array(
					'center',
					'10%',
					hapus_button($hapus),
					false
				)
				
			);
		}

		$cnt = $_ky+1;
		$data['table'][$cnt]['td'] = array(
				'Image'			=> array(
					'center',
					'auto',
					'<strong>Sub Total</strong>',
					true,
					6
				),
				'Subtotal'		=> array(
					'right',
					'auto',
					format_nominal($total),
					true
				),
				'Hapus'			=> array(
					'center',
					'10%',
					'',
					false
				)
			);
		
		$args['id'] = $_id;
		$args['data'] = $data;
		$args['fee'] = array(
			'disc' 		=> $disc,
			'ongkir'	=> $ongkir,
			'total'		=> $total * (1 - ($disc/100)) + $ongkir
		);
		return $args;
	}

	// ----------------------------------------------------------
	// Form data product ----------------------------------------
	// ----------------------------------------------------------

	public function _form_newProduct(){
		return parent::add_form();
	}
	
	public function _form_product($id=0){
		$_SESSION['filter_product'] = array();

		$id = str_replace('product_','',$id);
		intval($id);
		return parent::insert_to($id);
	}

	public function _add_product($args=array()){
		$args = sobad_asset::ajax_conv_json($args);
		$args['barang'] = str_replace('apply_','',$args['barang']);
		
		$q = sobad_item::get_id($args['barang'],array('price'));
		
		$args['qty'] = 1;
		$args['price'] = $q[0]['price'];
		$args['note'] = 'temp_'.get_id_user();
		
		$q = sobad_db::_insert_table('kit-transaksi',$args);
		
		return self::_get_table_product($q,$args['reff']);
	}

	public function _delete_product($args=array()){
		$args = sobad_asset::ajax_conv_json($args);
		
		$reff = $args['reff'];
		$id = $args['id'];
		$id = str_replace('del_','',$id);
		intval($id);
		
		$q = sobad_db::_delete_single($id,'kit-transaksi');
		
		return self::_get_table_product($q,$reff);
	}

	private function _get_table_product($q,$id){
		if($q!==0){
			$args = self::_detail_product($id);
			ob_start();
			metronic_layout::sobad_table($args['data']);
			self::_get_ongkir($args['fee']);
			return ob_get_clean();
		}
		
		return '';
	}

}