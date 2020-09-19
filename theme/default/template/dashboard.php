<?php
(!defined('THEMEPATH'))?exit:'';

class admin_dashboard{
	public function _dashboard($args=array()){
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
	
	private function _block_info($args=array()){
		// color blue-madison , red-intense , green-haze , purple-plum
		$check = array_filter($args);
		if(empty($check)){
			return '';
		}
		?>
			<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
				<div class="dashboard-stat <?php print($args['color']) ;?>">
					<div class="visual">
						<i class="fa fa-comments"></i>
					</div>
					<div class="details">
						<div class="number"> <?php print($args['qty']) ;?> </div>
						<div class="desc"> <?php print($args['desc']) ;?> </div>
					</div>
					<a id="sobad_<?php print($args['func']) ;?>" class="more" href="javascript:;">
						View more <i class="m-icon-swapright m-icon-white"></i>
					</a>
				</div>
			</div>
		<?php
	}
}