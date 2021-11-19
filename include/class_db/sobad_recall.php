<?php

class sobad_recall extends _class{
	public static $table = 'abs-user-recall';

	public static function blueprint(){
		$args = array(
			'type'	=> 'recall',
			'table'	=> self::$table,
			'detail'=> array(
				'user_id'	=> array(
					'key'		=> 'ID',
					'table'		=> 'abs-user',
					'column'	=> array('name')
				)
			)
		);

		return $args;
	}
}