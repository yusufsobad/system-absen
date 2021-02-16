<?php

class shift_absen extends _page{

	protected static $object = 'shift_absen';

	protected static $table = 'sobad_permit';

	// ----------------------------------------------------------
	// Layout category  ------------------------------------------
	// ----------------------------------------------------------

	protected function _array(){
		$args = array(
			'ID',
			'user',
			'start_date',
			'range_date',
			'note'
		);

		return $args;
	}

	protected function table(){
		$data = array();
		$args = self::_array();

		$start = intval(parent::$page);
		$nLimit = intval(parent::$limit);
		
		$kata = '';$where = "AND type IN (9) ";$_args = array();
		if(parent::$search){
			$_args = array('ID','user');
			$src = parent::like_search($_args,$where);	
			$cari = $src[0];
			$where = $src[0];
			$kata = $src[1];
		}else{
			$cari=$where;
		}
	
		$limit = 'ORDER BY start_date DESC LIMIT '.intval(($start - 1) * $nLimit).','.$nLimit;
		$where .= $limit;

		$object = self::$table;
		$args = $object::get_all($args,$where);
		$sum_data = $object::count("1=1 ".$cari,$_args);
		
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
			
			$hapus = array(
				'ID'	=> 'del_'.$id,
				'func'	=> '_delete',
				'color'	=> 'red',
				'icon'	=> 'fa fa-trash',
				'label'	=> 'hapus',
			);


			$work = sobad_work::get_id($val['note'],array('ID','name'));
			$worktime = array(
				'ID'	=> 'work_'.$work[0]['ID'],
				'func'	=> '_view',
				'color'	=> '',
				'icon'	=> '',
				'label'	=> $work[0]['name']
			);

			$range = strtotime($val['range_date']) - strtotime($val['start_date']);
			$range = floor($range / (60 * 60 * 24));
			
			$data['table'][$key]['tr'] = array('');
			$data['table'][$key]['td'] = array(
				'no'		=> array(
					'center',
					'5%',
					$no,
					true
				),
				'name'		=> array(
					'left',
					'auto',
					$val['name_user'],
					true
				),
				'mulai'		=> array(
					'center',
					'18%',
					conv_day_id($val['start_date']).', '.format_date_id($val['start_date']),
					true
				),
				'sampai'	=> array(
					'center',
					'18%',
					conv_day_id($val['range_date']).', '.format_date_id($val['range_date']),
					true
				),
				'Lama'		=> array(
					'center',
					'10%',
					($range + 1).' hari',
					true
				),
				'Worktime'	=> array(
					'center',
					'10%',
					_modal_button($worktime),
					true
				),
				'Edit'		=> array(
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
			'title'	=> 'Auto Shift <small>auto shift</small>',
			'link'	=> array(
				0	=> array(
					'func'	=> self::$object,
					'label'	=> 'auto shift'
				)
			),
			'date'	=> false
		); 
		
		return $args;
	}

	protected function get_box(){
		$data = self::table();
		
		$box = array(
			'label'		=> 'Auto Shift',
			'tool'		=> '',
			'action'	=> parent::action(),
			'func'		=> 'sobad_table',
			'data'		=> $data
		);

		return $box;
	}

	protected function layout(){
		$box = self::get_box();
		
		$opt = array(
			'title'		=> self::head_title(),
			'style'		=> array(),
			'script'	=> array(self::$object,'_script')
		);
		
		return portlet_admin($opt,$box);
	}

	// ----------------------------------------------------------
	// Form data category -----------------------------------
	// ----------------------------------------------------------
	public function add_form(){
		$vals = array(0,array(),date('d-m-Y'),date('d-m-Y'),0);
		$vals = array_combine(self::_array(),$vals);
		
		$args = array(
			'title'		=> 'Tambah data',
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
		
		$args = array(
			'title'		=> 'Edit data',
			'button'	=> '_btn_modal_save',
			'status'	=> array(
				'link'		=> '_update_db',
				'load'		=> 'sobad_portlet'
			)
		);
		
		return self::_data_form($args,$vals,true);
	}

	private function _data_form($args=array(),$vals=array(),$type=false){
		$check = array_filter($args);
		if(empty($check)){
			return '';
		}

		$user = sobad_user::get_employees(array('ID','name'));
		$user = convToOption($user,'ID','name');

		$intern = sobad_user::get_internships(array('ID','name'));
		$intern = convToOption($intern,'ID','name');

		$group = $user;
		foreach ($intern as $key => $val) {
			$group[$key] = $val;
		}

		$groups = array(
			'Karyawan'		=> $user,
			'Internship'	=> $intern
		);

		$shift = sobad_work::get_works();
		$shift = convToOption($shift,'ID','name');

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
				'key'			=> 'type',
				'value'			=> 9
			),
			array(
				'func'			=> 'opt_select_tags',
				'data'			=> $group,
				'key'			=> 'user',
				'label'			=> 'Nama',
				'class'			=> 'input-circle',
				'select'		=> $vals['user']
			),
			array(
				'func'			=> 'opt_select',
				'data'			=> $shift,
				'key'			=> 'note',
				'label'			=> 'Jam Kerja',
				'class'			=> 'input-circle',
				'select'		=> $vals['note'],
				'status'		=> ''
			),
			array(
				'id'			=> 'permit_date',
				'func'			=> 'opt_datepicker',
				'key'			=> 'start_date',
				'label'			=> 'Tanggal',
				'class'			=> 'input-circle',
				'value'			=> $vals['start_date'],
				'to'			=> 'range_date',
				'data'			=> $vals['range_date']
			)
		);

		if($type){
			$data[2]['func'] = 'opt_select';
			$data[2]['data'] = $groups;
			$data[2]['group'] = true;
			$data[2]['searching'] = true;
			$data[2]['status'] = '';
		}
		
		$args['func'] = array('sobad_form');
		$args['data'] = array($data);
		
		return modal_admin($args);
	}

