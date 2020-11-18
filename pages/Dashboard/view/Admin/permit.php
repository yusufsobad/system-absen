<?php

class permit_absen extends _page{

	protected static $object = 'permit_absen';

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
			'num_day',
			'type',
			'note',
			'type_date'
		);

		return $args;
	}

	protected function table(){
		$data = array();
		$args = self::_array();

		$start = intval(parent::$page);
		$nLimit = intval(parent::$limit);
		
		$kata = '';$where = "AND type NOT IN (9) ORDER BY start_date DESC ";
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
			
			$hapus = array(
				'ID'	=> 'del_'.$id,
				'func'	=> '_delete',
				'color'	=> 'red',
				'icon'	=> 'fa fa-trash',
				'label'	=> 'hapus',
			);

			$sts_day = 'hari';
			if($val['range_date']=='0000-00-00'){
				$val['range_date'] = date('Y-m-d');
			}

			$range = strtotime($val['range_date']) - strtotime($val['start_date']);
			$range = floor($range / (60 * 60 * 24));

			if($val['num_day']>0){
				$range = $val['num_day'];
				
				switch ($val['type_date']) {
					case 1:
						$sts_day = 'hari';
						$_num = $range.' days';
						break;

					case 2:
						$sts_day = 'bulan';
						$_num = $range.' months';
						break;

					case 3:
						$sts_day = 'tahun';
						$_num = $range.' years';
						break;
				}

				$range_date = strtotime($val['start_date']);
				$val['range_date'] = date('Y-m-d',strtotime('+'.$_num,$range_date));
			}
			
			$data['table'][$key]['tr'] = array('');
			$data['table'][$key]['td'] = array(
				'No'		=> array(
					'center',
					'5%',
					$no,
					true
				),
				'Name'		=> array(
					'left',
					'auto',
					$val['name_user'],
					true
				),
				'Mulai'		=> array(
					'center',
					'18%',
					conv_day_id($val['start_date']).', '.format_date_id($val['start_date']),
					true
				),
				'Sampai'	=> array(
					'center',
					'18%',
					conv_day_id($val['range_date']).', '.format_date_id($val['range_date']),
					true
				),
				'Jenis'		=> array(
					'center',
					'10%',
					self::_conv_type($val['type']),
					true
				),
				'Lama'		=> array(
					'center',
					'10%',
					($range + 1).' '.$sts_day,
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
			'title'	=> 'Izin <small>data izin</small>',
			'link'	=> array(
				0	=> array(
					'func'	=> self::$object,
					'label'	=> 'izin'
				)
			),
			'date'	=> false
		); 
		
		return $args;
	}

	protected function get_box(){
		$data = self::table();
		
		$box = array(
			'label'		=> 'Data Izin',
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

	protected function _conv_type($id=0){
		$args = array(3 => 'Cuti', 'Izin', 'Luar Kota', 'Libur');
		return isset($args[$id])?$args[$id]:'';
	}

	public function _script(){
		?>
			<script type="text/javascript">
				function sobad_option_permit(data,id){
					for(var _ky in data){
						$('#'+data[_ky]['id']).prop('disabled',data[_ky]['value']);
					}
				}
			</script>
		<?php
	}

	// ----------------------------------------------------------
	// Form data category -----------------------------------
	// ----------------------------------------------------------
	public function add_form(){
		$vals = array(0,array(),date('d-m-Y'),date('d-m-Y'),1,3,'',0);
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

		$permit = array(3 => 'Cuti', 5 => 'Luar Kota', 'Libur');
		$dayOff = sobad_module::_gets('day_off',array('ID','meta_value'));
		foreach ($dayOff as $key => $val) {
			$idx = ($val['ID'] + 10);
			$permit[$idx] = $val['meta_value'];
		}

		switch ($vals['type']) {
			case 3:
			case 6:
				$status = array(
					'start_date'	=> '',
					'range_date'	=> 'disabled',
					'num_day'		=> ''
				);
				break;

			case 5:
				$status = array(
					'start_date'	=> '',
					'range_date'	=> '',
					'num_day'		=> 'disabled'
				);
				break;
			
			default:
				$status = array(
					'start_date'	=> '',
					'range_date'	=> 'disabled',
					'num_day'		=> 'disabled'
				);
				break;
		}

		$data = array(
			0 => array(
				'func'			=> 'opt_hidden',
				'type'			=> 'hidden',
				'key'			=> 'ID',
				'value'			=> $vals['ID']
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
				'data'			=> $permit,
				'key'			=> 'type',
				'label'			=> 'Jenis',
				'class'			=> 'input-circle',
				'select'		=> $vals['type'],
				'status'		=> 'data-sobad="option_permit" data-load="permit" data-attribute="sobad_option_permit" '
			),
			array(
				'id'			=> 'permit_date',
				'func'			=> 'opt_datepicker',
				'key'			=> 'start_date',
				'label'			=> 'Tanggal',
				'class'			=> 'input-circle',
				'value'			=> $vals['start_date'],
				'status'		=> $status['start_date'],
				'to'			=> 'range_date',
				'data'			=> $vals['range_date'],
				'status2'		=> $status['range_date']
			),
			array(
				'id'			=> 'permit_day',
				'func'			=> 'opt_input',
				'type'			=> 'number',
				'key'			=> 'num_day',
				'label'			=> 'Jumlah Hari',
				'class'			=> 'input-circle',
				'value'			=> $vals['num_day'],
				'data'			=> $status['num_day']
			),
			array(
				'func'			=> 'opt_textarea',
				'key'			=> 'note',
				'label'			=> 'Catatan',
				'class'			=> 'input-circle',
				'value'			=> $vals['note'],
				'data'			=> 'placeholder="Catatan"',
				'rows'			=> 4
			),
		);

		if($type){
			$data[1]['func'] = 'opt_select';
			$data[1]['data'] = $groups;
			$data[1]['group'] = true;
			$data[1]['searching'] = true;
			$data[1]['status'] = '';
		}
		
		$args['func'] = array('sobad_form');
		$args['data'] = array($data);
		
		return modal_admin($args);
	}

	public function option_permit($id=0){
		switch ($id) {
			case 3: // Cuti
			case 6: // Libur
				return array(
					0 => array(
						'id'		=> 'permit_day',
						'value'		=> false
					),
					array(
						'id'		=> 'permit_date input[name=start_date]',
						'value'		=> false
					),
					array(
						'id'		=> 'permit_date input[name=range_date]',
						'value'		=> true
					)
				);
				break;

			case 5: // Luar Kota
				return array(
					0 => array(
						'id'		=> 'permit_date input',
						'value'		=> false
					),
					array(
						'id'		=> 'permit_day',
						'value'		=> true
					)
				);
				break;

			default:
				return array(
					0 => array(
						'id'		=> 'permit_day',
						'value'		=> true
					),
					array(
						'id'		=> 'permit_date input[name=start_date]',
						'value'		=> false
					),
					array(
						'id'		=> 'permit_date input[name=range_date]',
						'value'		=> true
					)
				);
				break;
		}
	}

	public function _conv_day_off($idx=0){
		$data = sobad_module::get_id($idx,array('meta_note'));
		$data = $data[0]['meta_note'];

		$data = preg_replace('/\s+/', '', $data);
		$data = strtolower($data);
		if($data=='-'){
			return array('value' => 0, 'type' => 0);
		}

		if(preg_match("/^[0-9]{2,4}(year|tahun|thn|t|y)/", $data)){
			$nilai = preg_replace("/year|tahun|thn|t|y/", '', $data);
			intval($nilai);

			return array('value' => $nilai, 'type' => 3);
		}

		if(preg_match("/^[0-9]{1,3}(month|bulan|bln|b|m)/", $data)){
			$nilai = preg_replace("/month|bulan|bln|b|m/", '', $data);
			intval($nilai);

			return array('value' => $nilai, 'type' => 2);
		}

		if(preg_match("/^[0-9]{1,3}(day|hari|hr|h|d)/", $data)){
			$nilai = preg_replace("/day|hari|hr|h|d/", '', $data);
			intval($nilai);

			return array('value' => $nilai, 'type' => 1);
		}

		intval($nilai);
		return array('value' => $nilai, 'type' => 1);
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
			'type'			=> $args['type'],
			'note'			=> $args['note']
		);

		if($args['type']>6){
			$conv = self::_conv_day_off($idx);
			$idx = $args['type'] - 10;

			$data['type'] = $idx;
			$data['num_day'] = $conv['value'];
			$data['type_date'] = $conv['type'];
		}

		if(isset($args['range_date'])){
			$data['range_date'] = $args['range_date'];
		}else{
			$data['range_date'] = '0000-00-00';
		}

		if(isset($args['num_day'])){
			$data['num_day'] = $args['num_day'];
		}

		$users = explode(',',$args['user']);
		foreach ($users as $key => $val) {
			if($args['type']==3){
				$_user = sobad_user::get_id($val,array('ID','dayOff'));
				$dayOff = $_user[0]['dayOff'];

				$cuti = isset($args['num_day'])?$args['num_day']:0;
				$dayOff -= $cuti;

				if($dayOff<0){
					die(_error::_alert_db('Sisa Cuti Tidak mencukupi!!!'));
				}

				sobad_db::_update_single($_user[0]['ID'],'abs-user',array('ID' => $_user[0]['ID'],'dayOff' => $dayOff));
			}

			$data['user'] = $val;
			$q = sobad_db::_insert_table('abs-permit',$data);
		}

		if($q!==0){
			$pg = isset($_POST['page'])?$_POST['page']:1;
			return parent::_get_table($pg,$src);
		}
	}
}