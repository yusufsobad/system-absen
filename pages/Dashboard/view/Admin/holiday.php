<?php

class holiday_absen extends _page{
	protected static $object = 'holiday_absen';

	protected static $table = 'sobad_holiday';

	// ----------------------------------------------------------
	// Layout category  -----------------------------------------
	// ----------------------------------------------------------

	protected function _array(){
		$args = array(
			'ID',
			'title',
			'holiday',
			'status'
		);

		return $args;

	}

	protected function table(){
		$data = array();$year = date('Y');
		$args = self::_array();

		$start = intval(parent::$page);
		$nLimit = intval(parent::$limit);
		
		$kata = '';$where = "AND YEAR(holiday)='$year' ";
		if(parent::$search){
			$src = parent::like_search($args,$where);	
			$cari = $src[0];
			$where = $src[0];
			$kata = $src[1];
		}else{
			$cari=$where;
		}
	
		$limit = 'LIMIT '.intval(($start - 1) * $nLimit).','.$nLimit;
		$where .= $limit;

		$object = self::$table;
		$args = $object::get_all($args,$where);
		$sum_data = $object::count("1=1 ".$cari);
		
		$data['data'] = array('data' => $kata);
		$data['search'] = array('Semua','nama');
		$data['class'] = '';
		$data['table'] = array();
		$data['page'] = array(
			'func'	=> '_pagination',
			'data'	=> array(
				'start'		=> $start,
				'qty'		=> $sum_data,
				'limit'		=> $nLimit
			)
		);

		$no = ($start-1) * $nLimit;
		$ky = -1;
		foreach($args as $key => $val){
			$no += 1;$ky += 1;
			$id = $val['ID'];

			$edit = array(
				'ID'	=> 'edit_'.$id,
				'func'	=> '_edit',
				'color'	=> 'blue',
				'icon'	=> 'fa fa-edit',
				'label'	=> 'edit'
			);
			
			$hapus = array(
				'ID'	=> 'del_'.$id,
				'func'	=> '_delete',
				'color'	=> 'red',
				'icon'	=> 'fa fa-trash',
				'label'	=> 'hapus',
			);

			switch ($val['status']) {
				case 1:
					$symbol = '<i class="fa fa-circle" style="color:#ffc000;"></i> ';
					break;

				case 2:
					$symbol = '<i class="fa fa-circle" style="color:#2f3293;"></i> ';
					break;

				case 3:
					$symbol = '<i class="fa fa-circle" style="color:#F3565D;"></i> ';
					break;
				
				default:
					$symbol = '';
					break;
			}
			
			$data['table'][$ky]['tr'] = array('');
			$data['table'][$ky]['td'] = array(
				'no'			=> array(
					'center',
					'5%',
					$no,
					true
				),
				'Tanggal'		=> array(
					'left',
					'auto',
					format_date_id($val['holiday']),
					true
				),
				'Deskripsi'	=> array(
					'center',
					'30%',
					$val['title'],
					true
				),
				'Status'	=> array(
					'left',
					'20%',
					$symbol.self::_conv_status($val['status']),
					true
				),
				'Edit'			=> array(
					'center',
					'10%',
					edit_button($edit),
					false
				),
				'Hapus'			=> array(
					'center',
					'10%',
					hapus_button($hapus),
					false
				)
			);
		}

		return $data;
	}

	private function head_title(){
		$args = array(
			'title'	=> 'Hari Libur <small>data hari libur</small>',
			'link'	=> array(
				0	=> array(
					'func'	=> self::$object,
					'label'	=> 'holidays'
				)
			),
			'date'	=> false
		); 
		
		return $args;
	}

	protected function get_box(){
		$data = self::table();
		
		$box = array(
			'label'		=> 'Data Hari libur',
			'tool'		=> '',
			'action'	=> self::action(),
			'func'		=> 'sobad_table',
			'data'		=> $data
		);

		return $box;
	}

	protected function layout(){
	// Check Holiday Tahun ini, Apakah sudah ada?
		self::_check_toyear();

		$box = self::get_box();
		
		$opt = array(
			'title'		=> self::head_title(),
			'style'		=> array(),
			'script'	=> array('')
		);
		
		return portlet_admin($opt,$box);
	}

	private function _check_toyear(){
		$year = date('Y');
		$holi = sobad_holiday::get_all(array('ID'),"AND YEAR(holiday)='$year'");

		$check = array_filter($holi);
		if(empty($check)){
			self::_synchronize();
		}
	}

