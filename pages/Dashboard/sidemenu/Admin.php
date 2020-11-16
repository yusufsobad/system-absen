<?php

function sidemenu_admin(){
	$args = array();
	$args['dashboard'] = array(
		'status'	=> 'active',
		'icon'		=> 'icon-home',
		'label'		=> 'Dashboard',
		'func'		=> 'dash_absensi',
		'child'		=> null
	);
	
	$args['general'] = array(
		'status'	=> '',
		'icon'		=> 'fa fa-bars',
		'label'		=> 'General',
		'func'		=> '#',
		'child'		=> menu_general()
	);

	$args['data_intern'] = array(
		'status'	=> '',
		'icon'		=> 'fa fa-university',
		'label'		=> 'Internship',
		'func'		=> '#',
		'child'		=> menu_internship()
	);

	$args['option'] = array(
		'status'	=> '',
		'icon'		=> 'fa fa-gear',
		'label'		=> 'Options',
		'func'		=> '#',
		'child'		=> menu_option()
	);

	$args['report'] = array(
		'status'	=> '',
		'icon'		=> 'fa fa-book',
		'label'		=> 'Report',
		'func'		=> '#',
		'child'		=> menu_report()
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

function menu_general(){
	$args = array();
	$args['divisi'] = array(
		'status'	=> '',
		'icon'		=> '',
		'label'		=> 'Jabatan',
		'func'		=> 'divisi_absen',
		'child'		=> NULL
	);

	$args['employee'] = array(
		'status'	=> '',
		'icon'		=> '',
		'label'		=> 'Karyawan',
		'func'		=> 'employee_absen',
		'child'		=> NULL
	);

	$args['worktime'] = array(
		'status'	=> '',
		'icon'		=> '',
		'label'		=> 'Jam Kerja',
		'func'		=> 'worktime_absen',
		'child'		=> NULL
	);	

	$args['permit'] = array(
		'status'	=> '',
		'icon'		=> '',
		'label'		=> 'Izin',
		'func'		=> 'permit_absen',
		'child'		=> NULL
	);

	$args['auto-shift'] = array(
		'status'	=> '',
		'icon'		=> '',
		'label'		=> 'Auto Shift',
		'func'		=> 'shift_absen',
		'child'		=> NULL
	);
	
	return $args;
}

function menu_internship(){
	$args = array();
	$args['prodi'] = array(
		'status'	=> '',
		'icon'		=> '',
		'label'		=> 'Prodi',
		'func'		=> 'prodi_absen',
		'child'		=> NULL
	);

	$args['faculty'] = array(
		'status'	=> '',
		'icon'		=> '',
		'label'		=> 'Fakultas',
		'func'		=> 'faculty_absen',
		'child'		=> NULL
	);

	$args['university'] = array(
		'status'	=> '',
		'icon'		=> '',
		'label'		=> 'University',
		'func'		=> 'university_absen',
		'child'		=> NULL
	);

	$args['internship'] = array(
		'status'	=> '',
		'icon'		=> '',
		'label'		=> 'Internship',
		'func'		=> 'internship_absen',
		'child'		=> NULL
	);
	
	return $args;
}

function menu_option(){
	$args = array();
	$args['group'] = array(
		'status'	=> '',
		'icon'		=> '',
		'label'		=> 'Group',
		'func'		=> 'group_absen',
		'child'		=> NULL
	);

	$args['holiday'] = array(
		'status'	=> '',
		'icon'		=> '',
		'label'		=> 'Hari Libur',
		'func'		=> 'holiday_absen',
		'child'		=> NULL
	);

	$args['day-off'] = array(
		'status'	=> '',
		'icon'		=> '',
		'label'		=> 'Cuti',
		'func'		=> 'dayOff_absen',
		'child'		=> NULL
	);

	$args['diagram'] = array(
		'status'	=> '',
		'icon'		=> '',
		'label'		=> 'Diagram',
		'func'		=> '',//'diagram_absen',
		'child'		=> NULL
	);	
	
	return $args;
}

function menu_report(){
	$args = array();
	$args['absen'] = array(
		'status'	=> '',
		'icon'		=> '',
		'label'		=> 'Group',
		'func'		=> '',//'report_absen',
		'child'		=> NULL
	);

	$args['punishment'] = array(
		'status'	=> '',
		'icon'		=> '',
		'label'		=> 'Punishment',
		'func'		=> 'punishment_absen',
		'child'		=> NULL
	);

	$args['statistik'] = array(
		'status'	=> '',
		'icon'		=> '',
		'label'		=> 'Statistik',
		'func'		=> '',//'statik_absen',
		'child'		=> NULL
	);	
	
	return $args;
}