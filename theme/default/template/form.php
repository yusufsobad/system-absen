<?php
(!defined('THEMEPATH'))?exit:'';

class create_form{

	private static $_types = array();

	public static $col_label = 4;
	public static $col_input = 7;
	
	private function option_form($args=array()){
		$inp = '';
		foreach($args as $key => $val){
			if($key === 'cols'){
				self::$col_label = $val[0];
				self::$col_input = $val[1];
			}else{
				if(is_callable(array(new self(),$val['func']))){
					$func = $val['func'];
					$inp = self::{$func}($val);
				}
				
				if(isset($val['type'])){
					if($val['type']!='hidden'){
						$inp = '<div class="form-group">'.$inp.'</div>';
					}
				}else{
					$inp = '<div class="form-group">'.$inp.'</div>';
				}
				
				echo $inp;
				
			}
		}
	}
	
	public function get_option($opt,$args=array(),$label=4,$input=7){
		$func = 'opt_'.$opt;

		if(!is_callable(array(new self(),$func))){
			return false;
		}

		self::$col_label = $label;
		self::$col_input = $input;
		return self::$func($args);
	}
	
	public function get_form($args){
		$check = array_filter($args);
		if(empty($check)){
			$args = array(
				0 => array(
					'key'			=> '',
					'label'			=> 'Info',
					'class'			=> '',
					'placeholder'	=> '',
					'value'			=> 'Tidak ada data yang ditemukan',
					'status'		=> 'readonly'
				),
				'cols'	=> array(4,7),
				'id'	=> ''
			);
		}
		
		$id = '';
		if(isset($args['id'])){
			$id = $args['id'];
			unset($args['id']);
		}
	
		?>
			<div class="col-lg-12">
				<form id="<?php print($id) ;?>" role="form" method="post" class="form-horizontal" enctype="multipart/form-data">
					<?php 
						self::option_form($args);
						if(!isset($_SESSION[_prefix.'input_form'])){
							$_SESSION[_prefix.'input_form'] = array();
						}

						$_SESSION[_prefix.'input_form'] = array_merge($_SESSION[_prefix.'input_form'],self::$_types);
					;?>
				</form>
			</div>
			<script>
				$('.money').on('keydown',function(){
					mask_money('.money');
				});

				$('.number').on('keydown',function(){
					mask_quantity('.number');
				});

				sobad_picker();
				sobad_clockpicker();
				ComponentsDropdowns.init();
				ComponentsEditors.init();
			</script>

			<script type="text/javascript">				
				$(".option_box").on('click',function(){
					//Check box
					if($(this).children('div.checker').children('span').hasClass("checked")){
						$(this).children('div.checker').children('span').removeClass("checked");
						$(this).children('div.checker').children('span').children('input').prop( "checked", false );
					}else{
						$(this).children('div.checker').children('span').addClass("checked");
						$(this).children('div.checker').children('span').children('input').prop( "checked", true );
					}
				});

			</script>
		<?php
	}
	
	private function opt_label($val){
		// label
		return '<label class="col-md-'. self::$col_label .' control-label">'.$val.'</label>';
	}
	
	private function opt_hidden($val=array()){
		$id = '';
		if(isset($val['id'])){
			$id = 'id="'.$val['id'].'"';
		}

		$inp = '<input '.$id.' type="hidden" name="'.$val['key'].'" value="'.$val['value'].'">';
		return $inp;
	}
	
	private function opt_input($val=array()){	
		// id, type , class , key , value , *data
		// *data = placeholder , status , max , min , dll
		// label (optional)
		$inp = '';
		if(isset($val['label'])){
			$inp .= self::opt_label($val['label']);
		}else{
			$inp .= '<div class="col-md-'. self::$col_label .'"></div>';
		}
		
		$id = '';
		if(isset($val['id'])){
			$id = 'id="'.$val['id'].'"';
		}

		$btn = '';
		$cols = self::$col_input;
		if(isset($val['button'])){
			$cols -= 1;
			$btn = '<div class="col-md-1">'.$val['button'].'</div>';
		}

		// Insert type data --->
		self::$_types[$val['key']] = $val['type'];

		switch ($val['type']) {
			case 'price':
				$val['type'] = 'text';
				$val['class'] .= ' money';
				break;

			case 'number':
				$val['type'] = 'text';
				$val['class'] .= ' number';
				break;

			case 'clock':
				$val['type'] = 'text';
				$val['class'] .= ' clockpicker';
				break;
			
			default:
				# code...
				break;
		}
		
		$inp .= '<div class="col-md-'. $cols .'">';
		$inp .= '<input '.$id.' type="'.$val['type'].'" class="form-control '.$val['class'].'" name="'.$val['key'].'" value="'.$val['value'].'" '.$val['data'].'>';
		
		$inp .= '</div>';
		return $inp.$btn;
	}

