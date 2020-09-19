<?php
(!defined('DEFPATH'))?exit:'';

$args = array();
$args['login'] = array(
	'page'	=> 'login_absen',
	'theme'	=> 'default',
	'home'	=> false
);

reg_hook('reg_page',$args);

class login_absen{

	public function _reg(){
		$GLOBALS['body'] = 'login';
		self::script_login();
	}

	private function script_login(){
		$script = new vendor_script();
		$theme = new theme_script();

		// url script css ----->
		$css = array_merge(
				$script->_get_('_css_global'),
				$script->_get_('_css_page_level',array('select2','bootstrap-toastr')),
				$theme->_get_('_css_page_level',array('themes-login-soft')),
				$theme->_get_('_css_theme')
			);
		
		// url script css ----->
		$js = array_merge(
				$script->_get_('_js_core'),
				$script->_get_('_js_page_level',array('bootstrap-toastr')),
				$script->_get_('_js_page_login'),
				$theme->_get_('_js_page_level')
			);
		
		unset($js['jquery-ui']);
		unset($js['bootstrap-hover']);
		unset($js['bootstrap-hover-dropdown']);
		unset($js['jquery-slimscroll']);
		unset($js['bootstrap-switch']);
		
		unset($js['themes-quick-sidebar']);
		unset($js['themes-index']);
		unset($js['themes-task']);
		unset($js['themes-editable']);
		unset($js['themes-picker']);
		unset($js['themes-contextmenu']);
		
		ob_start();
		self::load_script();
		$custom['login'] = ob_get_clean();

		reg_hook("reg_script_css",$css);
		reg_hook("reg_script_js",$js);
		reg_hook("reg_script_foot",$custom);
	}

	private function load_script(){
		?>
			<script>
			jQuery(document).ready(function() {     
			  Metronic.init(); // init metronic core components
			  Layout.init(); // init current layout
			  Login.init("login_absen");
			  Demo.init();
			       // init background slide images
			       $.backstretch([
			        "asset/img/bg/4.jpg",
					"asset/img/bg/5.jpg",
			        "asset/img/bg/6.jpg",
			        ], {
			          fade: 1000,
			          duration: 8000
			    }
			    );
			});
			</script>
		<?php
	}

	public function _page(){
		?>
		<!-- BEGIN LOGO -->
		<div class="logo">
			<img src="asset/img/logo-big.png" alt=""> 
		</div>
		<!-- END LOGO -->
		<!-- BEGIN LOGIN -->
		<div class="content">
		<?php
			print(user_login::login('login_absen'));
		?>
		</div>
		<!-- END LOGIN -->
		<!-- BEGIN COPYRIGHT -->
		<div class="copyright">
			<?php print(date('Y')) ;?> © System <?php print(constant('company')) ;?>
		</div>
		<!-- END COPYRIGHT -->
		<?php
	}

	public function check_login($args=array()){
		$data = sobad_asset::ajax_conv_json($args);
		$user = $data['username'];
		$pass = md5($data['password']);
		
		$q = sobad_user::check_login($user,$pass);

		$check = array_filter($q);
		if(!empty($check))
		{	
			$prefix = constant('_prefix');
			$time = 10 * 60 * 60; // 10 jam

			$r=$q[0];
			$_SESSION[$prefix.'page'] = $r['dept'];
			$_SESSION[$prefix.'user'] = $user;
			$_SESSION[$prefix.'id'] = $r['ID'];
			$_SESSION[$prefix.'name'] = $r['name'];

			setcookie('id',$r['ID'],time() + (60*60*10));
			setcookie('name',$user,time() + (60*60*10));
			
			return '/system-absen';
		}
		else
		{
			_error::_user_login();
		}
	}

	// ----------------------------------------------
	// Function Logout Admin ------------------------
	// ----------------------------------------------

	public function logout(){
		$prefix = constant('_prefix');

		unset($_SESSION[$prefix.'page']);
		unset($_SESSION[$prefix.'user']);
		unset($_SESSION[$prefix.'name']);

		setcookie('id','');
		setcookie('name','');		

		return '/system-absen/login';
	}	

}