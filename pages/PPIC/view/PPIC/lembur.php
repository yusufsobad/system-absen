<?php

class infoLembur_ppic extends _page{

	protected static $object = 'infoLembur_ppic';

	protected static $table = 'sobad_overtime';

	// ----------------------------------------------------------
	// Layout category  ------------------------------------------
	// ----------------------------------------------------------

	protected function _array(){
		$args = array(
			'ID',
			'title',
			'post_date',
			'approve',
			'accept',
			'note',
		);

		return $args;
	}

	protected function table(){
		$data = array();
		$args = self::_array();

		$m = date('m');$y = date('Y');

		$user_id = get_id_user();
		$where = "AND YEAR(`abs-overtime`.post_date)='$y' AND MONTH(`abs-overtime`.post_date)='$m'";

		$object = self::$table;
		$args = $object::get_all($args,$where);

		$data['class'] = '';
		$data['table'] = array();

		$no = 0;
		foreach($args as $key => $val){
			$no += 1;
			$id = $val['ID'];

			$qty = 0;
			$lembur = sobad_overtime::get_details($id,array('status'));
			foreach ($lembur as $ky => $vl) {
				$qty += $vl['status']==2?0:1;
			}

			$detail = array(
				'ID'	=> 'detail_'.$id,
				'func'	=> '_detail',
				'color'	=> '',
				'icon'	=> '',
				'label'	=> $qty.' Orang'
			);

			$acc_sts = $val['approve']>=1?'disabled':'';
			$accept = array(
				'ID'	=> 'accept_'.$id,
				'func'	=> '_accept',
				'color'	=> 'green',
				'icon'	=> 'fa fa-check',
				'label'	=> 'Terima',
				'status'=> $acc_sts
			);

			$tanggal = $val['post_date'];
			$tanggal = conv_day_id($tanggal).', '.format_date_id($tanggal);

			$color = '#cb5a5e';
			if($val['accept']>=1){
				$color = '#26a69a';
			}
			$hrd = '<i class="fa fa-circle" style="color:'.$color.'">';
			
			$data['table'][$key]['tr'] = array('');
			$data['table'][$key]['td'] = array(
				'no'		=> array(
					'center',
					'5%',
					$no,
					true
				),
				'Tanggal'		=> array(
					'left',
					'17%',
					$tanggal,
					true
				),
				'Keterangan'	=> array(
					'left',
					'auto',
					$val['note'],
					true
				),
				'HRD'		=> array(
					'center',
					'7%',
					$hrd,
					true
				),
				'Jumlah'	=> array(
					'right',
					'10%',
					_modal_button($detail),
					true
				),
				'Accept'	=> array(
					'center',
					'10%',
					_click_button($accept),
					false
				),
			);
		}
		
		return $data;
	}

	private function head_title(){
		$args = array(
			'title'	=> 'Lembur <small>data lembur</small>',
			'link'	=> array(
				0	=> array(
					'func'	=> self::$object,
					'label'	=> 'lembur'
				)
			),
			'date'	=> false
		); 
		
		return $args;
	}

	protected function get_box(){
		$data = self::table();
		
		$box = array(
			'label'		=> 'Data Lembur',
			'tool'		=> '',
			'action'	=> '',
			'func'		=> 'sobad_table',
			'data'		=> $data
		);

		return $box;
	}

	protected function layout(){
		$box = self::get_box();
		
		$opt = array(
			'title'		=> self::head_title(),
			'style'		=> array(''),
			'script'	=> array('')
		);
		
		return portlet_admin($opt,$box);
	}

	// ----------------------------------------------------------
	// Database Lembur ------------------------------------------
	// ----------------------------------------------------------

	public static function _detail($id=0){
		return lembur_supervisor::_detail($id);
	}

	public static function _accept($id=0){
		$id = str_replace('accept_', '', $id);
		intval($id);

		$q = sobad_db::_update_single($id,'abs-overtime',array(
			'approve'	=> get_id_user(),
		));

		if($q!==0){
			$table = self::table();
			return table_admin($table);
		}
	}
}