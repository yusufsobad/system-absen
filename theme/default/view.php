<?php

(!defined('THEMEPATH'))?exit:'';

require dirname(__FILE__).'/scripts.php';
require dirname(__FILE__).'/view_header.php';
require dirname(__FILE__).'/quick_sidebar.php';

class metronic_layout extends metronic_template{
	private static $page = array();

	private static $sidemenu = array();

	private static function _search(){
		?>
		<form class="sidebar-search " action="javascript:;" method="POST">
			<a href="javascript:;" class="remove">
				<i class="icon-close"></i>
			</a>
			<div class="input-group">
				<input type="text" class="form-control" placeholder="Search...">
				<span class="input-group-btn">
					<a href="javascript:;" class="btn submit"><i class="icon-magnifier"></i></a>
				</span>
			</div>
		</form>
		<?php
	}

	public static function load_here(){
		self::_header();
		self::_clearfix();
		self::_container();
		self::_footer();
	}

	private static function _header(){
		$lang = array();
		if(constant('language')){
			global $reg_language;
			$lang = $reg_language;
		}

		$args = array(
			0	=> array(
				'menu'	=> 'menu_notif',
				'data'	=> array()
			),
			1	=> array(
				'menu'	=> 'menu_language',
				'data'	=> $lang
			),
			2	=> array(
				'menu'	=> 'menu_user',
				'data'	=> ''
			),
			3	=> array(
				'menu'	=> 'side_toggle',
				'data'	=> ''
			),
		);

		unset($args[1]);
		
		?>
			<!-- BEGIN HEADER -->
			<div class="page-header -i navbar navbar-fixed-top">
				<?php
					metronic_header::_create($args);
				?>
			</div>
			<!-- END HEADER -->
		<?php
	}

	private static function _clearfix(){
		?>
			<div class="clearfix"></div>
		<?php
	}

	private static function _container(){
		?>
		<div class="page-container">
			<?php
				self::_sidebar();
				$request = self::$page;
			?>
			<div class="page-content-wrapper">
				<div id="here_content" class="page-content">
			
			<?php
				$check = array_filter($request);
				if(!empty($check)){
					$func = $request['func'];
					$data = $request['label'];
			
					if(class_exists($func)){
						if(is_callable(array($func,'_sidemenu'))){	
							echo $func::_sidemenu($data);
						}
					}
				}
			?>
			
				</div>
			</div>
			<?php	
				self::_quick_side();
			?>
		</div>	
		<?php
	}

	private static function _footer(){
		?>
		<div class="page-footer">
			<div class="page-footer-inner">
				<?php echo date('Y').' @ '.constant('company') ;?>
			</div>
			<div class="scroll-to-top" style="display: none;">
				<i class="icon-arrow-up"></i>
			</div>
		</div>
		<?php
	}

	private static function _sidebar(){
		global $reg_sidebar;
		
		?>
			<div class="page-sidebar-wrapper">
				<div class="page-sidebar navbar-collapse collapse">
					<div class="slimScrollDiv" style="position: relative; overflow: hidden; width: auto; height: 410px;">
						<ul class="page-sidebar-menu " data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200" data-height="410" data-initialized="1" style="overflow: hidden; width: auto; height: 410px;">
							<?php
								self::_sidemenu_active($reg_sidebar);

								$data = self::$sidemenu;
								self::$sidemenu = array();
								foreach ($data as list($a,$b)) {
									self::$sidemenu[$a] = $b;
								}

								self::_side_multiple($reg_sidebar);
							?>
						</ul>
					</div>
				</div>
			</div>
		<?php
	}

	private static function _quick_side(){
		?>
			<a href="javascript:;" class="page-quick-sidebar-toggler">
				<i class="icon-close"></i>
			</a>
		<?php
			metronic_quick_sidebar::_create();
	}

	private static function _sidemenu_active($args=array(),$idx=0){
		$page = get_page_url();

		foreach ($args as $key => $val) {
			self::$sidemenu[$idx] = array($key,false);

			if(!empty($page)){
				if($page==$key){
					foreach (self::$sidemenu as $ky => $vl) {
						self::$sidemenu[$ky][1] = true;
					}

					return true;
					break;
				}
			}else{
				if($val['status']=='active'){
					foreach (self::$sidemenu as $ky => $vl) {
						self::$sidemenu[$ky][1] = true;
					}

					return true;
					break;
				}
			}

			if($val['child']!=null){
				$break = self::_sidemenu_active($val['child'],$idx+1);

				if($break){
					return true;
				}
			}
		}

		return false;
	}

	private static function _side_multiple($args=array()){
		$req = array();
		$check = array_filter($args);
		if(!empty($check)){
			foreach($args as $key => $val){
				// Check active Sidemenu
				$select = '<span class="selected"></span>';

				if(array_key_exists($key, self::$sidemenu)){
					if(self::$sidemenu[$key]){
						$req['func'] = $val['func'];
						$req['label'] = $val['label'];
						$status = 'start open active';

						self::$page = $req;
					}
				}else{
					$status = '';
				}
				
				echo '<li class="'.$status.'">';
				
				$parent = '';
				$target = '';
				if(empty($val['func'])){
					$parent = 'disabled-link';
					$target = 'disable-target';
				}
				
				//$url = 'http://'.get_home().'/'.$val['func'];
				$side = '<a id="sobad_'.$val['func'].'" class="sobad_sidemenu '.$parent.'" data-uri="'.$key.'" href="javascript:">
					<i class="'.$val['icon'].' fa-fw"></i>
					<span class="title '.$target.'">'.$val['label'].'</span>';
				$side .= $select;
				
				// Check child sidemenu
				if($val['child']!=null){
					echo $side.'<span class="arrow"></span></a>';
					echo '<ul class="sub-menu">';
					self::_side_multiple($val['child']);
					echo '</ul>';
				}else{
					echo $side.'</a>';
				}
				
				echo '</li>';
			}
		}
	}

	public static function _head_content($args=array()){
		$check = array_filter($args);
		if(empty($check)){
			return 'Not Available';
		}

		$qty = isset($args['modal'])?$args['modal']:2;

		parent::_modal_form($qty);
		parent::_theme_option();
		?>
			<h3 class="page-title">
				<?php print($args['title']) ;?>
			</h3>
		<?php
		parent::_head_pagebar($args['link'],$args['date']);
	}

	public static function _content($func,$args = array()){
		if(method_exists('metronic_template',$func)){	
			// get content
			parent::{$func}($args);
			
		}else{
			?><div style="text-align:center;"> Tidak ada data yang di Load </div><?php
		}
	}
}