<?php

class sobad_holiday extends _class{
	public static $table = 'abs-holiday';

	public function blueprint(){
		$args = array(
			'type'	=> 'holiday',
			'table'	=> self::$table
		);

		return $args;
	}
}