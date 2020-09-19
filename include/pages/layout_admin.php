<?php

function portlet_admin($opt = array(),$args=array()){
	$check = array_filter($args);
	if(empty($check)){
		return '';
	}

	$metronic = new metronic_layout();
	
	$title = isset($opt['title'])?$opt['title']:'';
	$data = array();
	
	$data[] = array(
		'style'		=> isset($opt['style'])?$opt['style']:'',
		'script'	=> isset($opt['script'])?$opt['script']:'',
		'func'		=> '_portlet',
		'data'		=> $args
	);
	
	ob_start();
	$metronic->_head_content($title);
	$metronic->_content('_panel',$data);
	return ob_get_clean();
}

function tabs_admin($opt = array(),$args=array()){
	$check = array_filter($args);
	if(empty($check)){
		return '';
	}

	$metronic = new metronic_layout();
	
	$title = isset($opt['title'])?$opt['title']:'';
	$data = array();
	
	$data[] = array(
		'style'		=> isset($opt['style'])?$opt['style']:'',
		'script'	=> isset($opt['script'])?$opt['script']:'',
		'func'		=> '_tabs',
		'data'		=> $args
	);
	
	ob_start();
	$metronic->_head_content($title);
	$metronic->_content('_panel',$data);
	return ob_get_clean();
}

function modal_admin($args = array()){	
	$check = array_filter($args);
	if(empty($check)){
		return '';
	}

	$metronic = new metronic_layout();

	ob_start();
	$metronic->_content('_modal_content',$args);
	return ob_get_clean();
}

function table_admin($args=array()){
	$check = array_filter($args);
	if(empty($check)){
		return '';
	}

	$metronic = new metronic_layout();

	ob_start();
	$metronic->_content('sobad_table',$args);
	return ob_get_clean();
}