<?php

class dayOff_admin extends _page{

	protected static $object = 'dayOff_admin';

	protected static $table = 'sobad_module';

	// ----------------------------------------------------------
	// Layout category  ------------------------------------------
	// ----------------------------------------------------------

	protected function _array(){
		$args = array(
			'ID',
			'meta_value',
			'meta_note',
			'meta_key',
			'meta_reff'
		);

		return $args;
	}

	protected function table(){
		$data = array();
		$args = self::_array();

		$start = intval(parent::$page);
		$nLimit = intval(parent::$limit);
		
		$kata = '';$where = "AND meta_key='opt_dayoff'";
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
		foreach($args as $key => $val){
			$no += 1;
			$id = $val['ID'];

			$edit = array(
				'ID'	=> 'edit_'.$id,
				'func'	=> '_edit',
				'color'	=> 'blue',
				'icon'	=> 'fa fa-edit',
				'label'	=> 'edit'
			);
			
			$data['table'][$key]['tr'] = array('');
			$data['table'][$key]['td'] = array(
				'no'		=> array(
					'center',
					'5%',
					$no,
					true
				),
				'Tahun'		=> array(
					'left',
					'auto',
					'Cuti '.$val['meta_reff'],
					true
				),
				'Cuti Karyawan'	=> array(
					'left',
					'20%',
					$val['meta_value'].' hari',
					true
				),
				'Cuti Bersama'	=> array(
					'left',
					'20%',
					$val['meta_note'].' hari',
					true
				),
				'Edit'			=> array(
					'center',
					'10%',
					edit_button($edit),
					false
				),
			);
		}
		
		return $data;
	}

	private function head_title(){
		$args = array(
			'title'	=> 'Cuti <small>data cuti</small>',
			'link'	=> array(
				0	=> array(
					'func'	=> self::$object,
					'label'	=> 'cuti'
				)
			),
			'date'	=> false
		); 
		
		return $args;
	}

	protected function get_box(){
		$data = self::table();
		
		$box = array(
			'label'		=> 'Data Cuti',
			'tool'		=> '',
			'action'	=> parent::action(),
			'func'		=> 'sobad_table',
			'data'		=> $data
		);

		return $box;
	}

	protected function layout(){
		self::_loading();
		$box = self::get_box();
		
		$opt = array(
			'title'		=> self::head_title(),
			'style'		=> array(),
			'script'	=> array('')
		);
		
		return portlet_admin($opt,$box);
	}

	protected function _loading(){
		$awal = 2019;
		$now = date('Y');

		$date = array();
		for($i=$awal;$i<=$now;$i++){
			$date[] = $i;
		}

		$_date = implode(',', $date);
		$q = sobad_module::get_all(array('meta_reff'),"AND meta_key='opt_dayoff' AND meta_reff IN ($_date)");
		foreach ($q as $key => $val) {
			if(in_array($val['meta_reff'],$date)){
				$k = array_search($val['meta_reff'],$date);
				unset($date[$k]);
			}
		}

		foreach ($date as $key => $val) {
			sobad_db::_insert_table('abs-module',array(
				'meta_key' 		=> 'opt_dayoff', 
				'meta_value'	=> 6,
				'meta_note' 	=> 6,
				'meta_reff' 	=> $val
			));
		}
	}

	// ----------------------------------------------------------
	// Form data category ---------------------------------------
	// ----------------------------------------------------------

	protected function edit_form($vals=array()){
		$check = array_filter($vals);
		if(empty($check)){
			return '';
		}
		
		$args = array(
			'title'		=> 'Edit data cuti',
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

		$data = array(
			0 => array(
				'func'			=> 'opt_hidden',
				'type'			=> 'hidden',
				'key'			=> 'ID',
				'value'			=> $vals['ID']
			),
			array(
				'func'			=> 'opt_hidden',
				'type'			=> 'hidden',
				'key'			=> 'meta_key',
				'value'			=> $vals['meta_key']
			),
			array(
				'func'			=> 'opt_input',
				'type'			=> 'text',
				'key'			=> 'year',
				'label'			=> 'Tahun',
				'class'			=> 'input-circle',
				'value'			=> $vals['meta_reff'],
				'data'			=> 'readonly'
			),
			array(
				'func'			=> 'opt_input',
				'type'			=> 'price',
				'key'			=> 'meta_value',
				'label'			=> 'Cuti Karyawan',
				'class'			=> 'input-circle',
				'value'			=> $vals['meta_value'],
				'data'			=> ''
			),
			array(
				'func'			=> 'opt_input',
				'type'			=> 'price',
				'key'			=> 'mass',
				'label'			=> 'Cuti Bersama',
				'class'			=> 'input-circle',
				'value'			=> $vals['meta_note'],
				'data'			=> ''
			),
		);
		
		$args['func'] = array('sobad_form');
		$args['data'] = array($data);
		
		return modal_admin($args);
	}
}