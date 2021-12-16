<?php
class ppic_head1{
	public static function _layout(){
		metronic_layout::sobad_dashboard(self::_data());
	}

	public static function _data(){

		$dash[] = array(
			'func'	=> '_block_info',
			'data'	=> array(
				'icon'		=> '',
				'color'		=> 'grey-intense',
				'qty'		=> 0,
				'desc'		=> 'Undefined',
				'button'	=> button_toggle_block(array('ID' => 'absen_0','func' => '_view_block'))
			)
		);
		
		return $dash;
	}
}