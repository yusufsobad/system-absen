<?php
(!defined('THEMEPATH'))?exit:'';

class admin_dashboard{
	public static function _dashboard($args=array()){
		$check = array_filter($args);
		if(empty($check)){
			return '';
		}

		foreach($args as $key => $val){	
			if(is_callable(array(new self(),$val['func']))){
				$func = $val['func'];
				self::{$func}($val['data']);
			}
		}
	}
	 
	private static function _block_info($args=array()){
		// color blue-madison , red-intense , green-haze , purple-plum
		$check = array_filter($args);
		if(empty($check)){
			return '';
		}

		$icon = isset($args['icon']) && !empty($args['icon'])?$args['icon']:'<i class="fa fa-comments"></i>';

		if(!isset($args['column'])){
			$args['column'] = array();
		}

		$default = array(
			'lg' 	=> 3,
			'md'	=> 3,
			'sm'	=> 6,
			'xs'	=> 12
		);

		$cols = array();
		foreach ($default as $key => $val) {
			$_cols = isset($args['column'][$key])?$args['column'][$key]:$val;
			$cols[] = 'col-'.$key.'-'.$_cols;
		}

		$cols = implode(' ', $cols);
		?>
			<div class="<?php print($cols) ;?>">
				<div class="dashboard-stat <?php print($args['color']) ;?>">
					<div class="visual">
						<?php echo $icon ;?>
					</div>
					<div class="details">
						<div class="number"> <?php print($args['qty']) ;?> </div>
						<div class="desc"> <?php print($args['desc']) ;?> </div>
					</div>
					<?php 
						if(isset($args['button'])){
							print($args['button']);
						}
					?>
				</div>
			</div>
		<?php
	}
}