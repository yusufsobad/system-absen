<?php

class optStructure_absen extends _page{

	protected static $object = 'optStructure_absen';

	protected static $table = 'sobad_module';

	// ----------------------------------------------------------
	// Layout category  ------------------------------------------
	// ----------------------------------------------------------

	protected function _array(){
		$args = array(
			'ID',
			'meta_value',
			'meta_note',
			'meta_key',
			'meta_reff'
		);

		return $args;
	}

	private function head_title(){
		$args = array(
			'title'	=> 'Organisasi <small>struktur organisasi</small>',
			'link'	=> array(
				0	=> array(
					'func'	=> self::$object,
					'label'	=> 'organisasi'
				)
			),
			'date'	=> false
		); 
		
		return $args;
	}

	protected function get_box(){
		$box = array(
			'label'		=> 'Structure Organisasi',
			'tool'		=> '',
			'action'	=> self::_action(),
			'object'	=> self::$object,
			'func'		=> '_tree_layout',
			'data'		=> ''
		);

		return $box;
	}

	protected function layout(){
		$box = self::get_box();
		
		$opt = array(
			'title'		=> self::head_title(),
			'style'		=> array(),
			'script'	=> array(self::$object,'_script')
		);
		
		return portlet_admin($opt,$box);
	}

	private static function _action(){
		$save = array(
			'ID'	=> 'save_0',
			'func'	=> 'update_tree',
			'color'	=> 'btn-default',
			'icon'	=> 'fa fa-save',
			'label'	=> 'Simpan',
			'script'=> 'save_treeJH(this)'
		);
		
		return _click_button($save);
	}

	public static function _tree_layout(){
		?>
			<script src="vendor/jHTree/js/jquery-ui-1.10.4.custom.min.js"></script>
			<div id="tree-sobad" style="overflow-x: auto;padding-bottom: 50px;"></div>
		<?php
	}

	public static function _script(){
		$args = array(
			'head'		=> 'Organisasi Solo Abadi',
			'id'		=> 'tree_0',
			'contents'	=> '-'
		);

		$func = self::$table;
		$dept = $func::_gets_tree_division();

		$data = self::_conv_jhtree($dept);

		$check = array_filter($data);
		if(!empty($check)){
			$args['children'] = $data;
		}

		$args = json_encode($args);
		?>

		<script type="text/javascript">
			var myData = [<?php print($args) ;?>];

	        $(function () {
	            $("#tree-sobad").jHTree({
	                callType: 'obj',
	                structureObj: myData
	            });
	        });

	        function save_treeJH(val){
	        	var tree_dt = JSON.stringify(treeData);
	        	var ajx = $(val).attr('data-sobad');
	        	var html = $(val).html();

	        	$(val).html('<i class="fa fa-spinner fa-spin"></i>');
				$(val).attr('disabled','');

	        	tree_dt = "ajax="+ajx+"&object="+object+"&data="+tree_dt;
	        	sobad_ajax('#id',tree_dt,'html',true,val,html);
	        }
    	</script>
		<?php
	}

	private static function _conv_jhtree($data=array()){
		$args = array();

		$no = -1;
		foreach ($data as $key => $val) {
			$id = $val['ID'];
			$no += 1;

			$whr = "(divisi='$id' AND status!='7' AND end_status='0')";
			$qty = sobad_user::count($whr);

			$args[$no] = array(
				'head'		=> $val['meta_value'],
            	'id'		=> 'tree_' . $id,
            	'contents'	=> $qty . ' Orang',
			);

			$check = array_filter($val['child']);
			if(!empty($check)){
				$args[$no]['children'] = self::_conv_jhtree($val['child']);
			}
		}

		return $args;
	}

	// ----------------------------------------------------------
	// Function tree to database --------------------------------
	// ----------------------------------------------------------	

	public static function update_tree($args=array()){
		$args = json_decode($args,true);

		// Update referensi
		foreach ($args as $key => $val) {
			$id = str_replace('tree_', '', $key);
			$reff = str_replace('tree_', '', $val);

			if($id > 0){
				sobad_db::_update_single($id,'abs-module',array(
					'ID'		=> $id,
					'meta_reff'	=> $reff
				));
			}
		}

		return true;
	}
}