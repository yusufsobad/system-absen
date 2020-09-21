<?php
(!defined('THEMEPATH'))?exit:'';

class user_login extends create_form{
	public static function login($func,$args=array()){
		$check = array_filter($args);
		if(empty($check)){
			$data = 'placeholder="username" autofocus required';
			$args[0] = array(
				'option' 	=> 'input',
				'data'		=> array(
					'type'		=> 'text',
					'key'		=> 'user',
					'class'		=> '',
					'value'		=> '',
					'data'		=> $data
				)
			);

			$data = 'placeholder="password" required';
			$args[1] = array(
				'option' 	=> 'input',
				'data'		=> array(
					'type'		=> 'password',
					'key'		=> 'pass',
					'class'		=> '',
					'value'		=> '',
					'data'		=> $data
				)
			);
		}
	?>

	<!-- BEGIN LOGIN FORM -->
	<form class="login-form" data-sobad="<?php print($func) ;?>" action="javascript:void(0)" method="post">
		<h3 class="form-title"><?php print(__e('login_comment')) ;?></h3>
		<div class="alert alert-danger display-hide">
			<button class="close" data-close="alert"></button>
			<span>
			Enter any username and password. </span>
		</div>
		<div class="form-group">
			<!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
			<label class="control-label visible-ie8 visible-ie9"><?php print(__e('username')) ;?></label>
			<div class="input-icon">
				<i class="fa fa-user"></i>
				<input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="<?php print(__e('username')) ;?>" name="username"/>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label visible-ie8 visible-ie9"><?php print(__e('password')) ;?></label>
			<div class="input-icon">
				<i class="fa fa-lock"></i>
				<input class="form-control placeholder-no-fix" type="password" autocomplete="off" placeholder="<?php print(__e('password')) ;?>" name="password"/>
			</div>
		</div>
		<div class="form-actions">
			<label class="checkbox">
			<input type="checkbox" name="remember" value="1"/> <?php print(__e('remember me')) ;?> </label>
			<button id="btn_login_submit" data-sobad="<?php print($func) ;?>" type="submit" class="btn blue pull-right">
			<?php print(__e('login_button')) ;?> <i class="m-icon-swapright m-icon-white"></i>
			</button>
		</div>
	</form>
	<!-- END LOGIN FORM -->
		<?php
	}
}