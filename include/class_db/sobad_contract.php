<?php

class sobad_contract extends _class{
	public static $table = 'abs-contract';

	public static function blueprint(){
		$args = array(
			'type'	=> 'contract',
			'table'	=> self::$table,
			'detail'=> array(
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

		return $args;
	}

	public static function get_maxSurat($status=0){
		$args = array('MAX(no_surat) as no_surat');
		$where = "WHERE status='$status'";
		
		$data = parent::_get_data($where,$args);
		$check = array_filter($data);
		if(empty($check)){
			return 0;
		}

		return $data[0]['no_surat'];
	}
}