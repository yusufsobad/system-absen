<?php

class permit_absen extends _page{

	protected static $object = 'permit_absen';

	protected static $table = 'sobad_permit';

	// ----------------------------------------------------------
	// Layout category  ------------------------------------------
	// ----------------------------------------------------------

	protected static function _array(){
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
		
		$kata = '';$where = "AND type NOT IN (9)";
		if(parent::$search){
			$_args = array(
				'ID',
				'user'
			);

			$src = parent::like_search($_args,$where);	
			$cari = $src[0];
			$where = $src[0];
			$kata = $src[1];
		}else{
			$cari=$where;
		}
	
		$limit = ' ORDER BY start_date DESC LIMIT '.intval(($start - 1) * $nLimit).','.$nLimit;
		$where .= $limit;

		$object = self::$table;
		$args = $object::get_all($args,$where);
		$sum_data = $object::count("1=1 ".$cari,self::_array());
		
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
				$range = $val['num_day']-1;
				
				switch ($val['type_date']) {

					case 2:
						$sts_day = 'bulan';
						$_num = $range.' months';
						$val['range_date'] = _calc_date($val['start_date'],'+'.$range.' months');
						break;

					case 3:
						$sts_day = 'tahun';
						$_num = $range.' years';
						$val['range_date'] = _calc_date($val['start_date'],'+'.$range.' years');
						break;

					default:
						$_range = $range;
						if($val['num_day']==0.5){
							$_range = 0;
						}

						$sts_day = 'hari kerja';
						$val['range_date'] = _calc_date($val['start_date'],'+'.$_range.' days');

						$_num = $range.' days';
						break;
				}

				//$range_date = strtotime($val['start_date']);
				//$val['range_date'] = date('Y-m-d',strtotime('+'.$_num,$range_date));
			}

