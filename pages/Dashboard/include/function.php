<?php

include 'kontrak.php';

function style_punishment(){
	?>
		table.sobad-punishment{
			padding-left:25px;
			padding-right:25px;
		}

		table.sobad-punishment thead tr{
			text-align:center;
		}

		table.sobad-punishment thead tr.default{
			background-color:#a4a4ff;
		}

		table.sobad-punishment thead tr th{
			font-size:18px;
		}

		table.sobad-punishment tbody tr.default{
			background-color:#c2c2fb;
		}

		table.sobad-punishment tbody tr.danger{
			background-color:#ff1b1b;
		}

		table.sobad-punishment tbody tr.warning{
			background-color:#ffc310;
		}

		table.sobad-punishment thead tr th, table.sobad-punishment tbody tr td {
    		padding: 3px;
    		text-align:center;
		}

		ol li{
			list-style-type: decimal;
		}

		ul li{
			list-style-type: lower-alpha;
			padding-left:5px;
		}
	<?php
}

function style_history(){
	?>
		table.table {
		    width: 100%;
		}

		.table-bordered thead tr th, .table-bordered tbody tr td {
		    padding: 7px;
		}

		.table-bordered thead tr th {
			font-size:16px;
		    font-family: calibriBold;
		}
	<?php
}

function style_gantiJam(){
	?>
		table.table {
		    width: 100%;
		}

		.table-bordered thead tr th, .table-bordered tbody tr td {
		    padding: 5px;
		}

		.table-bordered thead tr th {
			font-size:16px;
		    font-family: calibriBold;
		}
	<?php
}

function get_rule_absen($first='00:00:00',$last='00:00:00',$worktime=0,$day=0){
	$waktu = _conv_time($first,$last,2);

	$restTime = 0;
	if(!empty($worktime)){
		$work = sobad_work::get_id($worktime,array('note'),"AND days='$day'");
		$check = array_filter($work);
		if(!empty($check)){
			$rests = array();
			$pointA = array(); $pointB = array();

			$work = explode(',', $work[0]['note']);
			$rests[0] = array('00:00:00','00:00:00');
			foreach ($work as $key => $val) {
				$rest = explode('-',$val);

			//Range Waktu
				$check = array_filter($pointA);
				if(empty($check)){
					if($rest[0]>$first){
						$pointA = array(1,$key);
					}else if($rest[0]<=$first && $rest[1]>=$first){
						$rest[0] = $first;
						$pointA = array(2,$key+1);
					}else if($rest[1]<$first){
						$pointA = array(3,$key+1);
					}
				}

				$check = array_filter($pointB);
				if(empty($check)){
					if($rest[0]>$last){
						$pointB = array(1,$key);
					}else if($rest[0]<=$last && $rest[1]>=$last){
						$rest[1] = $last;
						$pointB = array(2,$key+1);
					}else if($rest[1]<$first){
						$pointB = array(3,$key+1);
					}
				}

				$rests[] = $rest;
			}

			//Calculation jam istirahat
			if($pointB[1]>$pointA[1]){
				for ($i=$pointA[1]; $i<=$pointB[1] ; $i++) { 
					$restTime += _conv_time($rests[$i][0],$rests[$i][1],2);
				}
			}else{
				if($pointB[0]==2 && $pointA[0]==2){
					$waktu = 0;
				}
			}
		}
	}

	$waktu -= $restTime;

	if($waktu<=30){
		//Jika Izin kurang dari sama dengan 30 menit, Tidak ganti Jam
		return array(
			'time'		=> $waktu,
			'status'	=> 'Izin',
			'type'		=> 0
		);
	}

	$_check = $waktu % 30;
	if($_check<=10){
		$punish = $waktu - $_check;
	}else{
		$punish = $waktu + (30 - $_check);
	}

	//Jika Izin kurang dari setengah Hari, ganti Jam
	if($waktu>30 && $waktu<210){
		return array(
			'time'		=> $punish,
			'status'	=> 'Ganti Jam',
			'type'		=> 2
		);
	}

	//Jika Izin setengah hari, Ambil Cuti setengah
	if($waktu>=210 && $waktu<300){
		return array(
			'time'		=> $waktu,
			'hour'		=> $waktu%60,
			'punish'	=> $punish,
			'value'		=> 0.5,
			'status'	=> 'Cuti',
			'type'		=> 3
		);
	}

	//Jika Izin Full, Cuti 1 hari
	if($waktu>=300){
		return array(
			'time'		=> $waktu,
			'hour'		=> $waktu%60,
			'punish'	=> $punish,
			'value'		=> 1,
			'status'	=> 'Cuti',
			'type'		=> 3
		);
	}
}

