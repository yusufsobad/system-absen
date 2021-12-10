<?php
(!defined('AUTHPATH'))?exit:'';

class sobad_table{

	public static function _get_table($func){
		$func = str_replace('-','_',$func);
				
		$obj = new self();
		if(is_callable(array($obj,$func))){
			$list = $obj::{$func}();
				return $list;
			}
		
		return false;
	}
		
	public static function _get_list($func=''){
		$list = array();
		$lists = self::_get_table($func);
		if($lists){
			foreach ($lists as $key => $val) {
				$list[] = $key;
			}
		}
		
		return $list;
	}
		

	private static function _list_table(){
		// Information data table
		
		$table = array(
				'abs-about'		=> self::abs_about(),
				'abs-contract'		=> self::abs_contract(),
				'abs-history'		=> self::abs_history(),
				'abs-holiday'		=> self::abs_holiday(),
				'abs-log-detail'		=> self::abs_log_detail(),
				'abs-module'		=> self::abs_module(),
				'abs-permit'		=> self::abs_permit(),
				'abs-post'		=> self::abs_post(),
				'abs-university'		=> self::abs_university(),
				'abs-user'		=> self::abs_user(),
				'abs-user-log'		=> self::abs_user_log(),
				'abs-user-meta'		=> self::abs_user_meta(),
				'abs-user-recall'		=> self::abs_user_recall(),
				'abs-work'		=> self::abs_work(),
				'abs-work-normal'		=> self::abs_work_normal(),
				'tbl_wilayah'		=> self::tbl_wilayah(),
		);
		
		return $table;
	}
		

		private static function abs_about(){
			$list = array(
				'config_name'	=> '',
				'config_value'	=> '',
				'status'	=> 0,	
			);
			
			return $list;
		}

		private static function abs_contract(){
			$list = array(
				'user_id'	=> 0,
				'status'	=> 0,
				'no_surat'	=> 0,
				'inserted'	=> date('Y-m-d'),
				'notes'	=> '',	
			);
			
			return $list;
		}

		private static function abs_history(){
			$list = array(
				'meta_id'	=> 0,
				'meta_key'	=> '',
				'meta_value'	=> '',
				'meta_note'	=> '',
				'meta_var'	=> '',
				'meta_date'	=> date('Y-m-d H:i:s'),	
			);
			
			return $list;
		}

		private static function abs_holiday(){
			$list = array(
				'title'	=> '',
				'holiday'	=> date('Y-m-d'),
				'status'	=> 0,	
			);
			
			return $list;
		}

		private static function abs_log_detail(){
			$list = array(
				'log_id'	=> 0,
				'date_schedule'	=> date('Y-m-d'),
				'times'	=> 0,
				'status'	=> 0,
				'date_actual'	=> '',
				'log_history'	=> '',
				'type_log'	=> 0,	
			);
			
			return $list;
		}

		private static function abs_module(){
			$list = array(
				'meta_key'	=> '',
				'meta_value'	=> '',
				'meta_note'	=> '',
				'meta_reff'	=> 0,	
			);
			
			return $list;
		}

		private static function abs_permit(){
			$list = array(
				'user'	=> 0,
				'start_date'	=> date('Y-m-d'),
				'range_date'	=> date('Y-m-d'),
				'num_day'	=> 0.00,
				'type_date'	=> 0,
				'type'	=> 0,
				'note'	=> '',	
			);
			
			return $list;
		}

		private static function abs_post(){
			$list = array(
				'title'	=> 0,
				'company'	=> 0,
				'contact'	=> 0,
				'type'	=> 0,
				'user'	=> 0,
				'payment'	=> 0,
				'post_date'	=> date('Y-m-d'),
				'_status'	=> 0,
				'inserted'	=> date('Y-m-d H:i:s'),
				'updated'	=> date('Y-m-d H:i:s'),
				'var'	=> '',
				'notes'	=> '',
				'trash'	=> 0,	
			);
			
			return $list;
		}

		private static function abs_university(){
			$list = array(
				'name'	=> '',
				'phone_no'	=> '',
				'email'	=> '',
				'address'	=> '',
				'province'	=> 0,
				'city'	=> 0,
				'subdistrict'	=> 0,
				'post_code'	=> 0,	
			);
			
			return $list;
		}

		private static function abs_user(){
			$list = array(
				'username'	=> '',
				'password'	=> '',
				'no_induk'	=> 0,
				'divisi'	=> 0,
				'phone_no'	=> '',
				'name'	=> '',
				'picture'	=> 0,
				'work_time'	=> 0,
				'dayOff'	=> 0.00,
				'status'	=> 0,
				'end_status'	=> 0,
				'inserted'	=> date('Y-m-d'),	
			);
			
			return $list;
		}

		private static function abs_user_log(){
			$list = array(
				'user'	=> 0,
				'shift'	=> 0,
				'type'	=> 0,
				'_inserted'	=> date('Y-m-d'),
				'time_in'	=> '',
				'time_out'	=> '',
				'note'	=> '',
				'punish'	=> 0,
				'history'	=> '',	
			);
			
			return $list;
		}

		private static function abs_user_meta(){
			$list = array(
				'meta_id'	=> 0,
				'meta_key'	=> '',
				'meta_value'	=> '',	
			);
			
			return $list;
		}

		private static function abs_user_recall(){
			$list = array(
				'user_id'	=> 0,
				'end_status'	=> 0,
				'_entry_date'	=> date('Y-m-d'),
				'_resign_date'	=> date('Y-m-d'),
				'note'	=> '',	
			);
			
			return $list;
		}

		private static function abs_work(){
			$list = array(
				'name'	=> '',
				'type'	=> 0,	
			);
			
			return $list;
		}

		private static function abs_work_normal(){
			$list = array(
				'reff'	=> 0,
				'days'	=> 0,
				'time_in'	=> '',
				'time_out'	=> '',
				'note'	=> '',
				'status'	=> 0,	
			);
			
			return $list;
		}

		private static function tbl_wilayah(){
			$list = array(
				'id_prov'	=> 0,
				'id_kab'	=> 0,
				'id_kec'	=> 0,
				'provinsi'	=> '',
				'kabupaten'	=> '',
				'kecamatan'	=> '',
				'kelurahan'	=> '',
				'tipe'	=> '',
				'kodepos'	=> 0,	
			);
			
			return $list;
		}

}