			if($val['type_date']<2){
				$_num = ceil($range);
				$_date = strtotime($val['start_date']);
				for($i=0;$i<$_num;$i++){
					$_date = strtotime("+".$i." days",$_date);
					$_check = holiday_absen::_check_holiday(date('Y-m-d',$_date));
					if($_check){
						$range -= 1;
					}
				}
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
					'17%',
					conv_day_id($val['start_date']).', '.format_date_id($val['start_date']),
					true
				),
				'Sampai'	=> array(
					'center',
					'17%',
					conv_day_id($val['range_date']).', '.format_date_id($val['range_date']),
					true
				),
				'Jenis'		=> array(
					'center',
					'15%',
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

	public static function _conv_type($id=0){
		$args = array(3 => 'Cuti', 'Izin', 'Luar Kota', 'Libur');
		$type = isset($args[$id])?$args[$id]:'';

		if(!empty($type)){
			return $type;
		}

		$data = sobad_module::get_id(($id - 10),array('meta_value'));
		$check = array_filter($data);

		return !empty($check)?$data[0]['meta_value']:'';
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
				'type'			=> 'decimal',
				'key'			=> 'num_day',
				'label'			=> 'Jumlah Hari',
				'class'			=> 'input-circle',
				'value'			=> number_format($vals['num_day'],1,',','.'),
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

	public static function _calc_dateHoliday($start='',$numDay=1){
		if($numDay<0){
			return '0000-00-00';
		}

		$_date = empty($start)?date('Y-m-d'):$start;
		$date = $_date;
		$holidays = sobad_holiday::get_all(array('ID','holiday'),"AND holiday>='$_date'");

		$holiday = array();
		foreach ($holidays as $key => $val) {
			$holiday[] = $val['holiday'];
		}

		for($i=0;$i<=$numDay;$i++){
			$date = punishment_absen::_check_holiday($_date,$holiday);
			$_date = strtotime($date);
			$_date = date('Y-m-d',strtotime('+1 days',$_date));
		}

		return $date;
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

		intval($data);
		return array('value' => $data, 'type' => 1);
	}

	public function _delete($id=0){
		$id = str_replace('del_','',$id);
		intval($id);

		$permit = sobad_permit::get_id($id,array('user','num_day','type'));
		$permit = $permit[0];

		if($permit['type']==3){
			$dayOff = sobad_user::get_id($permit['user'],array('dayOff'));
			$dayOff = $dayOff[0]['dayOff'];

			$dayOff += $permit['num_day'];
			sobad_db::_update_single($permit['user'],'abs-user',array('ID' => $permit['user'],'dayOff' => $dayOff));
		}

		return parent::_delete($id);
	}

	protected static function _callback($args=array()){
		if($args['type']>6){
			$idx = $args['type'] - 10;
			$conv = self::_conv_day_off($idx);

			$args['type'] = $idx + 10;
			$args['num_day'] = $conv['value'];
			$args['type_date'] = $conv['type'];
		}else if($args['type']==3){
			$args['type_date'] = 1;
		}

		if(!isset($args['range_date'])){
			$args['range_date'] = '0000-00-00';

			if(isset($args['type_date'])){
				if($args['num_day']>0){
					$_num = $args['num_day'] - 1;
					switch ($args['type_date']) {
						case 2:
							$args['range_date'] = _calc_date($args['start_date'],'+'.$_num.' months');
							break;

						case 3:
							$args['range_date'] = _calc_date($args['start_date'],'+'.$_num.' years');
						
						default:
							if($args['num_day']==0.5){
								$_num = 0;
							}
							$args['range_date'] = self::_calc_dateHoliday($args['start_date'],$_num);
							break;
					}
				}
			}
		}

		//Check type
		$permit = sobad_permit::get_id($args['ID'],array('user','type','num_day'));
		$permit = $permit[0];

		if($permit['type']==3){
			//Reset data
			$_user = sobad_user::get_id($args['user'],array('ID','dayOff'));
			$dayOff = $_user[0]['dayOff'];

			$cuti = $permit['num_day'];
			$dayOff += $cuti;

			sobad_db::_update_single($_user[0]['ID'],'abs-user',array('ID' => $_user[0]['ID'],'dayOff' => $dayOff));
		}

		if($args['type']==3){
			self::_check_dayoff($args['user'],$args['num_day'],$args['start_date']);
		}

		return $args;
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
			'note'			=> $args['note'],
		);

		if($args['type']>6){
			$idx = $args['type'] - 10;
			$conv = self::_conv_day_off($idx);

			$data['type'] = $idx + 10;
			$data['num_day'] = $conv['value'];
			$data['type_date'] = $conv['type'];
		}else if($args['type']==3){
			$data['type_date'] = 1;
		}

		if(isset($args['num_day'])){
			$data['num_day'] = $args['num_day'];
		}

		if(isset($args['range_date'])){
			$data['range_date'] = $args['range_date'];
		}else{
			$data['range_date'] = '0000-00-00';

			if(isset($data['type_date'])){
				if($data['num_day']>0){
					$_num = $data['num_day'] - 1;
					switch ($data['type_date']) {
						case 2:
							$data['range_date'] = _calc_date($data['start_date'],'+'.$_num.' months');
							break;

						case 3:
							$data['range_date'] = _calc_date($data['start_date'],'+'.$_num.' years');
						
						default:
							if($data['num_day']==0.5){
								$_num = 0;
							}
							$data['range_date'] = self::_calc_dateHoliday($data['start_date'],$_num);
							break;
					}
				}
			}
		}

		$users = explode(',',$args['user']);
		foreach ($users as $key => $val) {
			if($args['type']==3){
				self::_check_dayoff($val,$args['num_day'],$data['start_date']);
			}

			$data['user'] = $val;
			$q = sobad_db::_insert_table('abs-permit',$data);
		}

		if($q!==0){
			$pg = isset($_POST['page'])?$_POST['page']:1;
			return parent::_get_table($pg,$src);
		}
	}

	protected function _check_dayoff($idx=0,$cuti=0,$start=''){
		$_user = sobad_user::get_id($idx,array('ID','dayOff','work_time'));
		$dayOff = $_user[0]['dayOff'];

		//$cuti = isset($args['num_day'])?$args['num_day']:0;
		$check = $dayOff - $cuti;

		if($check<0){
			//die(_error::_alert_db('Sisa Cuti Tidak mencukupi!!!'));

			//check permintaan dan sisa cuti
			if($dayOff>0){
				//Update cuti
				sobad_db::_update_single($_user[0]['ID'],'abs-user',array('ID' => $_user[0]['ID'],'dayOff' => 0));
				$cuti -= $dayOff;
			}

			//Ganti Jam
			if($cuti>0){
				$_date = $start;
				$_reff = $_user[0]['work_time'];

				$holidays = sobad_holiday::get_all(array('ID','holiday'),"AND holiday>='$_date'");

				$holiday = array();
				foreach ($holidays as $key => $val) {
					$holiday[] = $val['holiday'];
				}

				for($i=0;$i<$cuti;$i++){
					$date = punishment_absen::_check_holiday($_date,$holiday);
					$_date = strtotime($date);

					$_day = date('w',strtotime('+1 days',$_date));
					$_date = date('Y-m-d',strtotime('+1 days',$_date));

					$_work = sobad_work::get_all(array('time_in','time_out'),"AND reff='$_reff' AND days='$_day'");
					$_work = _conv_time($_work[0]['time_in'],$_work[0]['time_out'],2);

					// Pengurangan Jam Istirahat
					if(in_array($_day,array(1,2,3,4,5))){
						$_work -= 60;
					}

					//Insert Log Absen
					$_idx = sobad_db::_insert_table('abs-user-log',array(
								'user' 		=> $_user[0]['ID'],
								'shift' 	=> $_reff,
								'type'		=> 4,
								'_inserted'	=> $_date,
							)
						);

					// Insert ganti jam
					sobad_db::_insert_table('abs-log-detail',array(
						'log_id'		=> $_idx,
						'date_schedule'	=> $_date,
						'times'			=> $_work,
						'type_log'		=> 2
					));
				}
			}
		}else{
			sobad_db::_update_single($_user[0]['ID'],'abs-user',array('ID' => $_user[0]['ID'],'dayOff' => $check));
		}
	}
}