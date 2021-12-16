<?php

function sidemenu_supervisor(){
	$args = array();
	$args['dashboard'] = array(
		'status'	=> 'active',
		'icon'		=> 'icon-home',
		'label'		=> 'Dashboard',
		'func'		=> 'dash_user',
		'child'		=> null
	);
	
	$args['spt-lembur'] = array(
		'status'	=> '',
		'icon'		=> 'fa fa-gears',
		'label'		=> 'Lembur',
		'func'		=> 'lembur_supervisor',
		'child'		=> null
	);
	
	$args['about'] = array(
		'status'	=> '',
		'icon'		=> 'fa fa-dashboard',
		'label'		=> 'About',
		'func'		=> '',
		'child'		=> null
	);
	
	return $args;
}