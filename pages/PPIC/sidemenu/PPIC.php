<?php

function sidemenu_ppic(){
	$args = array();
	$args['dashboard'] = array(
		'status'	=> 'active',
		'icon'		=> 'icon-home',
		'label'		=> 'Dashboard',
		'func'		=> 'dash_user',
		'child'		=> null
	);
	
	$args['lembur'] = array(
		'status'	=> '',
		'icon'		=> 'fa fa-gears',
		'label'		=> 'Lembur',
		'func'		=> '#',
		'child'		=> lembur_ppic()
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

function lembur_ppic(){
	$args = array();
	$args['info-lembur'] = array(
		'status'	=> '',
		'icon'		=> '',
		'label'		=> 'Info Lembur',
		'func'		=> 'infoLembur_ppic',
		'child'		=> null
	);

	$args['spt-lembur'] = array(
		'status'	=> '',
		'icon'		=> '',
		'label'		=> 'Buat Lembur',
		'func'		=> 'lembur_supervisor',
		'child'		=> null
	);

	return $args;
}