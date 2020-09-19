<?php

function format_currency(){
	$current = get_locale();
	$args = array(
		'id_ID'	=> 'Rp',
		'en_US'	=> '$',
	);
	
	return $args[$current];
}

function format_nominal($nominal){
	$current = get_locale();
	$args = array(
		'id_ID'	=> array(0,',','.'),
		'en_US'	=> array(2,'.',','),
	);
	
	$val = $args[$current];
	
	return number_format($nominal,$val[0],$val[1],$val[2]);
}

function format_quantity($nominal){
	$current = get_locale();
	$args = array(
		'id_ID'	=> array(2,',','.'),
		'en_US'	=> array(2,'.',','),
	);
	
	$val = $args[$current];
	
	return number_format($nominal,$val[0],$val[1],$val[2]);
}

function clear_format($nominal){
	$current = get_locale();
	$args = array(
		'id_ID'	=> array(2,',','.'),
		'en_US'	=> array(2,'.',','),
	);

	$val = $args[$current];
	$nominal = str_replace($val[2],'', $nominal);
	$nominal = str_replace($val[1],'.', $nominal);

	return floatval($nominal);
}

function format_number_currency($current,$nominal){
	$format = format_currency($current);
	$format .= '<span class="sobad_currency"> ';
	$format .= format_nominal($current,$nominal);
	$format .= '</span>';
	
	return $format;
}

function format_date_id($date=''){
	$date = empty($date)?strtotime(date('Y-m-d')):strtotime($date);
	$date = date('Y-m-d',$date);
	$date = explode('-',$date);
	
	$y = $date[0];
	$m = conv_month_id($date[1]);
	$d = $date[2];
	
	return $d.' '.$m.' '.$y;
}

function format_time_id($date=''){
	$date = strtotime($date);
	if(!empty($date)){
		$date = date('H:i',$date);
	}

	return $date;
}

function sum_days($month=1,$year=1){
	return cal_days_in_month(CAL_GREGORIAN, $month, $year);
}

function conv_day_id($date){
	$date = strtotime($date);
	$int = date('w',$date);
	
	$args = array(
		'Minggu',
		'Senin',
		'Selasa',
		'Rabu',
		'Kamis',
		'Jum\'at',
		'Sabtu'
	);
	
	return $args[$int];
}

function conv_month_id($int=''){
	$int = intval($int);
	
	$args = array(
		1 => 'Januari',
		'Februari',
		'Maret',
		'April',
		'Mei',
		'Juni',
		'Juli',
		'Agustus',
		'September',
		'Oktober',
		'November',
		'Desember'
	);

	if(!empty($int)){
		$args = $args[$int];
	}
	
	return $args;
}

function format_terbilang($x=0) {
  $angka = ["", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas"];

  if ($x < 12)
    return " " . $angka[$x];
  elseif ($x < 20)
    return format_terbilang($x - 10) . " belas";
  elseif ($x < 100)
    return format_terbilang($x / 10) . " puluh" . format_terbilang($x % 10);
  elseif ($x < 200)
    return "seratus" . format_terbilang($x - 100);
  elseif ($x < 1000)
    return format_terbilang($x / 100) . " ratus" . format_terbilang($x % 100);
  elseif ($x < 2000)
    return "seribu" . format_terbilang($x - 1000);
  elseif ($x < 1000000)
    return format_terbilang($x / 1000) . " ribu" . format_terbilang($x % 1000);
  elseif ($x < 1000000000)
    return format_terbilang($x / 1000000) . " juta" . format_terbilang($x % 1000000);
}