<?php

class sobad_university extends _class{
	public static $table = 'abs-university';

	public function blueprint(){
		$args = array(
			'type'		=> 'university',
			'table'		=> self::$table
		);

		return $args;
	}
}