	public function _view($id=0){
		$id = str_replace('work_', '', $id);
		intval($id);

		$args = sobad_work::get_workTime($id);

		$data['class'] = '';
		$data['table'] = array();

		$no = 0;
		$days = worktime_absen::_get_days();
		foreach ($args as $key => $val) {
			$no += 1;

			$data['table'][$key]['tr'] = array('');
			$data['table'][$key]['td'] = array(
				'No'		=> array(
					'center',
					'5%',
					$no,
					false
				),
				'Hari'		=> array(
					'left',
					'auto',
					$days[$val['days']],
					false
				),
				'Masuk'		=> array(
					'center',
					'25%',
					$val['time_in'],
					false
				),
				'Pulang'	=> array(
					'center',
					'25%',
					$val['time_out'],
					false
				),
				'Status'	=> array(
					'center',
					'20%',
					$val['status']==1?'aktif':'non aktif',
					false
				),
			);
		}

		$args = array(
			'title'		=> 'Jam Kerja',
			'button'	=> '_btn_modal_save',
			'status'	=> array(),
			'func'		=> array('sobad_table'),
			'data'		=> array($data)
		);
		
		return modal_admin($args);
	}

	public function _add_db($_args=array(),$menu='default',$obj=''){
		$args = sobad_asset::ajax_conv_json($_args);
		$id = $args['ID'];
		unset($args['ID']);
	
		$src = array();
		if(isset($args['search'])){
			$src = array(
				'search'	=> $args['search'],
				'words'		=> $args['words']
			);

			unset($args['search']);
			unset($args['words']);
		}

		$data = array(
			'start_date'	=> $args['start_date'],
			'range_date'	=> $args['range_date'],
			'type'			=> $args['type'],
			'note'			=> $args['note'],
		);

		$users = explode(',',$args['user']);
		foreach ($users as $key => $val) {
			$data['user'] = $val;
			$q = sobad_db::_insert_table('abs-permit',$data);
		}

		if($q!==0){
			$pg = isset($_POST['page'])?$_POST['page']:1;
			return parent::_get_table($pg,$src);
		}
	}
}