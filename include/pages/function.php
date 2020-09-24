<?php
// include function external ----------
require 'form_product.php';
require 'file_manager.php';
require 'form_new_product.php';
require 'form_list_product.php';
require 'layout_admin.php';
require 'layout_pdf.php';
// ------------------------------------
// ---------- List Function -----------
// ------------------------------------

function convToOption($args=array(),$id,$value){
	$check = array_filter($args);
	if(empty($check)){
		return array();
	}
	
	$option = array();
	$check = array_filter($args);
	if(!empty($check)){
		foreach($args as $key => $val){
			$option[$val[$id]] = $val[$value];
		}
	}else{
		$option[0] = 'Tidak Ada';
	}
	
	return $option;
}

function convToGroup($args=array(),$data=array()){
	$check = array_filter($args);
	if(empty($check)){
		return array();
	}
	
	$group = array();
	$check = array_filter($args);
	if(!empty($check)){
		foreach($args as $key => $val){
			foreach ($data as $ky) {
				$group[$ky][] = $val[$ky];
			}
		}
	}else{
		$group[0] = array();
	}
	
	return $group;
}

function hapus_button($val){
	return _click_button($val);
}

function _click_button($val){
	$check = array_filter($val);
	if(empty($check)){
		return '';
	}

	$load = isset($val['load'])?$val['load']:'sobad_portlet';
	
	$val['toggle'] = '';
	$val['load'] = $load;
	$val['href'] = 'javascript:;';
	
	return buat_button($val);
}

function print_button($val){
	$check = array_filter($val);
	if(empty($check)){
		return '';
	}
	
	$val['toggle'] = '';
	$val['load'] = 'sobad_preview';
	$val['script'] = isset($val['script'])?$val['script']:'sobad_report(this)';
	$val['href'] = 'javascript:;';
	
	return buat_button($val);
}

function edit_button($val){
	return _modal_button($val,'');
}

function apply_button($val){	
	return _modal_button($val,2);
}

function _modal_button($val,$no=''){
	$check = array_filter($val);
	if(empty($check)){
		return '';
	}
	
	$val['toggle'] = 'modal';
	$val['load'] = 'here_modal'.$no;
	$val['href'] = '#myModal'.$no;
	$val['spin'] = false;
	
	return buat_button($val);
}

function edit_button_custom($val){
	$check = array_filter($val);
	if(empty($check)){
		return '';
	}
	
	return buat_button($val);
}

function editable_click($args=array()){
	$check = array_filter($args);
	if(empty($check)){
		return '';
	}
	
	$edit = '<a href="javascript:;" id="'.$args['key'].'" class="edit_input_txt" data-type="text" data-sobad="'.$args['func'].'" data-name="'.$args['name'].'" data-title="'.$args['title'].'" class="editable editable-click">'.$args['label'].'</a>';
	
	return $edit;
}

function editable_value($args=array()){
	$check = array_filter($args);
	if(empty($check)){
		return '';
	}

	if(!isset($args['class'])){
		$args['class'] = '';
	}

	if(!isset($args['data'])){
		$args['data'] = 'style="width:100%;"';
	}
	
	if(!isset($_SESSION[_prefix.'input_form'])){
		$_SESSION[_prefix.'input_form'] = array();
	}

	array_merge($_SESSION[_prefix.'input_form'],array($args['key'] => $args['type']));

	$edit = create_form::get_option('input',$args,0,12);
	//$edit = '<input style="width:100%;" type="'.$args['type'].'" name="'.$args['key'].'" value="'.$args['value'].'" '.$args['status'].'>';
	
	return $edit;
}

function buat_button($val=array()){
	$check = array_filter($val);
	if(empty($check)){
		return '';
	} 
	
	$status = '';
	if(isset($val['status'])){
		$status = $val['status'];
	}

	$type = '';
	if(isset($val['type'])){
		$type = $val['type'];
	}

	$alert = false;
	if(isset($val['alert'])){
		$alert = $val['alert'];
	}

	$class = 'btn-xs';
	if(isset($val['class'])){
		$class = $val['class'];
	}

	$spin = 1;
	if(isset($val['spin'])){
		$spin = $val['spin']?1:0;
	}
	
	$onclick = 'sobad_button(this,'.$spin.')';
	if(isset($val['script'])){
		$onclick = $val['script'];
	}
	
	$btn = '
	<a id="'.$val['ID'].'" data-toggle="'.$val['toggle'].'" data-sobad="'.$val['func'].'" data-load="'.$val['load'].'" data-type="'.$type.'" data-alert="'.$alert.'" href="'.$val['href'].'" class="btn '.$class.' '.$val['color'].' btn_data_malika" onclick="'.$onclick.'" '.$status.'>
		<i class="'.$val['icon'].'"></i> '.$val['label'].'
	</a>';
	
	return $btn;
}

function dropdown_button($args=array()){
	$check = array_filter($args);
	if(empty($check)){
		return '';
	}

	$btn = '';
	foreach ($args['button'] as $ky => $val) {
		if($val!='divider'){
			$btn .= '<li>'.$val.'</li>';
		}else{
			$btn .= '<li class="divider"></li>';
		}
	}

	$drop = '
		<div class="btn-group btn-group-solid">
			<button type="button" class="btn '.$args['color'].' dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="true">
				'.$args['label'].' <i class="fa fa-angle-down"></i>
			</button>
			<ul class="dropdown-menu" role="menu">
				'.$btn.'
			</ul>
		</div>
	';

	return $drop;
}

function _detectDelimiter($csvFile){
    $delimiters = array(
        ';' => 0,
        ',' => 0,
        "\t" => 0,
        "|" => 0
    );

    $handle = fopen($csvFile, "r");
    $firstLine = fgets($handle);
    fclose($handle); 
    foreach ($delimiters as $delimiter => &$count) {
        $count = count(str_getcsv($firstLine, $delimiter));
    }

    return array_search(max($delimiters), $delimiters);
}

function script_chart(){
	?>
	<script>
		$(".chart_malika").ready(function(){
			var ajx = $('.chart_malika').attr('data-sobad');
			var id = $('.chart_malika').attr('data-load');
			
			data = "ajax="+ajx+"&data=2019";
			sobad_ajax(id,data,sobad_chart);
		});
	</script>
	<?php
}