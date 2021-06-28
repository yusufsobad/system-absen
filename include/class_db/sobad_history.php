<?php

class sobad_history extends _class{
	public static $table = 'abs-history';

	public static function blueprint(){
		$args = array(
			'type'		=> 'history',
			'table'		=> self::$table,
		);

		return $args;
	}

	private static function _check_type($type=''){
		if(!empty($type)){
			$args = array(
				'_mutasi',
				'_warning',
			);

			if(in_array($type, $args)){
				return true;
			}
		}

		return false;
	}
	
	public static function _gets($meta_id=0,$type='',$args=array(),$limit=''){
		if(self::_check_type($type)){
			$where = "WHERE meta_id='$meta_id' AND meta_key='$type' $limit";
			return self::_check_join($where,$args,$type);
		}

		return array();
	}
}