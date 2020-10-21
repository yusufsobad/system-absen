<?php

class sobad_punishment extends _class{
	public static $table = 'abs-punishment';

	public static function blueprint(){
		$args = array(
			'type'	=> 'punishment',
			'table'	=> self::$table,
			'detail'=> array(
				'user_log'	=> array(
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
							'table'		=> 'abs-user-log',
							'column'	=> array('name')
						)
					)
				)
			)
		);

		return $args;
	}
}