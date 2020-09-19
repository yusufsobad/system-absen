<?php

class sobad_work extends _class{
	public static $table = 'abs-work';

	protected static $tbl_join = 'abs-work-normal';

	protected static $join = 'joined.reff ';

	public function blueprint(){
		$args = array(
			'type'		=> 'work',
			'table'		=> self::$table,
			'joined'	=> array(
				'key'		=> 'reff',
				'table'		=> self::$tbl_join
			)
		);

		return $args;
	}

	public function get_workTime($id=0){
		$args =	array('name','days','time_in','time_out','note','status');
		if($id){
			return parent::get_id($id,$args);
		}

		return parent::get_all($args);
	}

	public function get_works(){
		$args =	array('ID','name');

		$where = "WHERE 1=1";
		return parent::_get_data($where,$args);
	}
}