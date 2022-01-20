<?php
function _split_length_text($text='',$length=0,$nlbr='<br>'){
	// Check panjang title
	if(strlen($text)>$length){
		$_tmps=array();$_tmp = '';
		$_itm = explode(' ', $text);
		foreach ($_itm as $_ky => $_vl) {
			if(strlen($_tmp.$_vl)<=$length){
				$_tmps[] = $_vl;
				$_tmp .= $_vl.' ';
			}else{
				$_tmps[] = $nlbr.$_vl;
				$_tmp = $_vl.' ';
			}
		}

		$text = implode(' ', $_tmps);
	}

	return $text;
}

function surat_header(){
	?>
		<table style="width:100%;border-bottom: 2px solid  #15499a;">
			<tr>
				<td style="width:60%;padding-bottom: 10px;">
					<img style="width:320px;" src="../asset/img/logo-soloabadi.png">
				</td>
				<td style="width:40%;padding-bottom: 10px;">
					<div style="text-align: right;line-height: 1.4;">
						Jl. Slamet Raya Tawangsari<br>
						RT. 04 RW. 34 Mojosongo, Solo<br>
						Jawa Tengah - INDONESIA
					</div>
				</td>
			</tr>
		</table>
		<div>
			&nbsp;
		</div>
	<?php
}

function surat_kop($title='',$code=''){
	?>
		<div style="text-align:center;font-size: 12pt;font-family: calibriBold;margin-top: 15px;">
			<label style="line-height: 1.5;">
				PERJANJIAN KERJA<br>
				<?php print($title) ;?><br>
				No. {{no-surat}}/HRD-PK/<?php print($code) ;?>/{{romawi}}/{{year}}<br>
			</label>
			&nbsp;<br>
		</div>
	<?php
}

function surat_kop2(){
	?>
		<div style="text-align:center;font-size: 12pt;font-family: calibriBold;margin-top: 15px;">
			<label style="line-height: 1.6;">
				SURAT KEPUTUSAN<br>
				No. {{no-surat}}/HRD-SK/T/{{romawi}}/{{year}}<br>
			</label>
			&nbsp;<br>
			&nbsp;<br>
		</div>
	<?php
}

function surat_footer(){
	?>
		<table style="text-align: center;width: 100%;padding-top: 30px;font-family: times;">
			<tr>
				<td style="width: 50%;">HRD</td>
				<td style="width: 50%;">KARYAWAN</td>
			</tr>
			<tr>
				<td style="padding: 35px;">
					<i>&nbsp;</i>
				</td>
				<td>
					<i>&nbsp;</i>
				</td>
			</tr>
			<tr>
				<td style="text-transform: uppercase;">( {{name-hrd}} )</td>
				<td style="text-transform: uppercase;">( {{name}} )</td>
			</tr>
		</table>
	<?php
}

function surat_footer2(){
	?>
		<table style="text-align: center;width: 100%;padding-top: 20px;font-family: times;">
			<tr>
				<td>&nbsp;</td>
				<td>Surakarta, {{now}}<br>&nbsp;<br></td>
			</tr>
			<tr>
				<td style="width: 50%;">PIHAK PERTAMA<br>HRD</td>
				<td style="width: 50%;">PIHAK KEDUA<br>KARYAWAN</td>
			</tr>
			<tr>
				<td style="padding: 38px;">
					<i>&nbsp;</i>
				</td>
				<td>
					<i>&nbsp;</i>
				</td>
			</tr>
			<tr>
				<td style="text-transform: uppercase;">( {{name-hrd}} )</td>
				<td style="text-transform: uppercase;">( {{name}} )</td>
			</tr>
		</table>
	<?php
}

function surat_footer3(){
	?>
		<table style="text-align: center;width: 100%;padding-top: 30px;font-family: times;">
			<tr>
				<td>&nbsp;</td>
				<td>Surakarta, {{now}}<br>&nbsp;<br><br></td>
			</tr>
			<tr>
				<td style="width: 50%;">HRD</td>
				<td style="width: 50%;">KARYAWAN</td>
			</tr>
			<tr>
				<td style="padding: 55px;">
					<i>&nbsp;</i>
				</td>
				<td>
					<i>&nbsp;</i>
				</td>
			</tr>
			<tr>
				<td style="text-transform: uppercase;">( {{name-hrd}} )</td>
				<td style="text-transform: uppercase;">( {{name}} )</td>
			</tr>
		</table>
	<?php
}

