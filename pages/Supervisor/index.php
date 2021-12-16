<?php
require dirname(__FILE__).'/function.php';

$args = array();
$args['supervisor'] = array(
	'page'		=> 'supervisor_sobad',
	'home'		=> false
);
reg_hook('reg_page',$args);

class supervisor_sobad{
	public function _reg(){
		$GLOBALS['body'] = 'page-header-fixed';
		
		$prefix = constant('_prefix');
		if(!isset($_SESSION[$prefix.'page'])){
			_error::_page404();
		}
		
		self::_script();
		reg_hook('reg_language',array());
		reg_hook('reg_sidebar',sidemenu_supervisor());
	}

	public function _page(){
		metronic_layout::load_here();
	}

	private function _script(){
		$script = new vendor_script();
		$theme = new theme_script();

		// url script jQuery - Vendor
		$get_jquery = $script->_get_('_js_core',array('jquery-core'));
		$head[0] = '<script src="'.$get_jquery['jquery-core'].'"></script>';

		// url script css ----->
		$css = array_merge(
				$script->_get_('_css_global'),
				$script->_get_('_css_page_level',array('bootstrap-datepicker','bootstrap-clockpicker','fullcalender','bootstrap-editable')),
				$script->_get_('_css_dropzone'),
				$script->_get_('_css_contextmenu'),
				$script->_get_('_css_tags_input'),
				$script->_get_('_css_chart'),
				$script->_get_('_css_datatable',array('datatable')),
				$theme->_get_('_css_page_level',array('themes-search')),
				$theme->_get_('_css_page'),
				$theme->_get_('_css_theme')
			);
		
		// url script css ----->
		$js = array_merge(
				$script->_get_('_js_core'),
				$script->_get_('_js_page_level',array('bootstrap-toastr','bootstrap-datepicker','bootstrap-clockpicker')),
				$script->_get_('_js_dropzone'),
				$script->_get_('_js_mask_money'),
				$script->_get_('_js_contextmenu'),
				$script->_get_('_js_tags_input'),
				$script->_get_('_js_chart'),
				//$script->_get_('_js_form_editable'),
				//$script->_get_('_js_page_modal'),
				$theme->_get_('_js_page_level')
			);
		
	//	unset($script['bootstrap-modal']);
		unset($js['jquery-core']);
		unset($js['themes-modal']);	
		unset($js['themes-login-soft']);
		unset($js['themes-editable']);
			
		ob_start();
		self::load_script();
		$custom['login'] = ob_get_clean();

		reg_hook("reg_script_head",$head);
		reg_hook("reg_script_css",$css);
		reg_hook("reg_script_js",$js);
		reg_hook("reg_script_foot",$custom);
	}

	private function load_script(){
		?>
			<script>
			jQuery(document).ready(function() {    
			   Metronic.init(); // init metronic core componets
			   Layout.init(); // init layout
			   QuickSidebar.init(); // init quick sidebar
			Demo.init(); // init demo features
			//   Index.init();   
			//   Index.initDashboardDaterange();
			//   Index.initJQVMAP(); // init index page's custom scripts
			//   Index.initCalendar(); // init index page's custom scripts
			//   Index.initCharts(); // init index page's custom scripts
			//   Index.initChat();
			//   Index.initMiniCharts();
			//   Tasks.initDashboardWidget();
			//   UIExtendedModals.init();
			//FormEditable.init();
			});
			</script>
		<?php
	}
}