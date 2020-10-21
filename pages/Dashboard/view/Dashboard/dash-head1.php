<?php
class dash_head1{
	public static function _layout(){
		metronic_layout::sobad_chart(self::_data());
	}

	public static function _data(){
		$chart[] = array(
			'func'	=> '_site_load',
			'data'	=> array(
				'id'		=> 'dash-punishment',
				'func'		=> 'dash_punishment',
				'status'	=> '',
				'col'		=> 8,
				'label'		=> 'Data Punishment',
				'type'		=> ''
			),
		);
		
		return $chart;
	}

	public static function _statistic(){
		$date = date('Y-m');
		$start_date = $date.'-01';
		$end_date = $date.'-'.sum_days(date('m'),date('Y'));

		$label = array();

		$data = array();
		$data[0]['label'] = 'Punishment';
		$data[0]['type'] = '';

		$data[0]['bgColor'] = array();
		$data[0]['brdColor'] = 'rgba(256,256,256,1)';

		$data[0]['data'] = array();

		$user = sobad_user::get_all(array('ID','name'));
		foreach ($user as $key => $val) {
			$log = sobad_user::count_log($val['ID'],"AND time_in>'07:59:59' AND _inserted BETWEEN '$start_date' AND '$end_date'");

			$color = 0;
			if($log>0){
				if($log>2){
					$color = 1; // orange
				}

				if($log>4){
					$color = 2; // merah
				}

				$label[] = $val['name'];
				$data[0]['data'][] = $log;
				$data[0]['bgColor'][] = dash_absensi::get_color($color,0.8);
			}
		}

		$args = array(
			'type'		=> 'horizontalBar',
			'label'		=> $label,
			'data'		=> $data,
			'option'	=> '_option_bar'
		);
		
		return $args;
	}
}