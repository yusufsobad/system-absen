<?php

class sobad_overtime extends _class{
	public static $table = 'abs-overtime';

	public static $tbl_join = 'abs-overtime-detail';

	protected static $join = "joined.over_id ";

	public static function blueprint($key='overtime'){
		$args = array(
			'type'	=> 'overtime',
			'table'	=> self::$table,
			'detail'=> array(
				'user'	=> array(
					'key'		=> 'ID',
					'table'		=> 'abs-user',
					'column'	=> array('name','divisi','no_induk'),
					'detail'	=> array(
						'divisi'	=> array(
							'key'		=> 'ID',
							'table'		=> 'abs-module',
							'column'	=> array('meta_value')
						)
					)
				),
				'approve'	=> array(
					'key'		=> 'ID',
					'table'		=> 'abs-user',
					'column'	=> array('name','divisi','no_induk'),
				),
				'accept'	=> array(
					'key'		=> 'ID',
					'table'		=> 'abs-user',
					'column'	=> array('name','divisi','no_induk'),
				)
			),
			'joined'	=> array(
				'key'		=> 'over_id',
				'table'		=> self::$tbl_join,
			)
		);

		if($key=='over_detail'){
			$args = array(
				'type'		=> $key,
				'table'		=> self::$table,
				'detail'	=> array(
					'user_id'	=> array(
						'key'		=> 'ID',
						'table'		=> 'abs-user',
						'column'	=> array('name','divisi','no_induk'),
						'detail'	=> array(
							'divisi'	=> array(
								'key'		=> 'ID',
								'table'		=> 'abs-module',
								'column'	=> array('meta_value')
							)
						)
					)
				)
			);
		}

		return $args;
	}

// ------------------------------------------------------------
// ---- Function Overtime Detail ------------------------------
// ------------------------------------------------------------	

	public static function get_detail($id=0,$args=array(),$limit=''){	
		self::$table = 'abs-overtime-detail';	
		$where = "WHERE `abs-overtime-detail`.ID='$id' $limit";
		$data = parent::_check_join($where,$args,'over_detail');

		self::$table = 'abs-overtime';
		return $data;
	}

	public static function get_details($id=0,$args=array(),$limit=''){	
		self::$table = 'abs-overtime-detail';	
		$where = "WHERE `abs-overtime-detail`.over_id='$id' $limit";
		$data = parent::_check_join($where,$args,'over_detail');

		self::$table = 'abs-overtime';
		return $data;
	}	
}