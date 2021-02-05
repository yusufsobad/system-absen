<?php

class worktime_absen extends _page{
	
	protected static $object = 'worktime_absen';

	protected static $table = 'sobad_work';

	// ----------------------------------------------------------
	// Layout category  ------------------------------------------
	// ----------------------------------------------------------

	protected function _array(){
		$args = array(
			'ID',
			'name'
		);

		return $args;
	}

	protected function table(){
		$data = array();
		$args = self::_array();

		$start = intval(parent::$page);
		$nLimit = intval(parent::$limit);
		
		$kata = '';$where = "";
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

		$days = self::_get_days();

		$no = ($start-1) * $nLimit;
		foreach($args as $key => $val){
			$no += 1;
			$id = $val['ID'];

			$view = array(
				'ID'	=> 'view_'.$id,
				'func'	=> '_view',
				'color'	=> 'yellow',
				'icon'	=> 'fa fa-eye',
				'label'	=> 'view'
			);

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

			$table = array();
			$table['class'] = '';
			$table['table'] = array();

			$work = sobad_work::get_workTime($id);
			foreach ($work as $ky => $vl) {
				$table['table'][$ky]['tr'] = array('');
				$table['table'][$ky]['td'] = array(
					'Hari'		=> array(
						'left',
						'auto',
						$days[$vl['days']],
						false
					),
					'Masuk'		=> array(
						'center',
						'25%',
						$vl['time_in'],
						false
					),
					'Pulang'	=> array(
						'center',
						'25%',
						$vl['time_out'],
						false
					),
					'Status'	=> array(
						'center',
						'20%',
						$vl['status']==1?'aktif':'non aktif',
						false
					),
				);
			}

			ob_start();
			metronic_layout::sobad_table($table);
			$_table = ob_get_clean();

			$data['table'][$key]['tr'] = array('');
			$data['table'][$key]['td'] = array(
				'no'		=> array(
					'center',
					'5%',
					$no,
					true
				),
				'name'		=> array(
					'center',
					'15%',
					$val['name'],
					true
				),
				'Keterangan'	=> array(
					'left',
					'auto',
					$_table,
					true
				),
				'View'			=> array(
					'center',
					'10%',
					_modal_button($view),
					false
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
			'title'	=> 'jam Kerja <small>data Jam Kerja</small>',
			'link'	=> array(
				0	=> array(
					'func'	=> self::$object,
					'label'	=> 'jam kerja'
				)
			),
			'date'	=> false
		); 
		
		return $args;
	}

	protected function get_box(){
		$data = self::table();
		
		$box = array(
			'label'		=> 'Data Jam Kerja',
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
			'script'	=> array('')
		);
		
		return portlet_admin($opt,$box);
	}

	public static function _get_days(){
		$days = array(
			'Minggu',
			'Senin',
			'Selasa',
			'Rabu',
			'Kamis',
			'Jum\'at',
			'Sabtu'
		);

		return $days;
	}

	// ----------------------------------------------------------
	// Form data category -----------------------------------
	// ----------------------------------------------------------
	public function add_form($func=''){
		$vals = array(0,'');
		
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
		
		$vals = array(
			$vals['ID'],
			$vals['name']
		);
		
		$args = array(
			'title'		=> 'Edit data',
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
				'value'			=> $vals[0]
			),
			1 => array(
				'func'			=> 'opt_input',
				'type'			=> 'text',
				'key'			=> 'name',
				'label'			=> 'Nama',
				'class'			=> 'input-circle',
				'value'			=> $vals[1],
				'data'			=> 'placeholder="Nama"'
			)
		);
		
		$args['func'] = array('sobad_form','form_worktime');
		$args['data'] = array($data,$vals[0]);
		
		return modal_admin($args);
	}

	public static function form_worktime($data=0){
		$args = array();
		if($data!=0){
			$args = sobad_work::get_id($data,array('days','time_in','time_out','note','status'));
		}

		$days = self::_get_days();

		$data = array();
		$data['class'] = '';
		$data['table'] = array();

		for($i=0;$i<7;$i++){
			$sts = isset($args[$i])?$args[$i]['status']:0;
			$time_in = isset($args[$i])?$args[$i]['time_in']:0;
			$time_out = isset($args[$i])?$args[$i]['time_out']:0;
			$note = isset($args[$i])?$args[$i]['note']:'';

			$status = $sts==1?'checked':'';

			$data['table'][$i]['tr'] = array('');
			$data['table'][$i]['td'] = array(
				'Hari'		=> array(
					'center',
					'10%',
					$days[$i],
					false
				),
				'check'		=> array(
					'center',
					'5%',
					'<input type="checkbox" name="days_'.$i.'" value="1" '.$status.'>',
					false
				),
				'Masuk'		=> array(
					'center',
					'15%',
					'<div class="clockpicker"><input type="text" class="form-control" name="time_in" value="'.$time_in.'"></div>',
					false
				),
				'Pulang'	=> array(
					'center',
					'15%',
					'<div class="clockpicker"><input type="text" class="form-control" name="time_out" value="'.$time_out.'"></div>',
					false
				),
				'Istirahat'		=> array(
					'center',
					'auto',
					'<input type="text" name="note" value="'.$note.'">',
					false
				),
			);
		}

		?>
			<form>
				<div class="col-md-2">
					&nbsp;
				</div>
				<div class="col-md-9">
					<?php metronic_layout::sobad_table($data); ?>	
				</div>
				<script type="text/javascript">
					sobad_clockpicker();
				</script>
			</form>
		<?php
	}

	// ----------------------------------------------------------
	// Function category to database ----------------------------
	// ----------------------------------------------------------

	public function _view($id=0){
		$id = str_replace('view_', '', $id);
		intval($id);

		$args = sobad_user::get_all(array('picture','no_induk','name','divisi','status','inserted'),"AND work_time='$id' AND status!='0'");

		$data['class'] = '';
		$data['table'] = array();

		$no = 0;
		foreach ($args as $key => $val) {
			$no += 1;

			$image = empty($val['notes_pict'])?'no-profile.jpg':$val['notes_pict'];
			$status = employee_absen::_conv_status($val['status']);

			$data['table'][$key]['tr'] = array('');
			$data['table'][$key]['td'] = array(
				'No'		=> array(
					'center',
					'5%',
					$no,
					true
				),
				'Profile'	=> array(
					'left',
					'5%',
					'<img src="asset/img/user/'.$image.'" style="width:100%">',
					true
				),
				'NIK'		=> array(
					'left',
					'5%',
					$val['status']==7?internship_absen::_conv_no_induk($val['no_induk'],$val['inserted']):$val['no_induk'],
					true
				),
				'Nama'		=> array(
					'left',
					'auto',
					$val['name'],
					true
				),
				'Divisi'	=> array(
					'left',
					'20%',
					$val['meta_value_divi'],
					true
				),
				'Status'	=> array(
					'left',
					'13%',
					$status,
					true
				),
			);
		}

		$args = array(
			'title'		=> 'Detail data',
			'button'	=> '_btn_modal_save',
			'status'	=> array(),
			'func'		=> array('sobad_table'),
			'data'		=> array($data)
		);
		
		return modal_admin($args);
	}

	public function _update_db($args=array()){
		$lang = get_locale();
		$args = sobad_asset::ajax_conv_array_json($args);
		$id = $args['ID'][0];
		unset($args['ID'][0]);
		
		if(isset($args['search'][0])){
			$src = array(
				'search'	=> $args['search'][0],
				'words'		=> $args['words'][0]
			);

			unset($args['search']);
			unset($args['words']);
		}
		
		$data = array(
			'name'		=> $args['name'][0]
		);

		$q = sobad_db::_update_single($id,'abs-work',$data);

		// Update Work Normal
		for($i=0;$i<7;$i++){
			$data2 = array(
				'time_in'	=> $args['time_in'][$i],
				'time_out'	=> $args['time_out'][$i],
				'status'	=> isset($args['days_'.$i])?$args['days_'.$i][0]:0,
				'note'		=> $args['note'][$i]
			);

			$q = sobad_db::_update_multiple("reff='$id' AND days='$i'",'abs-work-normal',$data2);
		}
		
		if($q===1){
			$pg = isset($_POST['page'])?$_POST['page']:1;
			return parent::_get_table($pg,$src);
		}
	}

	public function _add_db($args=array(),$menu='contact',$obj=''){
		$lang = get_locale();
		$args = sobad_asset::ajax_conv_array_json($args);
	
		$id = $args['ID'][0];
		unset($args['ID'][0]);
		
		if(isset($args['search'][0])){
			$src = array(
				'search'	=> $args['search'][0],
				'words'		=> $args['words'][0]
			);

			unset($args['search']);
			unset($args['words']);
		}

		$data = array(
			'name'		=> $args['name'][0]
		);
		
		$idx = sobad_db::_insert_table('abs-work',$data);

		// Insert Work Normal
		for($i=0;$i<7;$i++){
			$data2 = array(
				'reff'		=> $idx,
				'days'		=> $i,
				'time_in'	=> $args['time_in'][$i],
				'time_out'	=> $args['time_out'][$i],
				'status'	=> isset($args['days_'.$i])?$args['days_'.$i][0]:0,
				'note'		=> $args['note'][$i]
			);

			$q = sobad_db::_insert_table('abs-work-normal',$data2);
		}
		
		if($q!==0){
			$pg = isset($_POST['page'])?$_POST['page']:1;
			return parent::_get_table($pg,$src);
		}
	}
}