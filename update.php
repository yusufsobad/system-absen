<?php

define('AUTHPATH',$_SERVER['SERVER_NAME']);
require "include/config/hostname.php";

// Check Hostname yang mengakses
new hostname();

require_once 'include/class_db/sync_db.php';

/* Sample schema ---> setting multiple DB
$schema = array(
	0 => array(
		'db' 	=> DB_NAME,
		'where'	=> ''
	)
);
*/

$status = sobad_db::_create_file_list();
if($status==false){
	$status = sobad_db::_update_file_list();
}

if($status){
	header('Location: http://'.AUTHPATH.'/system-absen/');
}