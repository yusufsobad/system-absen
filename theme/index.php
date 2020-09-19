<?php
(!defined('DEFPATH'))?exit:'';

function sobad_meta_html(){ 
?>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta content="width=device-width, initial-scale=1" name="viewport"/>
	<meta content="" name="description"/>
	<meta content="" name="author"/>

<?php
}

function sobad_themes(){
	global $reg_theme;

	if(empty($reg_theme)){
		$reg_theme = 'default';
	}

	// definisi path theme
	if(!defined('THEMEPATH')){
		define('THEMEPATH',dirname(__FILE__));
	}

	require THEMEPATH.'/'.$reg_theme.'/template.php';
	require THEMEPATH.'/'.$reg_theme.'/view.php';
}