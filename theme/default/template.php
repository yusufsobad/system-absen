<?php

(!defined('THEMEPATH'))?exit:'';

require dirname(__FILE__).'/template/chart.php';
require dirname(__FILE__).'/template/coming_soon.php';
require dirname(__FILE__).'/template/file_manager.php';
require dirname(__FILE__).'/template/dashboard.php';
require dirname(__FILE__).'/template/form.php';
require dirname(__FILE__).'/template/login.php';
require dirname(__FILE__).'/template/table.php';

abstract class metronic_template{

	// ---------------------------------------------
	// Create Panel --------------------------------
	// ---------------------------------------------
	protected static function _panel($args=array()){
		$check = array_filter($args);
		if(empty($check)){
			return '';
		}

		foreach($args as $key => $val){
			$func = $val['func'];

			$object = 'metronic_template';
			if(isset($val['object'])){
				if(class_exists($val['object'])){
					$object = $val['object'];
				}
			}

			if(method_exists($object,$func)){
				// add style
				$css = array_filter($val['style']);
				if(!empty($css)){
					if(is_callable(array(new $val['style'][0](),$val['style'][1]))){
						$obj = $val['style'][0];
						$style = $val['style'][1];						
						$obj::{$style}();
					}
				}
			
				echo '<div class="row">';
					$object::{$func}($val['data']);
				echo '</div>';
					self::_clearfix();
					
				// add script
				$js = array_filter($val['script']);
				if(!empty($js)){
					if(is_callable(array(new $val['script'][0](),$val['script'][1]))){
						$obj = $val['script'][0];
						$style = $val['script'][1];
						$obj::{$style}();
					}
				}
			}
		}
	}

	private static function _clearfix(){
		?>
			<div class="clearfix"></div>
		<?php
	}

	// ---------------------------------------------
	// Header Content ------------------------------
	// ---------------------------------------------

	public static function _modal_form($qty=0){
		?>
			<div class="modal fade bs-modal-lg" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
				<div id="here_modal" class="modal-dialog modal-full-custom">
				<!-- /.modal-dialog -->
					<?php self::_modal_loading() ;?>
					<!-- /.modal-content -->
				</div>
			</div>
			<?php 
				if($qty>1): 
					for($i=2;$i<=$qty;$i++){
					?>
						<div class="modal fade bs-modal-lg" id="myModal<?php print($i) ;?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
							<div id="here_modal<?php print($i) ;?>" class="modal-dialog modal-lg">
							<!-- /.modal-dialog -->
							</div>
						</div>
					<?php 
					}
				endif;
	}

	private static function _modal_loading(){
		?>
			<div class="modal-content">
				<div class="modal-body">
					<img src="asset/img/loading-spinner-grey.gif" alt="" class="loading">
					<span> &nbsp;&nbsp;Loading... </span>
				</div>
			</div>
		<?php
	}

	public static function _modal_content($args=array()){
		$check = array_filter($args);
		if(empty($check)){
			return '';
		}

		$id = isset($args['id'])?'id="'.$args['id'].'"':'';
		$obj = _object;
	
		?>
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
					<h4 class="modal-title"><?php print($args['title']) ;?></h4>
				</div>
				
				<?php foreach($args['func'] as $key => $func){ ?>
					<div class="modal-body">
						<div <?php print($id) ;?> class="row">
							<?php
								if(method_exists('metronic_template', $func)){
									self::{$func}($args['data'][$key]);
								}else if(method_exists($obj, $func)){
									$obj::{$func}($args['data'][$key]);
								}
							?>
						</div>
					</div>
				<?php }?>
				
				<div class="modal-footer">
					<?php
						$button = $args['button'];
						if(method_exists('metronic_template', $button)){
							self::{$button}($args['status']);
						}
					?>
				</div>
			</div>
		<?php
	}

	private static function _btn_modal_save($args=array()){
		$check = array_filter($args);
		if(empty($check)){
			return '';
		}
		
		$status = '';
		if(isset($args['status'])){
			$status = $args['status'];
		}

		$type = '';
		if(isset($args['type'])){
			$type = $args['type'];
		}
		
		?>
		<button data-sobad="<?php print($args['link']) ;?>" data-load="<?php print($args['load']) ;?>" data-type="<?php print($type) ;?>" type="button" class="btn blue" data-dismiss="modal" onclick="sobad_submit(this)" <?php print($status) ;?>>Save</button>
		<button type="button" class="btn default" data-dismiss="modal">Cancel</button>
		<?php
	}