function set_rule_absen($first='00:00:00',$last='00:00:00',$args=array()){
	$status = get_rule_absen($first,$last,$args['work'],$args['day']);

	if($status['type']==3){
		$user = sobad_user::get_id($args['user'],array('dayOff'));
		$user = $user[0];

		$cuti = $user['dayOff'] - $status['value'];
		if($user['dayOff']>0){
			$_cuti = $cuti;
			if($cuti<0){
				$_cuti = 0;				
				$status['punish'] -= ($user['dayOff'] * 420);
			}
			set_rule_cuti($status['value'],$_cuti,$args);
		}

		if($cuti<0){
			$status['status'] = 'Ganti Jam';
			$status['time'] = $status['punish'];
			$status['type'] = 2;
		}
	}

	if($status['type']==2){
		sobad_db::_insert_table('abs-log-detail',array(
			'log_id'		=> $args['id'],
			'date_schedule'	=> $args['date'],
			'times'			=> $status['time'],
			'type_log'		=> 2
		));
	}

	if($status['type']==0){
		sobad_db::_insert_table('abs-log-detail',array(
			'log_id'		=> $args['id'],
			'date_schedule'	=> $args['date'],
			'times'			=> $status['time'],
			'type_log'		=> 9
		));
	}

	return $status;
}

function set_rule_cuti($num_day=0,$cuti=0,$args=array()){
	sobad_db::_update_single($args['user'],'abs-user',array('ID' => $args['user'], 'dayOff' => $cuti));

	//Set Permit
	sobad_db::_insert_table('abs-permit',array(
		'user'			=> $args['user'],
		'start_date'	=> $args['date'],
		'range_date'	=> $args['date'],
		'num_day'		=> $num_day,
		'type_date'		=> 1,
		'type'			=> 3,
		'note'			=> $args['note']
	));
}

// --------------------------------------------------------------------------------------------
// --------------------------------------------------------------------------------------------
// --------------------------------------------------------------------------------------------

