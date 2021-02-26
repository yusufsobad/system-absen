<?php
class dash_head3{
	public static function _layout(){
		$data = self::_data();
		metronic_layout::sobad_chart($data);
	}

	public static function _data(){
		$chart[] = array(
			'func'	=> '_site_load',
			'data'	=> array(
				'id'		=> 'dash-sex',
				'func'		=> 'dash_comparison',
				'status'	=> '',
				'col'		=> 4,
				'label'		=> 'Perbandingan Jenis Kelamin',
				'type'		=> 'sex'
			),
		);

		$chart[] = array(
			'func'	=> '_site_load',
			'data'	=> array(
				'id'		=> 'dash-age',
				'func'		=> 'dash_comparison',
				'status'	=> '',
				'col'		=> 4,
				'label'		=> 'Perbandingan Usia',
				'type'		=> 'age'
			),
		);

		$chart[] = array(
			'func'	=> '_site_load',
			'data'	=> array(
				'id'		=> 'dash-residence',
				'func'		=> 'dash_comparison',
				'status'	=> '',
				'col'		=> 4,
				'label'		=> 'Perbandingan Asal Daerah',
				'type'		=> 'residence'
			),
		);
		
		return $chart;
	}

	public static function _sex(){
		$user = sobad_user::get_all(array('status','_sex'),"AND status!='0'");

		$sex = array('Karyawan','Internship');

		$status = array();
		foreach ($user as $key => $val) {
			$sts = 0;
			if($val['status']==7){
				$sts = 1;
			}

			if(!isset($status[$sts])){
				$status[$sts] = array();
			}

			if(!isset($status[$sts][$val['_sex']])){
				$status[$sts][$val['_sex']] = 0;
			}

			$status[$sts][$val['_sex']] += 1;
		}

		foreach ($status as $key => $val) {
			$data[0]['data'][$key] = isset($val['male'])?$val['male']:0;
			$data[1]['data'][$key] = isset($val['female'])?$val['female']:0;
		}

		$data[0]['label'] = 'Laki-Laki';
		$data[1]['label'] = 'Perempuan';

		$data[0]['type'] = 'bar';
		$data[1]['type'] = 'bar';

		$jml = 2;
		for($i=0;$i<$jml;$i++){
			$data[$i]['bgColor'] = dash_absensi::get_color($i,0.5);
			$data[$i]['brdColor'] = dash_absensi::get_color($i);
		}

		$args = array(
			'type'		=> 'bar',
			'label'		=> $sex,
			'data'		=> $data,
			'option'	=> ''
		);
		
		return $args;
	}

	public static function _age(){
		$now = strtotime(date('Y-m-d'));
		$user = sobad_user::get_all(array('ID','status','_birth_date'),"AND status NOT IN ('0','7')");

		$label = array('16 - 20','21 - 25','26 - 30','31 - 35','36 - 40','41 - 45','46 - 50','51 - 55','56 - 60');
		rsort($label);

		$data = array();
		$data[0]['label'] = 'Usia';
		$data[0]['type'] = '';

		$data[0]['bgColor'] = array();
		$data[0]['brdColor'] = 'rgba(256,256,256,1)';

		$usia = array();
		foreach ($user as $key => $val) {
			$umur = date($val['_birth_date']);
			$umur = strtotime($umur);
			$umur = $now - $umur;
			$umur = floor($umur / (60 * 60 * 24 * 365));

			foreach ($label as $_key => $_val) {
				$range = explode('-', $_val);
				if($umur >= intval($range[0]) && $umur <= intval($range[1])){
					if(!isset($usia[$_key])){ 
						$usia[$_key] = 0;
					}

					$usia[$_key] += 1;
				}
			}
		}

		foreach ($label as $key => $val){
			$data[0]['data'][] = isset($usia[$key])?$usia[$key]:0;
			$data[0]['bgColor'][] = dash_absensi::get_color(0,0.8);
		}

		$args = array(
			'type'		=> 'horizontalBar',
			'label'		=> $label,
			'data'		=> $data,
			'option'	=> '_option_bar'
		);
		
		return $args;
	}

	public static function _residence(){
		$now = strtotime(date('Y-m-d'));
		$user = sobad_user::get_all(array('ID','status','_city'),"AND status NOT IN ('0','7')");

		$label = array('Undefined');
		$data = array();

		$data[0]['label'] = 'Kota';
		$data[0]['type'] = 'bar';

		$data[0]['bgColor'] = dash_absensi::get_color(0,0.5);
		$data[0]['brdColor'] = dash_absensi::get_color(0);

		$city = array();$_label = array();
		foreach ($user as $key => $val) {
			if(empty($val['_city'])){
				$val['_city'] = 0;
			}

			if($val['_city']>0){
				$wilayah = sobad_wilayah::get_city($val['_city']);
				$check = array_filter($wilayah);
				if(empty($check)){
					$val['_city'] = 0;
				}
			}

			if(!isset($city[$val['_city']])){
				if(!$val['_city']==0){
					$_label[] = $wilayah[0]['kabupaten'];
				}

				$city[$val['_city']] = 0;
			}

			$city[$val['_city']] += 1;
		}

		if(!isset($city[0])){
			unset($label[0]);
		}

		$label = array_merge($label,$_label);
		foreach ($city as $key => $val){
			$data[0]['data'][] = $val;
		}

		$args = array(
			'type'		=> 'bar',
			'label'		=> $label,
			'data'		=> $data,
			'option'	=> ''
		);
		
		return $args;
	}
}