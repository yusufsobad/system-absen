<?php

function sidemenu_admin(){
	$args = array();
	$args['dashboard'] = array(
		'status'	=> 'active',
		'icon'		=> 'icon-home',
		'label'		=> 'Dashboard',
		'func'		=> 'dash_admin',
		'child'		=> null
	);
	
	$args['setting'] = array(
		'status'	=> '',
		'icon'		=> 'fa fa-gears',
		'label'		=> 'Option',
		'func'		=> '#',
		'child'		=> menu_optAdmin()
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

function menu_optAdmin(){
	$args = array();

	$args['employee'] = array(
		'status'	=> '',
		'icon'		=> '',
		'label'		=> 'Karyawan',
		'func'		=> 'employee_admin',
		'child'		=> NULL
	);

	$args['karir'] = array(
		'status'	=> '',
		'icon'		=> '',
		'label'		=> 'Karir',
		'func'		=> 'internship_admin',
		'child'		=> NULL
	);

	$args['dayoff'] = array(
		'status'	=> '',
		'icon'		=> '',
		'label'		=> 'Cuti',
		'func'		=> 'dayOff_admin',
		'child'		=> NULL
	);
	
	return $args;
}