	private function opt_box($val=array()){	
		// id, type , class , key , value , *data
		// *data = placeholder , status , max , min , dll
		// label (optional)
		$inp = '';
		if(isset($val['label'])){
			$inp .= self::opt_label($val['label']);
		}else{
			$inp .= '<div class="col-md-'. self::$col_label .'"></div>';
		}
		
		$id = '';
		if(isset($val['id'])){
			$id = 'id="'.$val['id'].'"';
		}

		$btn = '';
		$cols = self::$col_input;
		if(isset($val['button'])){
			$cols -= 1;
			$btn = '<div class="col-md-1">'.$val['button'].'</div>';
		}

		$class = $val['type']=='radio'?'radio-list':'checkbox-list';
		
		$inp .= '<div class="col-md-'. $cols .'">';
		$inp .= '<div '.$id.' class="'.$class.'">';

		foreach ($val['data'] as $ky => $vl) {
			$check = '';
			if($val['value']==$vl['value']){
				$check = 'checked';
			}

			$class = '';
			if(isset($val['inline'])){
				if($val['inline']){
					$class = $val['type']=='radio'?'radio-inline':'checkbox-inline';
				}
			}

			if($val['type']=='radio'){
				$type = 'radio';
			}else{
				$type = 'checker';
			}

			$inp .= '
				<label class="option_box '.$class.'" for="box_opt'.$val['key'].$ky.'">
					<div class="control-box">
						<input type="'.$val['type'].'" id="box_opt'.$val['key'].$ky.'" name="'.$val['key'].'" value="'.$vl['value'].'" '.$check.'>
						'.$vl['title'].' 
					</div>
				</label>
			';
		}
		
		$inp .= '</div></div>';
		return $inp.$btn;
	}
	
	private function opt_file($val=array()){	
		// id , class , key , value , *data
		// *data = placeholder , status , max , min , dll
		// label (optional)
		$inp = '';
		if(isset($val['label'])){
			$inp .= self::opt_label($val['label']);
		}else{
			$inp .= '<div class="col-md-'. self::$col_label .'"></div>';
		}
		
		$id = '';
		if(isset($val['id'])){
			$id = 'id="'.$val['id'].'"';
		}

		$txt = 'Import';
		if(isset($val['text'])){
			$txt = $val['text'];
		}
		
		$cols = self::$col_input;
		
		$inp .= '<div class="col-md-'. ($cols - 2) .'">';
		$inp .= '<input '.$id.' type="file" class="form-control" name="'.$val['key'].'" accept="'.$val['accept'].'" '.$val['data'].'>';
		$inp .= '</div>';
		
		$inp .= '<div class="col-md-2">';
		$inp .= '<input type="submit" name="import" value="'.$txt.'" class="btn green">';
		$inp .= '</div>';
		return $inp;
	}
	
	private function opt_textarea($val=array()){
		// id, key , class , rows , value
		// label (optional)
		$id = '';
		if(isset($val['id'])){
			$id = 'id="'.$val['id'].'"';
		}
		
		$inp = '';
		if(isset($val['label'])){
			$inp .= self::opt_label($val['label']);
		}else{
			$inp .= '<div class="col-md-'. self::$col_label .'"></div>';
		}

		// Insert type data --->
		self::$_types[$val['key']] = 'textarea';
		
		$inp .= '<div class="col-md-'. self::$col_input .'">';
		$inp .= '<textarea '.$id.' name="'.$val['key'].'" class="form-control '.$val['class'].'" rows="'.$val['rows'].'">'.$val['value'].'</textarea>';
		$inp .= '</div>';
		return $inp;
	}
	
	private function opt_wysihtml5($val=array()){
		
		$inp = '';
		if(isset($val['label'])){
			$inp .= self::opt_label($val['label']);
			$col_input = self::$col_input;
		}else{
			$col_input = self::$col_label + self::$col_input;
		}

		// Insert type data --->
		self::$_types[$val['key']] = 'html';
		
		$inp .= '<div class="col-md-'. $col_input .'">';
		$inp .= '<div name="'.$val['key'].'" id="summernote_1" class="'.$val['class'].'" '.$val['data'].'>'.$val['value'].'</div>';
		$inp .= '</div>';
		return $inp;
	}
	
	private function opt_button($val=array()){
		// id, key , class , label, text, click, data
		$inp = '';
		if(isset($val['label'])){
			$inp .= self::opt_label($val['label']);
		}else{
			$inp .= '<div class="col-md-'. self::$col_label .'"></div>';
		}
		
		$id = '';
		if(isset($val['id'])){
			$id = 'id="'.$val['id'].'"';
		}
		
		$inp .= '<div class="col-md-'. self::$col_input .'">';
		$inp .= '<button '.$id.' onclick="'.$val['click'].'" type="'.$val['key'].'" class="btn btn-default '.$val['class'].'" '.$val['data'].'>'.$val['text'].'</button>';
		$inp .= '</div>';
		return $inp;
	}
	
