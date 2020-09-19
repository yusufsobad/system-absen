<?php

class sobad_wilayah{
	private static $table = 'tbl_wilayah';
	
	private static function list(){
		$list = sobad_table::_get_list(parent::$table);
		$list[] = 'no';

		return $list;
	}

	public static function get_all($args=array(),$limit="1=1"){
		$check = array_filter($args);
		if(empty($check)){
			$args = self::list();
		}
		
		$where = "WHERE $limit";
		return self::_get_wilayah($where,$args);
	}
	
	public static function get_province($id=0){
		$args = array('id_prov','provinsi');
		
		$where = "WHERE id_prov='$id' GROUP BY id_prov";
		return self::_get_wilayah($where,$args);
	}
	
	public static function get_city($id=0){
		$args = array('id_kab','kabupaten','tipe');
		
		$where = "WHERE id_kab='$id' GROUP BY id_kab";
		return self::_get_wilayah($where,$args);
	}
	
	public static function get_subdistrict($id=0){
		$args = array('id_kec','kecamatan');
		
		$where = "WHERE id_kec='$id' GROUP BY id_kec";
		return self::_get_wilayah($where,$args);
	}
	
	public static function get_village($id=0){
		$args = array('no','kelurahan','kodepos');
		
		$where = "WHERE no='$id'";
		return self::_get_wilayah($where,$args);
	}
	
	public static function get_id_by_subdistrict($id=0){
		$args = array('id_prov','id_kab','id_kec');
		
		$where = "WHERE id_kec='$id'";
		return self::_get_wilayah($where,$args);
	}
	
	public static function get_id_by_village($val=''){
		$args = array('no','kelurahan','kodepos');
		
		$where = "WHERE kelurahan='$val'";
		return self::_get_wilayah($where,$args);
	}
	
	public static function get_postcode($id_prov=0,$id_kab=0,$id_kec=0){
		$args = array('no','kodepos');
		
		$where = "WHERE id_prov='$id_prov' AND id_kab='$id_kab' AND id_kec='$id_kec' GROUP BY kodepos";
		return self::_get_wilayah($where,$args);
	}
	
	public static function get_provinces(){
		$args = array('id_prov','provinsi');
		
		$where = "WHERE 1=1 GROUP BY id_prov";
		return self::_get_wilayah($where,$args);
	}
	
	public static function get_cities($id=0){
		$args = array('id_kab','kabupaten','tipe');
		
		$where = "WHERE id_prov='$id' GROUP BY id_kab";
		return self::_get_wilayah($where,$args);
	}
	
	public static function get_subdistricts($id=0){
		$args = array('id_kec','kecamatan');
		
		$where = "WHERE id_kab='$id' GROUP BY id_kec";
		return self::_get_wilayah($where,$args);
	}
	
	public static function get_villages($id=0){
		$args = array('no','kelurahan','kodepos');
		
		$where = "WHERE no='$id'";
		return self::_get_wilayah($where,$args);
	}
	
	private static function _get_wilayah($where='',$args=array()){
		$wilayah = array();
		
		$q = sobad_db::_select_table($where,self::$table,$args);
		if($q!==0){
			while($r=$q->fetch_assoc()){
				$item = array();
				foreach($r as $key => $val){
					$item[$key] = $val;
				}
				
				$wilayah[] = $item;
			}
		}
		
		return $wilayah;
	}
	
}