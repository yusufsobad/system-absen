<?php

class sobad_punishment extends _class{
	public static $table = 'abs-punishment';

	public static function blueprint(){
		$args = array(
			'type'	=> 'punishment',
			'table'	=> self::$table,
			'detail'=> array(
				'log_id'	=> array(
					'key'		=> 'ID',
					'table'		=> 'abs-user-log',
					'column'	=> array('user','shift','time_in'),
					'detail'	=> array(
						'user'		=> array(
							'key'		=> 'ID',
							'table'		=> 'abs-user',
							'column'	=> array('name','no_induk')
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
		$where = "WHERE user_log='$log'";

		return parent::_get_data($where,array('ID'));
	}
}