<?php

class sobad_logDetail extends _class{
	public static $table = 'abs-log-detail';

	public static function blueprint(){
		$args = array(
			'type'	=> 'log',
			'table'	=> self::$table,
			'detail'=> array(
				'log_id'	=> array(
					'key'		=> 'ID',
					'table'		=> 'abs-user-log',
					'column'	=> array('user','shift','time_in','time_out','history','note','_inserted'),
					'detail'	=> array(
						'user'		=> array(
							'key'		=> 'ID',
							'table'		=> 'abs-user',
							'column'	=> array('name','no_induk'),
						),
						'shift'		=> array(
							'key'		=> 'ID',
							'table'		=> 'abs-work',
							'column'	=> array('ID','name')
						)
					)
				)
			)
		);

		return $args;
	}

	public static function _check_log($log=0){
		$where = "WHERE log_id='$log'";

		return parent::_get_data($where,array('ID'));
	}

	public static function get_punishment($id='',$args=array(),$limit=''){
		return parent::get_id($id,$args,"AND `".self::$table."`.type_log='1' ".$limit);
	}

	public static function get_punishments($args=array(),$limit=''){
		return parent::get_all($args,"AND `".self::$table."`.type_log='1' ".$limit);
	}
}