<?php

class sobad_permit extends _class{
	public static $table = 'abs-permit';

	public static function blueprint(){
		$args = array(
			'type'	=> 'permit',
			'table'	=> self::$table,
			'detail'=> array(
				'user'	=> array(
					'key'		=> 'ID',
					'table'		=> 'abs-user',
					'column'	=> array('name')
				)
			)
		);

		return $args;
	}
}