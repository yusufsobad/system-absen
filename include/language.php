<?php

(!defined('DEFPATH'))?exit:'';

function get_locale(){
	$prefix = constant('_prefix');
	return $_SESSION[$prefix.'language'];
}

if(!constant('language')){
	return '';
}

function __e($word=''){
	return get_translate($word);
}

// ------------------------------------------------------------
// ---- Class Language Conversi -------------------------------
// ------------------------------------------------------------
	
function _file_get_json($file=''){
	$file = dirname(__FILE__).'/language/'.$file.'.json';		
	$lang = file_get_contents($file);
	$lang = json_decode($lang,true);

	return $lang;
}

function _file_save_json($file='',$data=array()){
	$file = dirname(__FILE__).'/language/'.$file.'.json';		
	$json = json_encode($data,JSON_PRETTY_PRINT);

	if(file_put_contents($file,$json)){
		return 1;
	}else{
		$err = new _error();
		die($err->_alert_db("Gagal menyimpan JSON"));
	}
}

function get_language($lang=''){
	$words = _file_get_json('language');	
	$words = $words['language'];

	if(!isset($words[$lang])){
		return $words;
	}

	return $words[$lang];
}

function get_translates(){
	return _file_get_json('translate');
}

function set_translates($data=array()){
	return _file_save_json('translate',$data);
}
	
function get_translate($word=''){
	if(empty($word)){
		return '';
	}

	$lang = get_locale();
	$words = get_translates();
	$words = $words['translate'];

	if(!isset($words[$word][$lang])){
		return $word;
	}

	return $words[$word][$lang];
}