<?php

class sobad_wilayah{
	private static $table = 'tbl_wilayah';
	
	private function list(){
		$_list = new sobad_table();
		$list = $_list->_get_list(self::$table);
		$list[] = 'no';

		return $list;
	}
	
	public function get_province($id=0){
		$args = array('id_prov','provinsi');
		
		$where = "WHERE id_prov='$id' GROUP BY id_prov";
		return self::_get_wilayah($where,$args);
	}
	
	public function get_city($id=0){
		$args = array('id_kab','kabupaten','tipe');
		
		$where = "WHERE id_kab='$id' GROUP BY id_kab";
		return self::_get_wilayah($where,$args);
	}
	
	public function get_subdistrict($id=0){
		$args = array('id_kec','kecamatan');
		
		$where = "WHERE id_kec='$id' GROUP BY id_kec";
		return self::_get_wilayah($where,$args);
	}
	
	public function get_village($id=0){
		$args = array('no','kelurahan','kodepos');
		
		$where = "WHERE no='$id'";
		return self::_get_wilayah($where,$args);
	}
	
	public function get_id_by_subdistrict($id=0){
		$args = array('id_prov','id_kab','id_kec');
		
		$where = "WHERE id_kec='$id'";
		return self::_get_wilayah($where,$args);
	}
	
	public function get_id_by_village($val=''){
		$args = array('no','kelurahan','kodepos');
		
		$where = "WHERE kelurahan='$val'";
		return self::_get_wilayah($where,$args);
	}
	
	public function get_postcode($id_prov=0,$id_kab=0,$id_kec=0){
		$args = array('no','kodepos');
		
		$where = "WHERE id_prov='$id_prov' AND id_kab='$id_kab' AND id_kec='$id_kec' GROUP BY kodepos";
		return self::_get_wilayah($where,$args);
	}
	
	public function get_provinces(){
		$args = array('id_prov','provinsi');
		
		$where = "WHERE 1=1 GROUP BY id_prov";
		return self::_get_wilayah($where,$args);
	}
	
	public function get_cities($id=0){
		$args = array('id_kab','kabupaten','tipe');
		
		$where = "WHERE id_prov='$id' GROUP BY id_kab";
		return self::_get_wilayah($where,$args);
	}
	
	public function get_subdistricts($id=0){
		$args = array('id_kec','kecamatan');
		
		$where = "WHERE id_kab='$id' GROUP BY id_kec";
		return self::_get_wilayah($where,$args);
	}
	
	public function get_villages($id=0){
		$args = array('no','kelurahan','kodepos');
		
		$where = "WHERE no='$id'";
		return self::_get_wilayah($where,$args);
	}

	public function _conv_address($address='',$args=array()){
		$data = array();
		$keys = array(
			'village'		=> 'kelurahan',
			'subdistrict'	=> 'kecamatan',
			'city'			=> 'kabupaten',
			'province'		=> 'provinsi'
		);

		$add = array();
		$add[] = $address;

		$data['address'] = $address;
		$data['postcode'] = isset($args['postcode'])?$args['postcode']:'';
		foreach ($keys as $ky => $vl) {
			if(isset($args[$ky]) && !empty($args[$ky])){
				$func = 'get_'.$ky;
				$lokasi = self::{$func}($args[$ky]);
				$_lokasi = $lokasi[0][$vl];

				if($ky=='city'){
					$_lokasi = $lokasi[0]['tipe'];
					if($_lokasi=='kabupaten'){
						$_lokasi = 'kab.';
					}else{
						$_lokasi = 'kota';
					}

					$_lokasi .= ' '.$lokasi[0][$vl];
				}

				$data[$ky] = $_lokasi;
				$add[] = $data[$ky];
			}else{
				$data[$ky] = '';
			}
		}

		$_address = implode(', ',$add);
		if(isset($args['postcode'])){
			$_address .= ' - '. $args['postcode'];
		}

		$data['result'] = $_address;
		return $data;
	}
	
	private function _get_wilayah($where='',$args=array()){
		$wilayah = array();
		
		$db = new sobad_db();
		$q = $db->_select_table($where,self::$table,$args);
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