	private static function _btn_modal_import($args=array()){
		$check = array_filter($args);
		if(empty($check)){
			return '';
		}
		
		$status = '';
		if(isset($args['status'])){
			$status = $args['status'];
		}

		$load = 'sobad_portlet';
		if(isset($args['status'])){
			$load = $args['load'];
		}
		
		?>
		<button id="importFile" data-sobad="<?php print($args['link']) ;?>" data-load="<?php print($load) ;?>" type="button" class="btn blue" data-dismiss="modal" <?php print($status) ;?> style="display:none;"></button>
		
		<script>
		$(document).ready(function (e) {
			$("#<?php print($args['id']) ;?>").submit(function(){
				sobad_load('<?php print($args['id']) ;?>');
				sobad_import(this,'<?php print($load) ;?>');
				Metronic.unblockUI('<?php print($args['id']) ;?>');
				return false;
			});
		});
		</script>
		<?php
	}

	private static function _btn_modal_yes($args=array()){
		$check = array_filter($args);
		if(empty($check)){
			return '';
		}
		
		?>
		<button data-sobad="<?php print($args['link']) ;?>" data-object="<?php print($args['func']) ;?>" type="button" class="btn green" onclick="sobad_quest(this)">Ya</button>
		<button type="button" class="btn red" data-dismiss="modal">Tidak</button>
		<?php
	}

	protected static function _theme_option(){
		include 'theme_option.php';
	}

	protected static function _head_pagebar($link=array(),$date=false){	
		$check = array_filter($link);
		?>
				<div class="page-bar">
					<ul class="page-breadcrumb">
						<li>
							<i class="fa fa-home"></i>
							<a href="">Home</a>
							<i class="fa fa-angle-right"></i>
						</li>
						<?php 
							if(!empty($check)){
								foreach($link as $key => $val){
									$angle = '';
									if($key<count($link)-1){
										$angle = '<i class="fa fa-angle-right"></i>';
									}

									echo '
										<li>
											<i class="fa"></i>
											<a id="sobad_'.$val['func'].'" class="sobad_linkheader" href="javascript:void(0)" onclick="sobad_sidemenu(this)">
												'.$val['label'].'
											</a>
											'.$angle.'
										</li>';
								}
							} 
						?>
					</ul>
					
				<?php if($date): ?>
					<div class="page-toolbar">
						<div id="dashboard-report-range" class="pull-right tooltips btn btn-sm btn-default" data-container="body" data-placement="bottom" data-original-title="Change dashboard date range">
							<i class="icon-calendar"></i>&nbsp; <span class="thin uppercase visible-lg-inline-block"></span>&nbsp; <i class="fa fa-angle-down"></i>
						</div>
					</div>
				<?php endif; ?>
				
				</div>
		<?php
	}