// ---------------------------------------------------------------------------
// Training ------------------------------------------------------------------
// ---------------------------------------------------------------------------

function surat_training(){
	?>
		<page backtop="10mm" backbottom="20mm" backleft="15mm" backright="15mm" pagegroup="new">
			<?php
				surat_header();
				surat_kop('UNTUK MASA PERCOBAAN 3 BULAN','MP');
				content_training();
				surat_footer();
			?>
		</page>
	<?php
}

function content_training(){
	?>
		<div style="font-size: 11pt;line-height: 1.6;font-family: times;">
			Pada hari ini {{hari}} tanggal {{tanggal}} bulan {{bulan}} tahun {{year}} telah dibuat dan disepakati perjanjian kerja antara:
		</div>
		<table style="margin-left: 35px;font-size: 11pt;font-family: times;">
			<tr>
				<td style="width: 15px;">1.</td>
				<td style="width: 130px;">NIP</td>
				<td>: {{no-hrd}}</td>
			</tr>
			<tr>
				<td></td>
				<td>Nama</td>
				<td>: {{name-hrd}}</td>
			</tr>
			<tr>
				<td></td>
				<td>Jabatan</td>
				<td>: HRD</td>
			</tr>
		</table>
		<div style="font-family: times;">
			Dalam hal ini mewakili PT SOLO ABADI INDONESIA, untuk selanjutnya disebut PERUSAHAAN. 
		</div>
		<div>
			&nbsp;
		</div>
		<table style="margin-left: 35px;font-size: 11pt;vertical-align: top;font-family: times;">
			<tr>
				<td style="width: 15px;">2.</td>
				<td style="width: 130px;">NIP</td>
				<td style="width: 3px;">:</td>
				<td style="width: 300px;">{{nip}}</td>
			</tr>
			<tr>
				<td></td>
				<td>Nama Lengkap</td>
				<td>:</td>
				<td>{{name}}</td>
			</tr>
			<tr>
				<td></td>
				<td>Tempat/Tgl Lahir</td>
				<td>:</td>
				<td>{{ttl}}</td>
			</tr>
			<tr>
				<td></td>
				<td>Alamat</td>
				<td>:</td>
				<td>{{alamat}}</td>
			</tr>
			<tr>
				<td></td>
				<td>Jabatan/Divisi</td>
				<td>:</td>
				<td>{{divisi}}</td>
			</tr>
		</table>
		<div style="font-size: 11pt;margin-bottom: 5px;font-family: times;">
			Untuk selanjutnya disebut KARYAWAN.<br>&nbsp;<br>
		</div>
		<div style="font-size: 11pt;font-family: times;">
			Isi: 
		</div>
		<table style="font-size:11pt;margin-left: 20px;font-family: times;">
			<tr>
				<td style="width:20px;font-size: 16px;vertical-align: top;">•</td>
				<td>Masa percobaan berlaku selama 3 (tiga) bulan, apabila sebelum masa percobaan berakhir dan<br>karyawan dianggap tidak mampu, perusahaan berhak memutuskan hubungan kerja, karyawan<br>tidak menuntut kompensasi dalam bentuk apapun.</td>
			</tr>
			<tr>
				<td style="font-size: 16px;vertical-align: top;">•</td>
				<td>Karyawan wajib menaati semua ketentuan perusahaan selama jam kerja yang ditetapkan oleh perusahaan.</td>
			</tr>
			<tr>
				<td style="font-size: 16px;vertical-align: top;">•</td>
				<td>Karyawan wajib melaksanakan setiap tugas dan pekerjaan yang diberikan dengan penuh tanggung jawab. </td>
			</tr>
			<tr>
				<td style="font-size: 16px;vertical-align: top;">•</td>
				<td>Setelah masa percobaan 3 bulan akan dievaluasi untuk ditentukan layak atau tidak dilanjutkan ke jenjang berikutnya.</td>
			</tr>
			<tr>
				<td style="font-size: 16px;vertical-align: top;">•</td>
				<td>Karyawan masuk kerja mulai {{tanggal-masuk}}</td>
			</tr>
			<tr>
				<td style="font-size: 16px;vertical-align: top;">•</td>
				<td>Perjanjian ini berlaku sejak karyawan masuk kerja sampai dengan 3 (tiga) bulan kedepan, yaitu<br>tanggal {{tanggal-kontrak}}.</td>
			</tr>
		</table>
		<div style="font-size: 11pt;font-family: times;">
			&nbsp;<br>
			Demikian Surat Perjanjian Kerja ini dibuat tanpa adanya paksaan dari pihak manapun. 
		</div>
	<?php
}