function content_html_employee($args=array()){
	$user = 0;
	$users = $args['checked_ids'];	
	if(count($users)>0){
		$user = implode(',', $users);
	}

	$where = "AND `abs-user`.ID IN ($user)";
	$data = sobad_user::get_employees(array(),$where);

	?>	
		<table style="width:100%;border-collapse:collapse;" class="layout1">
			<thead>
				<tr>
					<td rowspan="3" style="width:34px;font-family: calibriBold;text-align: center;border:1px solid #000;">No</td>
					<td colspan="20" style="font-family: calibriBold;border:1px solid #000;background-color: #2986cc;">DATA PRIBADI</td>
					<td colspan="8" style="font-family: calibriBold;border:1px solid #000;background-color: #ffc0cb;">Kontak Dalam Keadaan Darurat</td>
					<td colspan="18" style="font-family: calibriBold;border:1px solid #000;background-color: #ff0000;">DATA PERUSAHAAN</td>
				</tr>
				<tr>
					<!-- Biodata Diri -->
					<td rowspan="2" style="width:160px;font-family: calibriBold;text-align: center;border:1px solid #000;">Nama Lengkap</td>
					<td rowspan="2" style="width:96px;font-family: calibriBold;text-align: center;border:1px solid #000;">Nama Panggilan</td>
					<td rowspan="2" style="width:137px;font-family: calibriBold;text-align: center;border:1px solid #000;">No. KTP</td>
					<td rowspan="2" style="width:137px;font-family: calibriBold;text-align: center;border:1px solid #000;">No. KK</td>
					<td rowspan="2" style="width:77px;font-family: calibriBold;text-align: center;border:1px solid #000;">No. PASPOR</td>
					<td rowspan="2" style="width:134px;font-family: calibriBold;text-align: center;border:1px solid #000;">No. NPWP</td>
					<td rowspan="2" style="width:80px;font-family: calibriBold;text-align: center;border:1px solid #000;">Jenis Kelamin</td>
					<td rowspan="2" style="width:64px;font-family: calibriBold;text-align: center;border:1px solid #000;">Agama</td>
					<td rowspan="2" style="width:114px;font-family: calibriBold;text-align: center;border:1px solid #000;">Tempat Lahir</td>
					<td rowspan="2" style="width:134px;font-family: calibriBold;text-align: center;border:1px solid #000;">Tanggal Lahir</td>
					<td rowspan="2" style="width:75px;font-family: calibriBold;text-align: center;border:1px solid #000;">UMUR</td>
					<td rowspan="2" style="width:103px;font-family: calibriBold;text-align: center;border:1px solid #000;">GOLONGAN DARAH</td>
					<td rowspan="2" style="width:110px;font-family: calibriBold;text-align: center;border:1px solid #000;">Status Perkawinan</td>
					<td rowspan="2" style="width:416px;font-family: calibriBold;text-align: center;border:1px solid #000;">Alamat sesuai KTP</td>
					<td rowspan="2" style="width:120px;font-family: calibriBold;text-align: center;border:1px solid #000;">Kota Asal</td>
					<td rowspan="2" style="width:416px;font-family: calibriBold;text-align: center;border:1px solid #000;">Alamat Domisili</td>
					<td rowspan="2" style="width:120px;font-family: calibriBold;text-align: center;border:1px solid #000;">KOTA DOMISILI</td>
					<td rowspan="2" style="width:93px;font-family: calibriBold;text-align: center;border:1px solid #000;">No. Telp Rumah</td>
					<td rowspan="2" style="width:102px;font-family: calibriBold;text-align: center;border:1px solid #000;">No. Handphone</td>
					<td rowspan="2" style="width:227px;font-family: calibriBold;text-align: center;border:1px solid #000;">E-mail Pribadi</td>

					<!-- Kontak Dalam Keadaan Darurat -->
					<td colspan="4" style="font-family: calibriBold;text-align: center;border:1px solid #000;">KONTAK DARURAT 1</td>
					<td colspan="4" style="font-family: calibriBold;text-align: center;border:1px solid #000;">KONTAK DARURAT 2</td>

					<!-- Data Perusahaan -->
					<td rowspan="2" style="width:65px;font-family: calibriBold;text-align: center;border:1px solid #000;background-color: #ff0000;">NIK</td>
					<td rowspan="2" style="width:134px;font-family: calibriBold;text-align: center;border:1px solid #000;">Masuk Tanggal</td>
					<td rowspan="2" style="width:158px;font-family: calibriBold;text-align: center;border:1px solid #000;">Jabatan</td>
					<td rowspan="2" style="width:224px;font-family: calibriBold;text-align: center;border:1px solid #000;">E-mail Perusahaan</td>
					<td rowspan="2" style="width:87px;font-family: calibriBold;text-align: center;border:1px solid #000;">Status Karyawan</td>
					<td rowspan="2" style="width:64px;font-family: calibriBold;text-align: center;border:1px solid #000;">Cuti</td>
					<td colspan="2" style="font-family: calibriBold;text-align: center;border:1px solid #000;">Masa Percobaan</td>
					<td colspan="2" style="font-family: calibriBold;text-align: center;border:1px solid #000;">Kontrak 1</td>
					<td colspan="2" style="font-family: calibriBold;text-align: center;border:1px solid #000;">Kontrak 2</td>
					<td rowspan="2" style="width:131px;font-family: calibriBold;text-align: center;border:1px solid #000;">Tetap</td>
					<td colspan="3" style="font-family: calibriBold;text-align: center;border:1px solid #000;background-color: #40e0d0;">Data Rekening</td>
					<td colspan="2" style="font-family: calibriBold;text-align: center;border:1px solid #000;background-color: #8ff991;">Data BPJS</td>
				</tr>
				<tr>
					<!-- KONTAK DARURAT 1 -->
					<td style="width:200px;font-family: calibriBold;text-align: center;border:1px solid #000;background-color: #ffc0cb;">Nama</td>
					<td style="width:87px;font-family: calibriBold;text-align: center;border:1px solid #000;">Hubungan</td>
					<td style="width:424px;font-family: calibriBold;text-align: center;border:1px solid #000;">Alamat Darurat</td>
					<td style="width:112px;font-family: calibriBold;text-align: center;border:1px solid #000;">No. HP</td>
					
					<!-- KONTAK DARURAT 2 -->
					<td style="width:200px;font-family: calibriBold;text-align: center;border:1px solid #000;">Nama</td>
					<td style="width:87px;font-family: calibriBold;text-align: center;border:1px solid #000;">Hubungan</td>
					<td style="width:424px;font-family: calibriBold;text-align: center;border:1px solid #000;">Alamat Darurat</td>
					<td style="width:112px;font-family: calibriBold;text-align: center;border:1px solid #000;">No. HP</td>

					<!-- Masa Percobaan -->
					<td style="width:134px;font-family: calibriBold;text-align: center;border:1px solid #000;">Mulai</td>
					<td style="width:134px;font-family: calibriBold;text-align: center;border:1px solid #000;">Berakhir</td>

					<!-- Kontrak 1 -->
					<td style="width:134px;font-family: calibriBold;text-align: center;border:1px solid #000;">Mulai</td>
					<td style="width:134px;font-family: calibriBold;text-align: center;border:1px solid #000;">Berakhir</td>

					<!-- Kontrak 2 -->
					<td style="width:134px;font-family: calibriBold;text-align: center;border:1px solid #000;">Mulai</td>
					<td style="width:134px;font-family: calibriBold;text-align: center;border:1px solid #000;">Berakhir</td>

					<!-- Data Rekening -->
					<td style="width:136px;font-family: calibriBold;text-align: center;border:1px solid #000;background-color: #40e0d0;">Bank</td>
					<td style="width:128px;font-family: calibriBold;text-align: center;border:1px solid #000;">No. Rekening</td>
					<td style="width:158px;font-family: calibriBold;text-align: center;border:1px solid #000;">Atas Nama</td>

					<!-- Data BPJS -->
					<td style="width:167px;font-family: calibriBold;text-align: center;border:1px solid #000;">No. BPJS Kesehatan</td>
					<td style="width:109px;font-family: calibriBold;text-align: center;border:1px solid #000;">No. BPJS TK</td>
				</tr>
			</thead>

			<tbody>
				<?php
					$_status = array('belum menikah','menikah','cerai mati','cerai hidup');
					$_sex = array('male' => 'Laki - Laki','female' => 'Perempuan');
					$_agama = array(1 => 'Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu', 'Kepercayaan');

					$no = 0;$now = time();
					foreach ($data as $key => $val) {
						$no += 1;

						$umur = date($val['_birth_date']);
						$umur = strtotime($umur);
						$umur = $now - $umur;
						$umur = floor($umur / (60 * 60 * 24 * 365))." Tahun";

						$_sts_def = array('start'		=> '','finish'	=> '');
						$_sts_masa = array();
						for($m=1;$m<=4;$m++){
							$_sts_masa[$m] = $_sts_def;
						}

						$_in_date = format_date_id($val['_entry_date']);
						for($mi=1;$mi<=$val['status'];$mi++){
							$life = employee_absen::_check_lifetime($mi,$val['_entry_date']);
							$_sts_masa[$mi]['start'] = $_in_date;
							$_sts_masa[$mi]['finish'] = $life['end_date'];

							$_in_date = $life['end_date'];
						}

						$_address = sobad_wilayah::_conv_address($val['_address'],array(
							'province'		=> $val['_province'],
							'city'			=> $val['_city'],
							'subdistrict'	=> $val['_subdistrict'],
							'postcode'		=> $val['_postcode'],
						));
						$_address = $_address['result'];

						$place = sobad_wilayah::get_city($val['_place_date']);
						$place = $place[0]['tipe'] . " " . $place[0]['kabupaten'];

						$jabatan = sobad_module::get_id($val['divisi'],array('meta_value'));
						$jabatan = $jabatan[0]['meta_value'];

						$kelamin = isset($_sex[$val['_sex']])?$_sex[$val['_sex']]:'-';
						$status = employee_absen::_conv_status($val['status']);
						$lahir = format_date_id($val['_birth_date']);
						$entry_date = format_date_id($val['_entry_date']);
						?>
							<tr class="piutang_td">
								<!-- BIODATA DIRI -->
								<td style="border:1px solid #000;"><?php print($no) ;?>.</td>
								<td style="border:1px solid #000;"><?php print($val['name']) ;?></td>
								<td style="border:1px solid #000;"><?php print($val['_nickname']) ;?></td>
								<td style="border:1px solid #000;"></td>
								<td style="border:1px solid #000;"></td>
								<td style="border:1px solid #000;"></td>
								<td style="border:1px solid #000;"></td>
								<td style="border:1px solid #000;"><?php print($kelamin) ;?></td>
								<td style="border:1px solid #000;"><?php print($_agama[$val['_religion']]) ;?></td>
								<td style="border:1px solid #000;"><?php print($place) ;?></td>
								<td style="border:1px solid #000;"><?php print($lahir) ;?></td>
								<td style="border:1px solid #000;"><?php print($umur) ;?></td>
								<td style="border:1px solid #000;"></td>
								<td style="border:1px solid #000;"><?php print($_status[$val['_marital']]) ;?></td>
								<td style="border:1px solid #000;"><?php print($_address) ;?></td>
								<td style="border:1px solid #000;"></td>
								<td style="border:1px solid #000;"></td>
								<td style="border:1px solid #000;"></td>
								<td style="border:1px solid #000;"></td>
								<td style="border:1px solid #000;"><?php print($val['phone_no']) ;?></td>
								<td style="border:1px solid #000;"></td>

								<!-- KONTAK DARURAT 1 -->
								<td style="border:1px solid #000;"></td>
								<td style="border:1px solid #000;"></td>
								<td style="border:1px solid #000;"></td>
								<td style="border:1px solid #000;"></td>

								<!-- KONTAK DARURAT 2 -->
								<td style="border:1px solid #000;"></td>
								<td style="border:1px solid #000;"></td>
								<td style="border:1px solid #000;"></td>
								<td style="border:1px solid #000;"></td>

								<!-- Data Perusahaan -->
								<td style="border:1px solid #000;"><?php print($val['no_induk']) ;?></td>
								<td style="border:1px solid #000;"><?php print($entry_date) ;?></td>
								<td style="border:1px solid #000;"><?php print($jabatan) ;?></td>
								<td style="border:1px solid #000;"></td>
								<td style="border:1px solid #000;"><?php print($status) ;?></td>
								<td style="border:1px solid #000;"><?php print($val['dayOff']) ;?> Hari</td>
									<!-- Data Perusahaan :: Masa Percobaan -->
								<td style="border:1px solid #000;"><?php print($_sts_masa[1]['start']) ;?></td>
								<td style="border:1px solid #000;"><?php print($_sts_masa[1]['finish']) ;?></td>
									<!-- Data Perusahaan :: Kontrak 1 -->
								<td style="border:1px solid #000;"><?php print($_sts_masa[2]['start']) ;?></td>
								<td style="border:1px solid #000;"><?php print($_sts_masa[2]['finish']) ;?></td>
									<!-- Data Perusahaan :: Kontrak 2 -->
								<td style="border:1px solid #000;"><?php print($_sts_masa[3]['start']) ;?></td>
								<td style="border:1px solid #000;"><?php print($_sts_masa[3]['finish']) ;?></td>
									<!-- Data Perusahaan :: Tetap -->
								<td style="border:1px solid #000;"><?php print($_sts_masa[4]['start']) ;?></td>
									<!-- Data Perusahaan :: Data Rekening -->
								<td style="border:1px solid #000;"></td>
								<td style="border:1px solid #000;"></td>
								<td style="border:1px solid #000;"></td>
									<!-- Data Perusahaan :: Data BPJS -->
								<td style="border:1px solid #000;"></td>
								<td style="border:1px solid #000;"></td>
							</tr>
						<?php
					}
				?>
			</tbody>
		</table>
	<?php
}