	// ---------------------------------------------
	// Create Portlet Box --------------------------
	// ---------------------------------------------
	public static function _portlet($args = array()){	
		$check = array_filter($args);
		if(empty($check)){
			return '';
		}
		
		$_id = "sobad_portlet";
		if(isset($args['ID'])){
			$_id = $args['ID'];
		}

		if(isset($args['object'])){
			if(class_exists($args['object'])){
				$object = $args['object'];
			}else{
				return '';
			}
		}else{
			$object = 'metronic_template';
		}
		
		?>
		<div class="col-md-12" style="border:1px solid #fff">
			<div class="portlet box blue-madison">
				<div class="portlet-title">
					<div class="caption">
						<?php print($args['label']) ;?>
					</div>
					<div class="tools">
						<?php print($args['tool']) ;?>
					</div>
					<div class="actions">
						<?php
							print($args['action']);
						?>
					</div>
				</div>
				<div class="portlet-body">
					<div id="<?php print($_id) ;?>" class="dataTables_wrapper no-footer">
						<?php
							$func = $args['func'];
							if(method_exists($object, $func)){
								$object::{$func}($args['data']);
							}
						?>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	// ---------------------------------------------
	// Create Tabs ---------------------------------
	// ---------------------------------------------
	public static function _tabs($args = array()){
		$check = array_filter($args);
		if(empty($check)){
			return '';
		}

		if(isset($args['object'])){
			if(class_exists($args['object'])){
				$object = $args['object'];
			}else{
				return '';
			}
		}else{
			$object = 'metronic_template';
		}

		?>
		<div class="col-md-12">
			<div id="sobad_tabs" class="tabbable">
				<ul class="nav nav-tabs nav-tabs-lg">
					<?php

						$script = 'sobad_tabs(this)';
						if(isset($args['script'])){
							$script = $args['script'];
						}
						
						$active = isset($args['active'])?$args['active']:'';
						$li_cls = 'active';
						foreach($args['tab'] as $key => $val){
							$li_cls = empty($active)?$li_cls:$active==$val['key']?'active':'';
							$func = isset($val['func'])?$val['func']:"_tabs";
							$info = isset($val['info'])?$val['info']:"badge-success";

							echo '
								<li class="'.$li_cls.'">
									<a onclick="'.$script.'" id="'.$val['key'].'" data-sobad="'.$func.'" data-load="tab_malika" data-toggle="tab" href="#tab_malika'.$key.'" aria-expanded="true">
									'.$val['label'].' 
									<span class="badge '.$info.'">'.$val['qty'].'</span>
									</a>
								</li>
							';

							$li_cls = '';
						}
					?>
				</ul>
				<div class="tab-content">
					<?php 
						$no_tab = 0;
						foreach($args['tab'] as $key => $val){
							?>
							<div class="tab-pane active" id="tab_malika">
								<div class="row">

							<?php
								if($no_tab < 1){
									$func = $args['func'];
									if(method_exists($object, $func)){
										$object::{$func}($args['data']);
									}
								}
							?>
							
								</div>
							</div>
					<?php
						$no_tab += 1;
						}
					?>
				</div>
			<?php
	}

	// ---------------------------------------------
	// Create Inline Menu --------------------------
	// ---------------------------------------------

	public static function _inline_menu($args=array()){
		$check = array_filter($args);
		if(empty($check)){
			return '';
		}

		?>
			<div class="col-md-3">
				<ul class="ver-inline-menu tabbable margin-bottom-10">
					<?php
						$li_cls = 'active';
						foreach($args['menu'] as $key => $val){
							if(isset($args['active'])){
								$li_cls = '';
								if($args['active']==$key){
									$li_cls = 'active';
								}
							}

							echo '
								<li class="'.$li_cls.'">
									<a id="'.$val['key'].'" data-toggle="tab" href="#inline_malika'.$key.'" aria-expanded="true">
										<i class="'.$val['icon'].'"></i>
										'.$val['label'].' 
										<span class="after"></span>
									</a>
								</li>
							';
							$li_cls = '';
						}
					?>
				</ul>
			</div>
			<div class="col-md-9">
				<div class="tab-content">
					<?php
						$active = 'active';
						foreach($args['content'] as $key => $val){
							if(isset($args['active'])){
								$active = '';
								if($args['active']==$key){
									$active = 'active';
								}
							}

							$object = isset($val['object'])?$val['object']:'metronic_template';
							$func = $val['func'];
							if(method_exists($object, $func)){
								echo '<div id="inline_malika'.$key.'" class="tab-pane '.$active.'">';
								$object::{$func}($val['data']);
								echo '</div>';
								
								$active = '';
							}
						}
					?>
				</div>
			</div>
		<?php
	}

	// ---------------------------------------------
	// Create option dashboard ---------------------
	// ---------------------------------------------
	public static function sobad_dashboard($args = array()){
		$dash = admin_dashboard::_dashboard($args);
	}

	// ---------------------------------------------
	// Create Table --------------------------------
	// ---------------------------------------------
	public static function sobad_table($args = array()){
		$table = create_table::_table($args);
	}

	// ---------------------------------------------
	// Create option File manager ------------------
	// ---------------------------------------------
	public static function sobad_file_manager($args = array()){
		$manager = create_file_manager::_layout($args);
	}

	// ---------------------------------------------
	// Create Form ---------------------------------
	// ---------------------------------------------
	public static function sobad_form($args = array()){
		$form = create_form::get_form($args);
	}

	// ---------------------------------------------
	// Create Chart ---------------------------------
	// ---------------------------------------------
	public static function sobad_chart($args = array()){
		$chart = create_chart::_layout($args);
	}
}