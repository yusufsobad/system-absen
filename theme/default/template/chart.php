<?php

class create_chart{
	public function _layout($args=array()){
		$check = array_filter($args);
		if(empty($check)){
			return '';
		}
		
		foreach($args as $key => $val){	
			if(is_callable(array($this,$val['func']))){
				$func = $val['func'];
				self::{$func}($val['data']);
			}
		}
	}
	
	private function _site_load($args=array()){
		$check = array_filter($args);
		if(empty($check)){
			return '';
		}
		
		$status = isset($args['status'])?$args['status']:'';
		$col = intval($args['col']);
		$col = 'col-md-'.$col.' col-sm-'.$col;

		$type = isset($args['type'])?$args['type']:'';
		?>
			<div class="<?php print($col) ;?>">
					<!-- BEGIN PORTLET-->
					<div class="portlet light chart_malika" data-sobad="<?php print($args['func']) ;?>" data-load="<?php print($args['id']) ;?>" data-type="<?php print($type) ;?>" <?php print($status) ;?>>
						<div class="portlet-title" style="text-align:center;">
							<div class="caption" style="float:unset;">
								<?php print($args['label']) ;?>
							</div>
						</div>
						<div class="portlet-body">
							<div id="chart_loading_<?php print($args['id']) ;?>" style="text-align:center;">
								<img src="asset/img/loading.gif" alt="loading"/>
							</div>
							<div id="chart_content_<?php print($args['id']) ;?>" class="display-none">
								<canvas id="<?php print($args['id']) ;?>" style="height: 228px;">
								</canvas>
							</div>
						</div>
					</div>
					<!-- END PORTLET-->
			</div>
		<?php
	}
}