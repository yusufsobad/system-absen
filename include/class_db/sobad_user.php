<?php

class sobad_user extends _class{
	public static $table = 'abs-user';

	protected static $tbl_join = 'abs-user-log';

	protected static $tbl_meta = 'abs-user-meta';

	protected static $join = "joined.user ";

	protected static $group = " GROUP BY `abs-user-meta`.meta_id";

	protected static $list_meta = array();

	public static function set_listmeta(){
		$type = parent::$_type;
		$type = strtolower($type);

		switch ($type) {
			case 'internship':
				self::$list_meta = array(
					'_address','_email','_university','_education','_study_program','_faculty','_semester','_classes','_sex','_province','_city','_subdistrict','_postcode','_nickname','_entry_date','_resign_date'
				);
				break;
			
			default:
				self::$list_meta = array(
					'_address','_email','_sex','_entry_date','_place_date','_birth_date','_resign_date','_province','_city','_subdistrict','_postcode','_marital','_religion','_nickname','_resign_status'
				);
				break;
		}
	}

	public static function blueprint($type='employee'){
		self::set_listmeta();

		$args = array(
			'type'		=> 'employee',
			'table'		=> self::$table,
			'detail'	=> array(
				'divisi'	=> array(
					'key'		=> 'ID',
					'table'		=> 'abs-module',
					'column'	=> array('meta_value','meta_note')
				),
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

		if($type=='internship'){
			unset($args['detail']['divisi']);
		}

		return $args;
	}

	public static function check_login($user='',$pass=''){
		$conn = conn::connect();
		$args = array('`abs-user`.ID','`abs-user`.name','`abs-module`.meta_note AS dept');

		$user = $conn->real_escape_string($user);
		$pass = $conn->real_escape_string($pass);

		$inner = "LEFT JOIN `abs-module` ON `abs-user`.divisi = `abs-module`.ID ";
		$where = $inner."WHERE `abs-user`.username='$user' AND `abs-user`.password='$pass' AND `abs-user`.status IN ('1','2','3','4')";

		return parent::_get_data($where,$args);
	}
	
	public static function get_divisi($id=0,$args=array(),$limit=''){
		$where = "WHERE divisi='$id' $limit";
		return parent::_check_join($where,$args);
	}

// -----------------------------------------------------------------
// --- Function User-log -------------------------------------------
// -----------------------------------------------------------------

	public static function get_maxNIK(){
		$args = array('MAX(no_induk) as nik');
		$where = "WHERE divisi != '0' AND status IN ('0','1','2','3','4','5')";
		
		$data = parent::_get_data($where,$args);
		$check = array_filter($data);
		if(empty($check)){
			return 0;
		}

		return $data[0]['nik'];
	}

	public static function get_maxNIM(){
		$year = date('Y');
		$args = array('MAX(no_induk) as nim');
		$where = "WHERE divisi = '0' AND status IN ('0','7') AND YEAR(inserted)='$year'";
		
		$data = parent::_get_data($where,$args);
		$check = array_filter($data);
		if(empty($check)){
			return 0;
		}

		return $data[0]['nim'];
	}

	public static function not_work($args=array(),$limit=''){
		$where = "WHERE `".self::$tbl_join."`.type='0' $limit";
		return parent::_check_join($where,$args);
	}

	public static function go_work($args=array(),$limit=''){
		$where = "WHERE `".self::$tbl_join."`.type='1' $limit";
		return parent::_check_join($where,$args);
	}

	public static function go_home($args=array(),$limit=''){
		$where = "WHERE `".self::$tbl_join."`.type='2' $limit";
		return parent::_check_join($where,$args);
	}

	public static function go_holiday($args=array(),$limit=''){
		$where = "WHERE `".self::$tbl_join."`.type='3' $limit";
		return parent::_check_join($where,$args);
	}

	public static function go_permit($args=array(),$limit=''){
		$where = "WHERE `".self::$tbl_join."`.type='4' $limit";
		return parent::_check_join($where,$args);
	}

	public static function go_outCity($args=array(),$limit=''){
		$where = "WHERE `".self::$tbl_join."`.type='5' $limit";
		return parent::_check_join($where,$args);
	}

	public static function get_absen($args=array(),$date='',$limit=''){
		$date = empty($date)?date('Y-m-d'):$date;

		$where = "WHERE `".self::$tbl_join."`._inserted='$date' $limit";
		return parent::_check_join($where,$args);
	}

	public static function get_employees($args=array(),$limit=''){
		$whr = "(`abs-user`.status!='7' AND `abs-user`.end_status='0' OR `abs-user`.status='0' AND `abs-user`.end_status!='7')";
		$where = "WHERE $whr $limit";
		return parent::_check_join($where,$args);
	}

	public static function get_internships($args=array(),$limit=''){
		$whr = "(`abs-user`.status='7' AND `abs-user`.end_status='0' OR `abs-user`.status='0' AND `abs-user`.end_status='7')";
		$where = "WHERE $whr $limit";
		return parent::_check_join($where,$args,'internship');
	}

	public static function count_log($id=0,$limit=''){
		self::$table = 'abs-user-log';
		$where = "WHERE user='$id' $limit";

		$count = parent::_get_data($where,array('count(ID) AS cnt'));

		self::$table = 'abs-user';
		return $count[0]['cnt'];
	}

	public static function get_logs($args=array(),$limit='1=1'){
		self::$table = 'abs-user-log';
		$where = "WHERE $limit";

		$args = parent::_get_data($where,$args);

		self::$table = 'abs-user';
		return $args;
	}

	public static function get_late($date='',$limit=''){
		self::$table = 'abs-user-log';

		$work = array();
		$works = sobad_work::get_all(array('ID','name','days','time_in'));
		foreach ($works as $key => $val) {
			$idx = $val['ID'];
			if(!isset($work[$idx])){
				$work[$idx] = array();
			}

			$work[$idx][$val['days']] = $val['time_in'];
		}

		if(!empty($date)){
			$date = date($date);
			$date = strtotime($date);
			$year = date('Y',$date);
			$month = date('m',$date);

			$date = "AND YEAR(_inserted)='$year' AND MONTH(_inserted)='$month'";
		}

		$where = "WHERE punish='1' AND type IN (1,2) $date $limit";
			
		$data = array();
		$logs = parent::_get_data($where,array('ID','user','shift','type','time_in','_inserted'));
		foreach ($logs as $key => $val) {
			$_date = date($val['_inserted']);
			$_date = strtotime($_date);
			$_date = date('w',$_date);

			$punish = 30;
			$time = $work[$val['shift']][$_date];
			if($val['time_in']>=$time){
				$time = _calc_time($time,'5 minutes');

				if($val['time_in']>=$time){
					$punish = 60;
				}
				$val['punishment'] = $punish;
				$data[] = $val;
			}
		}

		self::$table = 'abs-user';

		return $data;
	}

	public static function get_late_old($date=''){
		$date = date($date);
		$date = strtotime($date);
		$year = date('Y',$date);
		$month = date('m',$date);

		$work = array();
		$works = sobad_work::get_all(array('ID','name','days','time_in'));
		foreach ($works as $key => $val) {
			$idx = $val['ID'];
			if(!isset($work[$idx])){
				$work[$idx] = array();
			}

			$work[$idx][$val['days']] = $val['time_in'];
		}

		self::$table = 'abs-user-log';
		$where = "WHERE YEAR(_inserted)='$year' AND MONTH(_inserted)='$month' AND type IN (1,2) AND punish='1'";

		$data = array();
		$logs = parent::_get_data($where,array('ID','user','shift','type','time_in','_inserted'));
		foreach ($logs as $key => $val) {
			$_date = date($val['_inserted']);
			$_date = strtotime($_date);
			$_date = date('w',$_date);

			$punish = 30;
			$time = $work[$val['shift']][$_date];
			if($val['time_in']>=$time){
				$time = _calc_time($time,'5 minutes');

				if($val['time_in']>=$time){
					$punish = 60;
				}
				$val['punishment'] = $punish;
				$data[] = $val;
			}
		}

		self::$table = 'abs-user';
		return $data;
	}

}