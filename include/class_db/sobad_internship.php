<?php

class sobad_internship extends _class{
	public static $table = 'abs-user';

	protected static $tbl_meta = 'abs-user-meta';

	protected static $group = " GROUP BY `abs-user-meta`.meta_id";

	protected static $list_meta = '';

	public function __construct(){
		self::$list_meta = array(
			'_address','_email','_university','_education','_study_program','_faculty','_semester','_classes','_sex','_province','_city','_subdistrict','_postcode'
		);
	}

	public static function blueprint(){
		$args = array(
			'type'		=> 'user',
			'table'		=> self::$table,
			'detail'	=> array(
				'work_time'	=> array(
					'key'		=> 'ID',
					'table'		=> 'abs-work',
					'column'	=> array('name')
				),
				'picture'	=> array(
					'key'		=> 'ID',
					'table'		=> 'abs-post',
					'column'	=> array('notes')
				)
			),
			'joined'	=> array(
				'key'		=> 'user',
				'table'		=> self::$tbl_join
			),
			'meta'		=> array(
				'key'		=> 'meta_id',
				'table'		=> self::$tbl_meta,
			)
		);

		return $args;
	}

}