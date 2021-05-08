<?php
(!defined('AUTHPATH'))?exit:'';

include 'list_table.php';

class sobad_db extends conn{

	public static function _create_file_list($schema=array()){
		//Check file
		if(!file_exists(dirname(__FILE__).'/list_table.php')){
			$file = fopen(dirname(__FILE__)."/list_table.php", "w");
			self::_create_list_table('sobad_table');
			return true;
		}

		//Check file
		if(!class_exists('sobad_table')){
			self::_update_file_list($schema);
			return true;
		}

		return false;
	}

	public static function _update_file_list($schema=array()){
		self::_create_list_table('sobad_table',$schema);
		return true;
	}
	
	public static function _table_db($table){
		$list = sobad_table::_get_table($table);
		
		if($list == 0){
			$alert = parent::_alert_db("Table Tidak ditemukan!!!");
			die($alert);
		}
		
		return $list;
	}
	
	private static function _def_table($table){
		$table = parent::_table_db($table);
		self::_check_array($table);
		
		$data = array();
		foreach($table as $key => $val){
			$data[] = $key;
		}
		
		return implode(",",$data);
	}
	
	private static function _check_array($args = array()){
		$alert = parent::_alert_db("Permintaan kosong!!!");
		$check = array_filter($args);
		
		if(empty($check)){
			die($alert);
		}
	}
	
	public static function _select_table($where,$table,$args = array()){
		$conn = parent::connect();
		$alert = parent::_alert_db("pengambilan data gagal!!!");
	
		self::_check_array($args);
		if(empty($table)){die("");}
		
		$args = implode(",",$args);
		$query = sprintf("SELECT %s FROM `%s` %s",$args,$table,$where);

		$q = $conn->query($query)or die($alert);	
		if($q->num_rows<1){
			return 0;
		}
		
		return $q;
	}
	
	public static function _insert_table($table,$args = array()){
		$conn = parent::connect();
		$alert = parent::_alert_db("Gagal membuat data baru!!!");
		
		self::_check_array($args);
		if(empty($table)){die("");}
		
		$def = self::_table_db($table);
		$args = array_replace($def,$args);
		foreach($args as $key => $val){
			$tbl[] = $key;
			$val = $conn->real_escape_string($val);
			$data[] = "'$val'";
		}
		
		$tbl = implode(",",$tbl);
		$data = implode(",",$data);
	
		$query = sprintf("INSERT INTO `%s`(%s) VALUES(%s)",$table,$tbl,$data);
		
		$conn->query($query)or die($alert);
		
		return self::_max_table($table);
	}
	
	public static function _max_table($table){
		$conn = parent::connect();
		$alert = parent::_alert_db("Gagal menghitung table!!!");
		if(empty($table)){die("");}

		$query = sprintf("SELECT MAX(ID) AS ID from `%s`",$table);
		$q=$conn->query($query) or die($alert);
		if($q->num_rows>0){
			$r=$q->fetch_assoc();
			return $r['ID'];
		}
	}
	
	public static function _update_single($id,$table,$args = array()){
		$where = "ID='$id'";
		$q = self::_update_table($where,$table,$args);	
		return $q;
	}
	
	public static function _update_multiple($where,$table,$args = array()){
		$q = self::_update_table($where,$table,$args);		
		return $q;
	}
	
	public static function _delete_single($id,$table){
		$alert = parent::_alert_db("index table undefined!!!");
		if(empty($id)){die($alert);}
		
		$query = "WHERE ID='$id'";
		$q = self::_delete_table($query,$table);

		return $q;
	}
	
	public static function _delete_multiple($where,$table){
		$alert = parent::_alert_db("index table undefined!!!");
		if(empty($where)){die($alert);}
		
		$query = "WHERE $where";
		$q = self::_delete_table($query,$table);

		return $q;
	}
	
	public static function _drop_table_tmp(){
		$conn = parent::connect();
		$alert = parent::_alert_db("Gagal mengkosongkan data!!!");
		
		$table = 'sdn-tmp';
		
		$query = sprintf("TRUNCATE `%s`",$table);
		$conn->query($query)or die($alert);
		
		return 1;
	}
	
	public static function _copy_data_table($kepada=array(),$dari=array()){
		$conn = parent::connect();
		$alert = parent::_alert_db("Gagal meng-copy data baru!!!");
		
		self::_check_array($kepada);
		self::_check_array($dari);
		
		$to = isset($kepada['table'])?$kepada['table']:die('');
		if(isset($kepada['colom'])){
			$args1 = implode(',',$kepada['colom']);
		}else{
			die('');
		}
		
		$from = isset($dari['table'])?$dari['table']:die('');
		if(isset($dari['colom'])){
			$args2 = implode(',',$dari['colom']);
		}else{
			die('');
		}
		
		$where = isset($dari['where'])?$dari['where']:die('');
		
		$query = sprintf("INSERT INTO `%s`(%s) SELECT %s FROM `%s` %s",$to,$args1,$args2,$from,$where);
	
		$conn->query($query)or die($alert);
		
		return self::_max_table($to);
	}
	
