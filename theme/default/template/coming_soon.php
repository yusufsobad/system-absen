<?php
(!defined('THEMEPATH'))?exit:'';

class sobad_coming_soon{
	
	public static function get_soon($args=array()){
		$check = array_filter($args);
		if(empty($check)){
			return '';
		}

		$this->get_layout($args);
		$this->script_countdown($args['countdown'],$args['date']);
	}

	private static function get_header($href='#',$logo=''){
		?>
			<div class="row">
				<div class="col-md-12 coming-soon-header">
					<a class="brand" href="<?php print($href) ;?>">
						<img src="<?php print($logo) ;?>" alt="logo"/>
					</a>
				</div>
			</div>
		<?php
	}

	private static function get_layout($args=array()){
		?>
			<div class="container">
				<?php $this->get_header($args['head_link'],$args['head_logo']) ;?>
				<div class="row">
					<div class="col-md-6 coming-soon-content">
						<?php 
							print($args['content']) ;
							isset($args['subscribe'])?$this->get_subscribe($args['subscribe']):'';
						?>
						
						<ul class="social-icons margin-top-20">
							<?php
								foreach ($args['sosmed'] as $key => $val) {
									echo '
										<li>
											<a href="'.$val['link'].'" data-original-title="'.$val['title'].'" class="'.$val['icon'].'"></a>
										</li>
									';
								}
							?>
						</ul>
					</div>
					<div class="col-md-6 coming-soon-countdown">
						<?php if(isset($args['countdown'])): ?>
							<div id="<?php print($args['countdown']) ;?>"></div>
						<?php endif ;?>
					</div>
				</div>
				<!--/end row-->
				<div class="row">
					<div class="col-md-12 coming-soon-footer">
						 <?php print(date('Y')) ;?> &copy; Antropometri Measurement
					</div>
				</div>
			</div>
		<?php
	}

	private static function get_subscribe($args=array()){
		$check = array_filter($args);
		if(!empty($check)){
			?>
				<form class="form-inline" action="#">
					<div class="input-group input-large">
						<input id="soon-subscribe-input" type="text" data-sobad="<?php print($args['func']) ;?>" data-object="_subscribe" data-load="<?php print($args['load']) ;?>" name="subscribe" class="form-control">
						<span class="input-group-btn">
							<button class="btn blue" data-sobad="<?php print($args['func']) ;?>" data-object="_subscribe" data-load="<?php print($args['load']) ;?>" onclick="sobad_submit(this)" type="button">
								<span> <?php print(isset($args['label'])?$args['label']:'Subscribe') ;?> </span>
								<i class="m-icon-swapright m-icon-white"></i>
							</button>
						</span>
					</div>
				</form>
			<?php
		}
	}

	private static function script_countdown($load='',$until=''){
		if(empty($until)){
			$until = date('Y-m-d');
			$until = strtotime($until);
			$until = date('Y-m-d',strtotime('+1 days',$date));
		}

		ob_start();
			?>
				<script type="text/javascript">
        		    var austDay = new Date('<?php print($until) ;?>');
		            $('#<?php print($load) ;?>').countdown({until: austDay});
		            
		            $('#soon-subscribe-input').keypress(
  						function(event){
    						if (event.which == '13') {
    							sobad_submit(this);
      							event.preventDefault();
    						}
						});
				</script>
			<?php
		$countdown['coming-soon'] = ob_get_clean();
		reg_hook("reg_script_foot",$countdown);
	}
}