// ---------------------------------------------------------------------------
// Kontrak -------------------------------------------------------------------
// ---------------------------------------------------------------------------

function surat_kontrak1(){
	$status = "Karyawan Kontrak Ke I PT SOLO ABADI INDONESIA";

	?>
		<page backtop="10mm" backbottom="20mm" backleft="15mm" backright="15mm" pagegroup="new">
			<?php
				surat_header();
				surat_kop('UNTUK WAKTU TERTENTU (PKWT)','WT');
				content_kontrak($status);
				surat_footer2();
			?>
		</page>
	<?php
}

function surat_kontrak2(){
	$status = "Karyawan Kontrak Ke II PT SOLO ABADI INDONESIA";

	?>
		<page backtop="10mm" backbottom="20mm" backleft="15mm" backright="15mm" pagegroup="new">
			<?php
				surat_header();
				surat_kop('UNTUK WAKTU TERTENTU (PKWT)','WT-II');
				content_kontrak($status);
				surat_footer2();
			?>
		</page>
	<?php
}

function content_kontrak($status=''){
	?>
		<div style="font-size: 11pt;line-height: 1.5;font-family: times;">
			Pada hari ini {{hari}} tanggal {{tanggal}} bulan {{bulan}} tahun {{year}} telah dibuat dan disepakati perjanjian kerja antara:
		</div>
		<table style="margin-left: 35px;font-size: 10pt;font-family: times;">
			<tr>
				<td style="width: 15px;">1.</td>
				<td style="width: 130px;">NIP</td>
				<td>: {{no-hrd}}</td>
			</tr>
			<tr>
				<td></td>
				<td>Nama</td>
				<td>: {{name-hrd}}</td>
			</tr>
			<tr>
				<td></td>
				<td>Jabatan</td>
				<td>: HRD</td>
			</tr>
		</table>
		<div style="line-height: 1.5;margin-top: 7px;font-family: times;">
			Dalam hal ini bertindak atas nama PT SOLO ABADI INDONESIA yang selanjutnya disebut sebagai PIHAK PERTAMA, dan 
		</div>
		<div>
			&nbsp;
		</div>
		<table style="margin-left: 35px;font-size: 11pt;vertical-align: top;margin-top: 10px;font-family: times;">
			<tr>
				<td style="width: 15px;">2.</td>
				<td style="width: 130px;">NIP</td>
				<td style="width: 3px;">:</td>
				<td style="width: 300px;">{{nip}}</td>
			</tr>
			<tr>
				<td></td>
				<td>Nama Lengkap</td>
				<td>:</td>
				<td>{{name}}</td>
			</tr>
			<tr>
				<td></td>
				<td>Tempat/Tgl Lahir</td>
				<td>:</td>
				<td>{{ttl}}</td>
			</tr>
			<tr>
				<td></td>
				<td>Alamat</td>
				<td>:</td>
				<td>{{alamat}}</td>
			</tr>
		</table>
		<div style="font-size: 11pt;line-height: 1.5;margin-bottom: 10px;font-family: times;">
			Dalam hal ini bertindak atas nama diri sendiri, yang selanjutnya disebut sebagai PIHAK KEDUA. <br>&nbsp;<br>
		</div>
		<div style="font-size: 11pt;line-height: 1.8;font-family: times;">
			Kedua belah pihak sepakat untuk mengikatkan diri dalam Perjanjian Kerja Untuk Waktu Tertentu dan menaati ketentuan-ketentuan maupun peraturan Perusahaan yang berlaku.<br>&nbsp;<br>
		</div>
		<div style="font-size: 11pt;line-height: 1.6;margin-bottom: 3px;font-family: times;">
			PIHAK PERTAMA menerima dan mempekerjakan PIHAK KEDUA sebagai:
		</div>
		<table style="font-size:11pt;margin-left: 20px;font-family: times;">
			<tr>
				<td style="width: 15px;font-size: 14px;vertical-align: top;">•</td>
				<td style="width: 100px;">Status</td>
				<td style="width: 3px;">:</td>
				<td><?php print($status) ;?></td>
			</tr>
			<tr>
				<td style="font-size: 14px;vertical-align: top;">•</td>
				<td>Masa Kontrak</td>
				<td>:</td>
				<td>1 Tahun terhitung sejak {{tanggal-masuk}} sampai dengan {{tanggal-kontrak}}</td>
			</tr>
			<tr>
				<td style="font-size: 14px;vertical-align: top;">•</td>
				<td>Jabatan/Divisi</td>
				<td>:</td>
				<td>{{divisi}}</td>
			</tr>
		</table>
		<div style="font-size: 11pt;line-height: 1.5;margin-top: 5px;font-family: times;">
			&nbsp;<br>
			Demikian Surat Perjanjian Kerja ini dibuat tanpa adanya paksaan dari pihak manapun. 
		</div>
	<?php
}