	private static function _delete_table($query,$table){
		$conn = parent::connect();
		$alert = parent::_alert_db("Gagal menghapus data!!!");
		
		if(empty($table)){die("");}
		if(empty($query)){die("");}
		
		$query = sprintf("DELETE FROM `%s` %s",$table,$query);
		$conn->query($query)or die($alert);
		
		return 1;
	}
	
	private static function _update_table($where,$table,$args = array()){
		$conn = parent::connect();
		$alert = parent::_alert_db("Gagal memperbarui data");
		
		self::_check_array($args);
		if(empty($table)){die("");}
		if(empty($where)){die("");}
		
		foreach($args as $key => $val){
			$val = $conn->real_escape_string($val);
			$value = "$key='$val'";
			$data[] = $value;
		}
		
		$data = implode(",",$data);
		$query = sprintf("UPDATE `%s` SET %s WHERE %s",$table,$data,$where);	

		$conn->query($query)or die($alert);
		
		return 1;
	}

	// ----------------------------------------------------------------------
	// ---- Table Query -----------------------------------------------------
	// ----------------------------------------------------------------------

	private static function _get_table_name($db='',$limit=''){
		$GLOBALS['DB_NAME'] = $db;
		$conn = parent::connect();
		
		$where = "WHERE TABLE_SCHEMA = '$db' $limit GROUP BY `TABLE_NAME`";
		$query = sprintf("SELECT %s FROM %s %s",'TABLE_NAME','INFORMATION_SCHEMA.COLUMNS',$where);
		$q = $conn->query($query)or die("Gagal mengambil schema");

		$table = array();
		while($r=$q->fetch_assoc()){
			foreach($r as $key => $val){
				$table['table_name'][] = $val;
			}
		}

		return $table;
	}

	private static function _get_column_table($db='',$table=''){
		$GLOBALS['DB_NAME'] = $db;
		$conn = parent::connect();
		
		$where = "WHERE TABLE_SCHEMA = '$db' AND TABLE_NAME='$table'";
		$query = sprintf("SELECT %s FROM %s %s",'COLUMN_NAME,DATA_TYPE,COLUMN_KEY','INFORMATION_SCHEMA.COLUMNS',$where);
		$q = $conn->query($query)or die("Gagal mengambil schema");

		$table = array();
		while($r=$q->fetch_assoc()){
			$item = array();
			foreach($r as $key => $val){
				$item[$key] = $val;
			}
				
			$table[] = $item;
		}

		return $table;
	}

	private static function _create_list_table($class='',$schema=array()){
		$check = array_filter($schema);
		$schema = empty($check)?array(0 => array('db' => DB_NAME, 'where' => '')):$schema;

		$data = array();
		foreach ($schema as $key => $val) {
			$data[$val['db']] = self::_get_table_name($val['db'],$val['where']);
		}

		self::_list_table_schema($class,$data);
	}

	private static function _list_table_schema($class='',$data=array()){
		$php = "<?php
(!defined('AUTHPATH'))?exit:'';\n\r";

		$list_tbl = '';
		$_column = '';
		foreach ($data as $_db => $table) {
			foreach ($table['table_name'] as $ky => $val) {
				$nm_tbl = str_replace('-', '_', $val)."()";
				$list_tbl .= "
				'$val'		=> self::".$nm_tbl.",";

				$_column .= "
		private static function ".$nm_tbl."{
			\$list = array(";

				$columns = self::_get_column_table($_db,$val);
				foreach ($columns as $ky => $vl) {
					if($vl['COLUMN_KEY']!='PRI'){
						$_column .= "
				'".$vl['COLUMN_NAME']."'	=> ".self::_convert_default_dataType($vl['DATA_TYPE']).",";
					}
				}

				$_column .="	
			);
			
			return \$list;
		}\n\r";
			
			}
		}

		$func = "
	public static function _get_table(\$func){
		\$func = str_replace('-','_',\$func);
				
		\$obj = new self();
		if(is_callable(array(\$obj,\$func))){
			\$list = \$obj::{\$func}();
				return \$list;
			}
		
		return false;
	}
		";

		$func .="
	public static function _get_list(\$func=''){
		\$list = array();
		\$lists = self::_get_table(\$func);
		if(\$lists){
			foreach (\$lists as \$key => \$val) {
				\$list[] = \$key;
			}
		}
		
		return \$list;
	}
		";

		$schema = "
	private static function _list_table(){
		// Information data table
		
		\$table = array(".$list_tbl."
		);
		
		return \$table;
	}
		";

		$class = "class $class{\n".$func."\n".$schema."\n".$_column."\n}";
		$txt = $php."\n\r".$class;

		$myfile = fopen(dirname(__FILE__)."/list_table.php", "w") or die("Unable to open file!");
		fwrite($myfile, $txt);
		fclose($myfile);
	}

	private static function _convert_default_dataType($dataType=''){

		if(in_array($dataType, array('tinyint','smallint','mediumint','int','bigint')))return '0';
		
		if(in_array($dataType, array('decimal')))return '0.00';
		
		if(in_array($dataType, array('varchar','text')))return "''";
		
		if(in_array($dataType, array('date')))return "date('Y-m-d')";
		
		if(in_array($dataType, array('datetime')))return "date('Y-m-d H:i:s')";

		return "''";
	}
	
}