	protected function action(){
		$sync = array(
			'ID'	=> 'sync_0',
			'func'	=> '_synchronize',
			'color'	=> 'btn-default',
			'icon'	=> 'fa fa-refresh',
			'label'	=> 'Synchronize',
			'alert'	=> true
		);

		$btn = _click_button($sync);

		$add = array(
			'ID'	=> 'add_0',
			'func'	=> 'add_form',
			'color'	=> 'btn-default',
			'icon'	=> 'fa fa-plus',
			'label'	=> 'Tambah'
		);
		
		$btn .= edit_button($add);

		return $btn;
	}

	private function _conv_status($id=0){
		$args = array(1 => 'Libur Nasional',2 => 'Libur Kantor',3 => 'Cuti Bersama');

		return isset($args[$id])?$args[$id]:'';
	}

	private function curl(){
		$url = "https://raw.githubusercontent.com/guangrei/Json-Indonesia-holidays/master/calendar.json";
		$array = json_decode(file_get_contents($url),true);

		return $array;
	}

	public function holiday($tahun=''){
		$holidays = self::curl();
		if(empty($tahun)){
			return $holidays;
		}

		foreach ($holidays as $key => $val) {
			if(!preg_match("/^$tahun/", $key)){
				unset($holidays[$key]);
			}
		}

		return $holidays;
	}

	// ----------------------------------------------------------
	// Form data category -----------------------------------
	// ----------------------------------------------------------
	public function add_form($func=''){
		$vals = array(0,'',date('Y-m-d'),1);
		
		$args = array(
			'title'		=> 'Tambah data holiday',
			'button'	=> '_btn_modal_save',
			'status'	=> array(
				'link'		=> '_add_db',
				'load'		=> 'sobad_portlet'
			)
		);
		
		return self::_data_form($args,$vals);
	}

	protected function edit_form($vals=array()){
		$check = array_filter($vals);
		if(empty($check)){
			return '';
		}
		
		$vals = array(
			$vals['ID'],
			$vals['title'],
			$vals['holiday'],
			$vals['status']
		);
		
		$args = array(
			'title'		=> 'Edit data holiday',
			'button'	=> '_btn_modal_save',
			'status'	=> array(
				'link'		=> '_update_db',
				'load'		=> 'sobad_portlet'
			)
		);
		
		return self::_data_form($args,$vals);
	}

	private function _data_form($args=array(),$vals=array()){
		$check = array_filter($args);
		if(empty($check)){
			return '';
		}

		$status = array(
			1	=> 'Libur nasional',
			2	=> 'Libur Kantor',
			3	=> 'Cuti Bersama'
		);

		$data = array(
			0 => array(
				'func'			=> 'opt_hidden',
				'type'			=> 'hidden',
				'key'			=> 'ID',
				'value'			=> $vals[0]
			),
			1 => array(
				'func'			=> 'opt_input',
				'type'			=> 'text',
				'key'			=> 'title',
				'label'			=> 'Deskripsi',
				'class'			=> 'input-circle',
				'value'			=> $vals[1],
				'data'			=> 'placeholder="Deskripsi"'
			),
			2 => array(
				'func'			=> 'opt_datepicker',
				'key'			=> 'holiday',
				'label'			=> 'Tanggal',
				'class'			=> 'input-circle',
				'value'			=> $vals[2]
			),
			3 => array(
				'func'			=> 'opt_select',
				'data'			=> $status,
				'key'			=> 'status',
				'label'			=> 'Status',
				'searching'		=> true,
				'class'			=> 'input-circle',
				'select'		=> $vals[3]
			)
		);
		
		$args['func'] = array('sobad_form');
		$args['data'] = array($data);
		
		return modal_admin($args);
	}

	// ----------------------------------------------------------
	// Function category to database -----------------------------
	// ----------------------------------------------------------

	public function _synchronize(){
		$args = self::holiday(date('Y'));

		foreach ($args as $key => $val) {
			$holi = sobad_holiday::get_id($key,array('ID'));

			$date = strtotime($key);
			$date = date('Y-m-d',$date);

			$status = 1;
			if(preg_match("/Cuti/", $val['deskripsi'])){
				$status = 3;
			}

			$args = array(
				'title'		=> $val['deskripsi'],
				'holiday'	=> $date,
				'status'	=> $status
			);

			$check = array_filter($holi);
			if(empty($check)){
				$args['ID'] = $key;
				sobad_db::_insert_table('abs-holiday',$args);
			}else{
				sobad_db::_update_single($key,'abs-holiday',$args);
			}
		}
		
		return parent::_get_table(1);
	}
}