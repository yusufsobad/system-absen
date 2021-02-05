<?php
class dash_head1{
	public static function _layout(){
		metronic_layout::sobad_dashboard(self::_data());
	}

	public static function _data(){
		$args = array(
			'total'		=> absensi::_employees(),
			'intern'	=> absensi::_internship(),
			'masuk'		=> absensi::_inWork(),
			'pulang'	=> absensi::_outWork(),
			'izin'		=> absensi::_permitWork(),
			'cuti'		=> absensi::_holidayWork(),
			'luar kota'	=> absensi::_outCity(),
		);

		$notAbsen = ($args['total'] + $args['intern']) - ($args['masuk'] + $args['pulang'] + $args['izin'] + $args['cuti'] + $args['luar kota']);

		$dash[] = array(
			'func'	=> '_block_info',
			'data'	=> array(
				'icon'		=> '',
				'color'		=> 'grey-intense',
				'qty'		=> $notAbsen,
				'desc'		=> 'Tidak Absen',
				'button'	=> button_toggle_block(array('ID' => 'absen_0','func' => '_view_block'))
			)
		);
		
		$dash[] = array(
			'func'	=> '_block_info',
			'data'	=> array(
				'icon'		=> '',
				'color'		=> 'green-haze',
				'qty'		=> $args['masuk'],
				'desc'		=> 'Absen',
				'button'	=> button_toggle_block(array('ID' => 'absen_1','func' => '_view_block'))
			)
		);
		
		$dash[] = array(
			'func'	=> '_block_info',
			'data'	=> array(
				'icon'		=> '',
				'color'		=> 'purple-plum',
				'qty'		=> $args['cuti'],
				'desc'		=> 'Cuti',
				'button'	=> button_toggle_block(array('ID' => 'absen_3','func' => '_view_block'))
			)
		);
		
		$dash[] = array(
			'func'	=> '_block_info',
			'data'	=> array(
				'icon'		=> '',
				'color'		=> 'blue-madison',
				'qty'		=> $args['izin'],
				'desc'		=> 'Izin',
				'button'	=> button_toggle_block(array('ID' => 'absen_4','func' => '_view_block'))
			)
		);
		
		$dash[] = array(
			'func'	=> '_block_info',
			'data'	=> array(
				'icon'		=> '',
				'color'		=> 'red-intense',
				'qty'		=> $args['luar kota'],
				'desc'		=> 'Luar Kota',
				'button'	=> button_toggle_block(array('ID' => 'absen_5','func' => '_view_block'))
			)
		);
		
		return $dash;
	}
}