	private function opt_select($val=array()){
		// id, key , class , data , select
		// label (optional)
		
		$arr_select = $val['select'];
		
		$func = '';
		if(is_array($val['data'])){
			foreach($val['data'] as $key => $opt){
				$select = '';
				
				if(!is_array($arr_select)){
					if($key==$arr_select){
						$select = 'selected';
					}
				}else{
					if(in_array($key,$arr_select)){
						$select = 'selected';
					}
				}
				
				// Grouped
				if(isset($val['group']) && $val['group']==true){
					if(is_array($opt)){ 
						// start grouping
						$func .= '<optgroup label="'.$key.'">';
						foreach($opt as $ky => $grp){
							$select = '';
							if(is_array($arr_select)){
								if(in_array($ky,$arr_select)){
									$select = 'selected';
								}
							}else{
								if($ky==$arr_select){
									$select = 'selected';
								}
							}
							
							$func .= self::opt_value_select($ky,$select,$grp);
						}
						$func .= '</optgroup>';
					}else{
						$func .= self::opt_value_select($key,$select,$opt);
					}
				}else{
					$func .= self::opt_value_select($key,$select,$opt);
				}
			}
		}else{
			$func = '<option value="0"> Tidak ada </option>';
		}
		
		$id = '';
		if(isset($val['id'])){
			$id = 'id="'.$val['id'].'"';
		}
		
		$inp = '';
		if(isset($val['label'])){
			$inp .= self::opt_label($val['label']);
		}else{
			$inp .= '<div class="col-md-'. self::$col_label .'"></div>';
		}
		
		$status = '';
		if(isset($val['status'])){
			$status = $val['status'];
		}

		$btn = '';
		$cols = self::$col_input;
		if(isset($val['button'])){
			$cols -= 1;
			$btn = '<div class="col-md-1">'.$val['button'].'</div>';
		}

		if(isset($val['searching'])){
			if($val['searching']){
				$val['class'] = 'bs-select';
				$status .= ' data-live-search="true" data-size="6" data-style="blue"';
			}
		}

		// Insert type data --->
		self::$_types[$val['key']] = 'select';
		
		$inp .= '<div class="col-md-'. $cols .'">';
		$inp .= '<select '.$id.' name="'.$val['key'].'" class="form-control '.$val['class'].'" '.$status.' onchange="sobad_options(this)">'.$func.'</select>';
		$inp .= '</div>';
		
		return $inp.$btn;
	}
	
	private function opt_value_select($key,$select,$opt){
		$func = '<option value="'.$key.'" '.$select.'>'.$opt.' </option>';
		return $func;
	}

	private function opt_datepicker($val=array()){
		// key , class , value , date
		// id, to, data, label (optional)
		
		$inp = '';
		if(isset($val['label'])){
			$inp .= self::opt_label($val['label']);
		}else{
			$inp .= '<div class="col-md-'. self::$col_label .'"></div>';
		}
		
		$id = '';
		if(isset($val['id'])){
			$id = 'id="'.$val['id'].'"';
		}

		$date = date('d-m-Y');
		if(isset($val['date'])){
			$date = $val['date'];
		}

		$status = '';
		if(isset($val['status'])){
			$status = $val['status'];
		}

		$status2 = '';
		if(isset($val['status2'])){
			$status2 = $val['status2'];
		}

		$class = '';
		if(isset($val['to'])){
			$class = 'input-group input-large';
		}

		// Insert type data --->
		self::$_types[$val['key']] = 'date';

		$val['value'] = date($val['value']);
		$val['value'] = strtotime($val['value']);
		$val['value'] = date('d-m-Y',$val['value']);

		$inp .= '<div class="col-md-'. self::$col_input .'">';
		$inp .= '<div '.$id.' class="'.$class.' input-daterange date-picker '.$val['class'].'" data-date="'.$date.'" data-date-format="dd-mm-yyyy">';
		$inp .= '<input type="text" class="form-control" value="'.$val['value'].'" name="'.$val['key'].'" '.$status.'>';
		
		if(isset($val['to'])){
			$val['data'] = date($val['data']);
			$val['data'] = strtotime($val['data']);
			$val['data'] = date('d-m-Y',$val['data']);

			$inp .= '<span class="input-group-addon"> to </span>';
			$inp .= '<input type="text" class="form-control" value="'.$val['data'].'" name="'.$val['to'].'" '.$status2.'>';

			// Insert type data --->
			self::$_types[$val['to']] = 'date';
		}

		$inp .= '</div>';
		$inp .= '</div>';

		return $inp;
	}
}