// ---------------------------------------------------------------------------
// Kontrak -------------------------------------------------------------------
// ---------------------------------------------------------------------------

function surat_tetap(){
	$status = "Karyawan Kontrak Ke I PT SOLO ABADI INDONESIA";

	?>
		<page backtop="10mm" backbottom="20mm" backleft="15mm" backright="15mm" pagegroup="new">
			<?php
				surat_header();
				surat_kop2();
				content_tetap();
				surat_footer3();
			?>
		</page>
	<?php
}

function content_tetap(){
	?>
		<div style="font-size: 11pt;line-height: 1.6;margin-bottom: 10px;font-family: times;">
			Berdasarkan kebijakan Manajemen PT SOLO ABADI INDONESIA dan melalui beberapa tahap<br>evaluasi, maka dengan ini ditetapkan bahwa:
		</div>
		<table style="margin-left: 35px;font-size: 11pt;vertical-align: top;font-family: times;">
			<tr>
				<td style="width: 15px;">&nbsp;</td>
				<td style="width: 130px;">NIP</td>
				<td style="width: 3px;">:</td>
				<td style="width: 300px;">{{nip}}</td>
			</tr>
			<tr>
				<td></td>
				<td>Nama Lengkap</td>
				<td>:</td>
				<td>{{name}}</td>
			</tr>
			<tr>
				<td></td>
				<td>Tempat/Tgl Lahir</td>
				<td>:</td>
				<td>{{ttl}}</td>
			</tr>
			<tr>
				<td></td>
				<td>Alamat</td>
				<td>:</td>
				<td style="line-height: 1.6;">{{alamat}}</td>
			</tr>
		</table>
		<div style="font-size: 11pt;line-height: 1.6;margin-top: 10px;font-family: times;">
			Telah secara resmi diangkat menjadi <strong>KARYAWAN TETAP PT SOLO ABADI INDONESIA</strong><br> terhitung sejak tanggal {{tanggal-masuk}} dengan Jabatan sebagai {{divisi}}.<br>&nbsp;<br>
		</div>
		<div style="font-size: 11pt;line-height: 1.6;margin-bottom: 20px;font-family: times;">
			Segala hak dan kewajiban lainnya diatur sebagaimana tercantum dalam ketentuan-ketentuan<br>maupun Peraturan Perusahaan yang berlaku.
		</div>
	<?php
}