<?php

class sobad_module extends _class{
	public static $table = 'abs-module';

	public static function blueprint(){
		$args = array(
			'type'		=> 'module',
			'table'		=> self::$table
		);

		return $args;
	}

	private static function _check_type($type=''){
		if(!empty($type)){
			$args = array(
				'department',
				'faculty',
				'study_program',
				'group',
				'day_off',
				'division'
			);

			if(in_array($type, $args)){
				return true;
			}
		}

		return false;
	}
	
	public static function _gets($type='',$args=array(),$limit=''){
		if(self::_check_type($type)){
			$where = "WHERE meta_key='$type' $limit";
			return self::_check_join($where,$args,$type);
		}

		return array();
	}

	public static function _get_divisions(){
		$divisi = array();
		$data = self::_gets('division',array('ID','meta_value','meta_note'));
		foreach ($data as $key => $val) {
			$detail = self::_conv_divisi($val['meta_note']);
			$detail = implode(', ',$detail['ID']);
			$args = sobad_user::get_all(array('ID','name','divisi'),"AND divisi IN ($detail) AND status!='0'");

			$divisi[$key] = array(
				'id'		=> $val['ID'],
				'name'		=> $val['meta_value'],
				'detail'	=> $args
			);
		}

		return $divisi;
	}

	public static function _conv_divisi($data=''){
		$data = unserialize($data);
		if(isset($data['data'])){
			$idx = implode(',', $data['data']);
			$data = sobad_module::_gets('department',array('ID','meta_value'),"AND ID IN($idx)");
			$data = convToGroup($data,array('ID','meta_value'));

			return $data;
		}

		return array();
	}

	public static function _get_division($id=0,$status=0){
		return self::_get_group($id,$status,'division');
	}

	public static function _gets_tree_division($id=0){
		$args = array();

		$dept = self::_gets('department',array(),"AND meta_reff='$id'");		
		foreach ($dept as $key => $val) {
			$id = $val['ID'];
			$args[$id] = $val;
			$args[$id]['child'] = self::_gets_tree_division($id);
		}

		return $args;
	}

	public static function _get_group($id=0,$status=0,$type='group'){
		$group = sobad_module::_gets($type,array('ID','meta_value','meta_note'));

		if($status==7){
			return array();
		}

		$args = array();
		foreach ($group as $key => $val) {
			$data = unserialize($val['meta_note']);

			if(in_array($id, $data['data'])){
				$args = array(
					'ID'		=> $val['ID'],
					'name'		=> $val['meta_value'],
					'data'		=> $data['data'],
					'status'	=> $data['status']	
				);

				break;
			}
		}

		return $args;
	}
}