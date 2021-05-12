<?php
date_default_timezone_set('Asia/Jakarta');

header("Content-Type:application/json");
$json = json_decode(file_get_contents('php://input'), true);

// Setting 

	define('AUTHPATH',$_SERVER['SERVER_NAME']);
	define('DEFPATH',$_SERVER['SERVER_NAME']);
	
	require 'config/hostname.php';
	require 'class_db.php';

// End Setting

$json = sobad_curl($json);
print($json);

function sobad_curl($args=array()){
    $check = array_filter($args);
    if(empty($check)){
        $data = array(
	        'status'    => 'error',
	        'msg'       => 'Request not Found!!!'
        );
	
    	return json_encode($data);
    }

    if(!isset($args['object'])){
		$data = array(
	        'status'    => 'error',
	        'msg'       => 'key Object not Found!!!'
        );
	
    	return json_encode($data);
	}

	if(!class_exists($args['object'])){
		$data = array(
	        'status'    => 'error',
	        'msg'       => 'Object not Found : '.$args['object']
        );
	
    	return json_encode($data);
	}
    
    $object = $args['object'];
	$ajax_func = $args['func'];
	$_data = $args['data'];

	if(!method_exists($object,$ajax_func)){
		$data = array(
	        'status'    => 'error',
	        'msg'       => 'Function undefined : '.$ajax_func
        );
	
    	return json_encode($data);
	}

	if(!is_callable(array(new $object(),$ajax_func))){
	    $data = array(
	        'status'    => 'error',
	        'msg'       => 'Not Call Function : '.$ajax_func
        );
	
    	return json_encode($data);
	}

	$dt = array('','','','','');
	for($i=0;$i<5;$i++){
		$dt[$i] = isset($_data[$i])?$_data[$i]:'';
	}
	
	$msg = $object::{$ajax_func}($dt[0],$dt[1],$dt[2],$dt[3],$dt[4]);
	
	$data = array(
	    'status'    => 'success',
	    'msg'       => $msg
    );
	
	return json_encode($data);
}