<?php

abstract class _class{

	private static $_join = array();
	
	private static $_inner = '';

	private static $_where = '';

	protected static $_type = '';

	private static function schema($key=''){
		$args = static::blueprint(self::$_type);

		if(!empty($key)){
			return $args = isset($args[$key])?$args[$key]:array();
		}

		return $args;
	}

	public static function _list(){
		$list = sobad_table::_get_list(static::$table);
		$list[] = 'ID';

		return $list;
	}

	private static function list_join(){
		$list = array();
		if(!empty(static::$tbl_join)){
			$list = sobad_table::_get_list(static::$tbl_join);
			$list[] = 'id_join';
		}

		return $list;
	}

	public static function list_meta($type=''){
		self::$_type = $type;
		$list = array();
		if(property_exists(new static,'list_meta')){
			static::set_listmeta();
			$list = static::$list_meta;
		}
		return $list;
	}

	protected static function _check_array($args=array(),$func='_list'){
		$check = array_filter($args);
		if(empty($check)){
			$args = self::{$func}();
		}

		return $args;
	}

	public static function count($limit='1=1 ',$args=array(),$type=''){
		$inner = '';$meta = false;
		$limit = empty($limit)?"1=1 ":$limit;

		$blueprint = self::schema();
		$table = $blueprint['table'];
		if(isset($blueprint['detail'])){
			$check = array_filter($blueprint['detail']);
			if(!empty($check)){
				self::_detail($args,$table,$blueprint['detail']);
				$inner = self::$_inner;
				self::$_inner = '';
			}
		}

		$check = array_filter(self::list_meta($type));
		if(!empty($check)){
			$inner .= "LEFT JOIN `".static::$tbl_meta."` ON `".static::$table."`.ID = `".static::$tbl_meta."`.meta_id ";
			$limit .= static::$group;
			$meta = true;
		}

		$count = self::_get_data($inner." WHERE ".$limit,array("COUNT('`$table`.ID') AS count"));
		
		if($meta){
			return count($count);
		}

		return $count[0]['count'];
	}
	
	public static function get_id($id,$args=array(),$limit='',$type=''){
		$where = "WHERE `".static::$table."`.ID='$id' $limit";
		return self::_check_join($where,$args,$type);
	}

	public static function get_all($args=array(),$limit='',$type=''){
		$where = "WHERE 1=1 $limit";
		return self::_check_join($where,$args,$type);
	}

	public static function check_meta($id=0,$key=''){
		$inner = "LEFT JOIN `".static::$tbl_meta."` ON `".static::$table."`.ID = `".static::$tbl_meta."`.meta_id ";;
		$where = $inner."WHERE meta_id='$id' AND meta_key='$key'";

		return self::_get_data($where,array('`'.static::$tbl_meta.'`.ID'));
	}

	// -----------------------------------------------------------------
	// --- Function Check Join -----------------------------------------
	// -----------------------------------------------------------------	

	protected static function _check_join($where='',$args=array(),$type=''){
		$user = self::_list();
		
		self::$_type = $type;
		self::$_join = array();
		self::$_inner = '';
		self::$_where = $where;

		$blueprint = self::schema();
		$table = $blueprint['table'];

		$check = array_filter($args);
		if(empty($args)){
			$joins = self::list_join();
			$metas = self::list_meta($type);

			$args = array_merge($user,$joins,$metas);
		}
	
		if(isset($blueprint['detail'])){
			$check = array_filter($blueprint['detail']);
			if(!empty($check)){
				self::_detail($args,$table,$blueprint['detail']);
			}
		}

		if(isset($blueprint['joined'])){
			$check = array_filter($blueprint['joined']);
			if(!empty($check)){
				self::_joined($args,$table,$blueprint['joined']);
			}
		}

		if(isset($blueprint['meta'])){
			$check = array_filter($blueprint['meta']);
			if(!empty($check)){
				$args = self::_meta($args,$type);
			}
		}

		$j_logs='';
		foreach ($args as $key => $val) {
			if(in_array($val, $user)){
				self::$_join[] = "`".static::$table."`.$val";
			}
		}

		$check = array_filter(self::$_join);
		if(!empty($check)){
			$args = self::$_join;
		}

		$where = self::$_inner.self::$_where;
		self::$_inner = '';self::$_where = '';
		return self::_get_data($where,$args);
	}

	private static function _detail($args=array(),$table='',$detail=''){

		foreach($detail as $_key => $val){
			if($args==='*' || in_array($_key,$args)){
				$key = "_".$_key;
				
				foreach($val['column'] as $ky => $vl){
					self::$_join[] = "$key.$vl AS ".$vl."_".substr($key,1,4);
				}
				
				$tbl = $val['table'];
				$col = $val['key'];

				self::$_inner .= "LEFT JOIN `$tbl` AS $key ON `$table`.$_key = $key.$col ";
				
				if(isset($val['detail'])){
					$_detail = $val['detail'];
					self::_detail($val['column'],$key,$_detail);
				}
				
				if(isset($val['joined'])){
					$_joined = $val['joined'];
					$_args = $_joined['column'];
					self::_joined($_args,$key,$_joined);
				}
			}
		}
	}
	
	private static function _joined($args=array(),$table='',$joined=''){

		$lst = isset($joined['column'])?$joined['column']:self::list_join();
		$tbl = $joined['table'];
		$col = $joined['key'];
		
		$inner = '';
		foreach($args as $key => $val){
			if(in_array($val,$lst)){
				if($val=='id_join'){
					self::$_join[] = "`$tbl`.ID AS id_join";
				}else{
					self::$_join[] = "`$tbl`.$val";
				}
				
				$inner = "LEFT JOIN `$tbl` ON `$table`.ID = `$tbl`.$col ";
			}
		}

		self::$_inner .= $inner;
		
		if(isset($joined['detail'])){
			$_detail = $joined['detail'];
			self::_detail($args,$tbl,$_detail);
		}

	}

	private static function _meta($args=array(),$type=''){
		$where = self::$_where;
		$inner = '';$group = $where;
		$meta = self::list_meta($type);
		$select = "max(case when `".static::$tbl_meta."`.meta_key = '{{key}}' then `".static::$tbl_meta."`.meta_value end) '{{key}}'";

		foreach ($args as $key => $val) {
			if(in_array($val, $meta)){
				self::$_join[] = str_replace('{{key}}', $val, $select);
				$inner = "LEFT JOIN `".static::$tbl_meta."` ON `".static::$table."`.ID = `".static::$tbl_meta."`.meta_id ";

				$group_by = static::$group;
				if(strpos($group, "ORDER BY") !== false){
					$group = str_replace("ORDER BY",$group_by." ORDER BY",$where);
				}else if(strpos($group, "LIMIT") !== false){
					$group = str_replace("LIMIT",$group_by." LIMIT",$where);
				}else{
					$group = $where.$group_by;
				}
			}
		}

		self::$_where = $group;
		self::$_inner .= $inner;

		return $args;
	}

	protected static function _get_data($where='',$args=array()){
		global $DB_NAME;
		$data = array();

		$_database = $DB_NAME;
		if(property_exists(new static,'database')){
			$DB_NAME = static::$database;
		}

		$q = sobad_db::_select_table($where,static::$table,$args);
		if($q!==0){
			while($r=$q->fetch_assoc()){
				$item = array();
				foreach($r as $key => $val){
					$item[$key] = $val;
				}
				
				$data[] = $item;
			}
		}
		
		$DB_NAME = $_database;
		return $data;
	}
}