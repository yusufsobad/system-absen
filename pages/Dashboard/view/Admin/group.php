<?php

class group_absen extends _page{
	
	protected static $object = 'group_absen';

	protected static $table = 'sobad_module';

	// ----------------------------------------------------------
	// Layout category  ------------------------------------------
	// ----------------------------------------------------------

	protected function _array(){
		$args = array(
			'ID',
			'meta_value',
			'meta_note',
			'meta_reff',
			'meta_key'
		);

		return $args;
	}

	protected function table(){
		$data = array();
		$args = self::_array();

		$start = intval(parent::$page);
		$nLimit = intval(parent::$limit);
		
		$kata = '';$where = "AND meta_key='group'";
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

			$view = array(
				'ID'	=> 'view_'.$id,
				'func'	=> '_view',
				'color'	=> 'yellow',
				'icon'	=> 'fa fa-eye',
				'label'	=> 'View'
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

			$divisi = sobad_module::_conv_divisi($val['meta_note']);
			$divisi = implode(', ',$divisi['meta_value']);

			$_data = unserialize($val['meta_note']);
			$status = 'Status : <br>';

			if(isset($_data['status'])){
				if(in_array(1, $_data['status'])){
					$status .= '- <strong>Aktif </strong><br>';
				}else{
					$status .= '- <strong>Non Aktif </strong><br>';
				}

				if(in_array(2, $_data['status'])){
					$status .= '- <strong>Exclude </strong><br>';
				}else{
					$status .= '- <strong>Include </strong><br>';
				}

				if(in_array(3, $_data['status'])){
					$status .= '- <strong>Punishmnent </strong><br>';
				}else{
					$status .= '- <strong>Non Punishmnent </strong><br>';
				}
			}
			
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
					$val['meta_value'],
					true
				),
				'jabatan'	=> array(
					'left',
					'30%',
					$divisi,
					true
				),
				'status'	=> array(
					'left',
					'12%',
					$status,
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
			'title'	=> 'Group <small>data group</small>',
			'link'	=> array(
				0	=> array(
					'func'	=> self::$object,
					'label'	=> 'group'
				)
			),
			'date'	=> false
		); 
		
		return $args;
	}

	protected function get_box(){
		$data = self::table();
		
		$box = array(
			'label'		=> 'Data Group',
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

	// ----------------------------------------------------------
	// Form data category -----------------------------------
	// ----------------------------------------------------------
	public function add_form($func=''){
		$vals = array(0,'',array(),array(1,3),'group');
		$vals = array_combine(self::_array(), $vals);
		
		$args = array(
			'title'		=> 'Tambah data group',
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

		$data = array();
		$_dt = unserialize($vals['meta_note']);
		if(isset($_dt['data'])){
			$data = $_dt['data'];
		}

		if(isset($_dt['status'])){
			$vals['meta_reff'] = $_dt['status'];
		}
		
		$vals['meta_note'] = $data;
		
		$args = array(
			'title'		=> 'Edit data group',
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

		$divisi = sobad_module::_gets('department',array('ID','meta_value'));
		$divisi = convToOption($divisi,'ID','meta_value');

		$data = array(
			0 => array(
				'func'			=> 'opt_hidden',
				'type'			=> 'hidden',
				'key'			=> 'ID',
				'value'			=> $vals['ID']
			),
			array(
				'func'			=> 'opt_box',
				'type'			=> 'checkbox',
				'key'			=> 'meta_reff',
				'label'			=> 'Status',
				'inline'		=> true,
				'value'			=> $vals['meta_reff'],
				'data'			=> array(
					0	=> array(
						'title'		=> 'Aktif',
						'value'		=> '1'
					),
					1	=> array(
						'title'		=> 'Exclude',
						'value'		=> '2'
					),
					2	=> array(
						'title'		=> 'Punishmnet',
						'value'		=> '3'
					)
				)
			),
			array(
				'func'			=> 'opt_input',
				'type'			=> 'text',
				'key'			=> 'meta_value',
				'label'			=> 'Nama group',
				'class'			=> 'input-circle',
				'value'			=> $vals['meta_value'],
				'data'			=> 'placeholder="Nama"'
			),
			array(
				'func'			=> 'opt_select_tags',
				'data'			=> $divisi,
				'key'			=> 'meta_note',
				'label'			=> 'Jabatan',
				'class'			=> 'input-circle',
				'select'		=> $vals['meta_note']
			)
		);
		
		$args['func'] = array('sobad_form');
		$args['data'] = array($data);
		
		return modal_admin($args);
	}

	public function _view($id=0){
		$id = str_replace('view_', '', $id);
		intval($id);

		$divisi = sobad_module::get_id($id,array('meta_note'));
		$divisi = sobad_module::_conv_divisi($divisi[0]['meta_note']);
		$divisi = implode(', ',$divisi['ID']);

		$args = sobad_user::get_all(array('picture','no_induk','name','divisi','status'),"AND divisi IN ($divisi)");

		$data['class'] = '';
		$data['table'] = array();

		$no = 0;
		foreach ($args as $key => $val) {
			$no += 1;

			$image = empty($val['notes_pict'])?'no-profile.jpg':$val['notes_pict'];
			$status = employee_absen::_conv_status($val['status']);

			$data['table'][$key]['tr'] = array('');
			$data['table'][$key]['td'] = array(
				'no'		=> array(
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
					$val['no_induk'],
					true
				),
				'Nama'		=> array(
					'left',
					'auto',
					$val['name'],
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
			'title'		=> 'Group User',
			'button'	=> '_btn_modal_save',
			'status'	=> array(),
			'func'		=> array('sobad_table'),
			'data'		=> array($data)
		);
		
		return modal_admin($args);
	}

	public static function _statusGroup($data = array()){
		$group = array();
		if(isset($data)){
			if(in_array(1,$data)){
				$group['status'] = 1;
			}else{
				$group['status'] = 0;
			}

			if(in_array(2,$data)){
				$group['group'] = 1;
			}else{
				$group['group'] = 0;
			}

			if(in_array(3,$data)){
				$group['punish'] = 1;
			}else{
				$group['punish'] = 0;
			}
		}

		return $group;
	}

	// ----------------------------------------------------------
	// Function category to database -----------------------------
	// ----------------------------------------------------------

	public function _callback($args=array(),$_args=array()){
		$_args = sobad_asset::ajax_conv_array_json($_args);
		$data = array(
			'data' 		=> explode(',', $args['meta_note']),
			'status'	=> $_args['meta_reff']
		);
		$data = serialize($data);

		$args['meta_key'] = 'group';
		$args['meta_note'] = $data;

		unset($args['meta_reff']);
		return $args;
	}
}